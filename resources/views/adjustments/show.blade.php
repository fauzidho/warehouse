@extends('layouts.app')

@section('title', 'Berita Acara Opname - ' . $adjustment->transaction_code)
@section('page-title', 'Berita Acara Stock Opname')
@section('page-subtitle', $adjustment->transaction_code)

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="no-print flex items-center justify-between">
        <a href="{{ route('adjustments.index') }}" class="text-xs text-zinc-400 hover:text-white flex items-center gap-1 font-semibold">
            &larr; Kembali ke Riwayat Opname
        </a>
        <div class="flex items-center gap-3">
            <button onclick="window.print()" class="px-4 py-2 rounded-xl bg-zinc-800 hover:bg-zinc-700 text-zinc-200 text-xs font-bold flex items-center gap-2 border border-zinc-700 transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                Cetak Berita Acara
            </button>
            <a href="{{ route('adjustments.create') }}" class="px-4 py-2 rounded-xl bg-cyan-600 hover:bg-cyan-500 text-zinc-950 text-xs font-bold transition-all">
                + Opname Baru
            </a>
        </div>
    </div>

    <div class="bg-zinc-900 border border-zinc-800 rounded-2xl p-8 sm:p-10 shadow-2xl relative">
        <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-6 pb-6 border-b border-zinc-800">
            <div>
                <div class="flex items-center gap-3 mb-2">
                    <div class="w-10 h-10 rounded-xl bg-cyan-500 flex items-center justify-center font-black text-zinc-950 text-base">
                        HK
                    </div>
                    <div>
                        <div class="font-extrabold text-lg text-white">HALF KOPI</div>
                        <div class="text-[11px] text-zinc-400">Gudang & Central Supply</div>
                    </div>
                </div>
                <div class="text-xs text-zinc-400 mt-2">
                    <p>Jl. Kaliurang KM 5.5, Yogyakarta</p>
                    <p>supply@halfkopi.com &bull; 0812-3456-7890</p>
                </div>
            </div>

            <div class="text-left sm:text-right space-y-1">
                <span class="inline-flex items-center px-3 py-1 rounded-lg text-xs font-black bg-cyan-500/10 text-cyan-400 border border-cyan-500/30">
                    BERITA ACARA REKONSILIASI STOK OPNAME
                </span>
                <div class="text-xl font-mono font-bold text-white mt-1">{{ $adjustment->transaction_code }}</div>
                <div class="text-xs text-zinc-400">Tanggal: <strong class="text-zinc-200">{{ $adjustment->date->translatedFormat('d F Y') }}</strong></div>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 py-6 border-b border-zinc-800 text-xs">
            <div>
                <div class="text-[11px] font-semibold text-zinc-500 uppercase tracking-wider mb-1">Keterangan Dokumen:</div>
                <div class="font-bold text-white text-sm">{{ $adjustment->destination_or_reason }}</div>
                @if($adjustment->notes)
                    <div class="text-zinc-400 mt-1 italic">{{ $adjustment->notes }}</div>
                @endif
            </div>

            <div class="sm:text-right space-y-1">
                <div>
                    <span class="text-zinc-500">No Dokumen / BA:</span>
                    <span class="text-zinc-200 font-mono font-semibold">{{ $adjustment->reference_number ?: '-' }}</span>
                </div>
                <div>
                    <span class="text-zinc-500">Auditor / Petugas:</span>
                    <span class="text-cyan-400 font-semibold">{{ $adjustment->user->name }} ({{ $adjustment->user->role }})</span>
                </div>
            </div>
        </div>

        <div class="py-6">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-zinc-800 text-zinc-400 font-semibold uppercase tracking-wider text-[10px]">
                        <th class="pb-3 w-10">No</th>
                        <th class="pb-3">Deskripsi Bahan Baku</th>
                        <th class="pb-3 text-center">Selisih (+ / -)</th>
                        <th class="pb-3">Catatan Audit</th>
                        <th class="pb-3 text-right">Dampak Finansial</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-800/60">
                    @foreach($adjustment->items as $idx => $it)
                        <tr>
                            <td class="py-3 text-zinc-500 font-mono">{{ $idx + 1 }}</td>
                            <td class="py-3">
                                <div class="font-bold text-white">{{ $it->item->name }}</div>
                                <div class="text-[11px] text-zinc-500 font-mono">SKU: {{ $it->item->sku }} &bull; {{ $it->item->category->name }}</div>
                            </td>
                            <td class="py-3 text-center font-mono">
                                <span class="px-2 py-0.5 rounded text-xs font-bold {{ $it->quantity >= 0 ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/30' : 'bg-rose-500/10 text-rose-400 border border-rose-500/30' }}">
                                    {{ $it->quantity >= 0 ? '+' : '' }}{{ number_format($it->quantity, 1, ',', '.') }} {{ $it->item->unit }}
                                </span>
                            </td>
                            <td class="py-3 text-zinc-300">
                                {{ $it->notes ?: '-' }}
                            </td>
                            <td class="py-3 text-right font-bold text-white">
                                Rp {{ number_format($it->subtotal, 0, ',', '.') }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="grid grid-cols-2 gap-8 pt-12 text-center text-xs text-zinc-400">
            <div>
                <p>Auditor / Tim Pelaksana Opname,</p>
                <div class="h-16"></div>
                <p class="font-bold text-zinc-200">( {{ $adjustment->user->name }} )</p>
                <p class="text-[10px] text-zinc-500">{{ $adjustment->user->role }}</p>
            </div>
            <div>
                <p>Mengetahui & Menyetujui,</p>
                <div class="h-16"></div>
                <p class="font-bold text-zinc-200">( ______________________ )</p>
                <p class="text-[10px] text-zinc-500">Owner / Manager Half Kopi</p>
            </div>
        </div>
    </div>
</div>
@endsection
