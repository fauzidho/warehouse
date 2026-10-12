@extends('layouts.app')

@section('title', 'Form Stock Opname')
@section('page-title', 'Form Stock Opname Fisik Gudang')
@section('page-subtitle', 'Masukkan hasil hitung fisik untuk rekonsiliasi dan penyesuaian otomatis stok')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">
    <!-- Category Filter Bar -->
    <div class="bg-zinc-900/80 border border-zinc-800 rounded-2xl p-4 sm:p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="text-xs font-bold text-white">Filter Kategori Yang Ingin Di-Opname:</div>
            <div class="text-[11px] text-zinc-400">Pilih kategori tertentu untuk audit bertahap, atau opname seluruh bahan sekaligus.</div>
        </div>
        <form method="GET" action="{{ route('adjustments.create') }}" class="flex items-center gap-2">
            <select name="category_id" onchange="this.form.submit()"
                class="bg-zinc-950 border border-zinc-700/80 rounded-xl px-3 py-2 text-xs text-zinc-100 focus:outline-none focus:border-cyan-500">
                <option value="">-- Semua Kategori ({{ $items->count() }} Bahan) --</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ $categoryId == $cat->id ? 'selected' : '' }}>
                        {{ $cat->name }}
                    </option>
                @endforeach
            </select>
        </form>
    </div>

    <!-- Main Opname Form -->
    <form action="{{ route('adjustments.store') }}" method="POST" class="space-y-6">
        @csrf

        <div class="bg-zinc-900/80 border border-zinc-800 rounded-2xl p-6">
            <h3 class="text-sm font-bold text-white mb-4 flex items-center gap-2">
                <span class="w-6 h-6 rounded-lg bg-cyan-500/20 text-cyan-400 flex items-center justify-center text-xs">1</span>
                Informasi Audit Opname
            </h3>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label for="date" class="block text-xs font-semibold text-zinc-300 mb-1.5">Tanggal Audit <span class="text-rose-400">*</span></label>
                    <input type="date" name="date" id="date" value="{{ old('date', $today) }}" required
                        class="w-full bg-zinc-950 border border-zinc-700/80 rounded-xl px-3 py-2 text-xs text-zinc-100 focus:outline-none focus:border-cyan-500">
                </div>

                <div>
                    <label for="reference_number" class="block text-xs font-semibold text-zinc-300 mb-1.5">No Dokumen / BA Opname</label>
                    <input type="text" name="reference_number" id="reference_number" value="{{ old('reference_number', 'SO-' . date('Ymd')) }}"
                        class="w-full bg-zinc-950 border border-zinc-700/80 rounded-xl px-3 py-2 text-xs text-zinc-100 font-mono focus:outline-none focus:border-cyan-500">
                </div>

                <div>
                    <label for="notes" class="block text-xs font-semibold text-zinc-300 mb-1.5">Catatan Tim Audit</label>
                    <input type="text" name="notes" id="notes" placeholder="Cth: Opname bulanan tim shift malam..."
                        class="w-full bg-zinc-950 border border-zinc-700/80 rounded-xl px-3 py-2 text-xs text-zinc-100 focus:outline-none focus:border-cyan-500">
                </div>
            </div>
        </div>

        <!-- Inventory List for Physical Counting -->
        <div class="bg-zinc-900/80 border border-zinc-800 rounded-2xl p-6">
            <h3 class="text-sm font-bold text-white mb-2 flex items-center gap-2">
                <span class="w-6 h-6 rounded-lg bg-cyan-500/20 text-cyan-400 flex items-center justify-center text-xs">2</span>
                Input Hitungan Fisik Aktual Gudang
            </h3>
            <p class="text-[11px] text-zinc-400 mb-4">
                Sistem otomatis mengisi stok fisik awal sesuai sistem. Ubah angka kolom <strong>Hitung Fisik</strong> jika ada selisih.
            </p>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="border-b border-zinc-800 text-zinc-400 font-semibold uppercase tracking-wider text-[10px]">
                            <th class="pb-3 w-4/12">Bahan Baku (SKU)</th>
                            <th class="pb-3 text-center w-2/12">Stok Sistem</th>
                            <th class="pb-3 text-center w-2/12">Hitung Fisik (Aktual)</th>
                            <th class="pb-3 text-center w-2/12">Selisih (+/-)</th>
                            <th class="pb-3 w-3/12">Keterangan / Alasan Selisih</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-800/60">
                        @foreach($items as $idx => $it)
                            <tr class="opname-row hover:bg-zinc-850/50 transition-colors">
                                <td class="py-3.5 pr-2">
                                    <input type="hidden" name="adjustments[{{ $idx }}][item_id]" value="{{ $it->id }}">
                                    <div class="font-bold text-white">{{ $it->name }}</div>
                                    <div class="text-[10px] text-zinc-500 font-mono">
                                        {{ $it->sku }} &bull; {{ $it->category->name }}
                                    </div>
                                </td>

                                <td class="py-3.5 px-2 text-center font-mono">
                                    <span class="system-stock text-zinc-300 font-bold" data-val="{{ $it->current_stock }}">
                                        {{ number_format($it->current_stock, 1, ',', '.') }}
                                    </span>
                                    <span class="text-[10px] text-zinc-500">{{ $it->unit }}</span>
                                </td>

                                <td class="py-3.5 px-2 text-center">
                                    <div class="flex items-center justify-center gap-1 max-w-[140px] mx-auto">
                                        <input type="number" step="0.01" min="0"
                                            name="adjustments[{{ $idx }}][physical_stock]"
                                            value="{{ old('adjustments.' . $idx . '.physical_stock', $it->current_stock) }}"
                                            oninput="calcDiff(this, {{ $it->current_stock }})"
                                            class="physical-input w-24 bg-zinc-950 border border-zinc-700/80 rounded-xl px-2.5 py-1.5 text-center text-xs text-zinc-100 font-bold focus:outline-none focus:border-cyan-500">
                                        <span class="text-[10px] text-zinc-400">{{ $it->unit }}</span>
                                    </div>
                                </td>

                                <td class="py-3.5 px-2 text-center font-mono">
                                    <span class="diff-badge px-2 py-0.5 rounded text-[11px] font-bold bg-zinc-800 text-zinc-400">
                                        0.0
                                    </span>
                                </td>

                                <td class="py-3.5 pl-2">
                                    <input type="text" name="adjustments[{{ $idx }}][reason]"
                                        placeholder="Alasan selisih jika ada..."
                                        class="w-full bg-zinc-950 border border-zinc-700/80 rounded-xl px-3 py-1.5 text-xs text-zinc-100 placeholder-zinc-600 focus:outline-none focus:border-cyan-500">
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('adjustments.index') }}" class="px-5 py-2.5 rounded-xl bg-zinc-800 hover:bg-zinc-700 text-zinc-300 text-xs font-semibold transition-all">
                Batal
            </a>
            <button type="submit" class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-cyan-500 to-cyan-600 hover:from-cyan-400 hover:to-cyan-500 text-zinc-950 text-xs font-bold shadow-lg shadow-cyan-950/40 transition-all transform active:scale-95">
                Simpan & Sesuaikan Stok
            </button>
        </div>
    </form>
</div>

<script>
    function calcDiff(inputElem, systemStock) {
        const row = inputElem.closest('.opname-row');
        const physicalVal = parseFloat(inputElem.value) || 0;
        const diff = physicalVal - systemStock;
        const badge = row.querySelector('.diff-badge');

        if (Math.abs(diff) < 0.001) {
            badge.textContent = '0.0';
            badge.className = 'diff-badge px-2 py-0.5 rounded text-[11px] font-bold bg-zinc-800 text-zinc-400';
        } else if (diff > 0) {
            badge.textContent = '+' + diff.toFixed(1);
            badge.className = 'diff-badge px-2 py-0.5 rounded text-[11px] font-bold bg-emerald-500/20 text-emerald-400 border border-emerald-500/40';
        } else {
            badge.textContent = diff.toFixed(1);
            badge.className = 'diff-badge px-2 py-0.5 rounded text-[11px] font-bold bg-rose-500/20 text-rose-400 border border-rose-500/40';
        }
    }
</script>
@endsection
