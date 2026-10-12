@extends('layouts.app')

@section('title', 'Kartu Stok - ' . $item->name)
@section('page-title', 'Detail Bahan & Kartu Stok')
@section('page-subtitle', $item->name . ' (' . $item->sku . ')')

@section('content')
<div class="space-y-6">
    <!-- Top Details Card -->
    <div class="bg-zinc-900/80 border border-zinc-800 rounded-2xl p-6 relative overflow-hidden">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="space-y-2">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="px-2.5 py-1 rounded-lg text-xs font-mono font-bold bg-amber-500/10 text-amber-400 border border-amber-500/20">
                        {{ $item->sku }}
                    </span>
                    <span class="px-2.5 py-1 rounded-lg text-xs font-medium bg-zinc-800 text-zinc-300 border border-zinc-700/60">
                        {{ $item->category->name }}
                    </span>
                    @if($item->current_stock <= 0)
                        <span class="px-2.5 py-1 rounded-lg text-xs font-bold bg-rose-500/10 text-rose-400 border border-rose-500/30">
                            HABIS TOTAL
                        </span>
                    @elseif($item->isLowStock())
                        <span class="px-2.5 py-1 rounded-lg text-xs font-bold bg-amber-500/10 text-amber-400 border border-amber-500/30">
                            STOK MENIPIS (&le; {{ $item->minimum_stock }} {{ $item->unit }})
                        </span>
                    @else
                        <span class="px-2.5 py-1 rounded-lg text-xs font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/30">
                            STOK AMAN
                        </span>
                    @endif
                </div>

                <h2 class="text-2xl font-black text-white tracking-tight">{{ $item->name }}</h2>

                @if($item->description)
                    <p class="text-xs text-zinc-400 max-w-xl">{{ $item->description }}</p>
                @endif
            </div>

            <!-- Stats Highlight -->
            <div class="flex items-center gap-6 bg-zinc-950/60 border border-zinc-800 p-4 rounded-xl self-start md:self-auto">
                <div>
                    <div class="text-[11px] font-semibold text-zinc-500 uppercase">Stok Tersedia</div>
                    <div class="text-3xl font-black {{ $item->current_stock <= 0 ? 'text-rose-500' : ($item->isLowStock() ? 'text-amber-400' : 'text-emerald-400') }}">
                        {{ number_format($item->current_stock, 1, ',', '.') }}
                        <span class="text-sm font-semibold text-zinc-400">{{ $item->unit }}</span>
                    </div>
                </div>

                <div class="h-10 w-px bg-zinc-800"></div>

                <div>
                    <div class="text-[11px] font-semibold text-zinc-500 uppercase">Harga Beli Terakhir</div>
                    <div class="text-lg font-bold text-white">
                        Rp {{ number_format($item->cost_price, 0, ',', '.') }}
                    </div>
                    <div class="text-[10px] text-zinc-400">Total Nilai: Rp {{ number_format($item->current_stock * $item->cost_price, 0, ',', '.') }}</div>
                </div>
            </div>
        </div>

        <!-- Quick Operation Shortcuts for this Item -->
        <div class="mt-6 pt-5 border-t border-zinc-800/80 flex flex-wrap items-center justify-between gap-3">
            <div class="flex items-center gap-2">
                <a href="{{ route('stock-in.create') }}" class="px-3 py-1.5 rounded-lg text-xs font-semibold bg-emerald-500/10 text-emerald-400 hover:bg-emerald-500/20 border border-emerald-500/20 transition-all flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                    Penerimaan Masuk
                </a>
                <a href="{{ route('stock-out.create') }}" class="px-3 py-1.5 rounded-lg text-xs font-semibold bg-amber-500/10 text-amber-400 hover:bg-amber-500/20 border border-amber-500/20 transition-all flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20 12H4"/></svg>
                    Pemakaian Barista
                </a>
            </div>

            <div class="flex items-center gap-2">
                @if(auth()->user()->isAdmin())
                    <a href="{{ route('items.edit', $item) }}" class="px-3 py-1.5 rounded-lg text-xs font-semibold bg-zinc-800 hover:bg-zinc-700 text-zinc-300 transition-all">
                        Edit Data Bahan
                    </a>
                @endif
                <a href="{{ route('items.index') }}" class="px-3 py-1.5 rounded-lg text-xs font-semibold bg-zinc-800 hover:bg-zinc-700 text-zinc-400 hover:text-white transition-all">
                    &larr; Kembali ke Katalog
                </a>
            </div>
        </div>
    </div>

    <!-- Stock Card Ledger (Kartu Stok) -->
    <div class="bg-zinc-900/70 border border-zinc-800 rounded-2xl p-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-6">
            <div>
                <h3 class="text-base font-extrabold text-white tracking-tight flex items-center gap-2">
                    <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    Buku Kartu Stok (Riwayat Mutasi)
                </h3>
                <p class="text-xs text-zinc-400">Catatan lengkap seluruh pergerakan barang, penerimaan, dan pengeluaran.</p>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-zinc-800 text-zinc-400 font-semibold uppercase tracking-wider text-[10px]">
                        <th class="pb-3 pl-2">Tanggal & Transaksi</th>
                        <th class="pb-3">Tipe Mutasi</th>
                        <th class="pb-3 text-center">Jumlah (Qty)</th>
                        <th class="pb-3 text-right">Harga Satuan</th>
                        <th class="pb-3 text-right">Subtotal</th>
                        <th class="pb-3">Keterangan / Batch</th>
                        <th class="pb-3 pr-2">Petugas</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-800/60">
                    @forelse($stockHistory as $history)
                        @php $tx = $history->transaction; @endphp
                        <tr class="hover:bg-zinc-850/50 transition-colors">
                            <td class="py-3.5 pl-2 font-mono">
                                <div class="font-bold text-zinc-200">{{ $tx->transaction_code }}</div>
                                <div class="text-[11px] text-zinc-500 font-sans">{{ $tx->date->translatedFormat('d M Y') }}</div>
                            </td>

                            <td class="py-3.5">
                                @if($tx->type === 'in')
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                                        &darr; Stok Masuk
                                    </span>
                                @elseif($tx->type === 'out')
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-bold bg-amber-500/10 text-amber-400 border border-amber-500/20">
                                        &uarr; Stok Keluar
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-bold bg-cyan-500/10 text-cyan-400 border border-cyan-500/20">
                                        &harr; Opname Fisik
                                    </span>
                                @endif
                                <div class="text-[11px] text-zinc-400 mt-0.5">{{ $tx->destination_or_reason }}</div>
                            </td>

                            <td class="py-3.5 text-center font-bold text-sm">
                                @if($tx->type === 'in')
                                    <span class="text-emerald-400">+{{ number_format($history->quantity, 1, ',', '.') }} {{ $item->unit }}</span>
                                @elseif($tx->type === 'out')
                                    <span class="text-amber-400">-{{ number_format($history->quantity, 1, ',', '.') }} {{ $item->unit }}</span>
                                @else
                                    <span class="text-cyan-400">{{ $history->quantity >= 0 ? '+' : '' }}{{ number_format($history->quantity, 1, ',', '.') }} {{ $item->unit }}</span>
                                @endif
                            </td>

                            <td class="py-3.5 text-right font-medium text-zinc-300">
                                Rp {{ number_format($history->unit_cost, 0, ',', '.') }}
                            </td>

                            <td class="py-3.5 text-right font-medium text-zinc-200">
                                Rp {{ number_format($history->subtotal, 0, ',', '.') }}
                            </td>

                            <td class="py-3.5 text-zinc-300">
                                {{ $history->notes ?: ($tx->reference_number ? 'Ref: ' . $tx->reference_number : '-') }}
                                @if($history->expiry_date)
                                    <div class="text-[10px] text-amber-400/90 font-medium mt-0.5">
                                        Exp: {{ \Carbon\Carbon::parse($history->expiry_date)->translatedFormat('d M Y') }}
                                    </div>
                                @endif
                            </td>

                            <td class="py-3.5 pr-2">
                                <div class="text-zinc-300 font-medium">{{ $tx->user->name ?? '-' }}</div>
                                <div class="text-[10px] text-zinc-500">{{ $tx->user->role ?? '' }}</div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-zinc-500">
                                Belum ada riwayat mutasi stok untuk bahan ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($stockHistory->hasPages())
            <div class="mt-4 pt-4 border-t border-zinc-800">
                {{ $stockHistory->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
