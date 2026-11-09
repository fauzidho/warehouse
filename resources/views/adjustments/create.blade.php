@extends('layouts.app')

@section('title', 'Form Stock Opname')
@section('page-title', 'Form Stock Opname Fisik Gudang')
@section('page-subtitle', 'Masukkan hasil hitung fisik untuk rekonsiliasi dan penyesuaian otomatis stok')

@section('content')
<div class="max-w-5xl mx-auto space-y-5">
    <!-- Helper Guidance for Staff -->
    <div class="p-4 rounded-xl bg-cyan-950/20 border border-cyan-500/20 flex items-start gap-3">
        <div class="w-7 h-7 rounded-lg bg-cyan-500/10 text-cyan-400 flex items-center justify-center shrink-0 mt-0.5">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <div class="text-xs text-zinc-300">
            <span class="font-bold text-white">Petunjuk Stock Opname Fisik:</span> Hitung jumlah fisik riil di rak penyimpanan/chiller. Masukkan angka hasil hitungan ke kolom <strong class="text-white">Hitung Fisik</strong>. Sistem otomatis menghitung selisih (surplus atau defisit) dan membuat Berita Acara Rekonsiliasi resmi.
        </div>
    </div>

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
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-3">
                <div>
                    <h3 class="text-sm font-bold text-white flex items-center gap-2">
                        <span class="w-6 h-6 rounded-lg bg-cyan-500/20 text-cyan-400 flex items-center justify-center text-xs">2</span>
                        Input Hitungan Fisik Aktual Gudang
                    </h3>
                    <p class="text-[11px] text-zinc-400 mt-0.5">
                        Sistem otomatis mengisi stok fisik awal sesuai sistem. Ubah angka kolom <strong>Hitung Fisik</strong> jika ada selisih.
                    </p>
                </div>

                <div class="flex items-center gap-2">
                    <input type="file" id="opnameExcelInput" accept=".xlsx, .xls, .csv" class="hidden" onchange="handleOpnameExcel(this.files)">
                    <button type="button" onclick="document.getElementById('opnameExcelInput').click()"
                        class="px-3 py-1.5 rounded-lg bg-zinc-850 hover:bg-zinc-800 text-cyan-300 border border-cyan-500/30 hover:border-cyan-500/60 text-xs font-semibold flex items-center gap-1.5 transition-all shadow-sm"
                        title="Unggah file Excel hasil hitung audit untuk otomatis mengisi kolom fisik">
                        <svg class="w-3.5 h-3.5 text-cyan-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Konversi dari Excel Opname
                    </button>
                </div>
            </div>

            <!-- Banner Notifikasi Konversi Excel Opname -->
            <div id="excelConvertNoticeOpname" style="display:none;" class="mb-4 p-3 rounded-xl bg-cyan-950/40 border border-cyan-500/30 flex items-center justify-between text-xs text-cyan-300">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-cyan-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    <span id="excelConvertMessageOpname">Berhasil mengonversi data hitung fisik dari Excel!</span>
                </div>
                <button type="button" onclick="this.parentElement.style.display='none'" class="text-cyan-400 hover:text-cyan-200 text-base font-bold">&times;</button>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="border-b border-zinc-800 text-zinc-400 font-semibold uppercase tracking-wider text-[10px]">
                            <th class="pb-3 w-4/12">Bahan Baku</th>
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
                                    <div class="text-[10px] text-zinc-500">
                                        {{ $it->category->name }}
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
            <button type="submit" class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 text-zinc-950 text-xs font-bold btn-primary-glow active:scale-95">
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

    // ══════════════════════════════════════════════════
    // KONVERSI EXCEL HASIL AUDIT KE TABEL OPNAME
    // ══════════════════════════════════════════════════
    function handleOpnameExcel(files) {
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
                    populateOpnameFromExcel(jsonRows, file.name);
                } catch (err) {
                    console.error('Opname Excel parse error:', err);
                    alert('Gagal membaca file Excel opname.');
                }
            };
            reader.readAsArrayBuffer(file);
        } else {
            reader.onload = function(e) {
                try {
                    const lines = e.target.result.split(/\r?\n/).filter(l => l.trim().length > 0);
                    const rows = lines.map(line => line.split(',').map(c => c.trim().replace(/^"|"$/g, '')));
                    populateOpnameFromExcel(rows, file.name);
                } catch (err) {
                    console.error('Opname CSV parse error:', err);
                    alert('Gagal membaca file CSV.');
                }
            };
            reader.readAsText(file);
        }
    }

    function populateOpnameFromExcel(rawRows, fileName) {
        if (!rawRows || rawRows.length < 2) {
            alert('File Excel tidak memiliki baris data.');
            return;
        }

        const headers = rawRows[0].map(h => String(h || '').trim().toLowerCase());
        let nameIdx = -1, physicalIdx = -1, reasonIdx = -1;

        headers.forEach((h, idx) => {
            if (h.includes('nama') || h.includes('item') || h.includes('bahan') || h.includes('barang')) nameIdx = idx;
            else if (h.includes('fisik') || h.includes('hitung') || h.includes('aktual') || h.includes('real')) physicalIdx = idx;
            else if (h.includes('ket') || h.includes('alasan') || h.includes('sebab') || h.includes('note')) reasonIdx = idx;
        });

        if (nameIdx === -1) nameIdx = 0;
        if (physicalIdx === -1) physicalIdx = 1;
        if (reasonIdx === -1) reasonIdx = 2;

        let matchedCount = 0;
        const rows = document.querySelectorAll('.opname-row');

        rows.forEach(tr => {
            const nameEl = tr.querySelector('.font-bold.text-white');
            if (!nameEl) return;
            const itemName = nameEl.textContent.trim().toLowerCase();

            // Find in excel rows
            for (let i = 1; i < rawRows.length; i++) {
                const r = rawRows[i];
                if (!r || r.length === 0) continue;
                const rowName = String(r[nameIdx] || '').trim().toLowerCase();
                if (!rowName) continue;

                if (itemName === rowName || itemName.includes(rowName) || rowName.includes(itemName)) {
                    const physicalVal = parseFloat(String(r[physicalIdx] || '0').replace(/[^0-9.-]/g, '')) || 0;
                    const reasonVal = String(r[reasonIdx] || '').trim();

                    const inputFisik = tr.querySelector('.physical-input');
                    const reasonInput = tr.querySelector('input[name*="[reason]"]');
                    const systemStockEl = tr.querySelector('.system-stock');
                    const sysVal = systemStockEl ? parseFloat(systemStockEl.getAttribute('data-val')) || 0 : 0;

                    if (inputFisik) {
                        inputFisik.value = physicalVal;
                        calcDiff(inputFisik, sysVal);
                    }
                    if (reasonInput && reasonVal) {
                        reasonInput.value = reasonVal;
                    }
                    matchedCount++;
                    break;
                }
            }
        });

        const notice = document.getElementById('excelConvertNoticeOpname');
        const msg = document.getElementById('excelConvertMessageOpname');
        msg.textContent = `Berhasil mengonversi dan mencocokkan ${matchedCount} bahan dari file "${fileName}" ke tabel opname!`;
        notice.style.display = 'flex';
        document.getElementById('opnameExcelInput').value = '';
    }
</script>
@endsection
