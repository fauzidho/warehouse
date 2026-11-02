import { initializeApp } from "firebase/app";
import { 
    getFirestore, 
    collection, 
    doc, 
    setDoc, 
    getDocs, 
    serverTimestamp,
    query,
    orderBy,
    limit 
} from "firebase/firestore";
import { getDatabase, ref, set as rtdbSet } from "firebase/database";

// Firebase Configuration
export const firebaseConfig = {
    apiKey: import.meta.env.VITE_FIREBASE_API_KEY || "AIzaSyDSfrl6WclAYdSoPtxsFt1Pvh5N2NReqMw",
    authDomain: import.meta.env.VITE_FIREBASE_AUTH_DOMAIN || "toko-kopi-8abbd.firebaseapp.com",
    databaseURL: import.meta.env.VITE_FIREBASE_DATABASE_URL || "https://toko-kopi-8abbd-default-rtdb.asia-southeast1.firebasedatabase.app",
    projectId: import.meta.env.VITE_FIREBASE_PROJECT_ID || "toko-kopi-8abbd",
    storageBucket: import.meta.env.VITE_FIREBASE_STORAGE_BUCKET || "toko-kopi-8abbd.firebasestorage.app",
    messagingSenderId: import.meta.env.VITE_FIREBASE_MESSAGING_SENDER_ID || "1076301234300",
    appId: import.meta.env.VITE_FIREBASE_APP_ID || "1:1076301234300:web:8fc39d721282f5ec70c9f9"
};

// Initialize App (lazy - hanya diinisialisasi jika diperlukan)
let _app = null;
let _db = null;
let _rtdb = null;

function getApp() {
    if (!_app) _app = initializeApp(firebaseConfig);
    return _app;
}

function getDb() {
    if (!_db) _db = getFirestore(getApp());
    return _db;
}

function getRtdb() {
    if (!_rtdb) _rtdb = getDatabase(getApp());
    return _rtdb;
}

// Toast Notification (hanya untuk halaman backup)
function showBackupToast(title, message, type = 'info') {
    let container = document.getElementById('backup-toast-container');
    if (!container) {
        container = document.createElement('div');
        container.id = 'backup-toast-container';
        container.style.cssText = 'position:fixed;bottom:1.25rem;right:1.25rem;z-index:50;display:flex;flex-direction:column;gap:0.5rem;max-width:360px;pointer-events:none;';
        document.body.appendChild(container);
    }

    const colors = {
        success: { bg: 'rgba(6,78,59,0.95)', border: 'rgba(16,185,129,0.4)', text: '#d1fae5' },
        warning: { bg: 'rgba(69,26,3,0.95)', border: 'rgba(245,158,11,0.4)', text: '#fef3c7' },
        info:    { bg: 'rgba(24,24,27,0.95)', border: 'rgba(63,63,70,0.8)', text: '#e4e4e7' },
    };
    const c = colors[type] || colors.info;

    const toast = document.createElement('div');
    toast.style.cssText = `background:${c.bg};border:1px solid ${c.border};color:${c.text};padding:0.875rem;border-radius:0.75rem;box-shadow:0 20px 40px rgba(0,0,0,0.4);pointer-events:auto;display:flex;align-items:flex-start;gap:0.75rem;font-size:0.75rem;transform:translateY(0.5rem);opacity:0;transition:all 0.3s ease;`;

    toast.innerHTML = `
        <div style="flex:1">
            <div style="font-weight:700;color:#fff;margin-bottom:2px">${title}</div>
            <div style="font-size:0.6875rem;opacity:0.85">${message}</div>
        </div>
        <button onclick="this.parentElement.remove()" style="background:none;border:none;color:inherit;cursor:pointer;padding:0;opacity:0.6;font-size:1rem;line-height:1">&times;</button>
    `;

    container.appendChild(toast);
    requestAnimationFrame(() => {
        toast.style.transform = 'translateY(0)';
        toast.style.opacity = '1';
    });

    setTimeout(() => {
        toast.style.opacity = '0';
        toast.style.transform = 'translateY(0.5rem)';
        setTimeout(() => toast.remove(), 400);
    }, 5000);
}

