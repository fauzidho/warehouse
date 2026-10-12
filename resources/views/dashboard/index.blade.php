@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard Gudang')
@section('page-subtitle', 'Monitoring stok dan pergerakan bahan baku Half Kopi')

@section('content')
<div class="space-y-8">
    <!-- Welcome Greeting & Quick Actions Banner -->
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-zinc-900 via-zinc-900 to-amber-950/40 border border-zinc-800 p-6 sm:p-8">
        <div class="absolute right-0 top-0 bottom-0 w-1/3 bg-radial from-amber-600/10 via-transparent to-transparent pointer-events-none"></div>
        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="space-y-2">
                <div class="inline-flex items-center gap-2 px-2.5 py-1 rounded-full bg-amber-500/10 border border-amber-500/30 text-amber-400 text-xs font-semibold">
                    <span class="w-1.5 h-1.5 rounded-full bg-amber-400 animate-ping"></span>
                    Sistem Aktif &bull; Gudang Half Kopi
                </div>
                <h2 class="text-2xl font-extrabold text-white tracking-tight">
                    Halo, {{ auth()->user()->name }} 👋
                </h2>
                <p class="text-xs sm:text-sm text-zinc-400 max-w-xl">
                    Anda login sebagai <strong class="text-zinc-200">{{ auth()->user()->isAdmin() ? 'Administrator' : 'Pegawai (Staff / Barista)' }}</strong>.
                    Periksa ketersediaan espresso beans, susu, sirup konsentrat, serta rekapitulasi mutasi harian.
                </p>
            </div>

            <!-- Fast Action Buttons -->
            <div class="flex flex-wrap items-center gap-3">
                <a href="{{ route('stock-in.create') }}"
                    class="px-4 py-2.5 rounded-xl bg-gradient-to-r from-emerald-500 to-emerald-600 hover:from-emerald-400 hover:to-emerald-500 text-zinc-950 font-bold text-xs flex items-center gap-2 shadow-lg shadow-emerald-950/40 transition-all transform active:scale-95">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                    </svg>
                    Catat Stok Masuk
                </a>
                <a href="{{ route('stock-out.create') }}"
                    class="px-4 py-2.5 rounded-xl bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 text-zinc-950 font-bold text-xs flex items-center gap-2 shadow-lg shadow-amber-950/40 transition-all transform active:scale-95">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M20 12H4"/>
                    </svg>
                    Pemakaian Barista
                </a>
                <a href="{{ route('adjustments.create') }}"
                    class="px-4 py-2.5 rounded-xl bg-zinc-800 hover:bg-zinc-700 text-zinc-200 border border-zinc-700 font-bold text-xs flex items-center gap-2 transition-all">
                    <svg class="w-4 h-4 text-cyan-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    Stock Opname
                </a>
            </div>
        </div>
    </div>

    @if($totalItems == 0)
        <!-- Onboarding Fresh State -->
        <div class="p-6 rounded-2xl bg-gradient-to-r from-amber-950/30 via-zinc-900 to-zinc-900 border border-amber-500/30 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-amber-500/10 border border-amber-500/20 text-amber-400 flex items-center justify-center shrink-0 text-xl font-bold">
                    🔥
                </div>
                <div>
                    <h3 class="text-sm font-bold text-white">Database Bersih (Siap Digunakan)</h3>
                    <p class="text-xs text-zinc-400 mt-0.5">Seluruh data dummy telah dibersihkan. Anda dapat mulai menambahkan bahan baku riil atau menyinkronkan data langsung dengan Google Cloud Firestore.</p>
                </div>
            </div>
            <div class="flex items-center gap-2 self-start md:self-auto shrink-0">
                @if(auth()->user()->isAdmin())
                    <a href="{{ route('items.create') }}" class="px-3.5 py-2 rounded-xl bg-amber-500 hover:bg-amber-400 text-zinc-950 font-bold text-xs transition-all">
                        + Tambah Bahan Pertama
                    </a>
                @endif
                <a href="{{ route('firebase.index') }}" class="px-3.5 py-2 rounded-xl bg-zinc-800 hover:bg-zinc-700 text-zinc-200 border border-zinc-700 font-bold text-xs transition-all">
                    Buka Firebase Center &rarr;
                </a>
            </div>
        </div>
    @endif

    <!-- 4 KPI Summary Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <!-- Card 1: Total Bahan Baku -->
        <div class="p-5 rounded-2xl bg-zinc-900/70 border border-zinc-800 hover:border-zinc-700 transition-all">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-semibold text-zinc-400 uppercase tracking-wider">Katalog Bahan</span>
                <div class="w-9 h-9 rounded-xl bg-blue-500/10 text-blue-400 flex items-center justify-center border border-blue-500/20">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                </div>
            </div>
            <div class="text-2xl font-black text-white tracking-tight">{{ number_format($totalItems) }}</div>
            <div class="text-[11px] text-zinc-400 mt-1 flex items-center justify-between">
                <span>SKU Terdaftar</span>
                <a href="{{ route('items.index') }}" class="text-amber-400 hover:underline font-medium">Lihat Semua &rarr;</a>
            </div>
        </div>

        <!-- Card 2: Valuasi Aset Stok (Khusus Admin atau ditampilkan jika berwenang) -->
        <div class="p-5 rounded-2xl bg-zinc-900/70 border border-zinc-800 hover:border-zinc-700 transition-all">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-semibold text-zinc-400 uppercase tracking-wider">Valuasi Stok</span>
                <div class="w-9 h-9 rounded-xl bg-amber-500/10 text-amber-400 flex items-center justify-center border border-amber-500/20">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
            <div class="text-2xl font-black text-white tracking-tight">
                Rp {{ number_format($totalValuation, 0, ',', '.') }}
            </div>
            <div class="text-[11px] text-zinc-400 mt-1 flex items-center justify-between">
                <span>Estimasi Nilai Gudang</span>
                <span class="text-emerald-400 font-medium">Aset Aktif</span>
            </div>
        </div>

        <!-- Card 3: Stok Menipis & Habis -->
        <div class="p-5 rounded-2xl bg-zinc-900/70 border {{ $lowStockItems->count() > 0 ? 'border-rose-500/30 bg-rose-950/10' : 'border-zinc-800' }} transition-all">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-semibold {{ $lowStockItems->count() > 0 ? 'text-rose-400' : 'text-zinc-400' }} uppercase tracking-wider">Stok Menipis</span>
                <div class="w-9 h-9 rounded-xl bg-rose-500/10 text-rose-400 flex items-center justify-center border border-rose-500/20">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
            </div>
            <div class="text-2xl font-black {{ $lowStockItems->count() > 0 ? 'text-rose-400' : 'text-white' }} tracking-tight">
                {{ $lowStockItems->count() }} <span class="text-sm font-normal text-zinc-400">Bahan</span>
            </div>
            <div class="text-[11px] text-zinc-400 mt-1 flex items-center justify-between">
                <span>Perlu PO / Restock</span>
                @if($outOfStockCount > 0)
                    <span class="text-rose-400 font-semibold">{{ $outOfStockCount }} Habis</span>
                @else
                    <span class="text-emerald-400">Tidak ada yg habis</span>
                @endif
            </div>
        </div>

        <!-- Card 4: Aktivitas Hari Ini -->
        <div class="p-5 rounded-2xl bg-zinc-900/70 border border-zinc-800 hover:border-zinc-700 transition-all">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-semibold text-zinc-400 uppercase tracking-wider">Mutasi Hari Ini</span>
                <div class="w-9 h-9 rounded-xl bg-emerald-500/10 text-emerald-400 flex items-center justify-center border border-emerald-500/20">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                    </svg>
                </div>
            </div>
            <div class="text-2xl font-black text-white tracking-tight">
                {{ $todayStockInCount + $todayStockOutCount }} <span class="text-sm font-normal text-zinc-400">Transaksi</span>
            </div>
            <div class="text-[11px] text-zinc-400 mt-1 flex items-center gap-3">
                <span class="text-emerald-400 font-medium">&darr; {{ $todayStockInCount }} Masuk</span>
                <span>•</span>
                <span class="text-amber-400 font-medium">&uarr; {{ $todayStockOutCount }} Keluar</span>
            </div>
        </div>
    </div>

    <!-- Alert Panel: Bahan Stok Menipis & Kadaluarsa Mendekat -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Low Stock Items List -->
        <div class="bg-zinc-900/70 border border-zinc-800 rounded-2xl p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-2">
                    <div class="w-2.5 h-2.5 rounded-full bg-rose-500 animate-ping"></div>
                    <h3 class="text-sm font-bold text-white tracking-tight">Peringatan Stok Menipis (Restock Alert)</h3>
                </div>
                <span class="text-xs text-zinc-400 font-medium">{{ $lowStockItems->count() }} Bahan</span>
            </div>

            @if($lowStockItems->count() > 0)
                <div class="space-y-3">
                    @foreach($lowStockItems as $lItem)
                        <div class="p-3.5 rounded-xl bg-zinc-950/70 border border-zinc-800/80 flex items-center justify-between hover:border-zinc-700 transition-all">
                            <div class="space-y-1">
                                <div class="flex items-center gap-2">
                                    <span class="text-xs font-bold text-white">{{ $lItem->name }}</span>
                                    <span class="text-[10px] px-1.5 py-0.2 rounded bg-zinc-800 text-zinc-300 font-mono">{{ $lItem->sku }}</span>
                                </div>
                                <div class="text-[11px] text-zinc-400">
                                    Kategori: <span class="text-zinc-300">{{ $lItem->category->name }}</span>
                                </div>
                            </div>
                            <div class="text-right flex items-center gap-4">
                                <div>
                                    <div class="text-xs font-black {{ $lItem->current_stock <= 0 ? 'text-rose-500' : 'text-amber-400' }}">
                                        {{ $lItem->current_stock }} / {{ $lItem->minimum_stock }} {{ $lItem->unit }}
                                    </div>
                                    <div class="text-[10px] text-zinc-500">
                                        {{ $lItem->current_stock <= 0 ? 'Habis Total' : 'Di Bawah Min' }}
                                    </div>
                                </div>
                                <a href="{{ route('stock-in.create') }}" class="p-2 rounded-lg bg-emerald-500/10 hover:bg-emerald-500/20 text-emerald-400 border border-emerald-500/20 transition-all text-xs" title="Restock Masuk">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                                    </svg>
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="py-12 text-center text-zinc-500 space-y-2">
                    <svg class="w-10 h-10 mx-auto text-emerald-400/60" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <div class="text-xs font-semibold text-zinc-300">Semua Stok Bahan Aman</div>
                    <div class="text-[11px]">Tidak ada bahan baku yang berada di bawah batas minimum.</div>
                </div>
            @endif
        </div>

        <!-- Expiring Batches Soon -->
        <div class="bg-zinc-900/70 border border-zinc-800 rounded-2xl p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-2">
                    <div class="w-2.5 h-2.5 rounded-full bg-amber-500"></div>
                    <h3 class="text-sm font-bold text-white tracking-tight">Perhatian Kadaluarsa (&le; 30 Hari)</h3>
                </div>
                <span class="text-xs text-zinc-400 font-medium">Batch Dairy / Beans</span>
            </div>

            @if($expiringBatches->count() > 0)
                <div class="space-y-3">
                    @foreach($expiringBatches as $batch)
                        <div class="p-3.5 rounded-xl bg-zinc-950/70 border border-zinc-800/80 flex items-center justify-between hover:border-zinc-700 transition-all">
                            <div class="space-y-1">
                                <div class="text-xs font-bold text-white">{{ $batch->item->name }}</div>
                                <div class="text-[11px] text-zinc-400">
                                    Penerimaan: <span class="text-zinc-300">{{ $batch->transaction->transaction_code }}</span>
                                    &bull; Qty: {{ $batch->quantity }} {{ $batch->item->unit }}
                                </div>
                            </div>
                            <div class="text-right">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold bg-amber-500/10 text-amber-400 border border-amber-500/30">
                                    Exp: {{ \Carbon\Carbon::parse($batch->expiry_date)->translatedFormat('d M Y') }}
                                </span>
                                <div class="text-[10px] text-zinc-500 mt-1">
                                    {{ \Carbon\Carbon::parse($batch->expiry_date)->diffForHumans() }}
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="py-12 text-center text-zinc-500 space-y-2">
                    <svg class="w-10 h-10 mx-auto text-zinc-600" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <div class="text-xs font-semibold text-zinc-300">Tidak Ada Batch Kritis</div>
                    <div class="text-[11px]">Seluruh batch terdata masih memiliki masa simpan yang cukup lama.</div>
                </div>
            @endif
        </div>
    </div>

    <!-- Recent Transactions Feed -->
    <div class="bg-zinc-900/70 border border-zinc-800 rounded-2xl p-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
            <div>
                <h3 class="text-sm font-bold text-white tracking-tight">Riwayat Mutasi & Transaksi Terkini</h3>
                <p class="text-xs text-zinc-400 mt-0.5">Aktivitas penerimaan supplier, pemakaian barista, dan penyesuaian stok</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('reports.index') }}" class="text-xs text-amber-400 hover:text-amber-300 font-semibold flex items-center gap-1">
                    Lihat Laporan Lengkap &rarr;
                </a>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-zinc-800 text-zinc-400 font-semibold uppercase tracking-wider text-[10px]">
                        <th class="pb-3 pl-2">Kode & Tanggal</th>
                        <th class="pb-3">Jenis & Keterangan</th>
                        <th class="pb-3">Rincian Bahan</th>
                        <th class="pb-3">Operator</th>
                        <th class="pb-3 text-right">Nilai Mutasi</th>
                        <th class="pb-3 text-right pr-2">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-800/60">
                    @forelse($recentTransactions as $tx)
                        @php $badge = $tx->type_badge; @endphp
                        <tr class="hover:bg-zinc-850/50 transition-colors">
                            <td class="py-3.5 pl-2 font-mono">
                                <div class="font-bold text-zinc-200">{{ $tx->transaction_code }}</div>
                                <div class="text-[11px] text-zinc-500 font-sans">{{ $tx->date->translatedFormat('d M Y') }}</div>
                            </td>
                            <td class="py-3.5">
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-semibold border {{ $badge['bg'] }} mb-1">
                                    {{ $badge['label'] }}
                                </span>
                                <div class="text-zinc-300 font-medium truncate max-w-xs">{{ $tx->destination_or_reason }}</div>
                                @if($tx->reference_number)
                                    <div class="text-[10px] text-zinc-500">Ref: {{ $tx->reference_number }}</div>
                                @endif
                            </td>
                            <td class="py-3.5">
                                <div class="space-y-0.5">
                                    @foreach($tx->items->take(2) as $ti)
                                        <div class="text-zinc-300 text-[11px]">
                                            &bull; {{ $ti->item->name ?? 'Item' }}
                                            <span class="text-amber-400/90 font-medium">({{ $ti->quantity }} {{ $ti->item->unit ?? '' }})</span>
                                        </div>
                                    @endforeach
                                    @if($tx->items->count() > 2)
                                        <div class="text-[10px] text-zinc-500">+{{ $tx->items->count() - 2 }} item lainnya</div>
                                    @endif
                                </div>
                            </td>
                            <td class="py-3.5">
                                <div class="text-zinc-300 font-medium">{{ $tx->user->name ?? '-' }}</div>
                                <div class="text-[10px] text-zinc-500 capitalize">{{ $tx->user->role ?? '' }}</div>
                            </td>
                            <td class="py-3.5 text-right font-medium text-zinc-200">
                                @if($tx->total_cost > 0)
                                    Rp {{ number_format($tx->total_cost, 0, ',', '.') }}
                                @else
                                    -
                                @endif
                            </td>
                            <td class="py-3.5 text-right pr-2">
                                @if($tx->type === 'in')
                                    <a href="{{ route('stock-in.show', $tx) }}" class="px-2.5 py-1 rounded bg-zinc-800 hover:bg-zinc-700 text-zinc-300 hover:text-white transition-all text-[11px] font-semibold">Detail</a>
                                @elseif($tx->type === 'out')
                                    <a href="{{ route('stock-out.show', $tx) }}" class="px-2.5 py-1 rounded bg-zinc-800 hover:bg-zinc-700 text-zinc-300 hover:text-white transition-all text-[11px] font-semibold">Detail</a>
                                @else
                                    <a href="{{ route('adjustments.show', $tx) }}" class="px-2.5 py-1 rounded bg-zinc-800 hover:bg-zinc-700 text-zinc-300 hover:text-white transition-all text-[11px] font-semibold">Detail</a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-zinc-500">Belum ada riwayat transaksi mutasi.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
