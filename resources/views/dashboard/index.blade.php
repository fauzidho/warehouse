@extends('layouts.app')

@section('title', 'Dasbor')
@section('page-title', 'Dasbor Utama')
@section('page-subtitle', 'Ringkasan operasional dan persediaan stok bahan baku')

@section('content')
<div class="dashboard-page">

    {{-- Welcome Banner --}}
    <div class="dashboard-welcome relative overflow-hidden rounded-2xl bg-gradient-to-br from-zinc-900 via-zinc-900 to-amber-950/30 border border-zinc-800 p-5 sm:p-7 card-interactive">
        <div class="absolute right-0 top-0 bottom-0 w-2/5 bg-gradient-to-l from-amber-600/8 to-transparent pointer-events-none"></div>
        <div class="relative flex flex-col md:flex-row md:items-center justify-between gap-5">
            <div class="space-y-1.5">
                <div class="inline-flex items-center gap-2 px-2.5 py-1 rounded-full bg-amber-500/10 border border-amber-500/20 text-amber-400 text-xs font-medium">
                    <span class="w-1.5 h-1.5 rounded-full bg-amber-400 animate-pulse"></span>
                    Sistem Aktif &bull; Gudang Half Kopi
                </div>
                <h2 class="text-xl sm:text-2xl font-extrabold text-white tracking-tight">
                    Selamat Datang, {{ auth()->user()->name }}
                </h2>
                <p class="text-sm text-zinc-400 max-w-lg">
                    Status akun: <strong class="text-zinc-200">{{ auth()->user()->isAdmin() ? 'Administrator' : 'Staf Pegawai' }}</strong>.
                    Pantau persediaan bahan baku dan catat pergerakan stok harian secara real-time.
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
                    class="px-4 py-2.5 rounded-xl bg-zinc-800 hover:bg-zinc-700 text-zinc-200 border border-zinc-700 font-bold text-xs flex items-center gap-2 transition-all active:scale-95">
                    <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    Opname Fisik
                </a>
            </div>
        </div>
    </div>



    @if($totalItems == 0)
        <div class="dashboard-empty p-5 rounded-2xl bg-amber-950/20 border border-amber-500/20 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="w-10 h-10 rounded-xl bg-amber-500/10 text-amber-400 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-white">Gudang Siap Digunakan</h3>
                    <p class="text-xs text-zinc-400 mt-0.5">Belum ada bahan baku terdaftar. Mulai dengan menambahkan data bahan baku ke katalog.</p>
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
    <div class="dashboard-kpis grid grid-cols-2 lg:grid-cols-4 gap-4">
        {{-- Total Bahan --}}
        <div class="dashboard-kpi p-5 rounded-2xl bg-zinc-900/70 border border-zinc-800 transition-all">
            <div class="flex items-center justify-between mb-3">
                <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider">Total Bahan Baku</span>
                <div class="w-8 h-8 rounded-lg bg-zinc-800 text-amber-400 flex items-center justify-center border border-zinc-700/60">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                </div>
            </div>
            <div class="text-2xl font-black text-white">{{ number_format($totalItems) }}</div>
            <div class="text-[11px] text-zinc-400 mt-1 flex items-center justify-between">
                <span>Jenis Bahan Terdaftar</span>
                <a href="{{ route('items.index') }}" class="text-amber-400 hover:text-amber-300 font-medium text-[10px]">Lihat Semua &rarr;</a>
            </div>
        </div>

        {{-- Valuasi Stok --}}
        <div class="dashboard-kpi p-5 rounded-2xl bg-zinc-900/70 border border-zinc-800 transition-all">
            <div class="flex items-center justify-between mb-3">
                <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider">Estimasi Nilai Aset</span>
                <div class="w-8 h-8 rounded-lg bg-amber-500/10 text-amber-400 flex items-center justify-center border border-amber-500/20">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
            <div class="text-lg font-black text-white leading-tight break-all">Rp {{ number_format($totalValuation, 0, ',', '.') }}</div>
            <div class="text-[11px] text-zinc-400 mt-1">
                <span class="text-emerald-400 font-medium">Nilai persediaan gudang</span>
            </div>
        </div>

        {{-- Stok Menipis --}}
        <div class="dashboard-kpi p-5 rounded-2xl bg-zinc-900/70 border {{ $lowStockItems->count() > 0 ? 'border-rose-500/30' : 'border-zinc-800' }} transition-all">
            <div class="flex items-center justify-between mb-3">
                <span class="text-[10px] font-bold {{ $lowStockItems->count() > 0 ? 'text-rose-400' : 'text-zinc-400' }} uppercase tracking-wider">Perlu Restock Segera</span>
                <div class="w-8 h-8 rounded-lg bg-rose-500/10 text-rose-400 flex items-center justify-center border border-rose-500/20">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </div>
            </div>
            <div class="text-2xl font-black {{ $lowStockItems->count() > 0 ? 'text-rose-400' : 'text-white' }}">
                {{ $lowStockItems->count() }}
                <span class="text-sm font-normal text-zinc-400">bahan</span>
            </div>
            <div class="text-[11px] mt-1">
                @if($outOfStockCount > 0)
                    <span class="text-rose-400 font-semibold">{{ $outOfStockCount }} bahan habis total</span>
                @else
                    <span class="text-emerald-400">Tidak ada stok yang habis</span>
                @endif
            </div>
        </div>

        {{-- Aktivitas Hari Ini --}}
        <div class="dashboard-kpi p-5 rounded-2xl bg-zinc-900/70 border border-zinc-800 transition-all">
            <div class="flex items-center justify-between mb-3">
                <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider">Aktivitas Hari Ini</span>
                <div class="w-8 h-8 rounded-lg bg-emerald-500/10 text-emerald-400 flex items-center justify-center border border-emerald-500/20">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                </div>
            </div>
            <div class="text-2xl font-black text-white">
                {{ $todayStockInCount + $todayStockOutCount }}
                <span class="text-sm font-normal text-zinc-400">transaksi</span>
            </div>
            <div class="text-[11px] text-zinc-400 mt-1 flex items-center gap-2">
                <span class="text-emerald-400 font-medium">{{ $todayStockInCount }} masuk</span>
                <span>&bull;</span>
                <span class="text-amber-400 font-medium">{{ $todayStockOutCount }} keluar</span>
            </div>
        </div>
    </div>

    {{-- Alert Panels: Stok Menipis & Kadaluarsa --}}
    <div class="dashboard-alerts grid grid-cols-1 lg:grid-cols-2 gap-5">

        {{-- Stok Menipis --}}
        <div class="dashboard-panel bg-zinc-900/70 border border-zinc-800 rounded-2xl p-5">
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
                        @php 
                            $percent = $lItem->minimum_stock > 0 ? min(100, round(($lItem->current_stock / $lItem->minimum_stock) * 100)) : 0; 
                        @endphp
                        <div class="p-3.5 rounded-xl bg-zinc-950/60 border border-zinc-800 flex items-center justify-between hover:border-zinc-700 transition-all">
                            <div class="flex-1 pr-4 min-w-0">
                                <div class="text-xs font-bold text-white truncate">{{ $lItem->name }}</div>
                                <div class="text-[11px] text-zinc-500 mt-0.5">
                                    Kategori: <span class="text-zinc-400">{{ $lItem->category->name }}</span>
                                </div>
                                {{-- Visual Gauge Progress Bar --}}
                                <div class="w-full max-w-[140px] h-1.5 bg-zinc-800 rounded-full overflow-hidden mt-2">
                                    <div class="h-full {{ $lItem->current_stock <= 0 ? 'bg-rose-500' : 'bg-amber-500' }} rounded-full transition-all" style="width: {{ $percent }}%"></div>
                                </div>
                            </div>
                            <div class="text-right flex items-center gap-3 shrink-0">
                                <div>
                                    <div class="text-xs font-bold {{ $lItem->current_stock <= 0 ? 'text-rose-400' : 'text-amber-400' }} font-mono">
                                        {{ $lItem->current_stock }} / {{ $lItem->minimum_stock }} <span class="text-zinc-500 text-[10px] font-sans">{{ $lItem->unit }}</span>
                                    </div>
                                    <div class="text-[10px] text-zinc-500">
                                        {{ $lItem->current_stock <= 0 ? 'Habis Total' : 'Sisa ' . $percent . '%' }}
                                    </div>
                                </div>
                                <a href="{{ route('stock-in.create') }}"
                                    class="px-2.5 py-1.5 rounded-lg bg-zinc-800 hover:bg-zinc-750 text-zinc-300 hover:text-white border border-zinc-700 text-[11px] font-semibold transition-all inline-flex items-center gap-1 active:scale-95"
                                    title="Catat Stok Masuk untuk item ini">
                                    <span class="text-emerald-400 font-bold">+</span> Restock
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="dashboard-empty-state py-10 text-center space-y-2">
                    <svg class="w-10 h-10 mx-auto text-emerald-400/50" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <div class="text-xs font-semibold text-zinc-300">Semua Stok Aman</div>
                    <div class="text-[11px] text-zinc-500">Tidak ada bahan baku yang berada di bawah batas minimum.</div>
                </div>
            @endif
        </div>

        {{-- Ritme Aktivitas 7 Hari Terakhir --}}
        <div class="dashboard-panel bg-zinc-900/70 border border-zinc-800 rounded-2xl p-5 flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-2">
                    <div class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-amber-400"></span>
                        <h3 class="text-sm font-bold text-white">Ritme Transaksi 7 Hari</h3>
                    </div>
                    <div class="flex items-center gap-3 text-[10px] text-zinc-400">
                        <span class="flex items-center gap-1"><span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span> Masuk</span>
                        <span class="flex items-center gap-1"><span class="w-1.5 h-1.5 rounded-full bg-amber-400"></span> Keluar</span>
                    </div>
                </div>
                <p class="text-[11px] text-zinc-500 mb-4">Grafik pergerakan stok harian gudang Half Kopi</p>

                <div class="grid grid-cols-7 gap-2 pt-2">
                    @php $maxVal = max(1, $last7Days->max('total') ?? 1); @endphp
                    @foreach($last7Days as $day)
                        @php 
                            $inH = round(($day['in'] / $maxVal) * 48);
                            $outH = round(($day['out'] / $maxVal) * 48);
                        @endphp
                        <div class="flex flex-col items-center p-2 rounded-xl hover:bg-zinc-850/80 transition-all cursor-pointer group"
                            title="{{ $day['day_full'] }}: {{ $day['in'] }} masuk, {{ $day['out'] }} keluar">
                            <div class="h-16 w-full flex items-end justify-center gap-1 pb-1">
                                <div class="w-2 rounded-t bg-emerald-500/80 group-hover:bg-emerald-400 transition-all" style="height: {{ max(4, $inH) }}px;"></div>
                                <div class="w-2 rounded-t bg-amber-500/80 group-hover:bg-amber-400 transition-all" style="height: {{ max(4, $outH) }}px;"></div>
                            </div>
                            <span class="text-[10px] font-semibold text-zinc-400 group-hover:text-white transition-colors">{{ $day['day'] }}</span>
                            <span class="text-[9px] text-zinc-600 font-mono">{{ $day['total'] }}</span>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="mt-4 pt-3 border-t border-zinc-800/80 flex items-center justify-between text-[11px] text-zinc-400">
                <span>Total 7 Hari: <strong class="text-zinc-200">{{ $last7Days->sum('total') }} Transaksi</strong></span>
                <span class="text-zinc-500">Terakhir update: Hari ini</span>
            </div>
        </div>
    </div>

    {{-- Riwayat Transaksi Terbaru dengan Filter Cepat & Pencarian Langsung --}}
    <div class="dashboard-recent bg-zinc-900/80 border border-zinc-800 rounded-2xl shadow-xl overflow-hidden">
        {{-- Header Panel --}}
        <div class="p-5 border-b border-zinc-800/80 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-2">
                    <h3 class="text-sm font-bold text-white">Riwayat Transaksi Terbaru</h3>
                    <span class="px-2 py-0.5 rounded-full bg-zinc-800 text-zinc-400 text-[10px] font-semibold border border-zinc-700/60" id="txCountBadge">
                        {{ $recentTransactions->count() }} Transaksi
                    </span>
                </div>
                <p class="text-[11px] text-zinc-400 mt-0.5">Penerimaan dari pemasok, pemakaian operasional barista, dan opname stok</p>
            </div>

            {{-- Interactive Controls: Quick Search & Full Report Link --}}
            <div class="flex flex-col sm:flex-row sm:items-center gap-3">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-zinc-500">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>
                    <input type="text" id="txSearchInput" placeholder="Cari kode, bahan, alasan..."
                        oninput="handleTxSearch(this.value)"
                        class="w-full sm:w-60 bg-zinc-950 border border-zinc-700/70 rounded-xl pl-8 pr-3 py-1.5 text-xs text-zinc-200 placeholder-zinc-500 focus:outline-none focus:border-amber-500/80 transition-all">
                </div>

                <a href="{{ route('reports.index') }}" class="text-xs text-zinc-400 hover:text-white font-semibold flex items-center gap-1 transition-colors self-start sm:self-auto shrink-0">
                    <span>Laporan Lengkap</span>
                    <svg class="w-3.5 h-3.5 text-amber-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                </a>
            </div>
        </div>

        {{-- Filter Tabs Interaktif (Client-side instant switch) --}}
        <div class="px-5 py-2.5 bg-zinc-950/40 border-b border-zinc-800/60 flex items-center gap-1.5 overflow-x-auto text-xs">
            <span class="text-[11px] text-zinc-500 mr-2 shrink-0 font-medium">Saring Tipe:</span>
            <button type="button" onclick="filterTxType('all')" id="tab-all"
                class="tx-tab px-3 py-1 rounded-lg text-[11px] font-semibold transition-all bg-zinc-800 text-white border border-zinc-700/80">
                Semua
            </button>
            <button type="button" onclick="filterTxType('in')" id="tab-in"
                class="tx-tab px-3 py-1 rounded-lg text-[11px] font-medium transition-all text-zinc-400 hover:text-zinc-200 hover:bg-zinc-850">
                <span class="inline-block w-1.5 h-1.5 rounded-full bg-emerald-400 mr-1"></span> Stok Masuk
            </button>
            <button type="button" onclick="filterTxType('out')" id="tab-out"
                class="tx-tab px-3 py-1 rounded-lg text-[11px] font-medium transition-all text-zinc-400 hover:text-zinc-200 hover:bg-zinc-850">
                <span class="inline-block w-1.5 h-1.5 rounded-full bg-amber-400 mr-1"></span> Stok Keluar
            </button>
            <button type="button" onclick="filterTxType('adjustment')" id="tab-adjustment"
                class="tx-tab px-3 py-1 rounded-lg text-[11px] font-medium transition-all text-zinc-400 hover:text-zinc-200 hover:bg-zinc-850">
                <span class="inline-block w-1.5 h-1.5 rounded-full bg-zinc-400 mr-1"></span> Opname Stok
            </button>
        </div>

        {{-- Table --}}
        <div class="overflow-x-auto">
            <table class="w-full text-xs text-left" style="min-width:680px;" id="dashboardTxTable">
                <thead>
                    <tr class="border-b border-zinc-800/80 bg-zinc-950/60 text-zinc-400 font-semibold uppercase tracking-wider text-[10px]">
                        <th class="py-3 px-5">Kode & Tanggal</th>
                        <th class="py-3 px-4">Jenis & Keterangan</th>
                        <th class="py-3 px-4 hidden md:table-cell">Rincian Bahan</th>
                        <th class="py-3 px-4 hidden sm:table-cell">Petugas</th>
                        <th class="py-3 px-4 text-right">Nilai</th>
                        <th class="py-3 px-5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-800/40" id="txTableBody">
                    @forelse($recentTransactions as $tx)
                        @php $badge = $tx->type_badge; @endphp
                        <tr class="tx-row hover:bg-zinc-850/40 transition-colors"
                            data-type="{{ $tx->type }}"
                            data-search="{{ strtolower($tx->transaction_code . ' ' . $tx->destination_or_reason . ' ' . ($tx->user->name ?? '') . ' ' . $tx->items->pluck('item.name')->implode(' ')) }}">
                            
                            {{-- Kode & Tanggal --}}
                            <td class="py-3.5 px-5 font-mono">
                                <div class="font-bold text-zinc-200 text-[11px]">{{ $tx->transaction_code }}</div>
                                <div class="text-[10px] text-zinc-500 font-sans mt-0.5">{{ $tx->date->translatedFormat('d M Y') }}</div>
                            </td>

                            {{-- Jenis & Keterangan --}}
                            <td class="py-3.5 px-4">
                                <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-md text-[10px] font-semibold {{ $badge['bg'] }} border mb-1">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $badge['dot'] }}"></span>
                                    {{ $badge['label'] }}
                                </span>
                                <div class="text-zinc-300 font-medium overflow-hidden text-ellipsis whitespace-nowrap" style="max-width:200px;">
                                    {{ $tx->destination_or_reason }}
                                </div>
                            </td>

                            {{-- Bahan (Clean neutral styling, NO ALAY YELLOW) --}}
                            <td class="py-3.5 px-4 hidden md:table-cell">
                                <div class="space-y-0.5">
                                    @foreach($tx->items->take(2) as $ti)
                                        <div class="text-zinc-300 text-[11px] flex items-center gap-1.5">
                                            <span class="text-zinc-500">&bull;</span>
                                            <span>{{ $ti->item->name ?? 'Item' }}</span>
                                            <span class="text-zinc-500 font-mono text-[10px]">({{ $ti->quantity }} {{ $ti->item->unit ?? '' }})</span>
                                        </div>
                                    @endforeach
                                    @if($tx->items->count() > 2)
                                        <div class="text-[10px] text-zinc-500 pl-2.5">+{{ $tx->items->count() - 2 }} bahan lainnya</div>
                                    @endif
                                </div>
                            </td>

                            {{-- Operator --}}
                            <td class="py-3.5 px-4 hidden sm:table-cell">
                                <div class="text-zinc-300 font-medium text-[11px]">{{ $tx->user->name ?? '-' }}</div>
                            </td>

                            {{-- Nilai Transaksi --}}
                            <td class="py-3.5 px-4 text-right text-zinc-200 font-medium tabular-nums">
                                @if($tx->total_cost > 0)
                                    Rp {{ number_format($tx->total_cost, 0, ',', '.') }}
                                @else
                                    <span class="text-zinc-600">&mdash;</span>
                                @endif
                            </td>

                            {{-- Aksi Interaktif (Modal Rincian Instan) --}}
                            <td class="py-3.5 px-5 text-right">
                                <button type="button" onclick="openTxDetailModal({{ $tx->id }})"
                                    class="px-3 py-1.5 rounded-lg bg-zinc-800 hover:bg-zinc-700 text-zinc-200 hover:text-white text-[11px] font-semibold border border-zinc-700/60 transition-all inline-flex items-center gap-1 active:scale-95"
                                    title="Klik untuk melihat rincian instan">
                                    <svg class="w-3 h-3 text-zinc-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    <span>Rincian</span>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr id="emptyRow">
                            <td colspan="6" class="py-12 text-center text-zinc-500 text-xs">
                                <svg class="w-8 h-8 mx-auto mb-2 text-zinc-700" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                Belum ada riwayat transaksi.
                            </td>
                        </tr>
                    @endforelse

                    {{-- Row saat filter pencarian tidak menemukan hasil --}}
                    <tr id="noMatchRow" style="display:none;">
                        <td colspan="6" class="py-10 text-center text-zinc-500 text-xs">
                            Tidak ditemukan transaksi yang cocok dengan kata kunci.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

