@extends('layouts.app')

@section('content')
<div class="container-fluid ledger-page">

    <div class="filter-section">
        <form method="GET" action="{{ route('ledger.index') }}">
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
    <div class="ledger-header">
        <h4>BUKU BESAR</h4>
        <div>Tahun: {{ $year }}</div>
        <div>
            Akun: {{ $selectedAccount->kode }} - {{ $selectedAccount->keterangan }}
        </div>
    </div>

    <table class="ledger-table no-equal-width">
        <thead>
            <tr>
                <th>No</th>
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
                <td colspan="4">Saldo Awal</td>
                <td>-</td>
                <td>-</td>
                <td>{{ number_format($openingBalance,0,',','.') }}</td>
            </tr>

            {{-- TRANSAKSI --}}
            @forelse($ledgerData as $i => $row)
            <tr>
                <td>{{ $i + 1 }}</td>
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
                <td colspan="7" class="text-center">Tidak ada transaksi</td>
            </tr>
            @endforelse

            {{-- TOTAL --}}
            <tr class="row-total">
                <td colspan="4">Total</td>
                <td>{{ number_format($totalDebit,0,',','.') }}</td>
                <td>{{ number_format($totalCredit,0,',','.') }}</td>
                <td>-</td>
            </tr>

            {{-- SALDO AKHIR --}}
            <tr class="row-ending">
                <td colspan="4">Saldo Akhir</td>
                <td>-</td>
                <td>-</td>
                <td>{{ number_format($endingBalance,0,',','.') }}</td>
            </tr>

        </tbody>
    </table>

    <div class="ledger-footer">
        <button onclick="window.print()" class="btn btn-secondary btn-sm">
            Cetak
        </button>
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
.ledger-page { padding: 20px; }
.ledger-header { text-align: center; margin-bottom: 20px; }
.ledger-header h4 { margin: 0 0 10px 0; font-weight: bold; }
.ledger-table { width: 100% !important; border-collapse: collapse; font-size: 13px; table-layout: fixed !important; }
.ledger-table th, .ledger-table td { border: 1px solid #000; padding: 4px 6px; }
.ledger-table th { background: #e9ecef; font-weight: bold; text-align: center; width: auto !important; }
.ledger-table th:nth-child(1) { width: 40px !important; }
.ledger-table th:nth-child(2) { width: 30% !important; }
.ledger-table th:nth-child(3) { width: 10% !important; }
.ledger-table th:nth-child(4) { width: 12% !important; }
.ledger-table th:nth-child(5) { width: 13% !important; }
.ledger-table th:nth-child(6) { width: 13% !important; }
.ledger-table th:nth-child(7) { width: 15% !important; }
.ledger-table td { overflow: hidden; text-overflow: ellipsis; }
.ledger-table td:nth-child(2) { white-space: normal; word-wrap: break-word; }
.ledger-table td:nth-child(5), .ledger-table td:nth-child(6), .ledger-table td:nth-child(7) { text-align: right; }
.row-opening, .row-total, .row-ending { font-weight: bold; background: #f8f9fa; }
.ledger-footer { margin-top: 20px; text-align: center; }
.ledger-table.no-equal-width { table-layout: fixed !important; }
@media print {
    .filter-section, .ledger-footer { display: none; }
}
</style>
