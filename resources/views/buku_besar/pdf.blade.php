<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Buku Besar - {{ $selectedAccount->kode }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 11px; margin: 20px; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h3 { margin: 5px 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #000; padding: 5px; }
        th { background: #e9ecef; font-weight: bold; text-align: center; }
        td { vertical-align: top; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .row-opening, .row-total, .row-ending { font-weight: bold; background: #f8f9fa; }
        @media print {
            body { margin: 0; }
            @page { margin: 1cm; }
        }
    </style>
    <script>
        window.onload = function() {
            window.print();
        }
    </script>
</head>
<body>
    <div class="header">
        <h3>BUKU BESAR</h3>
        <div>Tahun: {{ $year }}</div>
        <div>Akun: {{ $selectedAccount->kode }} - {{ $selectedAccount->keterangan }}</div>
    </div>

    <table>
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="10%">Tanggal</th>
                <th width="30%">Keterangan</th>
                <th width="10%">PIC</th>
                <th width="10%">No. Bukti</th>
                <th width="12%">Debit</th>
                <th width="12%">Kredit</th>
                <th width="11%">Saldo</th>
            </tr>
        </thead>
        <tbody>
            <tr class="row-opening">
                <td colspan="5">Saldo Awal</td>
                <td class="text-right">-</td>
                <td class="text-right">-</td>
                <td class="text-right">{{ number_format($openingBalance,0,',','.') }}</td>
            </tr>

            @forelse($bukuBesarData as $i => $row)
            <tr>
                <td class="text-center">{{ $i + 1 }}</td>
                <td class="text-center">{{ \Carbon\Carbon::parse($row['date'])->format('d/m/Y') }}</td>
                <td>{{ $row['description'] }}</td>
                <td>{{ $row['pic'] }}</td>
                <td>{{ $row['proof_number'] ?? '-' }}</td>
                <td class="text-right">{{ $row['debit'] > 0 ? number_format($row['debit'],0,',','.') : '-' }}</td>
                <td class="text-right">{{ $row['credit'] > 0 ? number_format($row['credit'],0,',','.') : '-' }}</td>
                <td class="text-right">{{ number_format($row['balance'],0,',','.') }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="8" class="text-center">Tidak ada transaksi</td>
            </tr>
            @endforelse

            <tr class="row-total">
                <td colspan="5">Total</td>
                <td class="text-right">{{ number_format($totalDebit,0,',','.') }}</td>
                <td class="text-right">{{ number_format($totalCredit,0,',','.') }}</td>
                <td class="text-right">-</td>
            </tr>

            <tr class="row-ending">
                <td colspan="5">Saldo Akhir</td>
                <td class="text-right">-</td>
                <td class="text-right">-</td>
                <td class="text-right">{{ number_format($endingBalance,0,',','.') }}</td>
            </tr>
        </tbody>
    </table>
</body>
</html>
