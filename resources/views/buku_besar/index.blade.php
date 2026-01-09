@extends('layouts.app')

@section('content')
<div class="container-fluid buku-besar-page">

    <div class="filter-section">
        <form method="GET" action="{{ route('buku-besar.index') }}">
            <div class="row align-items-end">
                <div class="col-md-5">
                    <label>Pilih Akun</label>
                    <select name="account_id" class="form-control" required>
                        <option value="">-- Pilih Akun --</option>
                        @foreach($accounts as $acc)
                            <option value="{{ $acc->id }}" {{ $selectedAccount && $selectedAccount->id == $acc->id ? 'selected' : '' }}>
                                {{ $acc->kode }} - {{ $acc->keterangan }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label>Tahun</label>
                    <input type="number" name="year" class="form-control" value="{{ $year }}" min="2020" max="2099">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">Tampilkan</button>
                </div>
            </div>
        </form>
    </div>

@if($selectedAccount)
    <div class="buku-besar-header">
        <h4>BUKU BESAR</h4>
        <div>Tahun: {{ $year }}</div>
        <div>
            Akun: {{ $selectedAccount->kode }} - {{ $selectedAccount->keterangan }}
        </div>
    </div>

    <table class="buku-besar-table no-equal-width">
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>Keterangan</th>
                <th>PIC</th>
                <th>No. Bukti</th>
                <th>Debit</th>
                <th>Kredit</th>
                <th>Saldo</th>
            </tr>
        </thead>
        <tbody>

            {{-- SALDO AWAL --}}
            <tr class="row-opening">
                <td colspan="5">Saldo Awal</td>
                <td>-</td>
                <td>-</td>
                <td>{{ number_format($openingBalance,0,',','.') }}</td>
            </tr>

            {{-- TRANSAKSI --}}
            @forelse($bukuBesarData as $i => $row)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ \Carbon\Carbon::parse($row['date'])->format('d/m/Y') }}</td>
                <td>{{ $row['description'] }}</td>
                <td>{{ $row['pic'] }}</td>
                <td>{{ $row['proof_number'] ?? '-' }}</td>
                <td>
                    {{ $row['debit'] > 0 ? number_format($row['debit'],0,',','.') : '-' }}
                </td>
                <td>
                    {{ $row['credit'] > 0 ? number_format($row['credit'],0,',','.') : '-' }}
                </td>
                <td>{{ number_format($row['balance'],0,',','.') }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="8" class="text-center">Tidak ada transaksi</td>
            </tr>
            @endforelse

            {{-- TOTAL --}}
            <tr class="row-total">
                <td colspan="5">Total</td>
                <td>{{ number_format($totalDebit,0,',','.') }}</td>
                <td>{{ number_format($totalCredit,0,',','.') }}</td>
                <td>-</td>
            </tr>

            {{-- SALDO AKHIR --}}
            <tr class="row-ending">
                <td colspan="5">Saldo Akhir</td>
                <td>-</td>
                <td>-</td>
                <td>{{ number_format($endingBalance,0,',','.') }}</td>
            </tr>

        </tbody>
    </table>

    <div class="buku-besar-footer">
        <button onclick="window.print()" class="btn btn-secondary btn-sm">
            Cetak
        </button>
        <a href="{{ route('buku-besar.export-pdf', ['account_id' => $selectedAccount->id, 'year' => $year, 'start_date' => $startDate, 'end_date' => $endDate]) }}" class="btn btn-danger btn-sm" target="_blank">
            Export PDF
        </a>
    </div>
@endif

</div>
@endsection

<style>
.filter-section {
    background: #f8f9fa;
    padding: 20px;
    margin-bottom: 20px;
    border-radius: 5px;
}
.buku-besar-page { padding: 20px; }
.buku-besar-header { text-align: center; margin-bottom: 20px; }
.buku-besar-header h4 { margin: 0 0 10px 0; font-weight: bold; }
.buku-besar-table { width: 100% !important; border-collapse: collapse; font-size: 13px; table-layout: fixed !important; }
.buku-besar-table th, .buku-besar-table td { border: 1px solid #000; padding: 4px 6px; }
.buku-besar-table th { background: #e9ecef; font-weight: bold; text-align: center; width: auto !important; }
.buku-besar-table th:nth-child(1) { width: 40px !important; }
.buku-besar-table th:nth-child(2) { width: 80px !important; }
.buku-besar-table th:nth-child(3) { width: 25% !important; }
.buku-besar-table th:nth-child(4) { width: 10% !important; }
.buku-besar-table th:nth-child(5) { width: 10% !important; }
.buku-besar-table th:nth-child(6) { width: 13% !important; }
.buku-besar-table th:nth-child(7) { width: 13% !important; }
.buku-besar-table th:nth-child(8) { width: 13% !important; }
.buku-besar-table td { overflow: hidden; text-overflow: ellipsis; }
.buku-besar-table td:nth-child(2) { text-align: center; }
.buku-besar-table td:nth-child(3) { white-space: normal; word-wrap: break-word; }
.buku-besar-table td:nth-child(6), .buku-besar-table td:nth-child(7), .buku-besar-table td:nth-child(8) { text-align: right; }
.row-opening, .row-total, .row-ending { font-weight: bold; background: #f8f9fa; }
.buku-besar-footer { margin-top: 20px; text-align: center; }
.buku-besar-table.no-equal-width { table-layout: fixed !important; }
@media print {
    .filter-section, .buku-besar-footer { display: none; }
}
</style>
