@extends('layouts.app')

@section('title', 'Bukti Pengeluaran Stok - ' . $stockOut->transaction_code)
@section('page-title', 'Tanda Terima Pengeluaran / Pemakaian')
@section('page-subtitle', $stockOut->transaction_code)

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Action Bar -->
    <div class="no-print flex items-center justify-between">
        <a href="{{ route('stock-out.index') }}" class="text-xs text-zinc-400 hover:text-white flex items-center gap-1 font-semibold">
            &larr; Kembali ke Riwayat Pengeluaran
        </a>
        <div class="flex items-center gap-3">
            <button onclick="window.print()" class="px-4 py-2 rounded-xl bg-zinc-800 hover:bg-zinc-700 text-zinc-200 text-xs font-bold flex items-center gap-2 border border-zinc-700 transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                Cetak Bukti Pengeluaran
            </button>
            <a href="{{ route('stock-out.create') }}" class="px-4 py-2 rounded-xl bg-amber-600 hover:bg-amber-500 text-zinc-950 text-xs font-bold transition-all">
                + Input Pengeluaran Baru
            </a>
        </div>
    </div>

    <!-- Voucher Paper Card -->
    <div class="bg-zinc-900 border border-zinc-800 rounded-2xl p-8 sm:p-10 shadow-2xl relative">
        <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-6 pb-6 border-b border-zinc-800">
            <div>
                <div class="flex items-center gap-3 mb-2">
                    <div class="w-10 h-10 rounded-xl bg-amber-500 flex items-center justify-center font-black text-zinc-950 text-base">
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
                <span class="inline-flex items-center px-3 py-1 rounded-lg text-xs font-black bg-amber-500/10 text-amber-400 border border-amber-500/30">
                    BUKTI PENGELUARAN BAHAN
                </span>
                <div class="text-xl font-mono font-bold text-white mt-1">{{ $stockOut->transaction_code }}</div>
                <div class="text-xs text-zinc-400">Tanggal: <strong class="text-zinc-200">{{ $stockOut->date->translatedFormat('d F Y') }}</strong></div>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 py-6 border-b border-zinc-800 text-xs">
            <div>
                <div class="text-[11px] font-semibold text-zinc-500 uppercase tracking-wider mb-1">Tujuan Pengeluaran:</div>
                <div class="font-bold text-white text-sm">{{ $stockOut->destination_or_reason }}</div>
                @if($stockOut->notes)
                    <div class="text-zinc-400 mt-1 italic">{{ $stockOut->notes }}</div>
                @endif
            </div>

            <div class="sm:text-right space-y-1">
                <div>
                    <span class="text-zinc-500">No Requisisi:</span>
                    <span class="text-zinc-200 font-mono font-semibold">{{ $stockOut->reference_number ?: '-' }}</span>
                </div>
                <div>
                    <span class="text-zinc-500">Petugas / Barista:</span>
                    <span class="text-amber-400 font-semibold">{{ $stockOut->user->name }} ({{ $stockOut->user->role }})</span>
                </div>
            </div>
        </div>

        <div class="py-6">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-zinc-800 text-zinc-400 font-semibold uppercase tracking-wider text-[10px]">
                        <th class="pb-3 w-10">No</th>
                        <th class="pb-3">Deskripsi Bahan Baku</th>
                        <th class="pb-3">Catatan Khusus</th>
                        <th class="pb-3 text-right">Jumlah Keluar</th>
                        <th class="pb-3 text-right">Biaya Pokok</th>
                        <th class="pb-3 text-right">Subtotal Nilai</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-800/60">
                    @foreach($stockOut->items as $idx => $it)
                        <tr>
                            <td class="py-3 text-zinc-500 font-mono">{{ $idx + 1 }}</td>
                            <td class="py-3">
                                <div class="font-bold text-white">{{ $it->item->name }}</div>
                                <div class="text-[11px] text-zinc-500 font-mono">SKU: {{ $it->item->sku }} &bull; {{ $it->item->category->name }}</div>
                            </td>
                            <td class="py-3 text-zinc-400">
                                {{ $it->notes ?: '-' }}
                            </td>
                            <td class="py-3 text-right font-bold text-amber-400 text-sm">
                                {{ number_format($it->quantity, 1, ',', '.') }} {{ $it->item->unit }}
                            </td>
                            <td class="py-3 text-right text-zinc-400">
                                Rp {{ number_format($it->unit_cost, 0, ',', '.') }}
                            </td>
                            <td class="py-3 text-right font-bold text-white">
                                Rp {{ number_format($it->subtotal, 0, ',', '.') }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="border-t-2 border-zinc-700">
                        <td colspan="5" class="pt-4 text-right font-bold text-zinc-300 text-sm">TOTAL ESTIMASI NILAI BAHAN:</td>
                        <td class="pt-4 text-right font-black text-amber-400 text-lg">
                            Rp {{ number_format($stockOut->total_cost, 0, ',', '.') }}
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <div class="grid grid-cols-2 gap-8 pt-12 text-center text-xs text-zinc-400">
            <div>
                <p>Diajukan / Pemohon (Barista/Staff),</p>
                <div class="h-16"></div>
                <p class="font-bold text-zinc-200">( {{ $stockOut->user->name }} )</p>
                <p class="text-[10px] text-zinc-500">{{ $stockOut->user->role }}</p>
            </div>
            <div>
                <p>Disetujui Penanggung Jawab Gudang,</p>
                <div class="h-16"></div>
                <p class="font-bold text-zinc-200">( ______________________ )</p>
                <p class="text-[10px] text-zinc-500">Supervisor / Head Barista</p>
            </div>
        </div>
    </div>
</div>
@endsection