</div>

{{-- ========================================================
     MODAL INTERAKTIF: RINCIAN TRANSAKSI INSTAN (0ms delay)
     ======================================================== --}}
<div id="txDetailModal" class="fixed inset-0 z-50 flex items-center justify-center p-4" style="display:none;">
    {{-- Backdrop --}}
    <div class="fixed inset-0 bg-black/75 backdrop-blur-sm transition-opacity" onclick="closeTxDetailModal()"></div>

    {{-- Modal Box --}}
    <div class="relative bg-zinc-900 border border-zinc-700/80 rounded-2xl w-full max-w-xl shadow-2xl overflow-hidden z-10 flex flex-col max-h-[90vh] animate-in fade-in zoom-in-95 duration-150">
        {{-- Modal Header --}}
        <div class="p-5 border-b border-zinc-800 flex items-center justify-between bg-zinc-950/60">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-zinc-800 flex items-center justify-center border border-zinc-700/70 text-zinc-300">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <h4 class="font-mono font-bold text-sm text-white" id="modalTxCode">-</h4>
                        <span id="modalTxBadge" class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded text-[10px] font-semibold bg-zinc-800 text-zinc-300 border border-zinc-700/60">
                            <span class="w-1.5 h-1.5 rounded-full" id="modalTxDot"></span>
                            <span id="modalTxTypeLabel">-</span>
                        </span>
                    </div>
                    <div class="text-[11px] text-zinc-400 mt-0.5">
                        <span id="modalTxDate">-</span> &bull; Dicatat oleh <strong class="text-zinc-200" id="modalTxUser">-</strong>
                    </div>
                </div>
            </div>
            <button type="button" onclick="closeTxDetailModal()" class="w-8 h-8 rounded-lg bg-zinc-800 hover:bg-zinc-700 text-zinc-400 hover:text-white flex items-center justify-center transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        {{-- Modal Info Summary --}}
        <div class="p-5 overflow-y-auto space-y-4">
            <div class="grid grid-cols-2 gap-3 p-3.5 rounded-xl bg-zinc-950/60 border border-zinc-800 text-xs">
                <div>
                    <div class="text-[10px] font-semibold text-zinc-500 uppercase tracking-wider">Keterangan / Tujuan</div>
                    <div class="text-zinc-200 font-semibold mt-0.5 text-xs" id="modalTxReason">-</div>
                </div>
                <div>
                    <div class="text-[10px] font-semibold text-zinc-500 uppercase tracking-wider">No. Faktur / Referensi</div>
                    <div class="text-zinc-300 font-mono text-xs mt-0.5" id="modalTxRef">-</div>
                </div>
            </div>

            {{-- Items List --}}
            <div>
                <div class="text-xs font-bold text-white mb-2 flex items-center justify-between">
                    <span>Daftar Bahan Baku</span>
                    <span class="text-[11px] text-zinc-500 font-normal" id="modalTxItemCount">0 Bahan</span>
                </div>
                <div class="border border-zinc-800 rounded-xl overflow-hidden">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-zinc-950/80 border-b border-zinc-800 text-zinc-400 text-[10px] uppercase font-semibold">
                            <tr>
                                <th class="py-2.5 px-3">Nama Bahan</th>
                                <th class="py-2.5 px-3 text-right">Jumlah</th>
                                <th class="py-2.5 px-3 text-right">Harga Satuan</th>
                                <th class="py-2.5 px-3 text-right">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-800/60" id="modalTxItemsBody">
                            {{-- Injected dynamically via JS --}}
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Total Nilai --}}
            <div class="flex items-center justify-between p-3.5 rounded-xl bg-zinc-950 border border-zinc-800">
                <span class="text-xs font-bold text-zinc-400">Total Nilai Transaksi</span>
                <span class="text-sm font-black text-white font-mono" id="modalTxTotal">Rp 0</span>
            </div>
        </div>

        {{-- Modal Footer --}}
        <div class="p-4 border-t border-zinc-800 bg-zinc-950/40 flex items-center justify-between gap-3">
            <a href="#" id="modalTxFullLink" target="_blank"
                class="px-3.5 py-2 rounded-xl bg-zinc-800 hover:bg-zinc-700 text-zinc-200 text-xs font-semibold border border-zinc-700/60 transition-all flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5 text-zinc-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                <span>Buka Bukti Lengkap</span>
            </a>
            <button type="button" onclick="closeTxDetailModal()"
                class="px-4 py-2 rounded-xl bg-zinc-800 hover:bg-zinc-700 text-zinc-300 text-xs font-semibold transition-all">
                Tutup
            </button>
        </div>
    </div>
