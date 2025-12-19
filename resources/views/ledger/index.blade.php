@extends('layouts.app')

@section('content')
<div class="container">
    <div class="page-header">
        <h1>Buku Besar (General Ledger)</h1>
    </div>

    <!-- Account Selection Form -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('ledger.index') }}">
                <div class="row">
                    <div class="col-md-6">
                        <label>Pilih Akun</label>
                        <select name="account_id" id="ledger-account-select" class="form-control" required>
                            <option value="">-- Search Account --</option>
                            @foreach($accounts as $account)
                                <option value="{{ $account->id }}" 
                                    {{ request('account_id') == $account->id ? 'selected' : '' }}>
                                    {{ $account->kode }} - {{ $account->keterangan }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label>Tahun</label>
                        <input type="number" name="year" class="form-control" 
                            value="{{ $year }}" min="2020" max="2099">
                    </div>
                    <div class="col-md-2">
                        <label>&nbsp;</label>
                        <button type="submit" class="btn btn-primary btn-block">Tampilkan</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @if(!$selectedAccount)
    <!-- Instruction Message -->
    <div class="alert alert-info">
        <i class="fas fa-info-circle"></i> Silakan pilih akun untuk menampilkan Buku Besar.
    </div>
    @endif

    @if($selectedAccount)
    <!-- Ledger Report -->
    <div class="card">
        <div class="card-body">
            <!-- Header -->
            <div class="text-center mb-4">
                <h3>BUKU BESAR</h3>
                <p class="mb-1"><strong>Tahun: {{ $year }}</strong></p>
                <p class="mb-1">Akun: <strong>{{ $selectedAccount->kode }} - {{ $selectedAccount->keterangan }}</strong></p>
                @if($startDate || $endDate)
                    <p class="mb-0">
                        Periode: {{ $startDate ? date('d/m/Y', strtotime($startDate)) : 'Awal' }} 
                        s/d {{ $endDate ? date('d/m/Y', strtotime($endDate)) : 'Akhir' }}
                    </p>
                @endif
            </div>

            <!-- Summary Panel -->
            <div class="row mb-3">
                <div class="col-md-6 offset-md-6">
                    <table class="table table-sm table-borderless">
                        <tr>
                            <th>Trial Balance</th>
                            <td class="text-end">
                                {{ number_format($trialBalanceTotal, 0, ',', '.') }}
                            </td>
                        </tr>
                        <tr>
                            <th>Buku Besar</th>
                            <td class="text-end">
                                {{ number_format($ledgerTotal, 0, ',', '.') }}
                            </td>
                        </tr>
                        <tr class="fw-bold {{ $selisih != 0 ? 'text-danger' : 'text-success' }}">
                            <th>Selisih</th>
                            <td class="text-end">
                                ({{ number_format(abs($selisih), 0, ',', '.') }})
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Ledger Table -->
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th width="5%">No</th>
                            <th width="35%">Keterangan</th>
                            <th width="10%">PIC</th>
                            <th width="15%">No. Bukti</th>
                            <th width="12%" class="text-right">Debit</th>
                            <th width="12%" class="text-right">Kredit</th>
                            <th width="12%" class="text-right">Saldo</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Opening Balance -->
                        <tr class="table-info">
                            <td colspan="4"><strong>Saldo Awal</strong></td>
                            <td class="text-right">-</td>
                            <td class="text-right">-</td>
                            <td class="text-right"><strong>{{ number_format($openingBalance, 0, ',', '.') }}</strong></td>
                        </tr>

                        <!-- Ledger Entries -->
                        @forelse($ledgerData as $index => $entry)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $entry['description'] }}</td>
                            <td>{{ $entry['pic'] }}</td>
                            <td>{{ $entry['proof_number'] ?? '-' }}</td>
                            <td class="text-right">{{ $entry['debit'] > 0 ? number_format($entry['debit'], 0, ',', '.') : '-' }}</td>
                            <td class="text-right">{{ $entry['credit'] > 0 ? number_format($entry['credit'], 0, ',', '.') : '-' }}</td>
                            <td class="text-right">{{ number_format($entry['balance'], 0, ',', '.') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center">Tidak ada transaksi</td>
                        </tr>
                        @endforelse

                        <!-- Totals -->
                        <tr class="table-secondary">
                            <td colspan="4" class="text-right"><strong>Total</strong></td>
                            <td class="text-right"><strong>{{ number_format($totalDebit, 0, ',', '.') }}</strong></td>
                            <td class="text-right"><strong>{{ number_format($totalCredit, 0, ',', '.') }}</strong></td>
                            <td class="text-right">-</td>
                        </tr>

                        <!-- Ending Balance -->
                        <tr class="table-success">
                            <td colspan="4"><strong>Saldo Akhir</strong></td>
                            <td class="text-right">-</td>
                            <td class="text-right">-</td>
                            <td class="text-right"><strong>{{ number_format($endingBalance, 0, ',', '.') }}</strong></td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Print Button -->
            <div class="mt-3">
                <button onclick="window.print()" class="btn btn-secondary">
                    <i class="fas fa-print"></i> Cetak
                </button>
            </div>
        </div>
    </div>
    @endif
</div>

<style>
@media print {
    .page-header, .card:first-child, .btn { display: none; }
    .card { border: none; box-shadow: none; }
}
</style>

@push('scripts')
<script>
$(document).ready(function() {
    // Initialize Select2 for ledger account dropdown
    $('#ledger-account-select').select2({
        theme: 'default',
        width: '100%',
        placeholder: '-- Search Account --',
        allowClear: true,
        matcher: function(params, data) {
            // If there are no search terms, return all data
            if ($.trim(params.term) === '') {
                return data;
            }

            // Do not display the item if there is no 'text' property
            if (typeof data.text === 'undefined') {
                return null;
            }

            // Search in both code and description
            var searchTerm = params.term.toLowerCase();
            var text = data.text.toLowerCase();
            
            if (text.indexOf(searchTerm) > -1) {
                return data;
            }

            // Return null if the term should not be displayed
            return null;
        }
    });
});
</script>
@endpush
@endsection
