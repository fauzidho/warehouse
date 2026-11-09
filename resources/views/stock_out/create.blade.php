@extends('layouts.app')

@section('title', 'Catat Pengeluaran Bahan')
@section('page-title', 'Form Pengeluaran Stok (Pemakaian Bar/Kitchen)')
@section('page-subtitle', 'Pencatatan pengambilan bahan baku untuk operasional kedai atau bahan rusak')

@section('content')
<div class="max-w-5xl mx-auto space-y-5">
    <!-- Helper Guidance for Staff -->
    <div class="p-4 rounded-xl bg-amber-950/20 border border-amber-500/20 flex items-start gap-3">
        <div class="w-7 h-7 rounded-lg bg-amber-500/10 text-amber-400 flex items-center justify-center shrink-0 mt-0.5">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <div class="text-xs text-zinc-300">
            <span class="font-bold text-white">Petunjuk Pengeluaran Bahan:</span> Pilih tujuan pemakaian (Station Bar Espresso, Kitchen Makanan, atau Waste/Spillage). Sistem akan memvalidasi agar jumlah yang dikeluarkan tidak melebihi sisa stok di gudang.
        </div>
    </div>

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
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-4">
                <h3 class="text-sm font-bold text-white flex items-center gap-2">
                    <span class="w-6 h-6 rounded-lg bg-amber-500/20 text-amber-400 flex items-center justify-center text-xs">2</span>
                    Daftar Bahan Yang Dikeluarkan
                </h3>

                <div class="flex items-center gap-2">
                    <!-- Tombol Konversi Excel Pengeluaran -->
                    <input type="file" id="stockOutExcelInput" accept=".xlsx, .xls, .csv" class="hidden" onchange="handleStockOutExcel(this.files)">
                    <button type="button" onclick="document.getElementById('stockOutExcelInput').click()"
                        class="px-3 py-1.5 rounded-lg bg-zinc-850 hover:bg-zinc-800 text-amber-300 border border-amber-500/30 hover:border-amber-500/60 text-xs font-semibold flex items-center gap-1.5 transition-all shadow-sm"
                        title="Unggah file Excel daftar pemakaian bar untuk otomatis mengisi tabel">
                        <svg class="w-3.5 h-3.5 text-amber-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Konversi dari Excel Pemakaian
                    </button>

                    <!-- Tombol Tambah Baris Manual -->
                    <button type="button" onclick="addRow()"
                        class="px-3 py-1.5 rounded-lg bg-amber-500/10 hover:bg-amber-500/20 text-amber-400 border border-amber-500/20 text-xs font-bold flex items-center gap-1.5 transition-all">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                        Tambah Baris
                    </button>
                </div>
            </div>

            <!-- Banner Notifikasi Konversi Excel -->
            <div id="excelConvertNoticeOut" style="display:none;" class="mb-4 p-3 rounded-xl bg-amber-950/40 border border-amber-500/30 flex items-center justify-between text-xs text-amber-300">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-amber-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    <span id="excelConvertMessageOut">Berhasil mengonversi baris Excel ke dalam tabel pengeluaran!</span>
                </div>
                <button type="button" onclick="this.parentElement.style.display='none'" class="text-amber-400 hover:text-amber-200 text-base font-bold">&times;</button>
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
                                            {{ $it->name }} - Tersedia: {{ $it->current_stock }} {{ $it->unit }}
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
                    ${itemsData.map(it => `<option value="${it.id}">${it.name} - Tersedia: ${it.current_stock} ${it.unit}</option>`).join('')}
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

    // ══════════════════════════════════════════════════
    // KONVERSI EXCEL PEMAKAIAN KE TABEL STOK KELUAR
    // ══════════════════════════════════════════════════
    function handleStockOutExcel(files) {
        if (!files || files.length === 0) return;
        const file = files[0];
        const reader = new FileReader();
        const isCsv = file.name.endsWith('.csv') || file.name.endsWith('.txt');

        if (window.XLSX && !isCsv) {
            reader.onload = function(e) {
                try {
                    const data = new Uint8Array(e.target.result);
                    const workbook = XLSX.read(data, { type: 'array' });
                    const sheet = workbook.Sheets[workbook.SheetNames[0]];
                    const jsonRows = XLSX.utils.sheet_to_json(sheet, { header: 1 });
                    populateStockOutTable(jsonRows, file.name);
                } catch (err) {
                    console.error('StockOut Excel parse error:', err);
                    alert('Gagal membaca file Excel pemakaian.');
                }
            };
            reader.readAsArrayBuffer(file);
        } else {
            reader.onload = function(e) {
                try {
                    const lines = e.target.result.split(/\r?\n/).filter(l => l.trim().length > 0);
                    const rows = lines.map(line => line.split(',').map(c => c.trim().replace(/^"|"$/g, '')));
                    populateStockOutTable(rows, file.name);
                } catch (err) {
                    console.error('StockOut CSV parse error:', err);
                    alert('Gagal membaca file CSV.');
                }
            };
            reader.readAsText(file);
        }
    }

    function populateStockOutTable(rawRows, fileName) {
        if (!rawRows || rawRows.length < 2) {
            alert('File Excel tidak memiliki baris data.');
            return;
        }

        const headers = rawRows[0].map(h => String(h || '').trim().toLowerCase());
        let nameIdx = -1, qtyIdx = -1, notesIdx = -1;

        headers.forEach((h, idx) => {
            if (h.includes('nama') || h.includes('item') || h.includes('bahan') || h.includes('barang')) nameIdx = idx;
            else if (h.includes('qty') || h.includes('jumlah') || h.includes('pakai') || h.includes('keluar')) qtyIdx = idx;
            else if (h.includes('ket') || h.includes('catatan') || h.includes('note') || h.includes('alasan')) notesIdx = idx;
        });

        if (nameIdx === -1) nameIdx = 0;
        if (qtyIdx === -1) qtyIdx = 1;
        if (notesIdx === -1) notesIdx = 2;

        let addedCount = 0;
        const tbody = document.getElementById('itemsBody');

        const firstRow = tbody.querySelector('.item-row');
        const firstSelect = firstRow ? firstRow.querySelector('.item-select') : null;
        if (firstSelect && (!firstSelect.value || firstSelect.value === "")) {
            tbody.innerHTML = '';
        }

        for (let i = 1; i < rawRows.length; i++) {
            const row = rawRows[i];
            if (!row || row.length === 0) continue;

            const nameRaw = String(row[nameIdx] || '').trim();
            if (!nameRaw) continue;

            const qty = parseFloat(String(row[qtyIdx] || '1').replace(/[^0-9.-]/g, '')) || 1;
            const notes = String(row[notesIdx] || '').trim();

            const matchedItem = itemsData.find(it => {
                const n1 = it.name.toLowerCase();
                const n2 = nameRaw.toLowerCase();
                return n1 === n2 || n1.includes(n2) || n2.includes(n1);
            });

            const tr = document.createElement('tr');
            tr.className = 'item-row';
            
            const selectedItemId = matchedItem ? matchedItem.id : (itemsData[0] ? itemsData[0].id : '');
            const unit = matchedItem ? matchedItem.unit : 'unit';

            tr.innerHTML = `
                <td class="py-3 pr-2">
                    <select name="items[${rowIndex}][item_id]" onchange="onItemChange(this, ${rowIndex})" required
                        class="item-select w-full bg-zinc-950 border border-zinc-700/80 rounded-xl px-3 py-2 text-xs text-zinc-100 focus:outline-none focus:border-amber-500">
                        <option value="">-- Pilih Bahan Baku --</option>
                        ${itemsData.map(it => `<option value="${it.id}" ${it.id == selectedItemId ? 'selected' : ''}>${it.name} - Tersedia: ${it.current_stock} ${it.unit}</option>`).join('')}
                    </select>
                    <div class="item-stock-warning text-[11px] text-zinc-400 font-medium mt-1">${matchedItem ? `Sisa setelah pemakaian: ${(matchedItem.current_stock - qty).toFixed(1)} ${matchedItem.unit}` : ''}</div>
                </td>
                <td class="py-3 px-2">
                    <div class="flex items-center gap-1">
                        <input type="number" step="0.01" min="0.01" name="items[${rowIndex}][quantity]" value="${qty}" oninput="checkStock(this, ${rowIndex})" required
                            class="item-qty w-full bg-zinc-950 border border-zinc-700/80 rounded-xl px-3 py-2 text-xs text-zinc-100 focus:outline-none focus:border-amber-500">
                        <span class="item-unit text-[10px] text-zinc-400 font-medium">${unit}</span>
                    </div>
                </td>
                <td class="py-3 px-2">
                    <input type="text" name="items[${rowIndex}][notes]" value="${notes}" placeholder="Catatan station, nomor grinder..."
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
            addedCount++;
        }

        const notice = document.getElementById('excelConvertNoticeOut');
        const msg = document.getElementById('excelConvertMessageOut');
        msg.textContent = `Berhasil mengonversi ${addedCount} baris pemakaian dari file "${fileName}" langsung ke tabel!`;
        notice.style.display = 'flex';
        document.getElementById('stockOutExcelInput').value = '';
    }
</script>
@endsection
