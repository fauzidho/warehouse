@extends('layouts.app')

@section('title', 'Katalog Bahan Baku')
@section('page-title', 'Bahan Baku Gudang')
@section('page-subtitle', 'Daftar seluruh bahan baku, biji kopi, dairy, sirup, dan packaging')

@section('content')
<div class="space-y-6">
    <!-- Header with Action Button -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-extrabold text-white tracking-tight">Katalog Stok Bahan Baku</h2>
            <p class="text-xs text-zinc-400">Kelola dan pantau stok bahan espresso, kitchen, dan packaging.</p>
        </div>

        <div class="flex flex-wrap items-center gap-2 self-start sm:self-auto">
            <!-- Tombol Ekspor Excel -->
            <button type="button" onclick="exportCurrentTableToExcel()"
                class="px-3 py-2 rounded-xl bg-zinc-900 hover:bg-zinc-800 text-zinc-300 hover:text-white border border-zinc-700/70 text-xs font-semibold flex items-center gap-1.5 transition-all shadow-sm"
                title="Ekspor seluruh data bahan baku ke format Excel">
                <svg class="w-3.5 h-3.5 text-emerald-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Ekspor Excel
            </button>

            @if(auth()->user()->isAdmin())
                <!-- Tombol Konversi & Impor Excel -->
                <button type="button" onclick="openExcelImportModal()"
                    class="px-3.5 py-2 rounded-xl bg-zinc-900 hover:bg-zinc-850 text-amber-300 hover:text-amber-200 border border-amber-500/40 hover:border-amber-500/80 text-xs font-bold flex items-center gap-1.5 transition-all shadow-sm">
                    <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Konversi & Impor Excel
                </button>

                <!-- Tombol Tambah Manual -->
                <a href="{{ route('items.create') }}"
                    class="px-4 py-2 rounded-xl bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 text-zinc-950 font-bold text-xs flex items-center gap-1.5 btn-primary-glow">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                    </svg>
                    Tambah Bahan Baru
                </a>
            @endif
        </div>
    </div>

    <!-- Filters & Search Bar -->
    <div class="bg-zinc-900/70 border border-zinc-800 rounded-2xl p-4 sm:p-5">
        <form method="GET" action="{{ route('items.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
            <!-- Search Keyword -->
            <div class="lg:col-span-2">
                <label class="block text-[11px] font-semibold text-zinc-400 mb-1">Cari Nama Bahan</label>
                <div class="relative">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cth: Espresso, Susu, BN-HB..."
                        class="w-full bg-zinc-950 border border-zinc-700/80 rounded-xl px-4 py-2 text-xs text-zinc-100 placeholder-zinc-500 focus:outline-none focus:border-amber-500 transition-all">
                </div>
            </div>

            <!-- Filter Kategori -->
            <div>
                <label class="block text-[11px] font-semibold text-zinc-400 mb-1">Kategori</label>
                <select name="category" class="w-full bg-zinc-950 border border-zinc-700/80 rounded-xl px-3 py-2 text-xs text-zinc-100 focus:outline-none focus:border-amber-500">
                    <option value="">Semua Kategori</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>
                            {{ $cat->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Filter Status Stok -->
            <div>
                <label class="block text-[11px] font-semibold text-zinc-400 mb-1">Status Stok</label>
                <div class="flex gap-2">
                    <select name="status" class="w-full bg-zinc-950 border border-zinc-700/80 rounded-xl px-3 py-2 text-xs text-zinc-100 focus:outline-none focus:border-amber-500">
                        <option value="">Semua Status</option>
                        <option value="low" {{ request('status') == 'low' ? 'selected' : '' }}>Stok Menipis (&le; Min)</option>
                        <option value="out" {{ request('status') == 'out' ? 'selected' : '' }}>Stok Habis (0)</option>
                        <option value="safe" {{ request('status') == 'safe' ? 'selected' : '' }}>Stok Aman</option>
                    </select>
                    <button type="submit" class="px-4 py-2 rounded-xl bg-zinc-800 hover:bg-zinc-700 text-zinc-200 hover:text-white text-xs font-semibold shrink-0 transition-all border border-zinc-700/60">
                        Saring
                    </button>
                    @if(request()->hasAny(['search', 'category', 'status']))
                        <a href="{{ route('items.index') }}" class="p-2 rounded-xl bg-zinc-800 hover:bg-zinc-700 text-zinc-400 hover:text-zinc-200 text-xs shrink-0 flex items-center justify-center transition-all border border-zinc-700/60" title="Atur ulang filter">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                        </a>
                    @endif
                </div>
            </div>
        </form>
    </div>

    <!-- Items Grid / Table -->
    <div class="bg-zinc-900/70 border border-zinc-800 rounded-2xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs" style="min-width:700px;">
                <thead>
                    <tr class="bg-zinc-900 border-b border-zinc-800 text-zinc-400 font-semibold uppercase tracking-wider text-[10px]">
                        <th class="py-3.5 px-4" style="width:25%;">Bahan Baku</th>
                        <th class="py-3.5 px-4" style="width:17%;">Kategori</th>
                        <th class="py-3.5 px-4 text-center" style="width:12%;">Stok Saat Ini</th>
                        <th class="py-3.5 px-4 text-center" style="width:12%;">Batas Min</th>
                        <th class="py-3.5 px-4 text-center" style="width:10%;">Status</th>
                        <th class="py-3.5 px-4 text-right" style="width:13%;">Harga Beli</th>
                        <th class="py-3.5 px-4 text-right" style="width:11%;">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-800/60">
                    @forelse($items as $item)
                        @php
                            $isOut = $item->current_stock <= 0;
                            $isLow = $item->current_stock <= $item->minimum_stock && !$isOut;
                        @endphp
                        <tr class="hover:bg-zinc-850/50 transition-colors">
                            <!-- Name -->
                            <td class="py-4 px-4" style="max-width:0;">
                                <div class="font-bold text-white text-sm overflow-hidden text-ellipsis whitespace-nowrap" title="{{ $item->name }}">{{ $item->name }}</div>
                                <div class="text-[11px] text-zinc-400 font-mono flex items-center gap-2 mt-0.5">
                                    <span>Satuan: <strong class="text-zinc-300 font-sans">{{ $item->unit }}</strong></span>
                                </div>
                            </td>

                            <!-- Category -->
                            <td class="py-4 px-4" style="max-width:0;">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-[11px] font-medium bg-zinc-800 text-zinc-300 border border-zinc-700/60 overflow-hidden text-ellipsis whitespace-nowrap max-w-full">
                                    {{ $item->category->name }}
                                </span>
                            </td>

                            <!-- Current Stock -->
                            <td class="py-4 px-4 text-center">
                                <div class="font-mono font-bold text-sm text-zinc-200">
                                    {{ number_format($item->current_stock, 1, ',', '.') }}
                                </div>
                                <div class="text-[10px] text-zinc-500">{{ $item->unit }}</div>
                            </td>

                            <!-- Min Stock -->
                            <td class="py-4 px-4 text-center font-mono text-zinc-400">
                                {{ number_format($item->minimum_stock, 1, ',', '.') }} {{ $item->unit }}
                            </td>

                            <!-- Status Badge (Muted Slate with micro-dot) -->
                            <td class="py-4 px-4 text-center">
                                @if($isOut)
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-md text-[10px] font-semibold bg-zinc-800 text-zinc-300 border border-zinc-700/60">
                                        <span class="w-1.5 h-1.5 rounded-full bg-rose-400"></span>
                                        Habis
                                    </span>
                                @elseif($isLow)
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-md text-[10px] font-semibold bg-zinc-800 text-zinc-300 border border-zinc-700/60">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-400"></span>
                                        Menipis
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-md text-[10px] font-semibold bg-zinc-800 text-zinc-300 border border-zinc-700/60">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                                        Aman
                                    </span>
                                @endif
                            </td>

                            <!-- Cost Price -->
                            <td class="py-4 px-4 text-right font-medium text-zinc-300">
                                Rp {{ number_format($item->cost_price, 0, ',', '.') }}
                                <div class="text-[10px] text-zinc-500">per {{ $item->unit }}</div>
                            </td>

                            <!-- Actions -->
                            <td class="py-4 px-4 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <a href="{{ route('items.show', $item) }}"
                                        class="px-2.5 py-1.5 rounded-lg bg-zinc-800 hover:bg-zinc-700 text-zinc-300 hover:text-white text-xs font-semibold transition-all"
                                        title="Lihat Kartu Stok">
                                        Kartu Stok
                                    </a>

                                    @if(auth()->user()->isAdmin())
                                        <a href="{{ route('items.edit', $item) }}"
                                            class="p-1.5 rounded-lg bg-zinc-850 hover:bg-zinc-800 text-amber-400 hover:text-amber-300 transition-all"
                                            title="Edit Bahan">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                            </svg>
                                        </a>

                                        <form action="{{ route('items.destroy', $item) }}" method="POST" onsubmit="return confirm('Hapus bahan baku {{ $item->name }} dari sistem?');" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1.5 rounded-lg bg-zinc-850 hover:bg-rose-500/10 text-zinc-500 hover:text-rose-400 transition-all" title="Hapus Bahan">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-zinc-500">
                                Tidak ada bahan baku yang sesuai dengan kriteria pencarian.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($items->hasPages())
            <div class="p-4 border-t border-zinc-800">
                {{ $items->links() }}
            </div>
        @endif
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════════════
     MODAL KONVERTER EXCEL KE TABEL & IMPOR BAHAN BAKU
     ═══════════════════════════════════════════════════════════════ --}}
