<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Mutasi Stok - Gudang Half Kopi</title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 11px;
            color: #111;
            margin: 20px;
        }
        .header {
            border-bottom: 2px solid #222;
            padding-bottom: 12px;
            margin-bottom: 15px;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }
        .brand {
            font-size: 20px;
            font-weight: bold;
            color: #714e36;
        }
        .subbrand {
            font-size: 11px;
            color: #555;
        }
        .title {
            text-align: right;
        }
        .title h2 {
            margin: 0;
            font-size: 15px;
            text-transform: uppercase;
        }
        .title p {
            margin: 3px 0 0 0;
            font-size: 11px;
            color: #444;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th, td {
            border: 1px solid #ccc;
            padding: 6px 8px;
            text-align: left;
        }
        th {
            background-color: #f2ede8;
            font-weight: bold;
            font-size: 10px;
            text-transform: uppercase;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        .signatures {
            margin-top: 40px;
            display: flex;
            justify-content: space-between;
            text-align: center;
        }
        .signature-box {
            width: 200px;
        }
        .signature-space {
            height: 60px;
        }
        @media print {
            .no-print { display: none; }
            body { margin: 10mm; }
        }
    </style>
</head>
<body>
    <div class="no-print" style="margin-bottom: 15px; text-align: right;">
        <button onclick="window.print()" style="padding: 8px 16px; background: #714e36; color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: bold;">
            Cetak Dokumen Sekarang (Print / PDF)
        </button>
    </div>

    <div class="header">
        <div>
            <div class="brand">HALF KOPI</div>
            <div class="subbrand">Gudang & Central Supply Chain &bull; Jl. Kaliurang KM 5.5, Yogyakarta</div>
            <div class="subbrand">Email: supply@halfkopi.com &bull; Telp: 0812-3456-7890</div>
        </div>
        <div class="title">
            <h2>Laporan Rekapitulasi Mutasi Stok</h2>
            <p>Periode: <strong>{{ \Carbon\Carbon::parse($startDate)->translatedFormat('d M Y') }}</strong> s/d <strong>{{ \Carbon\Carbon::parse($endDate)->translatedFormat('d M Y') }}</strong></p>
            @if($selectedCategory)
                <p>Kategori: <strong>{{ $selectedCategory->name }}</strong></p>
            @endif
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 25px;">No</th>
                <th>Tanggal</th>
                <th>No Bukti / Transaksi</th>
                <th>Tipe</th>
                <th>Bahan Baku (SKU)</th>
                <th class="text-center">Jumlah (Qty)</th>
                <th class="text-right">Harga Beli</th>
                <th class="text-right">Subtotal Nilai</th>
                <th>Keterangan / Keperluan</th>
            </tr>
        </thead>
        <tbody>
            @php $totalNilai = 0; @endphp
            @forelse($records as $idx => $rec)
                @php 
                    $tx = $rec->transaction; 
                    $totalNilai += $rec->subtotal;
                @endphp
                <tr>
                    <td class="text-center">{{ $idx + 1 }}</td>
                    <td>{{ $tx->date->format('d/m/Y') }}</td>
                    <td><strong>{{ $tx->transaction_code }}</strong></td>
                    <td class="text-center">
                        @if($tx->type === 'in') MASUK @elseif($tx->type === 'out') KELUAR @else OPNAME @endif
                    </td>
                    <td>
                        <strong>{{ $rec->item->name }}</strong> ({{ $rec->item->sku }})
                    </td>
                    <td class="text-center font-bold">
                        @if($tx->type === 'in')+{{ $rec->quantity }}@elseif($tx->type === 'out')-{{ $rec->quantity }}@else{{ $rec->quantity >= 0 ? '+' : '' }}{{ $rec->quantity }}@endif {{ $rec->item->unit }}
                    </td>
                    <td class="text-right">Rp {{ number_format($rec->unit_cost, 0, ',', '.') }}</td>
                    <td class="text-right font-bold">Rp {{ number_format($rec->subtotal, 0, ',', '.') }}</td>
                    <td>{{ $rec->notes ?: $tx->destination_or_reason }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="text-center" style="padding: 20px;">Tidak ada pergerakan stok pada periode ini.</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr style="background: #fbf8f5; font-weight: bold;">
                <td colspan="7" class="text-right">TOTAL NILAI TRANSAKSI MUTASI:</td>
                <td class="text-right">Rp {{ number_format($totalNilai, 0, ',', '.') }}</td>
                <td></td>
            </tr>
        </tfoot>
    </table>

    <div class="signatures">
        <div class="signature-box">
            <p>Dibuat Oleh (Staff Gudang),</p>
            <div class="signature-space"></div>
            <p class="font-bold">( {{ auth()->user()->name }} )</p>
            <p style="font-size: 10px; color: #555;">{{ auth()->user()->role }}</p>
        </div>

        <div class="signature-box">
            <p>Diketahui / Disetujui (Admin/Owner),</p>
            <div class="signature-space"></div>
            <p class="font-bold">( Budi Santoso )</p>
            <p style="font-size: 10px; color: #555;">Manager Gudang & Operasional</p>
        </div>
    </div>
</body>
</html>