// Global Firebase Sync Manager
export const FirebaseSync = {
    // 1. Health check - hanya panggil manual dari halaman backup
    async testConnection() {
        try {
            const db = getDb();
            const rtdb = getRtdb();
            const testRef = doc(db, "_health", "connection_test");
            await setDoc(testRef, {
                status: "connected",
                app: "Gudang Half Kopi",
                timestamp: serverTimestamp(),
            });

            const rtdbRef = ref(rtdb, "health/status");
            await rtdbSet(rtdbRef, {
                online: true,
                updatedAt: new Date().toISOString()
            });

            showBackupToast("Koneksi Berhasil", "Server cadangan data aktif dan siap digunakan.", "success");
            return { success: true };
        } catch (error) {
            console.warn("Backup server not reachable:", error.message);
            showBackupToast("Mode Lokal", "Sistem berjalan normal dalam mode penyimpanan lokal.", "warning");
            return { success: false, error: error.message };
        }
    },

    // 2. Upload data lokal ke Cloud (manual trigger saja)
    async syncAllToFirestore() {
        try {
            const db = getDb();
            const res = await fetch('/firebase/export');
            const result = await res.json();
            if (!result.data) throw new Error("Gagal mengambil data lokal.");

            const { items, categories, suppliers, transactions } = result.data;
            let count = 0;

            for (const cat of categories) {
                await setDoc(doc(db, "categories", String(cat.id)), {
                    id: cat.id, name: cat.name, slug: cat.slug,
                    description: cat.description || "",
                    updatedAt: serverTimestamp()
                });
                count++;
            }

            for (const sup of suppliers) {
                await setDoc(doc(db, "suppliers", String(sup.id)), {
                    id: sup.id, name: sup.name,
                    contact_person: sup.contact_person || "",
                    phone: sup.phone || "", email: sup.email || "",
                    address: sup.address || "",
                    updatedAt: serverTimestamp()
                });
                count++;
            }

            for (const item of items) {
                await setDoc(doc(db, "items", String(item.id)), {
                    id: item.id, name: item.name,
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

            for (const tx of transactions) {
                await setDoc(doc(db, "stock_transactions", String(tx.transaction_code)), {
                    id: tx.id, transaction_code: tx.transaction_code,
                    type: tx.type, date: tx.date,
                    destination_or_reason: tx.destination_or_reason,
                    total_cost: Number(tx.total_cost),
                    updatedAt: serverTimestamp()
                });
                count++;
            }

            showBackupToast("Pencadangan Selesai", `${count} data berhasil dicadangkan ke server cloud.`, "success");
            return { success: true, count };
        } catch (error) {
            console.error("Sync Error:", error);
            showBackupToast("Pencadangan Gagal", error.message, "warning");
            return { success: false, error: error.message };
        }
    },

    // 3. Tarik data dari Cloud ke lokal
    async pullFromFirestore() {
        try {
            const db = getDb();
            const catSnap = await getDocs(collection(db, "categories"));
            const categories = catSnap.docs.map(d => d.data());

            const supSnap = await getDocs(collection(db, "suppliers"));
            const suppliers = supSnap.docs.map(d => d.data());

            const itemSnap = await getDocs(collection(db, "items"));
            const items = itemSnap.docs.map(d => d.data());

            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

            const res = await fetch('/firebase/import', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken || ''
                },
                body: JSON.stringify({ categories, suppliers, items })
            });

            const json = await res.json();
            showBackupToast("Pemulihan Data Berhasil", "Data inventori telah diperbarui dari server cadangan.", "success");
            setTimeout(() => window.location.reload(), 1500);
            return json;
        } catch (error) {
            console.error("Pull Error:", error);
            showBackupToast("Pemulihan Terkendala", error.message, "warning");
            return { success: false, error: error.message };
        }
    },
};

// Expose globally - TANPA auto-run koneksi di setiap halaman
if (typeof window !== "undefined") {
    window.FirebaseSync = FirebaseSync;
    // TIDAK ada auto testConnection(), TIDAK ada initRealtimeListeners() otomatis
    // Hal ini menghilangkan 3 operasi network berat yang memperlambat setiap halaman
}

export default FirebaseSync;
