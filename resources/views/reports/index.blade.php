@extends('layouts.app')

@section('title', 'Laporan Stok')
@section('page-title', 'Laporan Stok Bahan Baku')
@section('page-subtitle', 'Riwayat semua pergerakan stok &mdash; masuk, pemakaian, dan penyesuaian')

@section('content')
<div class="space-y-6">

    {{-- ===== HEADER ===== --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-extrabold text-white tracking-tight">Riwayat Pergerakan Stok</h2>
            <p class="text-xs text-zinc-400 mt-0.5">
                Periode: <strong class="text-zinc-200">{{ \Carbon\Carbon::parse($startDate)->translatedFormat('d M Y') }}</strong>
                s/d <strong class="text-zinc-200">{{ \Carbon\Carbon::parse($endDate)->translatedFormat('d M Y') }}</strong>
            </p>
        </div>
        <a href="{{ route('reports.print', request()->query()) }}" target="_blank"
            class="px-4 py-2.5 rounded-xl bg-zinc-800 hover:bg-zinc-700 text-zinc-200 border border-zinc-700 font-bold text-xs flex items-center gap-2 transition-all self-start sm:self-auto">
            <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
            </svg>
            Cetak / Simpan PDF
        </a>
    </div>

    {{-- ===== FORM FILTER ===== --}}
    <form method="GET" action="{{ route('reports.index') }}" id="reportForm">
        <input type="hidden" name="start_date" id="hiddenStart" value="{{ $startDate }}">
        <input type="hidden" name="end_date" id="hiddenEnd" value="{{ $endDate }}">
        <input type="hidden" name="category_id" id="hiddenCategory" value="{{ $categoryId }}">
        <input type="hidden" name="type" id="hiddenType" value="{{ $type }}">

        {{-- Tab Jenis: Tombol besar modern dengan ikon SVG --}}
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 mb-4">
            <button type="button" onclick="setType('')"
                class="py-3 px-3 rounded-xl font-bold text-xs transition-all border flex flex-col items-center justify-center gap-1.5 {{ $type === '' ? 'bg-zinc-800 text-white border-zinc-500 shadow-md' : 'bg-zinc-900/70 text-zinc-400 border-zinc-800 hover:bg-zinc-800 hover:text-zinc-200' }}">
                <svg class="w-5 h-5 {{ $type === '' ? 'text-white' : 'text-zinc-400' }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                </svg>
                <span>Semua Transaksi</span>
            </button>
            <button type="button" onclick="setType('in')"
                class="py-3 px-3 rounded-xl font-bold text-xs transition-all border flex flex-col items-center justify-center gap-1.5 {{ $type === 'in' ? 'bg-emerald-500/20 text-emerald-300 border-emerald-500/50 shadow-md' : 'bg-zinc-900/70 text-zinc-400 border-zinc-800 hover:bg-zinc-800 hover:text-zinc-200' }}">
                <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
                </svg>
                <span>Stok Masuk</span>
            </button>
            <button type="button" onclick="setType('out')"
                class="py-3 px-3 rounded-xl font-bold text-xs transition-all border flex flex-col items-center justify-center gap-1.5 {{ $type === 'out' ? 'bg-amber-500/20 text-amber-300 border-amber-500/50 shadow-md' : 'bg-zinc-900/70 text-zinc-400 border-zinc-800 hover:bg-zinc-800 hover:text-zinc-200' }}">
                <svg class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 10l7-7m0 0l7 7m-7-7v18"/>
                </svg>
                <span>Pemakaian Bar</span>
            </button>
            <button type="button" onclick="setType('adjustment')"
                class="py-3 px-3 rounded-xl font-bold text-xs transition-all border flex flex-col items-center justify-center gap-1.5 {{ $type === 'adjustment' ? 'bg-cyan-500/20 text-cyan-300 border-cyan-500/50 shadow-md' : 'bg-zinc-900/70 text-zinc-400 border-zinc-800 hover:bg-zinc-800 hover:text-zinc-200' }}">
                <svg class="w-5 h-5 text-cyan-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                <span>Opname Fisik</span>
            </button>
        </div>

        {{-- Filter Tanggal & Kategori dengan Preset Cepat --}}
        <div class="bg-zinc-900/70 border border-zinc-800 rounded-2xl p-4 sm:p-5 space-y-3">
            <div class="flex flex-wrap items-center justify-between gap-2 pb-2 border-b border-zinc-800/80">
                <span class="text-[11px] font-bold text-zinc-400 uppercase tracking-wider">Filter Periode & Kategori</span>
                <div class="flex items-center gap-1.5 text-xs">
                    <span class="text-zinc-500 text-[11px]">Preset Cepat:</span>
                    <button type="button" onclick="setPreset('today')" class="px-2.5 py-1 rounded-lg bg-zinc-800 hover:bg-zinc-700 text-zinc-300 text-[11px] font-medium transition-all">Hari Ini</button>
                    <button type="button" onclick="setPreset('week')" class="px-2.5 py-1 rounded-lg bg-zinc-800 hover:bg-zinc-700 text-zinc-300 text-[11px] font-medium transition-all">7 Hari Terakhir</button>
                    <button type="button" onclick="setPreset('month')" class="px-2.5 py-1 rounded-lg bg-zinc-800 hover:bg-zinc-700 text-zinc-300 text-[11px] font-medium transition-all">Bulan Ini</button>
                </div>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                <div>
                    <label class="block text-[11px] font-semibold text-zinc-400 mb-1">Dari Tanggal</label>
                    <input type="date" id="inputStart" value="{{ $startDate }}"
                        onchange="document.getElementById('hiddenStart').value=this.value"
                        class="w-full bg-zinc-950 border border-zinc-700/80 rounded-xl px-3 py-2 text-xs text-zinc-100 focus:outline-none focus:border-amber-500">
                </div>
                <div>
                    <label class="block text-[11px] font-semibold text-zinc-400 mb-1">Sampai Tanggal</label>
                    <input type="date" id="inputEnd" value="{{ $endDate }}"
                        onchange="document.getElementById('hiddenEnd').value=this.value"
                        class="w-full bg-zinc-950 border border-zinc-700/80 rounded-xl px-3 py-2 text-xs text-zinc-100 focus:outline-none focus:border-amber-500">
                </div>
                <div>
                    <label class="block text-[11px] font-semibold text-zinc-400 mb-1">Kategori Bahan</label>
                    <select id="inputCategory"
                        onchange="document.getElementById('hiddenCategory').value=this.value"
                        class="w-full bg-zinc-950 border border-zinc-700/80 rounded-xl px-3 py-2 text-xs text-zinc-100 focus:outline-none focus:border-amber-500">
                        <option value="">Semua Kategori</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ $categoryId == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-end gap-2">
                    <button type="submit" class="flex-1 py-2 px-4 rounded-xl bg-amber-600 hover:bg-amber-500 text-white font-bold text-xs transition-all">
                        Tampilkan
                    </button>
                    <a href="{{ route('reports.index') }}" class="p-2 rounded-xl bg-zinc-800 hover:bg-zinc-700 text-zinc-400 hover:text-zinc-200 border border-zinc-700 transition-all" title="Reset ke default (bulan ini)">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                    </a>
                </div>
            </div>
        </div>
    </form>

    {{-- ===== RINGKASAN 4 ANGKA PENTING ===== --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="p-4 rounded-xl bg-zinc-900/70 border border-zinc-800">
            <div class="text-[11px] font-semibold text-zinc-500 uppercase">Bahan Diterima</div>
            <div class="text-2xl font-black text-emerald-400 mt-1">+{{ number_format($totalInQty, 1, ',', '.') }}</div>
            <div class="text-[10px] text-zinc-500 mt-0.5">Total qty masuk dari supplier</div>
        </div>
        <div class="p-4 rounded-xl bg-zinc-900/70 border border-zinc-800">
            <div class="text-[11px] font-semibold text-zinc-500 uppercase">Total Belanja</div>
            <div class="text-xl font-black text-white mt-1">Rp {{ number_format($totalInCost, 0, ',', '.') }}</div>
            <div class="text-[10px] text-zinc-500 mt-0.5">Nilai pembelian ke supplier</div>
        </div>
        <div class="p-4 rounded-xl bg-zinc-900/70 border border-zinc-800">
            <div class="text-[11px] font-semibold text-zinc-500 uppercase">Bahan Dipakai</div>
            <div class="text-2xl font-black text-amber-400 mt-1">-{{ number_format($totalOutQty, 1, ',', '.') }}</div>
            <div class="text-[10px] text-zinc-500 mt-0.5">Total qty dipakai barista/dapur</div>
        </div>
        <div class="p-4 rounded-xl bg-zinc-900/70 border border-zinc-800">
            <div class="text-[11px] font-semibold text-zinc-500 uppercase">Nilai Terpakai</div>
            <div class="text-xl font-black text-zinc-200 mt-1">Rp {{ number_format($totalOutCost, 0, ',', '.') }}</div>
            <div class="text-[10px] text-zinc-500 mt-0.5">Estimasi biaya bahan yang dipakai</div>
        </div>
    </div>

    {{-- ===== TABEL RIWAYAT ===== --}}
    <div class="bg-zinc-900/70 border border-zinc-800 rounded-2xl overflow-hidden">
        @if($records->isEmpty())
            <div class="py-16 text-center">
                <svg class="w-12 h-12 mx-auto mb-3 text-zinc-600" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <div class="text-zinc-300 font-bold text-base">Tidak ada data pada periode ini</div>
                <div class="text-xs text-zinc-500 mt-1">Coba ubah rentang tanggal atau klik "Semua Transaksi"</div>
                <a href="{{ route('reports.index') }}" class="inline-block mt-4 px-4 py-2 rounded-xl bg-zinc-800 text-zinc-300 text-xs font-semibold hover:bg-zinc-700 transition-all">
                    Reset Filter
                </a>
            </div>
        @else
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs" style="min-width:600px;">
                <thead>
                    <tr class="bg-zinc-900 border-b border-zinc-800 text-zinc-400 font-semibold uppercase tracking-wider text-[10px]">
                        <th class="py-3 px-4">Tanggal</th>
                        <th class="py-3 px-4">Jenis</th>
                        <th class="py-3 px-4">Nama Bahan</th>
                        <th class="py-3 px-4 text-center">Jumlah</th>
                        <th class="py-3 px-4 text-right">Harga / Satuan</th>
                        <th class="py-3 px-4 text-right">Total Harga</th>
                        <th class="py-3 px-4">Keterangan</th>
                        <th class="py-3 px-4">Dicatat Oleh</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-800/60">
                    @foreach($records as $rec)
                        @php $tx = $rec->transaction; @endphp
                        <tr class="hover:bg-zinc-800/30 transition-colors">
                            <td class="py-3.5 px-4">
                                <div class="font-semibold text-zinc-200">{{ $tx->date->translatedFormat('d M Y') }}</div>
                                <div class="text-[10px] text-zinc-500 font-mono">{{ $tx->transaction_code }}</div>
                            </td>
                            <td class="py-3.5 px-4">
                                @if($tx->type === 'in')
                                    <span class="inline-flex items-center gap-1.5 px-2 py-1 rounded-lg text-[10px] font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 14l-7 7m0 0l-7-7m7 7V3"/></svg>
                                        Masuk
                                    </span>
                                @elseif($tx->type === 'out')
                                    <span class="inline-flex items-center gap-1.5 px-2 py-1 rounded-lg text-[10px] font-bold bg-amber-500/10 text-amber-400 border border-amber-500/20">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 10l7-7m0 0l7 7m-7-7v18"/></svg>
                                        Pemakaian
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2 py-1 rounded-lg text-[10px] font-bold bg-cyan-500/10 text-cyan-400 border border-cyan-500/20">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                        Opname
                                    </span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4">
                                <div class="font-bold text-white">{{ $rec->item->name }}</div>
                                <div class="text-[10px] text-zinc-500">{{ $rec->item->category->name }}</div>
                            </td>
                            <td class="py-3.5 px-4 text-center font-bold">
                                @if($tx->type === 'in')
                                    <span class="text-emerald-400">+{{ number_format($rec->quantity, 1, ',', '.') }} {{ $rec->item->unit }}</span>
                                @elseif($tx->type === 'out')
                                    <span class="text-amber-400">-{{ number_format($rec->quantity, 1, ',', '.') }} {{ $rec->item->unit }}</span>
                                @else
                                    <span class="text-cyan-400">{{ $rec->quantity >= 0 ? '+' : '' }}{{ number_format($rec->quantity, 1, ',', '.') }} {{ $rec->item->unit }}</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-right text-zinc-400">
                                Rp {{ number_format($rec->unit_cost, 0, ',', '.') }}
                            </td>
                            <td class="py-3.5 px-4 text-right font-bold text-white">
                                Rp {{ number_format($rec->subtotal, 0, ',', '.') }}
                            </td>
                            <td class="py-3.5 px-4 text-zinc-400 max-w-[180px]">
                                <span class="block" title="{{ $rec->notes ?: $tx->destination_or_reason }}">
                                    {{ $rec->notes ?: $tx->destination_or_reason ?: '-' }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4">
                                <div class="text-zinc-200 font-medium">{{ $tx->user->name ?? '-' }}</div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @if($records->hasPages())
            <div class="p-4 border-t border-zinc-800">
                {{ $records->links() }}
            </div>
        @endif
        @endif
    </div>
</div>

@push('scripts')
<script>
    function setType(val) {
        document.getElementById('hiddenType').value = val;
        document.getElementById('hiddenCategory').value = document.getElementById('inputCategory').value;
        document.getElementById('hiddenStart').value = document.getElementById('inputStart').value;
        document.getElementById('hiddenEnd').value = document.getElementById('inputEnd').value;
        document.getElementById('reportForm').submit();
    }

    function setPreset(range) {
        const today = new Date();
        const formatDate = (d) => d.toISOString().split('T')[0];

        let start, end = formatDate(today);

        if (range === 'today') {
            start = end;
        } else if (range === 'week') {
            const weekAgo = new Date();
            weekAgo.setDate(today.getDate() - 6);
            start = formatDate(weekAgo);
        } else if (range === 'month') {
            const monthStart = new Date(today.getFullYear(), today.getMonth(), 1);
            start = formatDate(monthStart);
        }

        document.getElementById('inputStart').value = start;
        document.getElementById('inputEnd').value = end;
        document.getElementById('hiddenStart').value = start;
        document.getElementById('hiddenEnd').value = end;
        document.getElementById('hiddenCategory').value = document.getElementById('inputCategory').value;
        document.getElementById('reportForm').submit();
    }
</script>
@endpush
@endsection

