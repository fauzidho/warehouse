@extends('layouts.app')

@section('title', 'Catat Stok Masuk')
@section('page-title', 'Form Penerimaan Stok Masuk')
@section('page-subtitle', 'Pencatatan pasokan bahan baku yang masuk ke gudang')

@section('content')
<div class="max-w-5xl mx-auto">
    <form action="{{ route('stock-in.store') }}" method="POST" id="stockInForm" class="space-y-6">
        @csrf

        <!-- Informasi Utama Penerimaan -->
        <div class="bg-zinc-900/80 border border-zinc-800 rounded-2xl p-6">
            <h3 class="text-sm font-bold text-white mb-4 flex items-center gap-2">
                <span class="w-6 h-6 rounded-lg bg-emerald-500/20 text-emerald-400 flex items-center justify-center text-xs">1</span>
                Informasi Pengiriman & Supplier
            </h3>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div>
                    <label for="date" class="block text-xs font-semibold text-zinc-300 mb-1.5">Tanggal Penerimaan <span class="text-rose-400">*</span></label>
                    <input type="date" name="date" id="date" value="{{ old('date', $today) }}" required
                        class="w-full bg-zinc-950 border border-zinc-700/80 rounded-xl px-3 py-2 text-xs text-zinc-100 focus:outline-none focus:border-emerald-500">
                </div>

                <div>
                    <label for="supplier_id" class="block text-xs font-semibold text-zinc-300 mb-1.5">Pilih Supplier / Vendor</label>
                    <select name="supplier_id" id="supplier_id"
                        class="w-full bg-zinc-950 border border-zinc-700/80 rounded-xl px-3 py-2 text-xs text-zinc-100 focus:outline-none focus:border-emerald-500">
                        <option value="">-- Pembelian Tanpa Supplier Khusus --</option>
                        @foreach($suppliers as $sup)
                            <option value="{{ $sup->id }}" {{ old('supplier_id') == $sup->id ? 'selected' : '' }}>
                                {{ $sup->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="reference_number" class="block text-xs font-semibold text-zinc-300 mb-1.5">No Faktur / Surat Jalan</label>
                    <input type="text" name="reference_number" id="reference_number" value="{{ old('reference_number') }}" placeholder="Cth: SJ-2026/8812, INV-099"
                        class="w-full bg-zinc-950 border border-zinc-700/80 rounded-xl px-3 py-2 text-xs text-zinc-100 placeholder-zinc-500 focus:outline-none focus:border-emerald-500">
                </div>

                <div>
                    <label for="destination_or_reason" class="block text-xs font-semibold text-zinc-300 mb-1.5">Tujuan / Jenis Pengadaan <span class="text-rose-400">*</span></label>
                    <input type="text" name="destination_or_reason" id="destination_or_reason" value="{{ old('destination_or_reason', 'Pembelian Rutin Bahan Baku') }}" required
                        class="w-full bg-zinc-950 border border-zinc-700/80 rounded-xl px-3 py-2 text-xs text-zinc-100 focus:outline-none focus:border-emerald-500">
                </div>

                <div class="sm:col-span-2 lg:col-span-4">
                    <label for="notes" class="block text-xs font-semibold text-zinc-300 mb-1.5">Catatan Tambahan Penerimaan</label>
                    <input type="text" name="notes" id="notes" value="{{ old('notes') }}" placeholder="Kondisi pengiriman, suhu pengiriman cold storage, no batch supplier..."
                        class="w-full bg-zinc-950 border border-zinc-700/80 rounded-xl px-3 py-2 text-xs text-zinc-100 placeholder-zinc-500 focus:outline-none focus:border-emerald-500">
                </div>
            </div>
        </div>

        <!-- Daftar Barang Masuk (Multi Items) -->
        <div class="bg-zinc-900/80 border border-zinc-800 rounded-2xl p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-bold text-white flex items-center gap-2">
                    <span class="w-6 h-6 rounded-lg bg-emerald-500/20 text-emerald-400 flex items-center justify-center text-xs">2</span>
                    Daftar Bahan Yang Diterima
                </h3>
                <button type="button" onclick="addRow()"
                    class="px-3 py-1.5 rounded-lg bg-emerald-500/10 hover:bg-emerald-500/20 text-emerald-400 border border-emerald-500/20 text-xs font-bold flex items-center gap-1.5 transition-all">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                    Tambah Baris Bahan
                </button>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs" id="itemsTable">
                    <thead>
                        <tr class="border-b border-zinc-800 text-zinc-400 font-semibold uppercase tracking-wider text-[10px]">
                            <th class="pb-3 w-4/12">Pilih Bahan Baku</th>
                            <th class="pb-3 w-2/12">Jumlah (Qty)</th>
                            <th class="pb-3 w-2/12">Harga Beli (Rp)</th>
                            <th class="pb-3 w-2/12">Tanggal Kadaluarsa</th>
                            <th class="pb-3 text-right w-2/12">Subtotal (Rp)</th>
                            <th class="pb-3 text-center w-12">Hapus</th>
                        </tr>
                    </thead>
                    <tbody id="itemsBody" class="divide-y divide-zinc-800/60">
                        <!-- Default Row 0 -->
                        <tr class="item-row">
                            <td class="py-3 pr-2">
                                <select name="items[0][item_id]" onchange="onItemChange(this, 0)" required
                                    class="item-select w-full bg-zinc-950 border border-zinc-700/80 rounded-xl px-3 py-2 text-xs text-zinc-100 focus:outline-none focus:border-emerald-500">
                                    <option value="">-- Pilih Bahan Baku --</option>
                                    @foreach($items as $it)
                                        <option value="{{ $it->id }}" data-unit="{{ $it->unit }}" data-cost="{{ $it->cost_price }}">
                                            {{ $it->name }} - {{ $it->unit }}
                                        </option>
                                    @endforeach
                                </select>
                                <div class="item-info text-[10px] text-zinc-500 mt-1"></div>
                            </td>
                            <td class="py-3 px-2">
                                <div class="flex items-center gap-1">
                                    <input type="number" step="0.01" min="0.01" name="items[0][quantity]" value="1" oninput="calculateSubtotal(0)" required
                                        class="item-qty w-full bg-zinc-950 border border-zinc-700/80 rounded-xl px-3 py-2 text-xs text-zinc-100 focus:outline-none focus:border-emerald-500">
                                    <span class="item-unit text-[10px] text-zinc-400 font-medium">unit</span>
                                </div>
                            </td>
                            <td class="py-3 px-2">
                                <input type="number" step="100" min="0" name="items[0][unit_cost]" value="0" oninput="calculateSubtotal(0)" required
                                    class="item-cost w-full bg-zinc-950 border border-zinc-700/80 rounded-xl px-3 py-2 text-xs text-zinc-100 focus:outline-none focus:border-emerald-500">
                            </td>
                            <td class="py-3 px-2">
                                <input type="date" name="items[0][expiry_date]"
                                    class="w-full bg-zinc-950 border border-zinc-700/80 rounded-xl px-2.5 py-2 text-xs text-zinc-100 focus:outline-none focus:border-emerald-500">
                            </td>
                            <td class="py-3 px-2 text-right">
                                <span class="item-subtotal font-bold text-white text-xs">Rp 0</span>
                            </td>
                            <td class="py-3 pl-2 text-center">
                                <button type="button" onclick="removeRow(this)" class="p-1 text-zinc-600 hover:text-rose-400 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr class="border-t border-zinc-800 text-sm">
                            <td colspan="4" class="py-4 font-bold text-right text-zinc-300">Total Nilai Pembelian:</td>
                            <td class="py-4 text-right pr-2">
                                <span id="grandTotalText" class="font-black text-emerald-400 text-base">Rp 0</span>
                            </td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('stock-in.index') }}" class="px-5 py-2.5 rounded-xl bg-zinc-800 hover:bg-zinc-700 text-zinc-300 text-xs font-semibold transition-all">
                Batal
            </a>
            <button type="submit" class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-emerald-500 to-emerald-600 hover:from-emerald-400 hover:to-emerald-500 text-zinc-950 text-xs font-bold shadow-lg shadow-emerald-950/40 transition-all transform active:scale-95">
                Konfirmasi Simpan Stok Masuk
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

        if (item) {
            row.querySelector('.item-unit').textContent = item.unit;
            row.querySelector('.item-cost').value = item.cost_price;
            row.querySelector('.item-info').textContent = `Stok Saat Ini: ${item.current_stock} ${item.unit}`;
        } else {
            row.querySelector('.item-unit').textContent = 'unit';
            row.querySelector('.item-cost').value = 0;
            row.querySelector('.item-info').textContent = '';
        }
        calculateSubtotal(index, row);
    }

    function calculateSubtotal(index, rowElem) {
        const rows = document.querySelectorAll('.item-row');
        let grandTotal = 0;

        rows.forEach(row => {
            const qty = parseFloat(row.querySelector('.item-qty').value) || 0;
            const cost = parseFloat(row.querySelector('.item-cost').value) || 0;
            const subtotal = qty * cost;

            row.querySelector('.item-subtotal').textContent = 'Rp ' + subtotal.toLocaleString('id-ID');
            grandTotal += subtotal;
        });

        document.getElementById('grandTotalText').textContent = 'Rp ' + grandTotal.toLocaleString('id-ID');
    }

    function addRow() {
        const tbody = document.getElementById('itemsBody');
        const tr = document.createElement('tr');
        tr.className = 'item-row';
        tr.innerHTML = `
            <td class="py-3 pr-2">
                <select name="items[${rowIndex}][item_id]" onchange="onItemChange(this, ${rowIndex})" required
                    class="item-select w-full bg-zinc-950 border border-zinc-700/80 rounded-xl px-3 py-2 text-xs text-zinc-100 focus:outline-none focus:border-emerald-500">
                    <option value="">-- Pilih Bahan Baku --</option>
                    ${itemsData.map(it => `<option value="${it.id}">${it.name} - ${it.unit}</option>`).join('')}
                </select>
                <div class="item-info text-[10px] text-zinc-500 mt-1"></div>
            </td>
            <td class="py-3 px-2">
                <div class="flex items-center gap-1">
                    <input type="number" step="0.01" min="0.01" name="items[${rowIndex}][quantity]" value="1" oninput="calculateSubtotal(${rowIndex})" required
                        class="item-qty w-full bg-zinc-950 border border-zinc-700/80 rounded-xl px-3 py-2 text-xs text-zinc-100 focus:outline-none focus:border-emerald-500">
                    <span class="item-unit text-[10px] text-zinc-400 font-medium">unit</span>
                </div>
            </td>
            <td class="py-3 px-2">
                <input type="number" step="100" min="0" name="items[${rowIndex}][unit_cost]" value="0" oninput="calculateSubtotal(${rowIndex})" required
                    class="item-cost w-full bg-zinc-950 border border-zinc-700/80 rounded-xl px-3 py-2 text-xs text-zinc-100 focus:outline-none focus:border-emerald-500">
            </td>
            <td class="py-3 px-2">
                <input type="date" name="items[${rowIndex}][expiry_date]"
                    class="w-full bg-zinc-950 border border-zinc-700/80 rounded-xl px-2.5 py-2 text-xs text-zinc-100 focus:outline-none focus:border-emerald-500">
            </td>
            <td class="py-3 px-2 text-right">
                <span class="item-subtotal font-bold text-white text-xs">Rp 0</span>
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
            calculateSubtotal(0);
        } else {
            alert('Minimal harus ada 1 item bahan baku yang diterima.');
        }
    }
</script>
@endsection