</div>

{{-- Data JSON Transaksi untuk Modal Instan --}}
<script id="txRawData" type="application/json">
{!! json_encode($recentTransactions->map(function($t) {
    return [
        'id' => $t->id,
        'code' => $t->transaction_code,
        'type' => $t->type,
        'type_label' => $t->type_badge['label'],
        'type_dot' => $t->type_badge['dot'],
        'date' => $t->date->translatedFormat('d F Y'),
        'reason' => $t->destination_or_reason ?: '-',
        'ref' => $t->reference_number ?: '-',
        'user' => $t->user->name ?? '-',
        'total_cost' => $t->total_cost > 0 ? 'Rp ' . number_format($t->total_cost, 0, ',', '.') : 'Rp 0',
        'show_url' => $t->type === 'in' ? route('stock-in.show', $t) : ($t->type === 'out' ? route('stock-out.show', $t) : route('adjustments.show', $t)),
        'items' => $t->items->map(function($i) {
            return [
                'name' => $i->item->name ?? 'Bahan Baku',
                'unit' => $i->item->unit ?? '',
                'quantity' => $i->quantity,
                'cost_price' => $i->cost_price > 0 ? 'Rp ' . number_format($i->cost_price, 0, ',', '.') : '-',
                'subtotal' => $i->subtotal > 0 ? 'Rp ' . number_format($i->subtotal, 0, ',', '.') : '-',
            ];
        })
    ];
})) !!}
</script>