<div id="excelImportModal" class="fixed inset-0 z-50 bg-black/80 backdrop-blur-sm hidden items-center justify-center p-3 sm:p-5" style="display:none;">
    <div class="bg-zinc-900 border border-zinc-700/80 rounded-2xl w-full max-w-5xl max-h-[92vh] flex flex-col shadow-2xl overflow-hidden" onclick="event.stopPropagation()">
        
        <!-- Modal Header -->
        <div class="px-6 py-4 border-b border-zinc-800 flex items-center justify-between bg-zinc-900/90">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-amber-500/10 border border-amber-500/20 text-amber-400 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-white flex items-center gap-2">
                        Konverter Excel ke Tabel & Impor Bahan Baku
                        <span class="text-[10px] font-semibold px-2 py-0.5 rounded bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">Realtime Converter</span>
                    </h3>
                    <p class="text-[11px] text-zinc-400">File Excel (.xlsx, .xls) atau .csv langsung dikonversi ke tabel interaktif sebelum disimpan ke database.</p>
                </div>
            </div>
            <button type="button" onclick="closeExcelImportModal()" class="w-8 h-8 rounded-lg bg-zinc-800 hover:bg-zinc-700 text-zinc-400 hover:text-white flex items-center justify-center transition-all">
                &times;
            </button>
        </div>

        <!-- Modal Body (Scrollable) -->
        <div class="p-6 overflow-y-auto space-y-5 flex-1">

            <!-- STEP 1: DROPZONE FILE EXCEL -->
            <div id="dropzoneSection" class="space-y-4">
                <div id="dropArea"
                    class="border-2 border-dashed border-zinc-700 hover:border-amber-500/70 rounded-2xl p-8 text-center bg-zinc-950/50 hover:bg-zinc-950/80 transition-all cursor-pointer group">
                    <input type="file" id="excelFileInput" accept=".xlsx, .xls, .csv" class="hidden" onchange="onFileSelected(this.files)">
                    <div class="w-14 h-14 mx-auto rounded-2xl bg-zinc-850 group-hover:bg-amber-500/10 text-zinc-400 group-hover:text-amber-400 border border-zinc-700/60 group-hover:border-amber-500/30 flex items-center justify-center transition-all mb-3">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                        </svg>
                    </div>
                    <div class="text-sm font-bold text-white mb-1">
                        Tarik & Lepas File Excel ke Sini, atau <span class="text-amber-400 underline decoration-amber-400/40 group-hover:decoration-amber-400">Pilih File</span>
                    </div>
                    <p class="text-xs text-zinc-400 mb-4">Mendukung format spreadsheet <strong>.xlsx, .xls, atau .csv</strong></p>
                    
                    <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-zinc-850 border border-zinc-700/70 text-[11px] text-zinc-400">
                        <svg class="w-3.5 h-3.5 text-zinc-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Format kolom otomatis terdeteksi: <strong>Nama Bahan, Kategori, Satuan, Stok Awal, Stok Min, Harga Beli, Keterangan</strong>
                    </div>
                </div>

                <!-- Template Download Card -->
                <div class="p-3.5 rounded-xl bg-zinc-950 border border-zinc-800 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div class="flex items-center gap-2.5">
                        <div class="w-7 h-7 rounded-lg bg-emerald-500/10 text-emerald-400 flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                        </div>
                        <div>
                            <div class="text-xs font-bold text-white">Belum Punya File Format Sesuai?</div>
                            <div class="text-[10px] text-zinc-400">Gunakan template resmi kami yang sudah terisi contoh bahan kopi & sirup.</div>
                        </div>
                    </div>
                    <button type="button" onclick="downloadExcelTemplate()"
                        class="px-3 py-1.5 rounded-lg bg-zinc-800 hover:bg-zinc-700 text-emerald-400 border border-emerald-500/20 text-xs font-semibold flex items-center justify-center gap-1.5 transition-all shrink-0">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        Unduh Template Excel
                    </button>
                </div>
            </div>

            <!-- STEP 2: CONVERTED LIVE TABLE PREVIEW (Shown after parsing) -->
            <div id="previewSection" class="space-y-4" style="display:none;">
                <!-- Baris Info File & Filter -->
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 p-3.5 rounded-xl bg-zinc-950 border border-zinc-800">
                    <div class="flex items-center gap-2.5">
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-400 animate-pulse"></span>
                        <div>
                            <div class="text-xs font-bold text-white flex items-center gap-2">
                                <span id="parsedFileName">data.xlsx</span>
                                <span id="parsedRowCountBadge" class="px-2 py-0.5 rounded-md bg-emerald-500/10 text-emerald-400 text-[10px] font-mono border border-emerald-500/20">0 baris</span>
                            </div>
                            <div class="text-[10px] text-zinc-400">File berhasil dikonversi ke tabel. Anda dapat meneliti atau menghapus baris sebelum disimpan.</div>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <input type="text" id="previewSearchInput" oninput="filterPreviewTable(this.value)" placeholder="Saring baris hasil konversi..."
                            class="bg-zinc-900 border border-zinc-700/80 rounded-lg px-3 py-1.5 text-xs text-zinc-100 placeholder-zinc-500 focus:outline-none focus:border-amber-500 w-48 sm:w-60">
                        <button type="button" onclick="resetExcelDropzone()" class="px-2.5 py-1.5 rounded-lg bg-zinc-850 hover:bg-zinc-800 text-zinc-400 hover:text-white border border-zinc-700 text-xs transition-all" title="Ganti File">
                            Ganti File
                        </button>
                    </div>
                </div>

                <!-- Live Converted Table -->
                <div class="border border-zinc-800 rounded-xl overflow-hidden bg-zinc-950/60 max-h-[50vh] overflow-y-auto">
                    <table class="w-full text-left text-xs" id="convertedPreviewTable">
                        <thead class="sticky top-0 bg-zinc-900 border-b border-zinc-800 z-10 text-[10px] uppercase font-semibold text-zinc-400">
                            <tr>
                                <th class="py-2.5 px-3 w-10 text-center">#</th>
                                <th class="py-2.5 px-3">Nama Bahan</th>
                                <th class="py-2.5 px-3">Kategori</th>
                                <th class="py-2.5 px-3 text-center">Satuan</th>
                                <th class="py-2.5 px-3 text-center">Stok Awal</th>
                                <th class="py-2.5 px-3 text-center">Stok Min</th>
                                <th class="py-2.5 px-3 text-right">Harga Beli</th>
                                <th class="py-2.5 px-3">Keterangan</th>
                                <th class="py-2.5 px-3 text-center w-12">Hapus</th>
                            </tr>
                        </thead>
                        <tbody id="convertedPreviewBody" class="divide-y divide-zinc-800/60 text-zinc-300">
                            <!-- Rows injected via JS -->
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

        <!-- Modal Footer -->
        <div class="px-6 py-4 border-t border-zinc-800 bg-zinc-900/90 flex flex-col sm:flex-row items-center justify-between gap-3">
            <div class="text-[11px] text-zinc-400">
                <span id="footerInfoText">Pilih atau letakkan file Excel untuk memulai konversi.</span>
            </div>
            <div class="flex items-center gap-2.5 w-full sm:w-auto justify-end">
                <button type="button" onclick="closeExcelImportModal()"
                    class="px-4 py-2 rounded-xl bg-zinc-800 hover:bg-zinc-700 text-zinc-300 text-xs font-semibold transition-all">
                    Tutup
                </button>
                <button type="button" id="btnConfirmImport" onclick="submitConvertedData()" disabled
                    class="px-5 py-2 rounded-xl bg-zinc-700 text-zinc-500 font-bold text-xs flex items-center gap-2 cursor-not-allowed transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    <span>Simpan ke Katalog Bahan</span>
                </button>
            </div>
        </div>

    </div>
