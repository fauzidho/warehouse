@extends('layouts.app')

@section('title', 'Stok Masuk')
@section('page-title', 'Stok Masuk (Penerimaan Bahan)')
@section('page-subtitle', 'Pencatatan penerimaan pasokan bahan baku dari supplier & vendor')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-extrabold text-white tracking-tight">Riwayat Penerimaan Stok Masuk</h2>
            <p class="text-xs text-zinc-400">Daftar invoice & surat jalan bahan kopi, susu, dan kemasan yang telah masuk.</p>
        </div>

        <a href="{{ route('stock-in.create') }}"
            class="px-4 py-2.5 rounded-xl bg-gradient-to-r from-emerald-500 to-emerald-600 hover:from-emerald-400 hover:to-emerald-500 text-zinc-950 font-bold text-xs flex items-center gap-2 shadow-lg shadow-emerald-950/40 transition-all self-start sm:self-auto">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
            </svg>
            Catat Stok Masuk Baru
        </a>
    </div>

    <!-- Filters -->
    <div class="bg-zinc-900/70 border border-zinc-800 rounded-2xl p-4 sm:p-5">
        <form method="GET" action="{{ route('stock-in.index') }}" class="grid grid-cols-1 sm:grid-cols-3 lg:grid-cols-4 gap-3">
            <div class="lg:col-span-2">
                <label class="block text-[11px] font-semibold text-zinc-400 mb-1">Cari No Transaksi, Faktur, atau Supplier</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cth: IN-2026..., Roastery, INV-..."
                    class="w-full bg-zinc-950 border border-zinc-700/80 rounded-xl px-4 py-2 text-xs text-zinc-100 placeholder-zinc-500 focus:outline-none focus:border-emerald-500 transition-all">
            </div>

            <div>
                <label class="block text-[11px] font-semibold text-zinc-400 mb-1">Dari Tanggal</label>
                <input type="date" name="start_date" value="{{ request('start_date') }}"
                    class="w-full bg-zinc-950 border border-zinc-700/80 rounded-xl px-3 py-2 text-xs text-zinc-100 focus:outline-none focus:border-emerald-500">
            </div>

            <div>
                <label class="block text-[11px] font-semibold text-zinc-400 mb-1">Sampai Tanggal</label>
                <div class="flex gap-2">
                    <input type="date" name="end_date" value="{{ request('end_date') }}"
                        class="w-full bg-zinc-950 border border-zinc-700/80 rounded-xl px-3 py-2 text-xs text-zinc-100 focus:outline-none focus:border-emerald-500">
                    <button type="submit" class="px-4 py-2 rounded-xl bg-zinc-800 hover:bg-zinc-700 text-zinc-200 text-xs font-semibold shrink-0">
                        Filter
                    </button>
                    @if(request()->hasAny(['search', 'start_date', 'end_date']))
                        <a href="{{ route('stock-in.index') }}" class="p-2 rounded-xl bg-zinc-800 hover:bg-zinc-700 text-zinc-400 hover:text-zinc-200 text-xs shrink-0 flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                        </a>
                    @endif
                </div>
            </div>
        </form>
    </div>

    <!-- Table -->
    <div class="bg-zinc-900/70 border border-zinc-800 rounded-2xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs" style="min-width:600px;">
                <thead>
                    <tr class="bg-zinc-900 border-b border-zinc-800 text-zinc-400 font-semibold uppercase tracking-wider text-[10px]">
                        <th class="py-3.5 px-4">No Transaksi & Tanggal</th>
                        <th class="py-3.5 px-4">Supplier / Vendor</th>
                        <th class="py-3.5 px-4">Keterangan & No Faktur</th>
                        <th class="py-3.5 px-4 text-center">Jumlah Item</th>
                        <th class="py-3.5 px-4 text-right">Total Biaya</th>
                        <th class="py-3.5 px-4">Diterima Oleh</th>
                        <th class="py-3.5 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-800/60">
                    @forelse($transactions as $tx)
                        <tr class="hover:bg-zinc-850/50 transition-colors">
                            <td class="py-4 px-4 font-mono">
                                <div class="font-bold text-emerald-400">{{ $tx->transaction_code }}</div>
                                <div class="text-[11px] text-zinc-400 font-sans">{{ $tx->date->translatedFormat('d M Y') }}</div>
                            </td>

                            <td class="py-4 px-4">
                                <div class="font-semibold text-white">{{ $tx->supplier->name ?? 'Pembelian Langsung' }}</div>
                                @if($tx->supplier && $tx->supplier->contact_person)
                                    <div class="text-[11px] text-zinc-500">CP: {{ $tx->supplier->contact_person }}</div>
                                @endif
                            </td>

                            <td class="py-4 px-4">
                                <div class="text-zinc-300 font-medium">{{ $tx->destination_or_reason }}</div>
                                @if($tx->reference_number)
                                    <div class="text-[11px] text-zinc-400 font-mono">Faktur: {{ $tx->reference_number }}</div>
                                @endif
                            </td>

                            <td class="py-4 px-4 text-center">
                                <span class="px-2 py-0.5 rounded-full bg-zinc-800 text-zinc-300 font-bold text-[11px]">
                                    {{ $tx->items->count() }} Jenis
                                </span>
                            </td>

                            <td class="py-4 px-4 text-right font-bold text-white">
                                Rp {{ number_format($tx->total_cost, 0, ',', '.') }}
                            </td>

                            <td class="py-4 px-4">
                                <div class="text-zinc-300 font-medium">{{ $tx->user->name ?? '-' }}</div>
                                <div class="text-[10px] text-zinc-500">{{ $tx->user->role ?? '' }}</div>
                            </td>

                            <td class="py-4 px-4 text-right">
                                <a href="{{ route('stock-in.show', $tx) }}"
                                    class="px-3 py-1.5 rounded-lg bg-zinc-800 hover:bg-zinc-700 text-zinc-200 hover:text-white text-xs font-semibold transition-all">
                                    Lihat Bukti
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-zinc-500">
                                Belum ada riwayat transaksi penerimaan barang.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($transactions->hasPages())
            <div class="p-4 border-t border-zinc-800">
                {{ $transactions->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

