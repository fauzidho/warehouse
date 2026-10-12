@extends('layouts.app')

@section('title', 'Bukti Stok Masuk - ' . $stockIn->transaction_code)
@section('page-title', 'Tanda Terima Stok Masuk')
@section('page-subtitle', $stockIn->transaction_code)

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Action Bar -->
    <div class="no-print flex items-center justify-between">
        <a href="{{ route('stock-in.index') }}" class="text-xs text-zinc-400 hover:text-white flex items-center gap-1 font-semibold">
            &larr; Kembali ke Riwayat Penerimaan
        </a>
        <div class="flex items-center gap-3">
            <button onclick="window.print()" class="px-4 py-2 rounded-xl bg-zinc-800 hover:bg-zinc-700 text-zinc-200 text-xs font-bold flex items-center gap-2 border border-zinc-700 transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                Cetak Bukti Penerimaan
            </button>
            <a href="{{ route('stock-in.create') }}" class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold transition-all">
                + Input Masuk Baru
            </a>
        </div>
    </div>

    <!-- Voucher Paper Card -->
    <div class="bg-zinc-900 border border-zinc-800 rounded-2xl p-8 sm:p-10 shadow-2xl relative">
        <!-- Receipt Header -->
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
                <span class="inline-flex items-center px-3 py-1 rounded-lg text-xs font-black bg-emerald-500/10 text-emerald-400 border border-emerald-500/30">
                    BUKTI PENERIMAAN BARANG
                </span>
                <div class="text-xl font-mono font-bold text-white mt-1">{{ $stockIn->transaction_code }}</div>
                <div class="text-xs text-zinc-400">Tanggal: <strong class="text-zinc-200">{{ $stockIn->date->translatedFormat('d F Y') }}</strong></div>
            </div>
        </div>

        <!-- Supplier & Transaction Meta -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 py-6 border-b border-zinc-800 text-xs">
            <div>
                <div class="text-[11px] font-semibold text-zinc-500 uppercase tracking-wider mb-1">Diterima Dari (Supplier):</div>
                <div class="font-bold text-white text-sm">{{ $stockIn->supplier->name ?? 'Pembelian Mandiri / Non-Vendor' }}</div>
                @if($stockIn->supplier)
                    <div class="text-zinc-400 mt-0.5">{{ $stockIn->supplier->address }}</div>
                    <div class="text-zinc-400">Kontak: {{ $stockIn->supplier->contact_person }} ({{ $stockIn->supplier->phone }})</div>
                @endif
            </div>

            <div class="sm:text-right space-y-1">
                <div>
                    <span class="text-zinc-500">No Faktur / Ref:</span>
                    <span class="text-zinc-200 font-mono font-semibold">{{ $stockIn->reference_number ?: '-' }}</span>
                </div>
                <div>
                    <span class="text-zinc-500">Keperluan:</span>
                    <span class="text-zinc-200 font-medium">{{ $stockIn->destination_or_reason }}</span>
                </div>
                <div>
                    <span class="text-zinc-500">Penerima Gudang:</span>
                    <span class="text-emerald-400 font-semibold">{{ $stockIn->user->name }} ({{ $stockIn->user->role }})</span>
                </div>
            </div>
        </div>

        <!-- Items Table -->
        <div class="py-6">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-zinc-800 text-zinc-400 font-semibold uppercase tracking-wider text-[10px]">
                        <th class="pb-3 w-10">No</th>
                        <th class="pb-3">Deskripsi Bahan Baku</th>
                        <th class="pb-3 text-center">Batch / Exp Date</th>
                        <th class="pb-3 text-right">Jumlah (Qty)</th>
                        <th class="pb-3 text-right">Harga Satuan</th>
                        <th class="pb-3 text-right">Subtotal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-800/60">
                    @foreach($stockIn->items as $idx => $it)
                        <tr>
                            <td class="py-3 text-zinc-500 font-mono">{{ $idx + 1 }}</td>
                            <td class="py-3">
                                <div class="font-bold text-white">{{ $it->item->name }}</div>
                                <div class="text-[11px] text-zinc-500 font-mono">SKU: {{ $it->item->sku }} &bull; Kategori: {{ $it->item->category->name }}</div>
                            </td>
                            <td class="py-3 text-center font-mono">
                                @if($it->expiry_date)
                                    <span class="px-2 py-0.5 rounded bg-amber-500/10 text-amber-400 border border-amber-500/20 text-[11px]">
                                        {{ \Carbon\Carbon::parse($it->expiry_date)->translatedFormat('d M Y') }}
                                    </span>
                                @else
                                    <span class="text-zinc-600">-</span>
                                @endif
                            </td>
                            <td class="py-3 text-right font-bold text-white">
                                {{ number_format($it->quantity, 1, ',', '.') }} {{ $it->item->unit }}
                            </td>
                            <td class="py-3 text-right text-zinc-300">
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
                        <td colspan="5" class="pt-4 text-right font-bold text-zinc-300 text-sm">TOTAL PENERIMAAN:</td>
                        <td class="pt-4 text-right font-black text-emerald-400 text-lg">
                            Rp {{ number_format($stockIn->total_cost, 0, ',', '.') }}
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>

        @if($stockIn->notes)
            <div class="mt-4 p-3.5 rounded-xl bg-zinc-950 border border-zinc-800 text-xs text-zinc-400">
                <span class="font-semibold text-zinc-300">Catatan Penerimaan:</span> {{ $stockIn->notes }}
            </div>
        @endif

        <!-- Signatures for Goods Receipt -->
        <div class="grid grid-cols-2 gap-8 pt-12 text-center text-xs text-zinc-400">
            <div>
                <p>Pengirim / Driver Vendor,</p>
                <div class="h-16"></div>
                <p class="font-bold text-zinc-200">( ______________________ )</p>
                <p class="text-[10px] text-zinc-500">{{ $stockIn->supplier->name ?? 'Vendor' }}</p>
            </div>
            <div>
                <p>Diterima Petugas Gudang Half Kopi,</p>
                <div class="h-16"></div>
                <p class="font-bold text-zinc-200">( {{ $stockIn->user->name }} )</p>
                <p class="text-[10px] text-zinc-500">{{ $stockIn->user->role }}</p>
            </div>
        </div>
    </div>
</div>
@endsection
