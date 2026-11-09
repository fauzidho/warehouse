@extends('layouts.app')

@section('title', 'Stock Opname')
@section('page-title', 'Stock Opname (Penyesuaian Fisik)')
@section('page-subtitle', 'Pencocokan stok sistem gudang dengan perhitungan fisik aktual')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-extrabold text-white tracking-tight">Riwayat Stock Opname Gudang</h2>
            <p class="text-xs text-zinc-400">Audit berkala fisik biji kopi, susu, cup, dan penyesuaian selisih gramasi.</p>
        </div>

        <a href="{{ route('adjustments.create') }}"
            class="px-4 py-2.5 rounded-xl bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 text-zinc-950 font-bold text-xs flex items-center gap-2 btn-primary-glow self-start sm:self-auto">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
            </svg>
            Lakukan Opname Stok Baru
        </a>
    </div>

    <!-- Table -->
    <div class="bg-zinc-900/70 border border-zinc-800 rounded-2xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs" style="min-width:600px;">
                <thead>
                    <tr class="bg-zinc-900 border-b border-zinc-800 text-zinc-400 font-semibold uppercase tracking-wider text-[10px]">
                        <th class="py-3.5 px-4">No Berita Acara & Tanggal</th>
                        <th class="py-3.5 px-4">Keterangan / Acara</th>
                        <th class="py-3.5 px-4">Bahan Disesuaikan</th>
                        <th class="py-3.5 px-4 text-center">Jumlah Item</th>
                        <th class="py-3.5 px-4">Auditor / Petugas</th>
                        <th class="py-3.5 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-800/60">
                    @forelse($adjustments as $adj)
                        <tr class="hover:bg-zinc-850/50 transition-colors">
                            <td class="py-4 px-4 font-mono">
                                <div class="font-bold text-amber-400">{{ $adj->transaction_code }}</div>
                                <div class="text-[11px] text-zinc-400 font-sans">{{ $adj->date->translatedFormat('d M Y') }}</div>
                            </td>

                            <td class="py-4 px-4">
                                <div class="font-semibold text-white">{{ $adj->destination_or_reason }}</div>
                                @if($adj->reference_number)
                                    <div class="text-[11px] text-zinc-400 font-mono">Form: {{ $adj->reference_number }}</div>
                                @endif
                            </td>

                            <td class="py-4 px-4">
                                <div class="space-y-0.5">
                                    @foreach($adj->items->take(2) as $ai)
                                        <div class="text-zinc-300">
                                            &bull; {{ $ai->item->name ?? 'Item' }}
                                            <span class="{{ $ai->quantity >= 0 ? 'text-emerald-400' : 'text-rose-400' }} font-bold">
                                                ({{ $ai->quantity >= 0 ? '+' : '' }}{{ $ai->quantity }} {{ $ai->item->unit ?? '' }})
                                            </span>
                                        </div>
                                    @endforeach
                                    @if($adj->items->count() > 2)
                                        <div class="text-[10px] text-zinc-500">+{{ $adj->items->count() - 2 }} item lainnya</div>
                                    @endif
                                </div>
                            </td>

                            <td class="py-4 px-4 text-center">
                                <span class="px-2 py-0.5 rounded-full bg-zinc-800 text-zinc-300 font-bold text-[11px]">
                                    {{ $adj->items->count() }} Bahan
                                </span>
                            </td>

                            <td class="py-4 px-4">
                                <div class="text-zinc-300 font-medium">{{ $adj->user->name ?? '-' }}</div>
                                <div class="text-[10px] text-zinc-500">{{ $adj->user->role ?? '' }}</div>
                            </td>

                            <td class="py-4 px-4 text-right">
                                <a href="{{ route('adjustments.show', $adj) }}"
                                    class="px-3 py-1.5 rounded-lg bg-zinc-800 hover:bg-zinc-700 text-zinc-200 hover:text-white text-xs font-semibold transition-all">
                                    Lihat Hasil
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-zinc-500">
                                Belum ada riwayat stock opname tercatat.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($adjustments->hasPages())
            <div class="p-4 border-t border-zinc-800">
                {{ $adjustments->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