<script>
    let currentFilterType = 'all';
    let currentSearchTerm = '';
    let txStore = {};

    try {
        const raw = JSON.parse(document.getElementById('txRawData').textContent);
        raw.forEach(tx => { txStore[tx.id] = tx; });
    } catch (e) {
        console.error('Failed to parse tx data', e);
    }

    function filterTxType(type) {
        currentFilterType = type;

        // Update button states
        document.querySelectorAll('.tx-tab').forEach(b => {
            b.classList.remove('bg-zinc-800', 'text-white', 'border', 'border-zinc-700/80');
            b.classList.add('text-zinc-400');
        });
        const activeBtn = document.getElementById('tab-' + type);
        if (activeBtn) {
            activeBtn.classList.add('bg-zinc-800', 'text-white', 'border', 'border-zinc-700/80');
            activeBtn.classList.remove('text-zinc-400');
        }

        applyTableFilters();
    }

    function handleTxSearch(query) {
        currentSearchTerm = (query || '').toLowerCase().trim();
        applyTableFilters();
    }

    function applyTableFilters() {
        const rows = document.querySelectorAll('.tx-row');
        let visibleCount = 0;

        rows.forEach(row => {
            const rowType = row.getAttribute('data-type');
            const rowSearch = row.getAttribute('data-search') || '';

            const matchType = (currentFilterType === 'all') || (rowType === currentFilterType);
            const matchSearch = !currentSearchTerm || rowSearch.includes(currentSearchTerm);

            if (matchType && matchSearch) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });

        // Update count badge & no match message
        const countBadge = document.getElementById('txCountBadge');
        if (countBadge) countBadge.textContent = visibleCount + ' Transaksi';

        const noMatch = document.getElementById('noMatchRow');
        if (noMatch) {
            noMatch.style.display = (visibleCount === 0 && rows.length > 0) ? '' : 'none';
        }
    }

    function openTxDetailModal(id) {
        const tx = txStore[id];
        if (!tx) return;

        document.getElementById('modalTxCode').textContent = tx.code;
        document.getElementById('modalTxTypeLabel').textContent = tx.type_label;
        document.getElementById('modalTxDot').className = 'w-1.5 h-1.5 rounded-full ' + tx.type_dot;
        document.getElementById('modalTxDate').textContent = tx.date;
        document.getElementById('modalTxUser').textContent = tx.user;
        document.getElementById('modalTxReason').textContent = tx.reason;
        document.getElementById('modalTxRef').textContent = tx.ref;
        document.getElementById('modalTxTotal').textContent = tx.total_cost;
        document.getElementById('modalTxFullLink').href = tx.show_url;
        document.getElementById('modalTxItemCount').textContent = tx.items.length + ' Bahan';

        const tbody = document.getElementById('modalTxItemsBody');
        tbody.innerHTML = '';
        tx.items.forEach(it => {
            const tr = document.createElement('tr');
            tr.className = 'hover:bg-zinc-850/50';
            tr.innerHTML = `
                <td class="py-2.5 px-3 font-semibold text-zinc-200">${it.name}</td>
                <td class="py-2.5 px-3 text-right font-mono text-zinc-300">${it.quantity} <span class="text-zinc-500">${it.unit}</span></td>
                <td class="py-2.5 px-3 text-right font-mono text-zinc-400">${it.cost_price}</td>
                <td class="py-2.5 px-3 text-right font-mono font-semibold text-zinc-200">${it.subtotal}</td>
            `;
            tbody.appendChild(tr);
        });

        document.getElementById('txDetailModal').style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }

    function closeTxDetailModal() {
        document.getElementById('txDetailModal').style.display = 'none';
        document.body.style.overflow = '';
    }

    // Close on Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeTxDetailModal();
    });
</script>
@endsection