</div>

<script>
    let parsedItems = [];

    function openExcelImportModal() {
        const m = document.getElementById('excelImportModal');
        m.style.display = 'flex';
        m.classList.remove('hidden');
    }

    function closeExcelImportModal() {
        const m = document.getElementById('excelImportModal');
        m.style.display = 'none';
        m.classList.add('hidden');
    }

    // Drag and drop event listeners
    const dropArea = document.getElementById('dropArea');
    if (dropArea) {
        ['dragenter', 'dragover'].forEach(name => {
            dropArea.addEventListener(name, (e) => {
                e.preventDefault();
                e.stopPropagation();
                dropArea.classList.add('border-amber-500', 'bg-zinc-900/90');
            });
        });
        ['dragleave', 'drop'].forEach(name => {
            dropArea.addEventListener(name, (e) => {
                e.preventDefault();
                e.stopPropagation();
                dropArea.classList.remove('border-amber-500', 'bg-zinc-900/90');
            });
        });
        dropArea.addEventListener('drop', (e) => {
            const dt = e.dataTransfer;
            const files = dt.files;
            if (files && files.length > 0) {
                onFileSelected(files);
            }
        });
        dropArea.addEventListener('click', () => {
            document.getElementById('excelFileInput').click();
        });
    }

    function onFileSelected(files) {
        if (!files || files.length === 0) return;
        const file = files[0];
        document.getElementById('parsedFileName').textContent = file.name;

        const reader = new FileReader();
        const isCsv = file.name.endsWith('.csv') || file.name.endsWith('.txt');

        if (window.XLSX && !isCsv) {
            reader.onload = function(e) {
                try {
                    const data = new Uint8Array(e.target.result);
                    const workbook = XLSX.read(data, { type: 'array' });
                    const firstSheetName = workbook.SheetNames[0];
                    const worksheet = workbook.Sheets[firstSheetName];
                    const jsonRows = XLSX.utils.sheet_to_json(worksheet, { header: 1 });
                    processExcelRows(jsonRows);
                } catch (err) {
                    console.error('Error parsing XLSX:', err);
                    alert('Gagal membaca file Excel. Pastikan format file tidak rusak.');
                }
            };
            reader.readAsArrayBuffer(file);
        } else {
            // Text / CSV Fallback Parser
            reader.onload = function(e) {
                try {
                    const text = e.target.result;
                    const lines = text.split(/\r?\n/).filter(line => line.trim().length > 0);
                    const rows = lines.map(line => {
                        // Regex to split by comma while honoring quotes
                        const matches = line.match(/(".*?"|[^",\s]+)(?=\s*,|\s*$)/g);
                        if (!matches) {
                            return line.split(',').map(c => c.trim().replace(/^"|"$/g, ''));
                        }
                        return matches.map(c => c.trim().replace(/^"|"$/g, ''));
                    });
                    processExcelRows(rows);
                } catch (err) {
                    console.error('Error parsing CSV:', err);
                    alert('Gagal memproses file CSV.');
                }
            };
            reader.readAsText(file);
        }
    }

    function processExcelRows(rawRows) {
        if (!rawRows || rawRows.length < 2) {
            alert('File Excel kosong atau tidak memiliki baris data yang cukup.');
            return;
        }

        const headers = rawRows[0].map(h => String(h || '').trim().toLowerCase());
        const headerMap = {
            name: -1,
            category: -1,
            unit: -1,
            current_stock: -1,
            minimum_stock: -1,
            cost_price: -1,
            description: -1
        };

        headers.forEach((h, idx) => {
            if (h.includes('nama') || h.includes('item') || h.includes('barang') || h.includes('produk')) headerMap.name = idx;
            else if (h.includes('kategori') || h.includes('category')) headerMap.category = idx;
            else if (h.includes('satuan') || h.includes('unit') || h.includes('uom')) headerMap.unit = idx;
            else if (h.includes('stok awal') || h.includes('awal') || h.includes('saldo') || (h.includes('stok') && !h.includes('min'))) headerMap.current_stock = idx;
            else if (h.includes('min') || h.includes('batas')) headerMap.minimum_stock = idx;
            else if (h.includes('harga') || h.includes('beli') || h.includes('cost') || h.includes('price')) headerMap.cost_price = idx;
            else if (h.includes('ket') || h.includes('deskripsi') || h.includes('note')) headerMap.description = idx;
        });

        // Fallback default index positions if headers didn't match
        if (headerMap.name === -1) headerMap.name = 0;
        if (headerMap.category === -1) headerMap.category = 1;
        if (headerMap.unit === -1) headerMap.unit = 2;
        if (headerMap.current_stock === -1) headerMap.current_stock = 3;
        if (headerMap.minimum_stock === -1) headerMap.minimum_stock = 4;
        if (headerMap.cost_price === -1) headerMap.cost_price = 5;
        if (headerMap.description === -1) headerMap.description = 6;

        parsedItems = [];
        for (let i = 1; i < rawRows.length; i++) {
            const row = rawRows[i];
            if (!row || row.length === 0) continue;

            const name = String(row[headerMap.name] || '').trim();
            if (!name) continue;

            const category = String(row[headerMap.category] || 'Umum').trim() || 'Umum';
            const unit = String(row[headerMap.unit] || 'pcs').trim() || 'pcs';
            const curStock = parseFloat(String(row[headerMap.current_stock] || '0').replace(/[^0-9.-]/g, '')) || 0;
            const minStock = parseFloat(String(row[headerMap.minimum_stock] || '5').replace(/[^0-9.-]/g, '')) || 5;
            const cost = parseFloat(String(row[headerMap.cost_price] || '0').replace(/[^0-9.-]/g, '')) || 0;
            const desc = String(row[headerMap.description] || '').trim();

            parsedItems.push({
                name: name,
                category: category,
                unit: unit,
                current_stock: curStock,
                minimum_stock: minStock,
                cost_price: cost,
                description: desc
            });
        }

        if (parsedItems.length === 0) {
            alert('Tidak ditemukan baris bahan baku yang valid dalam file Excel tersebut.');
            return;
        }

        // Show Preview Section
        document.getElementById('dropzoneSection').style.display = 'none';
        document.getElementById('previewSection').style.display = 'block';
        document.getElementById('parsedRowCountBadge').textContent = `${parsedItems.length} baris terkonversi`;
        document.getElementById('footerInfoText').textContent = `${parsedItems.length} bahan baku berhasil dikonversi dan siap dimasukkan ke katalog.`;

        // Enable Submit Button
        const btn = document.getElementById('btnConfirmImport');
        btn.disabled = false;
        btn.className = "px-5 py-2 rounded-xl bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 text-zinc-950 font-bold text-xs flex items-center gap-2 shadow-lg shadow-amber-950/40 transition-all cursor-pointer";

        renderPreviewTable(parsedItems);
    }

    function renderPreviewTable(rows) {
        const tbody = document.getElementById('convertedPreviewBody');
        tbody.innerHTML = '';

        rows.forEach((item, idx) => {
            const tr = document.createElement('tr');
            tr.className = "hover:bg-zinc-850/50 transition-colors";
            tr.innerHTML = `
                <td class="py-2.5 px-3 text-center text-zinc-500 font-mono text-[11px]">${idx + 1}</td>
                <td class="py-2.5 px-3 font-semibold text-white">${escapeHtml(item.name)}</td>
                <td class="py-2.5 px-3">
                    <span class="px-2 py-0.5 rounded-md bg-zinc-800 text-zinc-300 border border-zinc-700/60 text-[10px] font-medium">
                        ${escapeHtml(item.category)}
                    </span>
                </td>
                <td class="py-2.5 px-3 text-center text-zinc-300 font-mono">${escapeHtml(item.unit)}</td>
                <td class="py-2.5 px-3 text-center font-mono text-emerald-400 font-semibold">${item.current_stock.toLocaleString('id-ID')}</td>
                <td class="py-2.5 px-3 text-center font-mono text-zinc-400">${item.minimum_stock.toLocaleString('id-ID')}</td>
                <td class="py-2.5 px-3 text-right font-mono font-medium text-amber-300">Rp ${item.cost_price.toLocaleString('id-ID')}</td>
                <td class="py-2.5 px-3 text-zinc-400 text-[11px] truncate max-w-[150px]" title="${escapeHtml(item.description)}">${escapeHtml(item.description || '-')}</td>
                <td class="py-2.5 px-3 text-center">
                    <button type="button" onclick="removePreviewRow(${idx})" class="p-1 rounded text-zinc-500 hover:text-rose-400 hover:bg-rose-500/10 transition-colors" title="Hapus baris ini">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    </button>
                </td>
            `;
            tbody.appendChild(tr);
        });
    }

    function removePreviewRow(index) {
        parsedItems.splice(index, 1);
        document.getElementById('parsedRowCountBadge').textContent = `${parsedItems.length} baris`;
        document.getElementById('footerInfoText').textContent = `${parsedItems.length} bahan baku siap dimasukkan.`;
        if (parsedItems.length === 0) {
            resetExcelDropzone();
        } else {
            renderPreviewTable(parsedItems);
        }
    }

    function filterPreviewTable(query) {
        const q = (query || '').toLowerCase().trim();
        if (!q) {
            renderPreviewTable(parsedItems);
            return;
        }
        const filtered = parsedItems.filter(item => 
            item.name.toLowerCase().includes(q) ||
            item.category.toLowerCase().includes(q) ||
            item.unit.toLowerCase().includes(q) ||
            item.description.toLowerCase().includes(q)
        );
        renderPreviewTable(filtered);
    }

    function resetExcelDropzone() {
        parsedItems = [];
        document.getElementById('excelFileInput').value = '';
        document.getElementById('dropzoneSection').style.display = 'block';
        document.getElementById('previewSection').style.display = 'none';
        document.getElementById('footerInfoText').textContent = 'Pilih atau letakkan file Excel untuk memulai konversi.';
        const btn = document.getElementById('btnConfirmImport');
        btn.disabled = true;
        btn.className = "px-5 py-2 rounded-xl bg-zinc-700 text-zinc-500 font-bold text-xs flex items-center gap-2 cursor-not-allowed transition-all";
    }

    function submitConvertedData() {
        if (!parsedItems || parsedItems.length === 0) {
            alert('Tidak ada data bahan untuk disimpan.');
            return;
        }

        const btn = document.getElementById('btnConfirmImport');
        const originalText = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = `<svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-zinc-950 inline" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Menyimpan ${parsedItems.length} Data...`;

        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        fetch("{{ route('items.import') }}", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": csrfToken,
                "Accept": "application/json"
            },
            body: JSON.stringify({ items: parsedItems })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message || 'Data Excel berhasil dikonversi dan disimpan ke katalog!');
                window.location.reload();
            } else {
                alert(data.message || 'Terjadi kesalahan saat menyimpan data.');
                btn.disabled = false;
                btn.innerHTML = originalText;
            }
        })
        .catch(err => {
            console.error('Import error:', err);
            alert('Gagal menghubungi server. Silakan coba lagi.');
            btn.disabled = false;
            btn.innerHTML = originalText;
        });
    }

    // Unduh Template Excel Resmi
    function downloadExcelTemplate() {
        const templateData = [
            ["Nama Bahan Baku", "Kategori", "Satuan", "Stok Awal", "Stok Minimal", "Harga Beli (Rp)", "Keterangan"],
            ["Espresso House Blend", "Coffee Beans", "kg", 15, 5, 120000, "Blend Arabica 70% Robusta 30%"],
            ["Single Origin Aceh Gayo", "Coffee Beans", "kg", 8, 3, 145000, "Medium roast untuk Manual Brew"],
            ["Fresh Milk Diamond", "Dairy", "liter", 30, 10, 18500, "Susu segar pasteurisasi dingin"],
            ["Sirup Vanilla Monin", "Syrup & Flavor", "botol", 6, 2, 135000, "Botol kaca 700ml"],
            ["Sirup Caramel Monin", "Syrup & Flavor", "botol", 5, 2, 135000, "Botol kaca 700ml"],
            ["Paper Cup Hot 8oz", "Packaging", "pcs", 500, 100, 450, "Cangkir kertas takeaway kopi panas"],
            ["Cup Injection PP 16oz", "Packaging", "pcs", 1000, 200, 650, "Gelas plastik ice coffee tebal"]
        ];

        if (window.XLSX) {
            const ws = XLSX.utils.aoa_to_sheet(templateData);
            const wb = XLSX.utils.book_new();
            XLSX.utils.book_append_sheet(wb, ws, "BahanBaku");
            XLSX.writeFile(wb, "template_impor_bahan_baku_halfkopi.xlsx");
        } else {
            // CSV Fallback
            let csvContent = "data:text/csv;charset=utf-8," + templateData.map(e => e.map(val => `"${val}"`).join(",")).join("\n");
            const encodedUri = encodeURI(csvContent);
            const link = document.createElement("a");
            link.setAttribute("href", encodedUri);
            link.setAttribute("download", "template_impor_bahan_baku_halfkopi.csv");
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }
    }

    // Ekspor Seluruh Tabel Saat Ini ke Excel
    function exportCurrentTableToExcel() {
        const table = document.querySelector('table');
        if (!table) return;

        if (window.XLSX) {
            const wb = XLSX.utils.table_to_book(table, { sheet: "KatalogBahan" });
            const todayStr = new Date().toISOString().slice(0, 10);
            XLSX.writeFile(wb, `katalog_bahan_baku_halfkopi_${todayStr}.xlsx`);
        } else {
            alert('Library Excel sedang dimuat, silakan coba beberapa saat lagi.');
        }
    }

    function escapeHtml(text) {
        if (!text) return '';
        return String(text)
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }
</script>
@endsection
