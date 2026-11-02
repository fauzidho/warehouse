@extends('layouts.app')

@section('title', 'Cadangan & Pemulihan Data')
@section('page-title', 'Cadangan & Pemulihan Data')
@section('page-subtitle', 'Cadangkan inventori ke server aman atau pulihkan data dari backup sebelumnya')

@section('content')
<div class="space-y-6" data-firebase-page="true">

    {{-- Header Banner --}}
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-zinc-900 to-zinc-900/60 border border-zinc-800 p-6 sm:p-8">
        <div class="absolute right-0 top-0 bottom-0 w-1/3 pointer-events-none">
            <div class="absolute inset-0 bg-gradient-to-l from-amber-600/5 to-transparent"></div>
        </div>
        <div class="relative flex flex-col md:flex-row md:items-center justify-between gap-5">
            <div class="space-y-2">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-zinc-800 border border-zinc-700/60 text-zinc-300 text-xs font-medium">
                    <svg class="w-3.5 h-3.5 text-amber-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                    </svg>
                    Sistem Cadangan Data Otomatis
                </div>
                <h2 class="text-xl font-black text-white tracking-tight">Cadangan & Pemulihan Data Inventori</h2>
                <p class="text-sm text-zinc-400 max-w-2xl leading-relaxed">
                    Simpan salinan seluruh data gudang ke server aman, atau pulihkan data dari backup sebelumnya jika terjadi masalah. Data mencakup katalog bahan baku, supplier, kategori, dan riwayat transaksi.
                </p>
            </div>
            <button type="button" onclick="testConnection(this)"
                class="shrink-0 px-4 py-2.5 rounded-xl bg-zinc-800 hover:bg-zinc-700 border border-zinc-700 text-zinc-200 font-semibold text-xs flex items-center gap-2 transition-all">
                <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                </svg>
                Uji Koneksi Server
            </button>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="p-5 rounded-2xl bg-zinc-900/70 border border-zinc-800">
            <div class="text-xs font-semibold text-zinc-500 uppercase tracking-wider mb-2">Data Bahan Baku</div>
            <div class="text-2xl font-black text-white">{{ $localStats['items_count'] }}</div>
            <div class="text-[11px] text-zinc-500 mt-1">Item tercatat di sistem</div>
        </div>
        <div class="p-5 rounded-2xl bg-zinc-900/70 border border-zinc-800">
            <div class="text-xs font-semibold text-zinc-500 uppercase tracking-wider mb-2">Riwayat Transaksi</div>
            <div class="text-2xl font-black text-white">{{ $localStats['transactions_count'] }}</div>
            <div class="text-[11px] text-zinc-500 mt-1">Transaksi mutasi tercatat</div>
        </div>
        <div class="p-5 rounded-2xl bg-zinc-900/70 border border-zinc-800">
            <div class="text-xs font-semibold text-zinc-500 uppercase tracking-wider mb-2">Status Data</div>
            <div class="text-sm font-bold text-emerald-400 flex items-center gap-2 mt-1">
                <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                {{ $localStats['items_count'] == 0 ? 'Database Bersih' : 'Data Tersimpan' }}
            </div>
            <div class="text-[11px] text-zinc-500 mt-1">
                {{ $localStats['items_count'] == 0 ? 'Siap diisi dari awal' : 'Siap untuk dicadangkan' }}
            </div>
        </div>
    </div>

    {{-- Main Backup/Restore Actions --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

        {{-- Backup: Local → Cloud --}}
        <div class="bg-zinc-900/70 border border-zinc-800 rounded-2xl p-6 flex flex-col">
            <div class="flex items-start gap-4 flex-1">
                <div class="w-12 h-12 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-white">Cadangkan Data ke Server</h3>
                    <p class="text-xs text-zinc-400 mt-1.5 leading-relaxed">
                        Kirim salinan semua data inventori (bahan baku, kategori, supplier, transaksi) ke server cadangan aman. Lakukan ini secara rutin untuk melindungi data Anda.
                    </p>
                    <div class="mt-3 space-y-1">
                        <div class="text-[11px] text-zinc-500 flex items-center gap-1.5">
                            <svg class="w-3 h-3 text-emerald-400" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            Data terenkripsi selama pengiriman
                        </div>
                        <div class="text-[11px] text-zinc-500 flex items-center gap-1.5">
                            <svg class="w-3 h-3 text-emerald-400" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            Data lama di server akan diperbarui
                        </div>
                    </div>
                </div>
            </div>
            <button type="button" onclick="runBackup(this)"
                class="mt-5 w-full py-2.5 px-4 rounded-xl font-bold text-sm bg-emerald-600 hover:bg-emerald-500 text-white transition-all flex items-center justify-center gap-2 shadow-lg shadow-emerald-950/30">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                <span>Mulai Pencadangan</span>
            </button>
        </div>

        {{-- Restore: Cloud → Local --}}
        <div class="bg-zinc-900/70 border border-zinc-800 rounded-2xl p-6 flex flex-col">
            <div class="flex items-start gap-4 flex-1">
                <div class="w-12 h-12 rounded-xl bg-amber-500/10 border border-amber-500/20 text-amber-400 flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-white">Pulihkan Data dari Server</h3>
                    <p class="text-xs text-zinc-400 mt-1.5 leading-relaxed">
                        Unduh dan terapkan data dari server cadangan ke sistem lokal ini. Gunakan saat ingin memindahkan data ke perangkat baru atau memulihkan dari insiden.
                    </p>
                    <div class="mt-3 p-2.5 rounded-lg bg-amber-500/5 border border-amber-500/15">
                        <div class="text-[11px] text-amber-400/80 flex items-start gap-1.5">
                            <svg class="w-3 h-3 mt-0.5 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                            Data yang diunduh akan menggantikan data master (bahan, kategori, supplier) di sistem ini.
                        </div>
                    </div>
                </div>
            </div>
            <button type="button" onclick="runRestore(this)"
                class="mt-5 w-full py-2.5 px-4 rounded-xl font-bold text-sm bg-zinc-800 hover:bg-zinc-700 text-zinc-200 border border-zinc-700 transition-all flex items-center justify-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                <span>Pulihkan dari Server</span>
            </button>
        </div>
    </div>

    {{-- Info Panel --}}
    <div class="bg-zinc-900/50 border border-zinc-800/60 rounded-2xl p-6">
        <h3 class="text-sm font-bold text-white mb-4 flex items-center gap-2">
            <svg class="w-4 h-4 text-zinc-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            Panduan Penggunaan Fitur Cadangan
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="p-4 rounded-xl bg-zinc-950/60 border border-zinc-800/80">
                <div class="text-xs font-bold text-zinc-200 mb-1.5">📅 Kapan Mencadangkan?</div>
                <p class="text-[11px] text-zinc-400 leading-relaxed">
                    Lakukan pencadangan setiap minggu atau setelah melakukan banyak perubahan data inventori secara signifikan.
                </p>
            </div>
            <div class="p-4 rounded-xl bg-zinc-950/60 border border-zinc-800/80">
                <div class="text-xs font-bold text-zinc-200 mb-1.5">🔒 Keamanan Data</div>
                <p class="text-[11px] text-zinc-400 leading-relaxed">
                    Semua data dikirim melalui koneksi terenkripsi. Hanya administrator yang bisa mengakses halaman ini.
                </p>
            </div>
            <div class="p-4 rounded-xl bg-zinc-950/60 border border-zinc-800/80">
                <div class="text-xs font-bold text-zinc-200 mb-1.5">🔄 Pindah Perangkat</div>
                <p class="text-[11px] text-zinc-400 leading-relaxed">
                    Untuk memindahkan data ke komputer baru: cadangkan dari perangkat lama, lalu gunakan "Pulihkan" di perangkat baru.
                </p>
            </div>
        </div>
    </div>

</div>

<script>
    async function testConnection(btn) {
        const orig = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = `<svg class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg> Menguji...`;
        try {
            await window.FirebaseSync.testConnection();
        } finally {
            btn.disabled = false;
            btn.innerHTML = orig;
        }
    }

    async function runBackup(btn) {
        if (!confirm('Mulai mencadangkan semua data ke server? Proses ini mungkin memakan waktu beberapa menit.')) return;
        const orig = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = `<svg class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg> Sedang Mencadangkan...`;
        try {
            await window.FirebaseSync.syncAllToFirestore();
        } finally {
            btn.disabled = false;
            btn.innerHTML = orig;
        }
    }

    async function runRestore(btn) {
        if (!confirm('PERHATIAN: Data master lokal (bahan, kategori, supplier) akan digantikan dengan data dari server. Lanjutkan?')) return;
        const orig = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = `<svg class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg> Sedang Memulihkan...`;
        try {
            await window.FirebaseSync.pullFromFirestore();
        } finally {
            btn.disabled = false;
            btn.innerHTML = orig;
        }
    }
</script>
@endsection
