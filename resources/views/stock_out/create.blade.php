@extends('layouts.app')

@section('title', 'Catat Pengeluaran Bahan')
@section('page-title', 'Form Pengeluaran Stok (Pemakaian Bar/Kitchen)')
@section('page-subtitle', 'Pencatatan pengambilan bahan baku untuk operasional kedai atau bahan rusak')

@section('content')
<div class="max-w-5xl mx-auto">
    <form action="{{ route('stock-out.store') }}" method="POST" id="stockOutForm" class="space-y-6">
        @csrf

        <!-- Informasi Utama Pengeluaran -->
        <div class="bg-zinc-900/80 border border-zinc-800 rounded-2xl p-6">
            <h3 class="text-sm font-bold text-white mb-4 flex items-center gap-2">
                <span class="w-6 h-6 rounded-lg bg-amber-500/20 text-amber-400 flex items-center justify-center text-xs">1</span>
                Tujuan Pengeluaran & Tanggal
            </h3>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                <div>
                    <label for="date" class="block text-xs font-semibold text-zinc-300 mb-1.5">Tanggal Pemakaian <span class="text-rose-400">*</span></label>
                    <input type="date" name="date" id="date" value="{{ old('date', $today) }}" required
                        class="w-full bg-zinc-950 border border-zinc-700/80 rounded-xl px-3 py-2 text-xs text-zinc-100 focus:outline-none focus:border-amber-500">
                </div>

                <div>
                    <label for="destination_or_reason" class="block text-xs font-semibold text-zinc-300 mb-1.5">Tujuan Pemakaian / Alasan <span class="text-rose-400">*</span></label>
                    <select name="destination_or_reason" id="destination_or_reason" required
                        class="w-full bg-zinc-950 border border-zinc-700/80 rounded-xl px-3 py-2 text-xs text-zinc-100 focus:outline-none focus:border-amber-500">
                        @foreach($destinations as $val => $label)
                            <option value="{{ $val }}" {{ old('destination_or_reason') == $val ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="reference_number" class="block text-xs font-semibold text-zinc-300 mb-1.5">No Requisisi / Shift / Bon</label>
                    <input type="text" name="reference_number" id="reference_number" value="{{ old('reference_number') }}" placeholder="Cth: REQ-BAR-PAGI-01"
                        class="w-full bg-zinc-950 border border-zinc-700/80 rounded-xl px-3 py-2 text-xs text-zinc-100 placeholder-zinc-500 focus:outline-none focus:border-amber-500">
                </div>

                <div class="sm:col-span-2 lg:col-span-3">
                    <label for="notes" class="block text-xs font-semibold text-zinc-300 mb-1.5">Keterangan Tambahan</label>
                    <input type="text" name="notes" id="notes" value="{{ old('notes') }}" placeholder="Cth: Permintaan restock chiller bawah bar, atau keterangan spillage botol pecah..."
                        class="w-full bg-zinc-950 border border-zinc-700/80 rounded-xl px-3 py-2 text-xs text-zinc-100 placeholder-zinc-500 focus:outline-none focus:border-amber-500">
                </div>
            </div>
        </div>

        <!-- Daftar Barang Keluar (Multi Items) -->
        <div class="bg-zinc-900/80 border border-zinc-800 rounded-2xl p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-bold text-white flex items-center gap-2">
                    <span class="w-6 h-6 rounded-lg bg-amber-500/20 text-amber-400 flex items-center justify-center text-xs">2</span>
                    Daftar Bahan Yang Dikeluarkan
                </h3>
                <button type="button" onclick="addRow()"
                    class="px-3 py-1.5 rounded-lg bg-amber-500/10 hover:bg-amber-500/20 text-amber-400 border border-amber-500/20 text-xs font-bold flex items-center gap-1.5 transition-all">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                    Tambah Baris Bahan
                </button>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs" id="itemsTable">
                    <thead>
                        <tr class="border-b border-zinc-800 text-zinc-400 font-semibold uppercase tracking-wider text-[10px]">
                            <th class="pb-3 w-5/12">Bahan Baku & Stok Tersedia</th>
                            <th class="pb-3 w-2/12">Jumlah Keluar (Qty)</th>
                            <th class="pb-3 w-4/12">Catatan Per Item (Opsional)</th>
                            <th class="pb-3 text-center w-12">Hapus</th>
                        </tr>
                    </thead>
                    <tbody id="itemsBody" class="divide-y divide-zinc-800/60">
                        <tr class="item-row">
                            <td class="py-3 pr-2">
                                <select name="items[0][item_id]" onchange="onItemChange(this, 0)" required
                                    class="item-select w-full bg-zinc-950 border border-zinc-700/80 rounded-xl px-3 py-2 text-xs text-zinc-100 focus:outline-none focus:border-amber-500">
                                    <option value="">-- Pilih Bahan Baku --</option>
                                    @foreach($items as $it)
                                        <option value="{{ $it->id }}" data-unit="{{ $it->unit }}" data-stock="{{ $it->current_stock }}" data-cost="{{ $it->cost_price }}">
                                            {{ $it->name }} ({{ $it->sku }}) - Tersedia: {{ $it->current_stock }} {{ $it->unit }}
                                        </option>
                                    @endforeach
                                </select>
                                <div class="item-stock-warning text-[11px] text-amber-400/90 font-medium mt-1"></div>
                            </td>
                            <td class="py-3 px-2">
                                <div class="flex items-center gap-1">
                                    <input type="number" step="0.01" min="0.01" name="items[0][quantity]" value="1" oninput="checkStock(this, 0)" required
                                        class="item-qty w-full bg-zinc-950 border border-zinc-700/80 rounded-xl px-3 py-2 text-xs text-zinc-100 focus:outline-none focus:border-amber-500">
                                    <span class="item-unit text-[10px] text-zinc-400 font-medium">unit</span>
                                </div>
                            </td>
                            <td class="py-3 px-2">
                                <input type="text" name="items[0][notes]" placeholder="Catatan station, nomor grinder..."
                                    class="w-full bg-zinc-950 border border-zinc-700/80 rounded-xl px-3 py-2 text-xs text-zinc-100 focus:outline-none focus:border-amber-500">
                            </td>
                            <td class="py-3 pl-2 text-center">
                                <button type="button" onclick="removeRow(this)" class="p-1 text-zinc-600 hover:text-rose-400 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('stock-out.index') }}" class="px-5 py-2.5 rounded-xl bg-zinc-800 hover:bg-zinc-700 text-zinc-300 text-xs font-semibold transition-all">
                Batal
            </a>
            <button type="submit" class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 text-zinc-950 text-xs font-bold shadow-lg shadow-amber-950/40 transition-all transform active:scale-95">
                Konfirmasi Simpan Pengeluaran
            </button>
        </div>
    </form>
</div>

<script>
    let rowIndex = 1;
    const itemsData = @json($items);

    function onItemChange(selectElem, index) {
        const row = selectElem.closest('.item-row');
        const selectedId = selectElem.value;
        const item = itemsData.find(i => i.id == selectedId);
        const qtyInput = row.querySelector('.item-qty');
        const warning = row.querySelector('.item-stock-warning');

        if (item) {
            row.querySelector('.item-unit').textContent = item.unit;
            qtyInput.max = item.current_stock;
            warning.textContent = `Sisa di gudang saat ini: ${item.current_stock} ${item.unit}`;
            warning.classList.remove('text-rose-400');
            warning.classList.add('text-zinc-400');
        } else {
            row.querySelector('.item-unit').textContent = 'unit';
            warning.textContent = '';
        }
        checkStock(qtyInput, index);
    }

    function checkStock(qtyElem, index) {
        const row = qtyElem.closest('.item-row');
        const selectElem = row.querySelector('.item-select');
        const selectedId = selectElem.value;
        const item = itemsData.find(i => i.id == selectedId);
        const warning = row.querySelector('.item-stock-warning');

        if (item) {
            const requestedQty = parseFloat(qtyElem.value) || 0;
            if (requestedQty > item.current_stock) {
                warning.textContent = `PERINGATAN: Stok tidak cukup! Hanya tersedia ${item.current_stock} ${item.unit}.`;
                warning.classList.remove('text-zinc-400');
                warning.classList.add('text-rose-400');
            } else {
                warning.textContent = `Sisa setelah pemakaian: ${(item.current_stock - requestedQty).toFixed(1)} ${item.unit}`;
                warning.classList.remove('text-rose-400');
                warning.classList.add('text-emerald-400');
            }
        }
    }

    function addRow() {
        const tbody = document.getElementById('itemsBody');
        const tr = document.createElement('tr');
        tr.className = 'item-row';
        tr.innerHTML = `
            <td class="py-3 pr-2">
                <select name="items[${rowIndex}][item_id]" onchange="onItemChange(this, ${rowIndex})" required
                    class="item-select w-full bg-zinc-950 border border-zinc-700/80 rounded-xl px-3 py-2 text-xs text-zinc-100 focus:outline-none focus:border-amber-500">
                    <option value="">-- Pilih Bahan Baku --</option>
                    ${itemsData.map(it => `<option value="${it.id}">${it.name} (${it.sku}) - Tersedia: ${it.current_stock} ${it.unit}</option>`).join('')}
                </select>
                <div class="item-stock-warning text-[11px] text-zinc-400 font-medium mt-1"></div>
            </td>
            <td class="py-3 px-2">
                <div class="flex items-center gap-1">
                    <input type="number" step="0.01" min="0.01" name="items[${rowIndex}][quantity]" value="1" oninput="checkStock(this, ${rowIndex})" required
                        class="item-qty w-full bg-zinc-950 border border-zinc-700/80 rounded-xl px-3 py-2 text-xs text-zinc-100 focus:outline-none focus:border-amber-500">
                    <span class="item-unit text-[10px] text-zinc-400 font-medium">unit</span>
                </div>
            </td>
            <td class="py-3 px-2">
                <input type="text" name="items[${rowIndex}][notes]" placeholder="Catatan station, nomor grinder..."
                    class="w-full bg-zinc-950 border border-zinc-700/80 rounded-xl px-3 py-2 text-xs text-zinc-100 focus:outline-none focus:border-amber-500">
            </td>
            <td class="py-3 pl-2 text-center">
                <button type="button" onclick="removeRow(this)" class="p-1 text-zinc-600 hover:text-rose-400 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                </button>
            </td>
        `;
        tbody.appendChild(tr);
        rowIndex++;
    }

    function removeRow(btn) {
        const rows = document.querySelectorAll('.item-row');
        if (rows.length > 1) {
            btn.closest('tr').remove();
        } else {
            alert('Minimal harus ada 1 item bahan baku yang dikeluarkan.');
        }
    }
</script>
@endsection
