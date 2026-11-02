@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')
@section('page-subtitle', 'Ringkasan stok & aktivitas gudang hari ini')

@section('content')
<div class="space-y-6">

    {{-- Welcome Banner --}}
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-zinc-900 via-zinc-900 to-amber-950/30 border border-zinc-800 p-5 sm:p-7">
        <div class="absolute right-0 top-0 bottom-0 w-2/5 bg-gradient-to-l from-amber-600/6 to-transparent pointer-events-none"></div>
        <div class="relative flex flex-col md:flex-row md:items-center justify-between gap-5">
            <div class="space-y-1.5">
                <div class="inline-flex items-center gap-2 px-2.5 py-1 rounded-full bg-amber-500/8 border border-amber-500/20 text-amber-400 text-xs font-medium">
                    <span class="w-1.5 h-1.5 rounded-full bg-amber-400 animate-pulse"></span>
                    Sistem Aktif — Gudang Half Kopi
                </div>
                <h2 class="text-xl sm:text-2xl font-extrabold text-white tracking-tight">
                    Halo, {{ auth()->user()->name }} 👋
                </h2>
                <p class="text-sm text-zinc-400 max-w-lg">
                    Anda masuk sebagai <strong class="text-zinc-200">{{ auth()->user()->isAdmin() ? 'Administrator' : 'Pegawai' }}</strong>.
                    Pantau stok bahan baku dan catat semua pergerakan barang dari halaman ini.
                </p>
            </div>
            <div class="flex flex-wrap gap-2 shrink-0">
                <a href="{{ route('stock-in.create') }}"
                    class="px-4 py-2.5 rounded-xl bg-gradient-to-r from-emerald-500 to-emerald-600 hover:from-emerald-400 hover:to-emerald-500 text-zinc-950 font-bold text-xs flex items-center gap-2 shadow-lg shadow-emerald-950/30 transition-all active:scale-95">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                    Catat Stok Masuk
                </a>
                <a href="{{ route('stock-out.create') }}"
                    class="px-4 py-2.5 rounded-xl bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 text-zinc-950 font-bold text-xs flex items-center gap-2 shadow-lg shadow-amber-950/30 transition-all active:scale-95">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20 12H4"/></svg>
                    Catat Pemakaian
                </a>
                <a href="{{ route('adjustments.create') }}"
                    class="px-4 py-2.5 rounded-xl bg-zinc-800 hover:bg-zinc-700 text-zinc-200 border border-zinc-700 font-bold text-xs flex items-center gap-2 transition-all">
                    <svg class="w-4 h-4 text-cyan-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    Stock Opname
                </a>
            </div>
        </div>
    </div>

    {{-- Panduan Cepat (untuk pegawai baru) --}}
    <div class="bg-zinc-900/50 border border-zinc-800/70 rounded-2xl p-5">
        <div class="flex items-center gap-2.5 mb-4">
            <div class="w-7 h-7 rounded-lg bg-amber-500/10 border border-amber-500/20 text-amber-400 flex items-center justify-center shrink-0">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div>
                <h3 class="text-xs font-bold text-white">Panduan Alur Kerja Harian</h3>
                <p class="text-[11px] text-zinc-500">3 langkah standar operasional pengelolaan bahan baku</p>
            </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
            <a href="{{ route('stock-in.create') }}" class="group p-3.5 rounded-xl bg-zinc-950/60 border border-zinc-800 hover:border-emerald-500/30 transition-all">
                <div class="flex items-center gap-2 mb-1.5">
                    <span class="w-5 h-5 rounded-md bg-emerald-500/15 text-emerald-400 font-bold text-[11px] flex items-center justify-center shrink-0">1</span>
                    <span class="text-xs font-bold text-zinc-200 group-hover:text-emerald-400 transition-colors">Barang Masuk</span>
                </div>
                <p class="text-[11px] text-zinc-500 leading-relaxed">Saat kiriman supplier tiba, input jumlah fisik bahan yang diterima beserta nomor faktur/surat jalan.</p>
            </a>
            <a href="{{ route('stock-out.create') }}" class="group p-3.5 rounded-xl bg-zinc-950/60 border border-zinc-800 hover:border-amber-500/30 transition-all">
                <div class="flex items-center gap-2 mb-1.5">
                    <span class="w-5 h-5 rounded-md bg-amber-500/15 text-amber-400 font-bold text-[11px] flex items-center justify-center shrink-0">2</span>
                    <span class="text-xs font-bold text-zinc-200 group-hover:text-amber-400 transition-colors">Pemakaian Barista</span>
                </div>
                <p class="text-[11px] text-zinc-500 leading-relaxed">Setiap pengambilan biji kopi, susu, atau sirup ke bar/kitchen, catat pengurangan stoknya di sini.</p>
            </a>
            <a href="{{ route('adjustments.create') }}" class="group p-3.5 rounded-xl bg-zinc-950/60 border border-zinc-800 hover:border-cyan-500/30 transition-all">
                <div class="flex items-center gap-2 mb-1.5">
                    <span class="w-5 h-5 rounded-md bg-cyan-500/15 text-cyan-400 font-bold text-[11px] flex items-center justify-center shrink-0">3</span>
                    <span class="text-xs font-bold text-zinc-200 group-hover:text-cyan-400 transition-colors">Hitung Fisik Berkala</span>
                </div>
                <p class="text-[11px] text-zinc-500 leading-relaxed">Hitung stok fisik secara rutin untuk mencocokkan data sistem dengan kondisi gudang nyata.</p>
            </a>
        </div>
    </div>

    @if($totalItems == 0)
        <div class="p-5 rounded-2xl bg-amber-950/20 border border-amber-500/20 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="w-10 h-10 rounded-xl bg-amber-500/10 text-amber-400 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-white">Gudang Siap Digunakan</h3>
                    <p class="text-xs text-zinc-400 mt-0.5">Belum ada bahan baku. Mulai dengan menambahkan katalog bahan baku terlebih dahulu.</p>
                </div>
            </div>
            @if(auth()->user()->isAdmin())
                <a href="{{ route('items.create') }}" class="px-4 py-2 rounded-xl bg-amber-500 hover:bg-amber-400 text-zinc-950 font-bold text-xs transition-all shrink-0">
                    + Tambah Bahan Pertama
                </a>
            @endif
        </div>
    @endif

    {{-- 4 KPI Cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        {{-- Total Bahan --}}
        <div class="p-5 rounded-2xl bg-zinc-900/70 border border-zinc-800 hover:border-zinc-700 transition-all">
            <div class="flex items-center justify-between mb-3">
                <span class="text-[10px] font-bold text-zinc-500 uppercase tracking-wider">Bahan Baku</span>
                <div class="w-8 h-8 rounded-lg bg-blue-500/8 text-blue-400 flex items-center justify-center border border-blue-500/15">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                </div>
            </div>
            <div class="text-2xl font-black text-white">{{ number_format($totalItems) }}</div>
            <div class="text-[11px] text-zinc-500 mt-1 flex items-center justify-between">
                <span>Jenis Bahan</span>
                <a href="{{ route('items.index') }}" class="text-amber-400 hover:text-amber-300 font-medium text-[10px]">Lihat →</a>
            </div>
        </div>

        {{-- Valuasi Stok --}}
        <div class="p-5 rounded-2xl bg-zinc-900/70 border border-zinc-800 hover:border-zinc-700 transition-all">
            <div class="flex items-center justify-between mb-3">
                <span class="text-[10px] font-bold text-zinc-500 uppercase tracking-wider">Nilai Stok</span>
                <div class="w-8 h-8 rounded-lg bg-amber-500/8 text-amber-400 flex items-center justify-center border border-amber-500/15">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
            <div class="text-lg font-black text-white leading-tight">Rp {{ number_format($totalValuation, 0, ',', '.') }}</div>
            <div class="text-[11px] text-zinc-500 mt-1">
                <span class="text-emerald-400 font-medium">Estimasi aset gudang</span>
            </div>
        </div>

        {{-- Stok Menipis --}}
        <div class="p-5 rounded-2xl bg-zinc-900/70 border {{ $lowStockItems->count() > 0 ? 'border-rose-500/25' : 'border-zinc-800' }} transition-all">
            <div class="flex items-center justify-between mb-3">
                <span class="text-[10px] font-bold {{ $lowStockItems->count() > 0 ? 'text-rose-400' : 'text-zinc-500' }} uppercase tracking-wider">Perlu Restock</span>
                <div class="w-8 h-8 rounded-lg bg-rose-500/8 text-rose-400 flex items-center justify-center border border-rose-500/15">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </div>
            </div>
            <div class="text-2xl font-black {{ $lowStockItems->count() > 0 ? 'text-rose-400' : 'text-white' }}">
                {{ $lowStockItems->count() }}
                <span class="text-sm font-normal text-zinc-500">bahan</span>
            </div>
            <div class="text-[11px] mt-1">
                @if($outOfStockCount > 0)
                    <span class="text-rose-400 font-semibold">{{ $outOfStockCount }} habis total</span>
                @else
                    <span class="text-emerald-400">Tidak ada yang habis</span>
                @endif
            </div>
        </div>

        {{-- Aktivitas Hari Ini --}}
        <div class="p-5 rounded-2xl bg-zinc-900/70 border border-zinc-800 hover:border-zinc-700 transition-all">
            <div class="flex items-center justify-between mb-3">
                <span class="text-[10px] font-bold text-zinc-500 uppercase tracking-wider">Aktivitas Hari Ini</span>
                <div class="w-8 h-8 rounded-lg bg-emerald-500/8 text-emerald-400 flex items-center justify-center border border-emerald-500/15">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                </div>
            </div>
            <div class="text-2xl font-black text-white">
                {{ $todayStockInCount + $todayStockOutCount }}
                <span class="text-sm font-normal text-zinc-500">transaksi</span>
            </div>
            <div class="text-[11px] text-zinc-500 mt-1 flex items-center gap-2">
                <span class="text-emerald-400 font-medium">{{ $todayStockInCount }} masuk</span>
                <span>•</span>
                <span class="text-amber-400 font-medium">{{ $todayStockOutCount }} keluar</span>
            </div>
        </div>
    </div>

    {{-- Alert Panels: Stok Menipis & Kadaluarsa --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">

        {{-- Stok Menipis --}}
        <div class="bg-zinc-900/70 border border-zinc-800 rounded-2xl p-5">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-2">
                    @if($lowStockItems->count() > 0)
                        <span class="w-2 h-2 rounded-full bg-rose-500 animate-ping"></span>
                    @else
                        <span class="w-2 h-2 rounded-full bg-zinc-600"></span>
                    @endif
                    <h3 class="text-sm font-bold text-white">Peringatan Stok Menipis</h3>
                </div>
                <span class="text-xs text-zinc-500 font-medium">{{ $lowStockItems->count() }} bahan</span>
            </div>

            @if($lowStockItems->count() > 0)
                <div class="space-y-2.5">
                    @foreach($lowStockItems as $lItem)
                        <div class="p-3.5 rounded-xl bg-zinc-950/60 border border-zinc-800 flex items-center justify-between hover:border-zinc-700 transition-all">
                            <div>
                                <div class="text-xs font-bold text-white">{{ $lItem->name }}</div>
                                <div class="text-[11px] text-zinc-500 mt-0.5">
                                    Kategori: <span class="text-zinc-400">{{ $lItem->category->name }}</span>
                                </div>
                            </div>
                            <div class="text-right flex items-center gap-3">
                                <div>
                                    <div class="text-xs font-black {{ $lItem->current_stock <= 0 ? 'text-rose-500' : 'text-amber-400' }}">
                                        {{ $lItem->current_stock }} / {{ $lItem->minimum_stock }} {{ $lItem->unit }}
                                    </div>
                                    <div class="text-[10px] text-zinc-600">
                                        {{ $lItem->current_stock <= 0 ? 'Habis Total' : 'Di Bawah Minimum' }}
                                    </div>
                                </div>
                                <a href="{{ route('stock-in.create') }}"
                                    class="p-1.5 rounded-lg bg-emerald-500/10 hover:bg-emerald-500/18 text-emerald-400 border border-emerald-500/20 transition-all"
                                    title="Catat Stok Masuk untuk item ini">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="py-10 text-center space-y-2">
                    <svg class="w-10 h-10 mx-auto text-emerald-400/50" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <div class="text-xs font-semibold text-zinc-300">Semua Stok Aman</div>
                    <div class="text-[11px] text-zinc-500">Tidak ada bahan baku yang berada di bawah batas minimum.</div>
                </div>
            @endif
        </div>

        {{-- Kadaluarsa --}}
        <div class="bg-zinc-900/70 border border-zinc-800 rounded-2xl p-5">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full {{ $expiringBatches->count() > 0 ? 'bg-amber-500' : 'bg-zinc-600' }}"></span>
                    <h3 class="text-sm font-bold text-white">Mendekati Kadaluarsa</h3>
                </div>
                <span class="text-xs text-zinc-500 font-medium">&le; 30 hari</span>
            </div>

            @if($expiringBatches->count() > 0)
                <div class="space-y-2.5">
                    @foreach($expiringBatches as $batch)
                        <div class="p-3.5 rounded-xl bg-zinc-950/60 border border-zinc-800 flex items-center justify-between hover:border-zinc-700 transition-all">
                            <div>
                                <div class="text-xs font-bold text-white">{{ $batch->item->name }}</div>
                                <div class="text-[11px] text-zinc-500 mt-0.5">
                                    Ref: {{ $batch->transaction->transaction_code }} &bull; {{ $batch->quantity }} {{ $batch->item->unit }}
                                </div>
                            </div>
                            <div class="text-right">
                                <span class="inline-flex items-center px-2 py-1 rounded-lg text-[11px] font-bold bg-amber-500/10 text-amber-400 border border-amber-500/20">
                                    Exp {{ \Carbon\Carbon::parse($batch->expiry_date)->translatedFormat('d M Y') }}
                                </span>
                                <div class="text-[10px] text-zinc-500 mt-1">
                                    {{ \Carbon\Carbon::parse($batch->expiry_date)->diffForHumans() }}
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="py-10 text-center space-y-2">
                    <svg class="w-10 h-10 mx-auto text-zinc-600" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    <div class="text-xs font-semibold text-zinc-300">Tidak Ada Batch Kritis</div>
                    <div class="text-[11px] text-zinc-500">Semua batch masih memiliki masa simpan yang cukup.</div>
                </div>
            @endif
        </div>
    </div>

    {{-- Riwayat Transaksi Terbaru --}}
    <div class="bg-zinc-900/70 border border-zinc-800 rounded-2xl">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 p-5 border-b border-zinc-800/80">
            <div>
                <h3 class="text-sm font-bold text-white">Riwayat Transaksi Terbaru</h3>
                <p class="text-[11px] text-zinc-500 mt-0.5">Penerimaan supplier, pemakaian barista, dan penyesuaian stok</p>
            </div>
            <a href="{{ route('reports.index') }}" class="text-xs text-amber-400 hover:text-amber-300 font-semibold shrink-0">
                Lihat Laporan Lengkap →
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-xs">
                <thead>
                    <tr class="border-b border-zinc-800/60 text-zinc-500 font-semibold uppercase tracking-wider text-[10px]">
                        <th class="py-3 px-5 text-left">Kode & Tanggal</th>
                        <th class="py-3 px-4 text-left">Jenis & Keterangan</th>
                        <th class="py-3 px-4 text-left hidden md:table-cell">Bahan</th>
                        <th class="py-3 px-4 text-left hidden sm:table-cell">Operator</th>
                        <th class="py-3 px-4 text-right">Nilai</th>
                        <th class="py-3 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-800/40">
                    @forelse($recentTransactions as $tx)
                        @php $badge = $tx->type_badge; @endphp
                        <tr class="hover:bg-zinc-800/20 transition-colors">
                            <td class="py-3.5 px-5 font-mono">
                                <div class="font-bold text-zinc-200 text-[11px]">{{ $tx->transaction_code }}</div>
                                <div class="text-[10px] text-zinc-500 font-sans">{{ $tx->date->translatedFormat('d M Y') }}</div>
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold border {{ $badge['bg'] }} mb-1">
                                    {{ $badge['label'] }}
                                </span>
                                <div class="text-zinc-300 font-medium truncate max-w-[180px]">{{ $tx->destination_or_reason }}</div>
                            </td>
                            <td class="py-3.5 px-4 hidden md:table-cell">
                                <div class="space-y-0.5">
                                    @foreach($tx->items->take(2) as $ti)
                                        <div class="text-zinc-400 text-[11px]">
                                            • {{ $ti->item->name ?? 'Item' }}
                                            <span class="text-amber-400/80 font-medium">({{ $ti->quantity }} {{ $ti->item->unit ?? '' }})</span>
                                        </div>
                                    @endforeach
                                    @if($tx->items->count() > 2)
                                        <div class="text-[10px] text-zinc-600">+{{ $tx->items->count() - 2 }} lainnya</div>
                                    @endif
                                </div>
                            </td>
                            <td class="py-3.5 px-4 hidden sm:table-cell">
                                <div class="text-zinc-300 font-medium text-[11px]">{{ $tx->user->name ?? '-' }}</div>
                            </td>
                            <td class="py-3.5 px-4 text-right text-zinc-300 font-medium">
                                @if($tx->total_cost > 0)
                                    Rp {{ number_format($tx->total_cost, 0, ',', '.') }}
                                @else
                                    <span class="text-zinc-600">—</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-right">
                                @if($tx->type === 'in')
                                    <a href="{{ route('stock-in.show', $tx) }}" class="px-2.5 py-1 rounded-lg bg-zinc-800 hover:bg-zinc-700 text-zinc-300 text-[11px] font-semibold transition-all">Detail</a>
                                @elseif($tx->type === 'out')
                                    <a href="{{ route('stock-out.show', $tx) }}" class="px-2.5 py-1 rounded-lg bg-zinc-800 hover:bg-zinc-700 text-zinc-300 text-[11px] font-semibold transition-all">Detail</a>
                                @else
                                    <a href="{{ route('adjustments.show', $tx) }}" class="px-2.5 py-1 rounded-lg bg-zinc-800 hover:bg-zinc-700 text-zinc-300 text-[11px] font-semibold transition-all">Detail</a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-zinc-500 text-xs">
                                <svg class="w-8 h-8 mx-auto mb-2 text-zinc-700" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                Belum ada riwayat transaksi.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
