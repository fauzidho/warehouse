import { initializeApp } from "firebase/app";
import { 
    getFirestore, 
    collection, 
    doc, 
    setDoc, 
    getDocs, 
    onSnapshot, 
    serverTimestamp,
    query,
    orderBy,
    limit 
} from "firebase/firestore";
import { getDatabase, ref, set as rtdbSet, onValue } from "firebase/database";

// Firebase Configuration Toko Kopi
export const firebaseConfig = {
    apiKey: import.meta.env.VITE_FIREBASE_API_KEY || "AIzaSyDSfrl6WclAYdSoPtxsFt1Pvh5N2NReqMw",
    authDomain: import.meta.env.VITE_FIREBASE_AUTH_DOMAIN || "toko-kopi-8abbd.firebaseapp.com",
    databaseURL: import.meta.env.VITE_FIREBASE_DATABASE_URL || "https://toko-kopi-8abbd-default-rtdb.asia-southeast1.firebasedatabase.app",
    projectId: import.meta.env.VITE_FIREBASE_PROJECT_ID || "toko-kopi-8abbd",
    storageBucket: import.meta.env.VITE_FIREBASE_STORAGE_BUCKET || "toko-kopi-8abbd.firebasestorage.app",
    messagingSenderId: import.meta.env.VITE_FIREBASE_MESSAGING_SENDER_ID || "1076301234300",
    appId: import.meta.env.VITE_FIREBASE_APP_ID || "1:1076301234300:web:8fc39d721282f5ec70c9f9"
};

// Initialize App
export const app = initializeApp(firebaseConfig);
export const db = getFirestore(app);
export const rtdb = getDatabase(app);

// Helper Toast Notification
function showLiveToast(title, message, type = 'info') {
    let container = document.getElementById('firebase-toast-container');
    if (!container) {
        container = document.createElement('div');
        container.id = 'firebase-toast-container';
        container.className = 'fixed bottom-5 right-5 z-50 flex flex-col gap-2 max-w-sm pointer-events-none';
        document.body.appendChild(container);
    }

    const toast = document.createElement('div');
    toast.className = `p-3.5 rounded-xl border shadow-xl backdrop-blur-xl pointer-events-auto transform transition-all duration-300 translate-y-2 opacity-0 flex items-start gap-3 text-xs ${
        type === 'success' 
            ? 'bg-emerald-950/90 border-emerald-500/40 text-emerald-200' 
            : type === 'warning'
            ? 'bg-amber-950/90 border-amber-500/40 text-amber-200'
            : 'bg-zinc-900/90 border-zinc-700/80 text-zinc-200'
    }`;

    toast.innerHTML = `
        <div class="w-5 h-5 rounded-md bg-amber-500/20 text-amber-400 flex items-center justify-center shrink-0 font-bold text-[10px]">🔥</div>
        <div class="flex-1">
            <div class="font-bold tracking-tight text-white">${title}</div>
            <div class="text-[11px] text-zinc-300 mt-0.5">${message}</div>
        </div>
        <button onclick="this.parentElement.remove()" class="text-zinc-400 hover:text-white">&times;</button>
    `;

    container.appendChild(toast);
    setTimeout(() => {
        toast.classList.remove('translate-y-2', 'opacity-0');
    }, 50);

    setTimeout(() => {
        toast.classList.add('opacity-0', 'translate-y-2');
        setTimeout(() => toast.remove(), 400);
    }, 5000);
}

