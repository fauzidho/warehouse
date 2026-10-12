@extends('layouts.app')

@section('title', 'Firebase Cloud Center')
@section('page-title', 'Firebase Cloud Center')
@section('page-subtitle', 'Integrasi Cloud Firestore & Realtime Database Toko Kopi')

@section('content')
<div class="space-y-8">
    <!-- Header Hero Card -->
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-zinc-900 via-zinc-900 to-amber-950/40 border border-zinc-800 p-6 sm:p-8">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="space-y-2">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-amber-500/10 border border-amber-500/30 text-amber-400 text-xs font-semibold">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-ping"></span>
                    Project ID: <strong class="text-white font-mono">toko-kopi-8abbd</strong>
                </div>
                <h2 class="text-2xl font-black text-white tracking-tight">
                    Cloud Firestore & Realtime Sync Center 🔥
                </h2>
                <p class="text-xs sm:text-sm text-zinc-400 max-w-2xl">
                    Sistem gudang terhubung langsung dengan Google Firebase untuk sinkronisasi mutasi stok, multi-device realtime update di bar/kitchen, dan backup cloud database.
                </p>
            </div>

            <!-- Quick Action Test Buttons -->
            <div class="flex flex-wrap items-center gap-3">
                <button type="button" onclick="window.FirebaseSync.testConnection()"
                    class="px-4 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-400 text-zinc-950 font-bold text-xs flex items-center gap-2 shadow-lg shadow-amber-950/40 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    Tes Koneksi Firebase
                </button>
            </div>
        </div>
    </div>

    <!-- 3 Configuration Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
        <!-- Card 1: Project Credentials -->
        <div class="p-5 rounded-2xl bg-zinc-900/70 border border-zinc-800">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-semibold text-zinc-400 uppercase tracking-wider">Cloud Firestore</span>
                <span class="text-[10px] px-2 py-0.5 rounded bg-emerald-500/10 text-emerald-400 font-bold border border-emerald-500/20">Siap</span>
            </div>
            <div class="text-sm font-bold text-white font-mono">{{ $firebaseConfig['project_id'] }}</div>
            <div class="text-[11px] text-zinc-500 mt-2 space-y-1">
                <div>Region: <span class="text-zinc-300 font-mono">asia-southeast1</span></div>
                <div class="truncate">Auth: <span class="text-zinc-300 font-mono">{{ $firebaseConfig['auth_domain'] }}</span></div>
            </div>
        </div>

        <!-- Card 2: Realtime Database -->
        <div class="p-5 rounded-2xl bg-zinc-900/70 border border-zinc-800">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-semibold text-zinc-400 uppercase tracking-wider">Realtime Database</span>
                <span class="text-[10px] px-2 py-0.5 rounded bg-cyan-500/10 text-cyan-400 font-bold border border-cyan-500/20">Aktif</span>
            </div>
            <div class="text-xs font-bold text-white font-mono truncate">{{ $firebaseConfig['database_url'] }}</div>
            <div class="text-[11px] text-zinc-500 mt-2">
                Menyediakan event listener latensi rendah untuk live stock notification di bar barista.
            </div>
        </div>

        <!-- Card 3: Status Sinkronisasi Lokal -->
        <div class="p-5 rounded-2xl bg-zinc-900/70 border border-zinc-800">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-semibold text-zinc-400 uppercase tracking-wider">Data Gudang Saat Ini</span>
                <span class="text-[10px] px-2 py-0.5 rounded bg-zinc-800 text-zinc-300 font-bold">Lokal</span>
            </div>
            <div class="text-lg font-bold text-white flex items-center gap-4">
                <span>{{ $localStats['items_count'] }} <span class="text-xs text-zinc-400 font-normal">Bahan</span></span>
                <span>•</span>
                <span>{{ $localStats['transactions_count'] }} <span class="text-xs text-zinc-400 font-normal">Transaksi</span></span>
            </div>
            <div class="text-[11px] text-zinc-400 mt-2">
                {{ $localStats['items_count'] == 0 ? 'Data bersih (tanpa dummy). Siap diisi dari awal.' : 'Tersedia data lokal siap disinkronkan.' }}
            </div>
        </div>
    </div>

    <!-- Sinkronisasi 2-Arah Card -->
    <div class="bg-zinc-900/70 border border-zinc-800 rounded-2xl p-6 sm:p-8">
        <h3 class="text-base font-extrabold text-white tracking-tight mb-2 flex items-center gap-2">
            <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
            </svg>
            Operasi Sinkronisasi Dua Arah (Cloud Firestore)
        </h3>
        <p class="text-xs text-zinc-400 mb-6 max-w-2xl">
            Gunakan tombol di bawah ini untuk mencadangkan (backup) seluruh data inventori ke Cloud Firestore atau menarik data dari Firestore ke database lokal.
        </p>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <!-- Sync Up: Local -> Firestore -->
            <div class="p-5 rounded-xl bg-zinc-950/70 border border-zinc-800 space-y-3">
                <div class="font-bold text-sm text-white flex items-center gap-2">
                    <span class="text-emerald-400">&uarr;</span>
                    Unggah Data Lokal ke Firestore
                </div>
                <p class="text-xs text-zinc-400">
                    Mengirim seluruh data bahan baku, kategori, supplier, dan transaksi yang ada di lokal ke koleksi Cloud Firestore (<code class="text-amber-400 font-mono">/items</code>, <code class="text-amber-400 font-mono">/stock_transactions</code>).
                </p>
                <button type="button" onclick="runSyncToFirestore(this)"
                    class="w-full py-2.5 px-4 rounded-xl font-bold text-xs bg-emerald-600 hover:bg-emerald-500 text-white transition-all flex items-center justify-center gap-2 shadow-lg shadow-emerald-950/40">
                    <span>Unggah ke Cloud Firestore</span>
                </button>
            </div>

            <!-- Sync Down: Firestore -> Local -->
            <div class="p-5 rounded-xl bg-zinc-950/70 border border-zinc-800 space-y-3">
                <div class="font-bold text-sm text-white flex items-center gap-2">
                    <span class="text-cyan-400">&darr;</span>
                    Tarik Data dari Firestore ke Lokal
                </div>
                <p class="text-xs text-zinc-400">
                    Mengambil seluruh dokumen bahan baku dan kategori yang tersimpan di Cloud Firestore dan memperbarui database lokal secara otomatis.
                </p>
                <button type="button" onclick="runPullFromFirestore(this)"
                    class="w-full py-2.5 px-4 rounded-xl font-bold text-xs bg-cyan-600 hover:bg-cyan-500 text-zinc-950 transition-all flex items-center justify-center gap-2 shadow-lg shadow-cyan-950/40">
                    <span>Tarik Data dari Firestore</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Security Rules Documentation Card -->
    <div class="bg-zinc-900/70 border border-zinc-800 rounded-2xl p-6 sm:p-8">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h3 class="text-base font-extrabold text-white tracking-tight flex items-center gap-2">
                    <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                    Firebase Security Rules
                </h3>
                <p class="text-xs text-zinc-400 mt-0.5">
                    Konfigurasi aturan keamanan Firestore di file <code class="text-zinc-300 font-mono">firestore.rules</code>
                </p>
            </div>
            <a href="https://console.firebase.google.com/project/toko-kopi-8abbd/firestore/rules" target="_blank"
                class="px-3 py-1.5 rounded-lg bg-zinc-800 hover:bg-zinc-700 text-zinc-300 text-xs font-semibold flex items-center gap-1.5 transition-all">
                <span>Buka Firebase Console</span>
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
            </a>
        </div>

        <div class="p-4 rounded-xl bg-zinc-950 font-mono text-xs text-zinc-300 border border-zinc-800 overflow-x-auto space-y-1">
            <div class="text-zinc-500">// Rules yang sudah disesuaikan untuk sistem gudang toko kopi:</div>
            <div>rules_version = '2';</div>
            <div>service cloud.firestore {</div>
            <div class="pl-4">match /databases/{database}/documents {</div>
            <div class="pl-8 text-emerald-400">// Koleksi bahan baku: Read & Write untuk operasional toko kopi</div>
            <div class="pl-8">match /items/{itemId} { allow read, write: if true; }</div>
            <div class="pl-8">match /stock_transactions/{txId} { allow read, write: if true; }</div>
            <div class="pl-8">match /categories/{catId} { allow read, write: if true; }</div>
            <div class="pl-8">match /suppliers/{supId} { allow read, write: if true; }</div>
            <div class="pl-8">match /{document=**} { allow read, write: if true; }</div>
            <div class="pl-4">}</div>
            <div>}</div>
        </div>
    </div>
</div>

<script>
    async function runSyncToFirestore(btn) {
        btn.disabled = true;
        const originalText = btn.innerHTML;
        btn.innerHTML = `<span>Sedang Mengunggah...</span>`;
        try {
            await window.FirebaseSync.syncAllToFirestore();
        } finally {
            btn.disabled = false;
            btn.innerHTML = originalText;
        }
    }

    async function runPullFromFirestore(btn) {
        btn.disabled = true;
        const originalText = btn.innerHTML;
        btn.innerHTML = `<span>Sedang Menarik Data...</span>`;
        try {
            await window.FirebaseSync.pullFromFirestore();
        } finally {
            btn.disabled = false;
            btn.innerHTML = originalText;
        }
    }
</script>
@endsection
