@extends('layouts.app')

@section('title', 'Laporan Mutasi Stok')
@section('page-title', 'Laporan Mutasi & Nilai Stok')
@section('page-subtitle', 'Rekapitulasi keluar masuk bahan baku dan pergerakan stok gudang')

@section('content')
<div class="space-y-6">
    <!-- Header with Action -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-extrabold text-white tracking-tight">Laporan Mutasi Stok & Nilai Pembelian</h2>
            <p class="text-xs text-zinc-400">Analisis pengeluaran bar dan penerimaan supply periode {{ \Carbon\Carbon::parse($startDate)->translatedFormat('d M Y') }} s/d {{ \Carbon\Carbon::parse($endDate)->translatedFormat('d M Y') }}.</p>
        </div>

        <a href="{{ route('reports.print', request()->query()) }}" target="_blank"
            class="px-4 py-2.5 rounded-xl bg-zinc-800 hover:bg-zinc-700 text-zinc-200 border border-zinc-700 font-bold text-xs flex items-center gap-2 transition-all self-start sm:self-auto">
            <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
            </svg>
            Cetak Format Laporan (Print / PDF)
        </a>
    </div>

    <!-- Filter Card -->
    <div class="bg-zinc-900/70 border border-zinc-800 rounded-2xl p-5">
        <form method="GET" action="{{ route('reports.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
            <div>
                <label class="block text-[11px] font-semibold text-zinc-400 mb-1">Dari Tanggal</label>
                <input type="date" name="start_date" value="{{ $startDate }}"
                    class="w-full bg-zinc-950 border border-zinc-700/80 rounded-xl px-3 py-2 text-xs text-zinc-100 focus:outline-none focus:border-amber-500">
            </div>

            <div>
                <label class="block text-[11px] font-semibold text-zinc-400 mb-1">Sampai Tanggal</label>
                <input type="date" name="end_date" value="{{ $endDate }}"
                    class="w-full bg-zinc-950 border border-zinc-700/80 rounded-xl px-3 py-2 text-xs text-zinc-100 focus:outline-none focus:border-amber-500">
            </div>

            <div>
                <label class="block text-[11px] font-semibold text-zinc-400 mb-1">Jenis Mutasi</label>
                <select name="type" class="w-full bg-zinc-950 border border-zinc-700/80 rounded-xl px-3 py-2 text-xs text-zinc-100 focus:outline-none focus:border-amber-500">
                    <option value="">Semua Mutasi</option>
                    <option value="in" {{ $type === 'in' ? 'selected' : '' }}>Stok Masuk (Penerimaan)</option>
                    <option value="out" {{ $type === 'out' ? 'selected' : '' }}>Stok Keluar (Pemakaian Bar)</option>
                    <option value="adjustment" {{ $type === 'adjustment' ? 'selected' : '' }}>Penyesuaian (Opname)</option>
                </select>
            </div>

            <div>
                <label class="block text-[11px] font-semibold text-zinc-400 mb-1">Kategori Bahan</label>
                <select name="category_id" class="w-full bg-zinc-950 border border-zinc-700/80 rounded-xl px-3 py-2 text-xs text-zinc-100 focus:outline-none focus:border-amber-500">
                    <option value="">Semua Kategori</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ $categoryId == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-end gap-2">
                <button type="submit" class="w-full py-2 px-4 rounded-xl bg-amber-600 hover:bg-amber-500 text-white font-bold text-xs transition-all">
                    Terapkan Filter
                </button>
            </div>
        </form>
    </div>

    <!-- Summary Metrics -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="p-4 rounded-xl bg-zinc-900/70 border border-zinc-800">
            <div class="text-[11px] font-semibold text-zinc-500 uppercase">Total Qty Diterima</div>
            <div class="text-xl font-black text-emerald-400 mt-1">+{{ number_format($totalInQty, 1, ',', '.') }}</div>
            <div class="text-[10px] text-zinc-400 mt-0.5">Bahan masuk ke gudang</div>
        </div>

        <div class="p-4 rounded-xl bg-zinc-900/70 border border-zinc-800">
            <div class="text-[11px] font-semibold text-zinc-500 uppercase">Nilai Pembelian Masuk</div>
            <div class="text-xl font-black text-white mt-1">Rp {{ number_format($totalInCost, 0, ',', '.') }}</div>
            <div class="text-[10px] text-zinc-400 mt-0.5">Total belanja supplier</div>
        </div>

        <div class="p-4 rounded-xl bg-zinc-900/70 border border-zinc-800">
            <div class="text-[11px] font-semibold text-zinc-500 uppercase">Total Qty Dikeluarkan</div>
            <div class="text-xl font-black text-amber-400 mt-1">-{{ number_format($totalOutQty, 1, ',', '.') }}</div>
            <div class="text-[10px] text-zinc-400 mt-0.5">Dipakai bar / kitchen</div>
        </div>

        <div class="p-4 rounded-xl bg-zinc-900/70 border border-zinc-800">
            <div class="text-[11px] font-semibold text-zinc-500 uppercase">Nilai Pemakaian Pokok</div>
            <div class="text-xl font-black text-zinc-200 mt-1">Rp {{ number_format($totalOutCost, 0, ',', '.') }}</div>
            <div class="text-[10px] text-zinc-400 mt-0.5">COGS / Nilai modal terpakai</div>
        </div>
    </div>

    <!-- Report Table -->
    <div class="bg-zinc-900/70 border border-zinc-800 rounded-2xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-zinc-900 border-b border-zinc-800 text-zinc-400 font-semibold uppercase tracking-wider text-[10px]">
                        <th class="py-3 px-4">Tanggal & No Transaksi</th>
                        <th class="py-3 px-4">Tipe Mutasi</th>
                        <th class="py-3 px-4">Bahan Baku (SKU)</th>
                        <th class="py-3 px-4 text-center">Jumlah (Qty)</th>
                        <th class="py-3 px-4 text-right">Harga Beli</th>
                        <th class="py-3 px-4 text-right">Subtotal</th>
                        <th class="py-3 px-4">Keterangan</th>
                        <th class="py-3 px-4">Petugas</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-800/60">
                    @forelse($records as $rec)
                        @php $tx = $rec->transaction; @endphp
                        <tr class="hover:bg-zinc-850/50 transition-colors">
                            <td class="py-3.5 px-4 font-mono">
                                <div class="font-bold text-zinc-200">{{ $tx->transaction_code }}</div>
                                <div class="text-[11px] text-zinc-500 font-sans">{{ $tx->date->translatedFormat('d M Y') }}</div>
                            </td>

                            <td class="py-3.5 px-4">
                                @if($tx->type === 'in')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                                        Masuk
                                    </span>
                                @elseif($tx->type === 'out')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-amber-500/10 text-amber-400 border border-amber-500/20">
                                        Keluar
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-cyan-500/10 text-cyan-400 border border-cyan-500/20">
                                        Opname
                                    </span>
                                @endif
                            </td>

                            <td class="py-3.5 px-4">
                                <div class="font-bold text-white">{{ $rec->item->name }}</div>
                                <div class="text-[10px] text-zinc-500 font-mono">{{ $rec->item->sku }} &bull; {{ $rec->item->category->name }}</div>
                            </td>

                            <td class="py-3.5 px-4 text-center font-bold">
                                @if($tx->type === 'in')
                                    <span class="text-emerald-400">+{{ number_format($rec->quantity, 1, ',', '.') }} {{ $rec->item->unit }}</span>
                                @elseif($tx->type === 'out')
                                    <span class="text-amber-400">-{{ number_format($rec->quantity, 1, ',', '.') }} {{ $rec->item->unit }}</span>
                                @else
                                    <span class="text-cyan-400">{{ $rec->quantity >= 0 ? '+' : '' }}{{ number_format($rec->quantity, 1, ',', '.') }} {{ $rec->item->unit }}</span>
                                @endif
                            </td>

                            <td class="py-3.5 px-4 text-right text-zinc-400">
                                Rp {{ number_format($rec->unit_cost, 0, ',', '.') }}
                            </td>

                            <td class="py-3.5 px-4 text-right font-bold text-white">
                                Rp {{ number_format($rec->subtotal, 0, ',', '.') }}
                            </td>

                            <td class="py-3.5 px-4 text-zinc-400 max-w-xs truncate">
                                {{ $rec->notes ?: $tx->destination_or_reason }}
                            </td>

                            <td class="py-3.5 px-4">
                                <div class="text-zinc-200">{{ $tx->user->name ?? '-' }}</div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-12 text-center text-zinc-500">
                                Tidak ada mutasi stok pada rentang tanggal dan kriteria yang dipilih.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($records->hasPages())
            <div class="p-4 border-t border-zinc-800">
                {{ $records->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