// Global Firebase Sync & Realtime Manager
export const FirebaseSync = {
    app,
    db,
    rtdb,

    // 1. Health check & test write to Firestore
    async testConnection() {
        try {
            const testRef = doc(db, "_health", "connection_test");
            await setDoc(testRef, {
                status: "connected",
                app: "Gudang Half Kopi",
                projectId: firebaseConfig.projectId,
                timestamp: serverTimestamp(),
                userAgent: navigator.userAgent
            });

            // Also test Realtime Database
            const rtdbRef = ref(rtdb, "health/status");
            await rtdbSet(rtdbRef, {
                online: true,
                updatedAt: new Date().toISOString()
            });

            this.updateNavbarStatus(true);
            showLiveToast("Firebase Terkoneksi", "Koneksi ke Firestore & Realtime DB aktif!", "success");
            return { success: true, message: "Koneksi ke Firestore & Realtime DB berhasil!" };
        } catch (error) {
            console.error("Firebase Connection Error:", error);
            this.updateNavbarStatus(false);
            showLiveToast("Gagal Terkoneksi", error.message, "warning");
            return { success: false, error: error.message };
        }
    },

    // 2. Upload local database to Cloud Firestore
    async syncAllToFirestore() {
        try {
            const res = await fetch('/firebase/export');
            const result = await res.json();
            if (!result.data) throw new Error("Gagal mengambil data lokal.");

            const { items, categories, suppliers, transactions } = result.data;
            let count = 0;

            // Categories
            for (const cat of categories) {
                await setDoc(doc(db, "categories", String(cat.id)), {
                    id: cat.id,
                    name: cat.name,
                    slug: cat.slug,
                    description: cat.description || "",
                    updatedAt: serverTimestamp()
                });
                count++;
            }

            // Suppliers
            for (const sup of suppliers) {
                await setDoc(doc(db, "suppliers", String(sup.id)), {
                    id: sup.id,
                    name: sup.name,
                    contact_person: sup.contact_person || "",
                    phone: sup.phone || "",
                    email: sup.email || "",
                    updatedAt: serverTimestamp()
                });
                count++;
            }

            // Items
            for (const item of items) {
                await setDoc(doc(db, "items", String(item.sku)), {
                    id: item.id,
                    sku: item.sku,
                    name: item.name,
                    category_id: item.category_id,
                    category_name: item.category ? item.category.name : "",
                    unit: item.unit,
                    current_stock: Number(item.current_stock),
                    minimum_stock: Number(item.minimum_stock),
                    cost_price: Number(item.cost_price),
                    description: item.description || "",
                    updatedAt: serverTimestamp()
                });
                count++;
            }

            // Transactions
            for (const tx of transactions) {
                await setDoc(doc(db, "stock_transactions", String(tx.transaction_code)), {
                    id: tx.id,
                    transaction_code: tx.transaction_code,
                    type: tx.type,
                    date: tx.date,
                    destination_or_reason: tx.destination_or_reason,
                    total_cost: Number(tx.total_cost),
                    updatedAt: serverTimestamp()
                });
                count++;
            }

            showLiveToast("Sinkronisasi Selesai", `Berhasil mengunggah ${count} dokumen ke Cloud Firestore.`, "success");
            return { success: true, count };
        } catch (error) {
            console.error("Sync Error:", error);
            showLiveToast("Gagal Sinkronisasi", error.message, "warning");
            return { success: false, error: error.message };
        }
    },

    // 3. Pull data from Cloud Firestore into local database
    async pullFromFirestore() {
        try {
            // Fetch categories
            const catSnap = await getDocs(collection(db, "categories"));
            const categories = catSnap.docs.map(d => d.data());

            // Fetch suppliers
            const supSnap = await getDocs(collection(db, "suppliers"));
            const suppliers = supSnap.docs.map(d => d.data());

            // Fetch items
            const itemSnap = await getDocs(collection(db, "items"));
            const items = itemSnap.docs.map(d => d.data());

            // Post to backend import endpoint
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') 
                || (window.Laravel && window.Laravel.csrfToken);

            const res = await fetch('/firebase/import', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken || ''
                },
                body: JSON.stringify({ categories, suppliers, items })
            });

            const json = await res.json();
            showLiveToast("Impor Firestore Berhasil", json.message || "Data lokal telah diperbarui.", "success");
            setTimeout(() => window.location.reload(), 1500);
            return json;
        } catch (error) {
            console.error("Pull Error:", error);
            showLiveToast("Gagal Impor Firestore", error.message, "warning");
            return { success: false, error: error.message };
        }
    },

    // 4. Real-time listeners for live updates across devices
    initRealtimeListeners() {
        // Listen to live transactions
        try {
            const txQuery = query(collection(db, "stock_transactions"), orderBy("updatedAt", "desc"), limit(5));
            let initialLoad = true;

            onSnapshot(txQuery, (snapshot) => {
                if (initialLoad) {
                    initialLoad = false;
                    return;
                }

                snapshot.docChanges().forEach((change) => {
                    if (change.type === "added" || change.type === "modified") {
                        const data = change.doc.data();
                        showLiveToast(
                            "Mutasi Stok Realtime 🔥",
                            `Transaksi ${data.transaction_code || ''} (${data.type === 'in' ? 'Stok Masuk' : 'Stok Keluar'}) baru saja diperbarui di Cloud Firestore.`,
                            "info"
                        );
                    }
                });
            }, (err) => {
                console.warn("Firestore realtime snapshot notice:", err.message);
            });
        } catch (e) {
            console.warn("Listeners init warning:", e);
        }
    },

    updateNavbarStatus(online) {
        const pill = document.getElementById('firebaseStatusPill');
        if (pill) {
            if (online) {
                pill.innerHTML = `
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                    <span>Firebase: Auto-Sync Aktif</span>
                `;
                pill.className = "hidden sm:flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/30";
            } else {
                pill.innerHTML = `
                    <span class="w-1.5 h-1.5 rounded-full bg-amber-400"></span>
                    <span>Firebase: Menghubungkan...</span>
                `;
                pill.className = "hidden sm:flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-500/10 text-amber-400 border border-amber-500/30";
            }
        }
    }
};

// Expose globally
if (typeof window !== "undefined") {
    window.firebaseApp = app;
    window.firestoreDb = db;
    window.realtimeDb = rtdb;
    window.FirebaseSync = FirebaseSync;

    // Run health check, realtime listener, and AUTOMATIC SYNC on page load
    document.addEventListener("DOMContentLoaded", async () => {
        await FirebaseSync.testConnection();
        FirebaseSync.initRealtimeListeners();

        // JIKA ADA TRANSAKSI / PERUBAHAN DATA (DITANDAI FLASH MESSAGE), OTOMATIS UPLOAD KE FIREBASE!
        const changeMarker = document.getElementById('flash-success-marker');
        if (changeMarker) {
            console.log("⚡ Mendeteksi perubahan data lokal, mengunggah otomatis ke Cloud Firestore...");
            setTimeout(async () => {
                await FirebaseSync.syncAllToFirestore();
            }, 600);
        }
    });
}

export default FirebaseSync;
