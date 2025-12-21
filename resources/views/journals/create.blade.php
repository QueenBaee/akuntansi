@extends('layouts.app')

@section('title', 'Tambah Jurnal')

@section('page-header')
    <div class="page-pretitle">Transaksi</div>
    <h2 class="page-title" id="pageTitle">
        @if ($selectedAccount)
            Jurnal {{ $selectedAccount->keterangan }} - Tahun {{ request('year', date('Y')) }}
        @else
            Jurnal Kas/Bank
        @endif
    </h2>
    <div class="page-subtitle text-muted" id="pageSubtitle">
        @if ($selectedAccount)
            Saldo Awal: {{ number_format($openingBalance, 0, ',', '.') }}
        @else
            Pilih akun dari menu untuk memulai
        @endif
    </div>
@endsection

@section('page-actions')
    @if ($selectedAccount)
        <form method="GET" class="d-flex">
            <input type="hidden" name="ledger_id" value="{{ request('ledger_id') }}">
            <input type="number" name="year" value="{{ request('year', date('Y')) }}" class="form-control me-2" placeholder="Tahun" style="width: 100px;">
            <button class="btn btn-outline-primary">Filter</button>
        </form>
    @endif
@endsection

@section('content')
<style>
    body {
        overflow-x: hidden;
    }
    
    /* Searchable Select Styles */
    .searchable-select {
        position: static;
        display: inline-block;
        width: 100%;
    }
    
    .searchable-select input {
        width: 100%;
        padding: 4px 8px;
        border: 1px solid #ddd;
        border-radius: 4px;
        font-size: 12px;
    }
    
    .searchable-select .dropdown-list {
        position: fixed;
        background: white;
        border: 1px solid #ddd;
        border-radius: 4px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        max-height: 200px;
        overflow-y: auto;
        z-index: 9999;
        display: none;
        min-width: 200px;
    }
    
    .searchable-select .dropdown-item {
        padding: 8px;
        cursor: pointer;
        font-size: 12px;
        border-bottom: 1px solid #eee;
    }
    
    .searchable-select .dropdown-item:hover {
        background-color: #f8f9fa;
    }
    
    .searchable-select .dropdown-item.selected {
        background-color: #007bff;
        color: white;
    }
</style>

    <!-- Alert Messages -->
    <div id="alert-container"></div>

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible">
            <div class="d-flex">
                <div>
                    @foreach ($errors->all() as $error)
                        {{ $error }}<br>
                    @endforeach
                </div>
            </div>
            <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
        </div>
    @endif



    @if (!$selectedAccount)
        <div class="alert alert-warning alert-dismissible">
            <div class="d-flex">
                <div><strong>Perhatian!</strong> Pilih akun kas/bank dari menu di atas untuk memulai membuat jurnal.</div>
            </div>
            <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
        </div>
    @endif

    <div class="row">
        <div class="col-12">
            <form method="POST" action="{{ route('journals.store') }}" id="journalForm" enctype="multipart/form-data" onsubmit="return validateForm()">
                @csrf
                <input type="hidden" name="selected_cash_account_id"
                    value="{{ $selectedAccount ? $selectedAccount->id : '' }}">
                <input type="hidden" name="ledger_id"
                    value="{{ $selectedLedger ? $selectedLedger->id : '' }}">

                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="card-title">
                                @if ($selectedAccount)
                                    {{ $selectedAccount->kode }} - {{ $selectedAccount->keterangan }}
                                @else
                                    Jurnal Kas/Bank
                                @endif
                            </h3>
                        </div>
                        @if ($selectedAccount)
                            <div class="text-end">
                                <small class="text-muted">Saldo Awal:</small><br>
                                <strong>{{ number_format($openingBalance, 0, ',', '.') }}</strong>
                            </div>
                        @endif
                    </div>
                    <div class="card-body">
                        @if ($selectedAccount)
                            <div class="table-responsive">
                                <table class="table table-bordered" id="journalTable" style="border: 1px solid #dee2e6;">
                                    <thead class="table-light">
                                        <tr style="border: 1px solid #dee2e6;">
                                            <th style="border: 1px solid #dee2e6; width: 80px; text-align:center">Tanggal</th>
                                            <th style="border: 1px solid #dee2e6; width: 200px; text-align:center">Keterangan</th>
                                            <th style="border: 1px solid #dee2e6; width: 100px; text-align:center">PIC</th>
                                            <th style="border: 1px solid #dee2e6; width: 100px; text-align:center">Bukti</th>
                                            <th style="border: 1px solid #dee2e6; width: 100px; text-align:center">No. Bukti</th>
                                            <th style="border: 1px solid #dee2e6; width: 120px; text-align:center">Masuk</th>
                                            <th style="border: 1px solid #dee2e6; width: 120px; text-align:center">Keluar</th>
                                            <th style="border: 1px solid #dee2e6; width: 120px; text-align:center">Saldo</th>
                                            <th style="border: 1px solid #dee2e6; width: 230px; text-align:center">Kode & Akun CF</th>
                                            <th style="border: 1px solid #dee2e6; width: 150px; text-align:center">Debit</th>
                                            <th style="border: 1px solid #dee2e6; width: 150px; text-align:center">Kredit</th>
                                            <th style="border: 1px solid #dee2e6; width: 50px; text-align:center">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody id="journalLines">
                                        @foreach ($journalsHistory as $history)
                                            <tr data-existing="1" data-balance="{{ $history['balance'] }}"
                                                data-journal-id="{{ $history['journal_id'] }}"
                                                style="border: 1px solid #dee2e6; background-color: #f8f9fa;">
                                                <td style="border: 1px solid #dee2e6; padding: 4px; font-size: 12px;">
                                                    {{ $history['date'] }}</td>
                                                <td style="border: 1px solid #dee2e6; padding: 4px; font-size: 12px;">
                                                    {{ $history['description'] }}</td>
                                                <td style="border: 1px solid #dee2e6; padding: 4px; font-size: 12px;">
                                                    {{ $history['pic'] ?? '-' }}</td>
                                                <td
                                                    style="border: 1px solid #dee2e6; padding: 4px; font-size: 12px; text-align: center;">
                                                    @if ($history['attachments'] && count($history['attachments']) > 0)
                                                        <button type="button" class="btn btn-sm btn-info"
                                                            onclick="viewAttachments({{ $history['journal_id'] }})"
                                                            style="font-size: 10px; padding: 2px 6px;">Lihat
                                                            Lampiran</button>
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                                <td style="border: 1px solid #dee2e6; padding: 4px; font-size: 12px;">
                                                    {{ $history['proof_number'] ?? '-' }}</td>
                                                <td
                                                    style="border: 1px solid #dee2e6; padding: 4px; font-size: 12px; text-align: right;">
                                                    {{ $history['cash_in'] > 0 ? number_format($history['cash_in'], 0, ',', '.') : '' }}
                                                </td>
                                                <td
                                                    style="border: 1px solid #dee2e6; padding: 4px; font-size: 12px; text-align: right;">
                                                    {{ $history['cash_out'] > 0 ? number_format($history['cash_out'], 0, ',', '.') : '' }}
                                                </td>
                                                <td
                                                    style="border: 1px solid #dee2e6; padding: 4px; font-size: 12px; text-align: right; background: #e3f2fd;">
                                                    {{ number_format($history['balance'], 0, ',', '.') }}</td>
                                                <td style="border: 1px solid #dee2e6; padding: 4px; font-size: 12px;">
                                                    {{ ($history['cashflow_code'] ?? '-') . ' - ' . ($history['cashflow_desc'] ?? '-') }}</td>
                                                <td style="border: 1px solid #dee2e6; padding: 4px; font-size: 12px;">
                                                    {{ $history['debit_account'] }}</td>
                                                <td style="border: 1px solid #dee2e6; padding: 4px; font-size: 12px;">
                                                    {{ $history['credit_account'] }}</td>
                                                <td style="border: 1px solid #dee2e6; padding: 4px; text-align: center;">
                                                    <button type="button" class="btn btn-sm btn-warning me-1"
                                                        onclick="editTransaction(this, {{ $history['journal_id'] }})"
                                                        style="font-size: 10px; padding: 2px 6px;">✎</button>
                                                    @if($history['can_create_asset'] ?? false)
                                                        <button type="button" class="btn btn-sm btn-success me-1" onclick="createAssetFromTransaction({{ $history['journal_id'] }})" style="font-size: 10px; padding: 2px 6px;" title="Buat Aset Tetap">+</button>
                                                    @else
                                                        @if(isset($history['journal_id']) && \App\Models\Journal::find($history['journal_id'])?->fixed_asset_id)
                                                            <span class="badge bg-success" style="font-size: 9px;" title="Aset sudah dibuat">✓ Aset</span>
                                                        @endif
                                                    @endif
                                                    <button type="button" class="btn btn-sm btn-danger"
                                                        onclick="deleteTransaction({{ $history['journal_id'] }})"
                                                        style="font-size: 10px; padding: 2px 6px;">×</button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-5">
                                <p class="text-muted">Pilih akun kas/bank dari menu di atas untuk memulai membuat jurnal.
                                </p>
                            </div>
                        @endif

                        @error('entries')
                            <div class="alert alert-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="card-footer text-end">
                        @if ($selectedAccount)
                            <button type="button" class="btn btn-success me-2" onclick="addJournalLine()">+ Tambah
                                Baris</button>
                            <button type="submit" class="btn btn-primary">Simpan</button>
                        @endif
                    </div>
                </div>
            </form>
        </div>
    </div>

    @if ($selectedAccount)
        @push('scripts')
            <script>
                let lineIndex = 0;
                let openingBalance = {{ $openingBalance }};
                let selectedCashAccountId = {{ $selectedAccount ? $selectedAccount->id : 'null' }};
                let selectedAccountName = {!! json_encode($selectedAccount ? $selectedAccount->kode . ' - ' . $selectedAccount->keterangan : '') !!};
                let currentBalance = openingBalance;
                const formatter = new Intl.NumberFormat('id-ID');

                // Build account options
                let accountOptions = '<option value="">Pilih Akun</option>';
                @foreach ($accounts as $account)
                    accountOptions += '<option value="{{ $account->id }}">{{ str_replace(["'", '"'], ["\'", '\"'], $account->kode . ' - ' . $account->keterangan) }}</option>';
                @endforeach

                // Build cashflow options
                let cashflowOptions = '<option value="">Pilih Kode & Akun CF</option>';
                @foreach ($cashflows as $cashflow)
                    cashflowOptions += '<option value="{{ $cashflow->id }}" data-description="{{ str_replace(["'", '"'], ["\'", '\"'], $cashflow->keterangan) }}">{{ str_replace(["'", '"'], ["\'", '\"'], $cashflow->kode . ' - ' . $cashflow->keterangan) }}</option>';
                @endforeach

                // Build cashflow data object
                const cashflowData = {
                    @foreach ($cashflows as $cashflow)
                        '{{ $cashflow->id }}': {
                            code: '{{ str_replace(["'", '"'], ["\'", '\"'], $cashflow->kode) }}',
                            description: '{{ str_replace(["'", '"'], ["\'", '\"'], $cashflow->keterangan) }}',
                            trial_balance_id: {{ $cashflow->trial_balance_id ?? 'null' }}
                        }@if(!$loop->last),@endif
                    @endforeach
                };
                
                // Build account data array
                const accountData = [
                    @foreach ($accounts as $account)
                        {
                            id: '{{ $account->id }}',
                            text: '{{ str_replace(["'", '"'], ["\'", '\"'], $account->kode . ' - ' . $account->keterangan) }}'
                        }@if(!$loop->last),@endif
                    @endforeach
                ];
                
                // Build cashflow data array
                const cashflowDataArray = [
                    @foreach ($cashflows as $cashflow)
                        {
                            id: '{{ $cashflow->id }}',
                            text: '{{ str_replace(["'", '"'], ["\'", '\"'], $cashflow->kode . ' - ' . $cashflow->keterangan) }}'
                        }@if(!$loop->last),@endif
                    @endforeach
                ];

                function showAlert(type, message) {
                    const container = document.getElementById('alert-container');
                    const alert = document.createElement('div');
                    alert.className = `alert alert-${type === 'success' ? 'success' : 'danger'} alert-dismissible`;
                    alert.innerHTML = `
                        <div class="d-flex">
                            <div>${message}</div>
                        </div>
                        <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
                    `;
                    
                    container.innerHTML = '';
                    container.appendChild(alert);
                    
                    setTimeout(() => {
                        if (alert.parentNode) {
                            alert.remove();
                        }
                    }, 5000);
                }

                document.addEventListener('DOMContentLoaded', function() {
                    console.log('Cashflow data:', cashflowData);
                    console.log('Account options length:', accountOptions.length);
                    console.log('Cashflow options length:', cashflowOptions.length);
                    console.log('Selected account ID:', selectedCashAccountId);
                    
                    const historyRows = document.querySelectorAll('tr[data-existing="1"]');
                    if (historyRows.length > 0) {
                        const lastRow = historyRows[historyRows.length - 1];
                        currentBalance = parseFloat(lastRow.getAttribute('data-balance'));
                    }
                    addJournalLine();
                });

                function addJournalLine() {
                    const tbody = document.getElementById('journalLines');
                    const row = document.createElement('tr');
                    row.style.border = '1px solid #dee2e6';
                    
                    const currentDate = '{{ date('Y-m-d') }}';
                    const currentIndex = lineIndex;
                    
                    row.innerHTML = 
                        '<td style="border: 1px solid #dee2e6; padding: 2px;">' +
                            '<input type="date" name="entries[' + currentIndex + '][date]" class="form-control form-control-sm" style="border: none; font-size: 12px;" value="' + currentDate + '">' +
                        '</td>' +
                        '<td style="border: 1px solid #dee2e6; padding: 2px;">' +
                            '<input type="text" name="entries[' + currentIndex + '][description]" class="form-control form-control-sm" style="border: none; font-size: 12px;" placeholder="Deskripsi">' +
                        '</td>' +
                        '<td style="border: 1px solid #dee2e6; padding: 2px;">' +
                            '<input type="text" name="entries[' + currentIndex + '][pic]" class="form-control form-control-sm" style="border: none; font-size: 12px;" placeholder="PIC">' +
                        '</td>' +
                        '<td style="border: 1px solid #dee2e6; padding: 2px;">' +
                            '<input type="file" name="entries[' + currentIndex + '][attachments][]" class="form-control form-control-sm" style="border: none; font-size: 11px;" accept=".jpg,.jpeg,.png,.pdf" multiple onchange="generateProofNumber(this, ' + currentIndex + ')">' +
                        '</td>' +
                        '<td style="border: 1px solid #dee2e6; padding: 2px;">' +
                            '<input type="text" name="entries[' + currentIndex + '][proof_number]" class="form-control form-control-sm proof-number" style="border: none; font-size: 12px;" placeholder="Auto" readonly>' +
                        '</td>' +
                        '<td style="border: 1px solid #dee2e6; padding: 2px;">' +
                            '<input type="number" name="entries[' + currentIndex + '][cash_in]" class="form-control form-control-sm cash-in" style="border: none; font-size: 12px; text-align: right;" placeholder="0" min="0" step="1" onchange="calculateBalance(this); updateProofNumber(this)" oninput="handleCashInput(this, \'in\')">' +
                        '</td>' +
                        '<td style="border: 1px solid #dee2e6; padding: 2px;">' +
                            '<input type="number" name="entries[' + currentIndex + '][cash_out]" class="form-control form-control-sm cash-out" style="border: none; font-size: 12px; text-align: right;" placeholder="0" min="0" step="1" onchange="calculateBalance(this); updateProofNumber(this)" oninput="handleCashInput(this, \'out\')">' +
                        '</td>' +
                        '<td style="border: 1px solid #dee2e6; padding: 2px; text-align: right; background: #e8f5e8;">' +
                            '<span class="balance-display" style="font-size: 12px; font-weight: bold;">' + formatter.format(currentBalance) + '</span>' +
                        '</td>' +
                        '<td style="border: 1px solid #dee2e6; padding: 2px;">' +
                            '<div class="searchable-select">' +
                                '<input type="text" class="form-control form-control-sm cashflow-input" placeholder="Pilih Kode & Akun CF" style="border: none; font-size: 12px;" readonly onclick="toggleDropdown(this)">' +
                                '<input type="hidden" name="entries[' + currentIndex + '][cashflow_id]" class="cashflow-select">' +
                                '<div class="dropdown-list cashflow-dropdown"></div>' +
                            '</div>' +
                        '</td>' +
                        '<td style="border: 1px solid #dee2e6; padding: 2px;">' +
                            '<div class="searchable-select">' +
                                '<input type="text" class="form-control form-control-sm debit-input" placeholder="Pilih Akun Debit" style="border: none; font-size: 12px;" readonly onclick="toggleDropdown(this)">' +
                                '<input type="hidden" name="entries[' + currentIndex + '][debit_account_id]" class="debit-account">' +
                                '<div class="dropdown-list debit-dropdown"></div>' +
                            '</div>' +
                        '</td>' +
                        '<td style="border: 1px solid #dee2e6; padding: 2px;">' +
                            '<div class="searchable-select">' +
                                '<input type="text" class="form-control form-control-sm credit-input" placeholder="Pilih Akun Kredit" style="border: none; font-size: 12px;" readonly onclick="toggleDropdown(this)">' +
                                '<input type="hidden" name="entries[' + currentIndex + '][credit_account_id]" class="credit-account">' +
                                '<div class="dropdown-list credit-dropdown"></div>' +
                            '</div>' +
                        '</td>' +
                        '<td style="border: 1px solid #dee2e6; padding: 2px; text-align: center;">' +
                            '-' +
                        '</td>';
                    
                    tbody.appendChild(row);
                    
                    // Initialize dropdowns for the new row
                    initializeDropdowns(row);
                    
                    lineIndex++;
                }

                function handleCashInput(input, type) {
                    const row = input.closest('tr');
                    const cashInInput = row.querySelector('.cash-in');
                    const cashOutInput = row.querySelector('.cash-out');
                    const debitInput = row.querySelector('.debit-input');
                    const creditInput = row.querySelector('.credit-input');
                    const debitHidden = row.querySelector('.debit-account');
                    const creditHidden = row.querySelector('.credit-account');
                    const cashflowHidden = row.querySelector('.cashflow-select');

                    if (input.value && parseFloat(input.value) > 0) {
                        if (type === 'in') {
                            cashOutInput.value = '';
                            // Set debit to cash account
                            setDropdownValue(row, 'debit', selectedCashAccountId, selectedAccountName);
                            debitInput.style.backgroundColor = '#e9ecef';
                            debitInput.disabled = true;
                            creditInput.disabled = false;
                            creditInput.style.backgroundColor = '';
                            
                            // Auto-set trial balance account if cashflow is selected
                            handleCashflowSelection(row, cashflowHidden.value);
                        } else {
                            cashInInput.value = '';
                            // Set credit to cash account
                            setDropdownValue(row, 'credit', selectedCashAccountId, selectedAccountName);
                            creditInput.style.backgroundColor = '#e9ecef';
                            creditInput.disabled = true;
                            debitInput.disabled = false;
                            debitInput.style.backgroundColor = '';
                            
                            // Auto-set trial balance account if cashflow is selected
                            handleCashflowSelection(row, cashflowHidden.value);
                        }
                    } else {
                        debitInput.disabled = false;
                        creditInput.disabled = false;
                        debitInput.style.backgroundColor = '';
                        creditInput.style.backgroundColor = '';
                        clearDropdownValue(row, 'debit');
                        clearDropdownValue(row, 'credit');
                    }
                }

                function calculateBalance(input) {
                    const row = input.closest('tr');
                    const cashInInput = row.querySelector('.cash-in');
                    const cashOutInput = row.querySelector('.cash-out');
                    const balanceDisplay = row.querySelector('.balance-display');

                    let prevBalance = currentBalance;
                    const prevRow = row.previousElementSibling;
                    if (prevRow) {
                        if (prevRow.hasAttribute('data-existing')) {
                            prevBalance = parseFloat(prevRow.getAttribute('data-balance'));
                        } else {
                            const prevBalanceDisplay = prevRow.querySelector('.balance-display');
                            if (prevBalanceDisplay) {
                                prevBalance = parseFloat(prevBalanceDisplay.textContent.replace(/[^0-9.-]/g, ''));
                            }
                        }
                    }

                    const cashIn = parseFloat(cashInInput.value) || 0;
                    const cashOut = parseFloat(cashOutInput.value) || 0;
                    const newBalance = prevBalance + cashIn - cashOut;

                    balanceDisplay.textContent = formatter.format(newBalance);

                    let nextRow = row.nextElementSibling;
                    let runningBalance = newBalance;

                    while (nextRow && !nextRow.hasAttribute('data-existing')) {
                        const nextCashIn = parseFloat(nextRow.querySelector('.cash-in').value) || 0;
                        const nextCashOut = parseFloat(nextRow.querySelector('.cash-out').value) || 0;
                        runningBalance = runningBalance + nextCashIn - nextCashOut;

                        const nextBalanceDisplay = nextRow.querySelector('.balance-display');
                        if (nextBalanceDisplay) {
                            nextBalanceDisplay.textContent = formatter.format(runningBalance);
                        }
                        nextRow = nextRow.nextElementSibling;
                    }
                }

                function generateProofNumber(input, index) {
                    if (input.files.length > 0) {
                        updateProofNumber(input);
                    } else {
                        const row = input.closest('tr');
                        const proofInput = row.querySelector('.proof-number');
                        proofInput.value = '';
                    }
                }

                function updateProofNumber(input) {
                    const row = input.closest('tr');
                    const cashIn = parseFloat(row.querySelector('.cash-in').value) || 0;
                    const cashOut = parseFloat(row.querySelector('.cash-out').value) || 0;
                    const proofInput = row.querySelector('.proof-number');
                    const dateInput = row.querySelector('input[name*="[date]"]');

                    if (cashIn > 0 || cashOut > 0) {
                        const transactionType = cashIn > 0 ? 'M' : 'K';
                        const transactionDate = new Date(dateInput.value);
                        const day = transactionDate.getDate().toString().padStart(2, '0');
                        const month = (transactionDate.getMonth() + 1).toString().padStart(2, '0');
                        const year = transactionDate.getFullYear().toString().slice(-2);
                        const dateStr = `${day}-${month}-${year}`;

                        let sameTypeCount = 1;
                        const historyRows = document.querySelectorAll('tr[data-existing="1"]');
                        historyRows.forEach(historyRow => {
                            const historyProofCell = historyRow.children[4];
                            if (historyProofCell && historyProofCell.textContent) {
                                const proofText = historyProofCell.textContent.trim();
                                if (proofText.includes(`/${transactionType}/`) && proofText.includes(dateStr)) {
                                    sameTypeCount++;
                                }
                            }
                        });

                        const currentRows = document.querySelectorAll('input[name*="[proof_number]"]');
                        currentRows.forEach(inp => {
                            if (inp !== proofInput && inp.value && inp.value.includes(`/${transactionType}/`) && inp.value.includes(dateStr)) {
                                sameTypeCount++;
                            }
                        });

                        const transactionNumber = sameTypeCount.toString().padStart(3, '0');
                        const proofNumber = `${selectedAccountName}/${transactionType}/${transactionNumber}-${dateStr}`;
                        proofInput.value = proofNumber;
                    } else {
                        proofInput.value = '';
                    }
                }

                function viewAttachments(journalId) {
                    const modal = document.createElement('div');
                    modal.className = 'modal fade';
                    modal.id = 'attachmentModal';
                    modal.innerHTML = `
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">File Lampiran</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body" id="attachmentsList">
                                    <div class="text-center">Loading...</div>
                                </div>
                            </div>
                        </div>
                    `;
                    document.body.appendChild(modal);

                    const bsModal = new bootstrap.Modal(modal);
                    bsModal.show();

                    fetch(`/journals/${journalId}/attachments`)
                        .then(response => response.json())
                        .then(data => {
                            const list = document.getElementById('attachmentsList');
                            if (data.attachments && data.attachments.length > 0) {
                                list.innerHTML = data.attachments.map(att => `
                                    <div class="mb-2">
                                        <a href="/storage/${att.file_path}" target="_blank" class="btn btn-outline-primary btn-sm">
                                            📎 ${att.original_name}
                                        </a>
                                        <small class="text-muted ms-2">(${(att.file_size/1024).toFixed(1)} KB)</small>
                                    </div>
                                `).join('');
                            } else {
                                list.innerHTML = '<p class="text-muted">Tidak ada file lampiran</p>';
                            }
                        })
                        .catch(error => {
                            document.getElementById('attachmentsList').innerHTML = '<p class="text-danger">Error loading attachments</p>';
                        });

                    modal.addEventListener('hidden.bs.modal', () => {
                        document.body.removeChild(modal);
                    });
                }

                // Searchable dropdown functions
                function initializeDropdowns(row) {
                    const cashflowDropdown = row.querySelector('.cashflow-dropdown');
                    const debitDropdown = row.querySelector('.debit-dropdown');
                    const creditDropdown = row.querySelector('.credit-dropdown');
                    
                    // Populate cashflow dropdown
                    cashflowDropdown.innerHTML = cashflowDataArray.map(item => 
                        `<div class="dropdown-item" data-value="${item.id}">${item.text}</div>`
                    ).join('');
                    
                    // Populate account dropdowns
                    const accountItems = accountData.map(item => 
                        `<div class="dropdown-item" data-value="${item.id}">${item.text}</div>`
                    ).join('');
                    
                    debitDropdown.innerHTML = accountItems;
                    creditDropdown.innerHTML = accountItems;
                    
                    // Add click handlers
                    cashflowDropdown.addEventListener('click', (e) => {
                        if (e.target.classList.contains('dropdown-item')) {
                            selectDropdownItem(row, 'cashflow', e.target.dataset.value, e.target.textContent);
                            handleCashflowSelection(row, e.target.dataset.value);
                        }
                    });
                    
                    debitDropdown.addEventListener('click', (e) => {
                        if (e.target.classList.contains('dropdown-item')) {
                            selectDropdownItem(row, 'debit', e.target.dataset.value, e.target.textContent);
                        }
                    });
                    
                    creditDropdown.addEventListener('click', (e) => {
                        if (e.target.classList.contains('dropdown-item')) {
                            selectDropdownItem(row, 'credit', e.target.dataset.value, e.target.textContent);
                        }
                    });
                }
                
                function toggleDropdown(input) {
                    const dropdown = input.nextElementSibling.nextElementSibling;
                    const isVisible = dropdown.style.display === 'block';
                    
                    // Hide all other dropdowns
                    document.querySelectorAll('.dropdown-list').forEach(d => d.style.display = 'none');
                    
                    if (!isVisible) {
                        // Position dropdown relative to input with viewport awareness
                        const rect = input.getBoundingClientRect();
                        const viewportHeight = window.innerHeight;
                        const dropdownHeight = 200; // max-height from CSS
                        
                        let top = rect.bottom + window.scrollY;
                        
                        // Check if dropdown would go below viewport
                        if (rect.bottom + dropdownHeight > viewportHeight) {
                            // Position above input if there's more space
                            if (rect.top > dropdownHeight) {
                                top = rect.top + window.scrollY - dropdownHeight;
                            } else {
                                // Keep below but adjust to fit viewport
                                top = window.scrollY + viewportHeight - dropdownHeight - 10;
                            }
                        }
                        
                        dropdown.style.left = rect.left + 'px';
                        dropdown.style.top = top + 'px';
                        dropdown.style.width = Math.max(rect.width, 200) + 'px';
                        
                        // Make input editable for search
                        input.readOnly = false;
                        input.focus();
                        input.select();
                        
                        // Show dropdown
                        dropdown.style.display = 'block';
                        
                        // Add search functionality
                        const originalValue = input.value;
                        input.addEventListener('input', function searchHandler(e) {
                            const searchTerm = e.target.value.toLowerCase();
                            const items = dropdown.querySelectorAll('.dropdown-item');
                            
                            items.forEach(item => {
                                const text = item.textContent.toLowerCase();
                                item.style.display = text.includes(searchTerm) ? 'block' : 'none';
                            });
                        });
                        
                        // Handle blur to restore readonly and original value if no selection
                        input.addEventListener('blur', function blurHandler(e) {
                            setTimeout(() => {
                                if (!dropdown.contains(document.activeElement)) {
                                    input.readOnly = true;
                                    dropdown.style.display = 'none';
                                    
                                    // Restore original value if no valid selection
                                    const hidden = input.nextElementSibling;
                                    if (!hidden.value) {
                                        input.value = originalValue;
                                    }
                                    
                                    // Remove event listeners
                                    input.removeEventListener('input', searchHandler);
                                    input.removeEventListener('blur', blurHandler);
                                }
                            }, 150);
                        });
                    }
                }
                
                function selectDropdownItem(row, type, value, text) {
                    const input = row.querySelector(`.${type}-input`);
                    const hidden = row.querySelector(`.${type}-account, .${type}-select`);
                    const dropdown = row.querySelector(`.${type}-dropdown`);
                    
                    input.value = text;
                    input.readOnly = true;
                    hidden.value = value;
                    dropdown.style.display = 'none';
                    
                    // Show all items again for next search
                    dropdown.querySelectorAll('.dropdown-item').forEach(item => {
                        item.style.display = 'block';
                    });
                }
                
                function setDropdownValue(row, type, value, text) {
                    const input = row.querySelector(`.${type}-input`);
                    const hidden = row.querySelector(`.${type}-account`);
                    
                    if (input && hidden) {
                        input.value = text;
                        hidden.value = value;
                    }
                }
                
                function clearDropdownValue(row, type) {
                    const input = row.querySelector(`.${type}-input`);
                    const hidden = row.querySelector(`.${type}-account`);
                    
                    if (input && hidden) {
                        input.value = '';
                        hidden.value = '';
                    }
                }
                
                function findAccountById(id) {
                    return accountData.find(account => account.id == id);
                }
                
                function findCashflowById(id) {
                    return cashflowDataArray.find(cashflow => cashflow.id == id);
                }
                
                function updateCashflowDescription(selectElement) {
                    const row = selectElement.closest('tr');
                    const selectedId = selectElement.value;

                    if (selectedId && cashflowData && cashflowData[selectedId]) {
                        // Auto-set trial balance account if available
                        if (cashflowData[selectedId].trial_balance_id) {
                            setTrialBalanceAccount(row, cashflowData[selectedId].trial_balance_id);
                        }
                    }
                }

                function setTrialBalanceAccount(row, trialBalanceId) {
                    const cashInInput = row.querySelector('.cash-in');
                    const cashOutInput = row.querySelector('.cash-out');
                    const debitSelect = row.querySelector('select[name*="[debit_account_id]"]');
                    const creditSelect = row.querySelector('select[name*="[credit_account_id]"]');
                    
                    const cashIn = parseFloat(cashInInput.value) || 0;
                    const cashOut = parseFloat(cashOutInput.value) || 0;
                    
                    if (cashIn > 0) {
                        // For cash in, set credit account to trial balance account
                        creditSelect.value = trialBalanceId;
                    } else if (cashOut > 0) {
                        // For cash out, set debit account to trial balance account
                        debitSelect.value = trialBalanceId;
                    }
                }

                function handleCashflowSelection(row, cashflowId) {
                    if (!cashflowId || !cashflowData[cashflowId] || !cashflowData[cashflowId].trial_balance_id) {
                        return;
                    }
                    
                    const trialBalanceAccount = findAccountById(cashflowData[cashflowId].trial_balance_id);
                    if (!trialBalanceAccount) {
                        return;
                    }
                    
                    const cashInInput = row.querySelector('.cash-in');
                    const cashOutInput = row.querySelector('.cash-out');
                    const cashIn = parseFloat(cashInInput.value) || 0;
                    const cashOut = parseFloat(cashOutInput.value) || 0;
                    
                    if (cashIn > 0) {
                        // Cash in: Debit = Cash Account, Credit = Trial Balance Account
                        setDropdownValue(row, 'credit', trialBalanceAccount.id, trialBalanceAccount.text);
                    } else if (cashOut > 0) {
                        // Cash out: Debit = Trial Balance Account, Credit = Cash Account
                        setDropdownValue(row, 'debit', trialBalanceAccount.id, trialBalanceAccount.text);
                    } else {
                        // No cash amount yet, set both possibilities
                        // User will choose cash in/out later and it will auto-set correctly
                    }
                }
                
                // Close dropdowns when clicking outside
                document.addEventListener('click', (e) => {
                    if (!e.target.closest('.searchable-select')) {
                        document.querySelectorAll('.dropdown-list').forEach(d => d.style.display = 'none');
                    }
                });

                function editTransaction(button, journalId) {
                    const row = button.closest('tr');
                    const isEditing = row.classList.contains('editing');
                    
                    if (isEditing) {
                        // Save changes
                        saveTransaction(row, journalId);
                    } else {
                        // Enter edit mode
                        enterEditMode(row, button);
                    }
                }

                function enterEditMode(row, editButton) {
                    row.classList.add('editing');
                    row.style.backgroundColor = '#fff3cd';
                    
                    const cells = row.children;
                    const journalId = row.getAttribute('data-journal-id');
                    
                    // Get current values
                    const currentDate = cells[0].textContent.trim();
                    const description = cells[1].textContent.trim();
                    const pic = cells[2].textContent.trim() === '-' ? '' : cells[2].textContent.trim();
                    const proofNumber = cells[4].textContent.trim() === '-' ? '' : cells[4].textContent.trim();
                    const cashIn = cells[5].textContent.replace(/[^0-9]/g, '') || '0';
                    const cashOut = cells[6].textContent.replace(/[^0-9]/g, '') || '0';
                    const cashflowCode = cells[8].textContent.trim();
                    const debitAccount = cells[9].textContent.trim();
                    const creditAccount = cells[10].textContent.trim();
                    
                    // Convert date format from d/m/Y to Y-m-d
                    const dateParts = currentDate.split('/');
                    const formattedDate = dateParts.length === 3 ? `${dateParts[2]}-${dateParts[1].padStart(2, '0')}-${dateParts[0].padStart(2, '0')}` : currentDate;
                    
                    // Convert to editable fields
                    cells[0].innerHTML = `<input type="date" class="form-control form-control-sm edit-date" value="${formattedDate}" style="font-size: 12px; border: none;">`;
                    cells[1].innerHTML = `<input type="text" class="form-control form-control-sm edit-description" value="${description}" style="font-size: 12px; border: none;">`;
                    cells[2].innerHTML = `<input type="text" class="form-control form-control-sm edit-pic" value="${pic}" style="font-size: 12px; border: none;">`;
                    cells[3].innerHTML = `<input type="file" class="form-control form-control-sm edit-attachments" accept=".jpg,.jpeg,.png,.pdf" multiple style="font-size: 11px; border: none;">`;
                    cells[4].innerHTML = `<input type="text" class="form-control form-control-sm edit-proof" value="${proofNumber}" style="font-size: 12px; border: none;">`;
                    cells[5].innerHTML = `<input type="number" class="form-control form-control-sm edit-cash-in cash-in" value="${cashIn}" style="font-size: 12px; text-align: right; border: none;" min="0" oninput="handleEditCashInput(this, 'in')">`;
                    cells[6].innerHTML = `<input type="number" class="form-control form-control-sm edit-cash-out cash-out" value="${cashOut}" style="font-size: 12px; text-align: right; border: none;" min="0" oninput="handleEditCashInput(this, 'out')">`;
                    cells[8].innerHTML = `
                        <div class="searchable-select">
                            <input type="text" class="form-control form-control-sm edit-cashflow-input" placeholder="Pilih Kode & Akun CF" style="border: none; font-size: 12px;" readonly onclick="toggleDropdown(this)">
                            <input type="hidden" class="edit-cashflow cashflow-select">
                            <div class="dropdown-list edit-cashflow-dropdown"></div>
                        </div>`;
                    cells[9].innerHTML = `
                        <div class="searchable-select">
                            <input type="text" class="form-control form-control-sm edit-debit-input" placeholder="Pilih Akun Debit" style="border: none; font-size: 12px;" readonly onclick="toggleDropdown(this)">
                            <input type="hidden" class="edit-debit debit-account">
                            <div class="dropdown-list edit-debit-dropdown"></div>
                        </div>`;
                    cells[10].innerHTML = `
                        <div class="searchable-select">
                            <input type="text" class="form-control form-control-sm edit-credit-input" placeholder="Pilih Akun Kredit" style="border: none; font-size: 12px;" readonly onclick="toggleDropdown(this)">
                            <input type="hidden" class="edit-credit credit-account">
                            <div class="dropdown-list edit-credit-dropdown"></div>
                        </div>`;
                    
                    // Initialize edit dropdowns
                    initializeEditDropdowns(row);
                    
                    // Set current selections
                    setTimeout(() => {
                        // Set cashflow selection by text
                        const combinedCashflowText = cashflowCode;
                        const cashflowItem = cashflowDataArray.find(item => item.text === combinedCashflowText);
                        if (cashflowItem) {
                            setEditDropdownValue(row, 'cashflow', cashflowItem.id, cashflowItem.text);
                        }
                        
                        // Set account selections by text
                        const debitItem = accountData.find(item => item.text === debitAccount);
                        if (debitItem) {
                            setEditDropdownValue(row, 'debit', debitItem.id, debitItem.text);
                        }
                        
                        const creditItem = accountData.find(item => item.text === creditAccount);
                        if (creditItem) {
                            setEditDropdownValue(row, 'credit', creditItem.id, creditItem.text);
                        }
                    }, 10);
                    
                    // Change button to save
                    editButton.innerHTML = '✓';
                    editButton.className = 'btn btn-sm btn-success me-1';
                    editButton.title = 'Simpan';
                }

                function saveTransaction(row, journalId) {
                    const formData = new FormData();
                    
                    formData.append('date', row.querySelector('.edit-date').value);
                    formData.append('description', row.querySelector('.edit-description').value);
                    formData.append('pic', row.querySelector('.edit-pic').value);
                    formData.append('proof_number', row.querySelector('.edit-proof').value);
                    formData.append('cash_in', row.querySelector('.edit-cash-in').value || 0);
                    formData.append('cash_out', row.querySelector('.edit-cash-out').value || 0);
                    formData.append('cashflow_id', row.querySelector('.edit-cashflow').value);
                    formData.append('debit_account_id', row.querySelector('.edit-debit').value);
                    formData.append('credit_account_id', row.querySelector('.edit-credit').value);
                    formData.append('_method', 'PUT');
                    
                    // Handle file attachments
                    const fileInput = row.querySelector('.edit-attachments');
                    if (fileInput.files.length > 0) {
                        for (let i = 0; i < fileInput.files.length; i++) {
                            formData.append('attachments[]', fileInput.files[i]);
                        }
                    }
                    
                    fetch(`/journals/${journalId}`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Accept': 'application/json'
                        },
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            showAlert('success', 'Transaksi berhasil diperbarui!');
                            setTimeout(() => window.location.reload(), 1500);
                        } else {
                            showAlert('error', data.message || 'Gagal memperbarui transaksi');
                        }
                    })
                    .catch(error => {
                        showAlert('error', 'Gagal memperbarui transaksi');
                    });
                }

                function initializeEditDropdowns(row) {
                    const cashflowDropdown = row.querySelector('.edit-cashflow-dropdown');
                    const debitDropdown = row.querySelector('.edit-debit-dropdown');
                    const creditDropdown = row.querySelector('.edit-credit-dropdown');
                    
                    // Populate dropdowns
                    cashflowDropdown.innerHTML = cashflowDataArray.map(item => 
                        `<div class="dropdown-item" data-value="${item.id}">${item.text}</div>`
                    ).join('');
                    
                    const accountItems = accountData.map(item => 
                        `<div class="dropdown-item" data-value="${item.id}">${item.text}</div>`
                    ).join('');
                    
                    debitDropdown.innerHTML = accountItems;
                    creditDropdown.innerHTML = accountItems;
                    
                    // Add click handlers
                    cashflowDropdown.addEventListener('click', (e) => {
                        if (e.target.classList.contains('dropdown-item')) {
                            setEditDropdownValue(row, 'cashflow', e.target.dataset.value, e.target.textContent);
                            handleEditCashflowSelection(row, e.target.dataset.value);
                        }
                    });
                    
                    debitDropdown.addEventListener('click', (e) => {
                        if (e.target.classList.contains('dropdown-item')) {
                            setEditDropdownValue(row, 'debit', e.target.dataset.value, e.target.textContent);
                        }
                    });
                    
                    creditDropdown.addEventListener('click', (e) => {
                        if (e.target.classList.contains('dropdown-item')) {
                            setEditDropdownValue(row, 'credit', e.target.dataset.value, e.target.textContent);
                        }
                    });
                }
                
                function setEditDropdownValue(row, type, value, text) {
                    const input = row.querySelector(`.edit-${type}-input`);
                    const hidden = row.querySelector(`.edit-${type}`);
                    const dropdown = row.querySelector(`.edit-${type}-dropdown`);
                    
                    if (input && hidden) {
                        input.value = text;
                        input.readOnly = true;
                        hidden.value = value;
                        if (dropdown) {
                            dropdown.style.display = 'none';
                            // Show all items again for next search
                            dropdown.querySelectorAll('.dropdown-item').forEach(item => {
                                item.style.display = 'block';
                            });
                        }
                    }
                }
                
                function handleEditCashInput(input, type) {
                    const row = input.closest('tr');
                    const cashInInput = row.querySelector('.edit-cash-in');
                    const cashOutInput = row.querySelector('.edit-cash-out');
                    const debitInput = row.querySelector('.edit-debit-input');
                    const creditInput = row.querySelector('.edit-credit-input');
                    const cashflowHidden = row.querySelector('.edit-cashflow');

                    if (input.value && parseFloat(input.value) > 0) {
                        if (type === 'in') {
                            cashOutInput.value = '';
                            setEditDropdownValue(row, 'debit', selectedCashAccountId, selectedAccountName);
                            debitInput.disabled = true;
                            debitInput.style.backgroundColor = '#e9ecef';
                            creditInput.disabled = false;
                            creditInput.style.backgroundColor = '';
                            
                            // Auto-set trial balance account if cashflow is selected
                            handleEditCashflowSelection(row, cashflowHidden.value);
                        } else {
                            cashInInput.value = '';
                            setEditDropdownValue(row, 'credit', selectedCashAccountId, selectedAccountName);
                            creditInput.disabled = true;
                            creditInput.style.backgroundColor = '#e9ecef';
                            debitInput.disabled = false;
                            debitInput.style.backgroundColor = '';
                            
                            // Auto-set trial balance account if cashflow is selected
                            handleEditCashflowSelection(row, cashflowHidden.value);
                        }
                    } else {
                        debitInput.disabled = false;
                        creditInput.disabled = false;
                        debitInput.style.backgroundColor = '';
                        creditInput.style.backgroundColor = '';
                        setEditDropdownValue(row, 'debit', '', '');
                        setEditDropdownValue(row, 'credit', '', '');
                    }
                }

                function deleteTransaction(journalId) {
                    showConfirmModal(
                        'Konfirmasi Hapus',
                        'Apakah Anda yakin ingin menghapus transaksi ini?',
                        () => {
                            fetch(`/journals/${journalId}`, {
                                method: 'DELETE',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                    'Accept': 'application/json'
                                }
                            })
                            .then(response => response.json())
                            .then(data => {
                                showAlert('success', 'Transaksi berhasil dihapus!');
                                setTimeout(() => window.location.reload(), 1500);
                            })
                            .catch(error => {
                                showAlert('error', 'Gagal menghapus transaksi');
                            });
                        }
                    );
                }

                function handleEditCashflowSelection(row, cashflowId) {
                    if (!cashflowId || !cashflowData[cashflowId] || !cashflowData[cashflowId].trial_balance_id) {
                        return;
                    }
                    
                    const trialBalanceAccount = findAccountById(cashflowData[cashflowId].trial_balance_id);
                    if (!trialBalanceAccount) {
                        return;
                    }
                    
                    const cashInInput = row.querySelector('.edit-cash-in');
                    const cashOutInput = row.querySelector('.edit-cash-out');
                    const cashIn = parseFloat(cashInInput.value) || 0;
                    const cashOut = parseFloat(cashOutInput.value) || 0;
                    
                    if (cashIn > 0) {
                        // Cash in: Debit = Cash Account, Credit = Trial Balance Account
                        setEditDropdownValue(row, 'credit', trialBalanceAccount.id, trialBalanceAccount.text);
                    } else if (cashOut > 0) {
                        // Cash out: Debit = Trial Balance Account, Credit = Cash Account
                        setEditDropdownValue(row, 'debit', trialBalanceAccount.id, trialBalanceAccount.text);
                    }
                }

                function showConfirmModal(title, message, onConfirm) {
                    const modal = document.createElement('div');
                    modal.className = 'modal modal-blur fade';
                    modal.innerHTML = `
                        <div class="modal-dialog modal-sm modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-body">
                                    <div class="modal-title">${title}</div>
                                    <div>${message}</div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                    <button type="button" class="btn btn-danger" id="confirm-btn">Ya, Hapus</button>
                                </div>
                            </div>
                        </div>
                    `;
                    document.body.appendChild(modal);
                    
                    const bsModal = new bootstrap.Modal(modal);
                    bsModal.show();
                    
                    modal.querySelector('#confirm-btn').addEventListener('click', () => {
                        bsModal.hide();
                        onConfirm();
                    });
                    
                    modal.addEventListener('hidden.bs.modal', () => {
                        document.body.removeChild(modal);
                    });
                }

                function showSuccessModal(title, message, onClose = null) {
                    const modal = document.createElement('div');
                    modal.className = 'modal modal-blur fade';
                    modal.innerHTML = `
                        <div class="modal-dialog modal-sm modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-body text-center">
                                    <div class="text-success mb-3">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-check" width="48" height="48" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="m0 0h24v24H0z" fill="none"></path>
                                            <path d="m5 12l5 5l10 -10"></path>
                                        </svg>
                                    </div>
                                    <div class="modal-title">${title}</div>
                                    <div>${message}</div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-success" data-bs-dismiss="modal">OK</button>
                                </div>
                            </div>
                        </div>
                    `;
                    document.body.appendChild(modal);
                    
                    const bsModal = new bootstrap.Modal(modal);
                    bsModal.show();
                    
                    modal.addEventListener('hidden.bs.modal', () => {
                        document.body.removeChild(modal);
                        if (onClose) onClose();
                    });
                }

                function createAssetFromTransaction(journalId) {
                    window.location.href = `/fixed-assets/create-from-transaction?journal_id=${journalId}`;
                }

                function showErrorModal(title, message) {
                    const modal = document.createElement('div');
                    modal.className = 'modal modal-blur fade';
                    modal.innerHTML = `
                        <div class="modal-dialog modal-sm modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-body text-center">
                                    <div class="text-danger mb-3">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-x" width="48" height="48" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="m0 0h24v24H0z" fill="none"></path>
                                            <path d="m18 6l-12 12"></path>
                                            <path d="m6 6l12 12"></path>
                                        </svg>
                                    </div>
                                    <div class="modal-title">${title}</div>
                                    <div>${message}</div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">OK</button>
                                </div>
                            </div>
                        </div>
                    `;
                    document.body.appendChild(modal);
                    
                    const bsModal = new bootstrap.Modal(modal);
                    bsModal.show();
                    
                    modal.addEventListener('hidden.bs.modal', () => {
                        document.body.removeChild(modal);
                    });
                }

                function validateForm() {
                    const rows = document.querySelectorAll('#journalLines tr:not([data-existing])');
                    let hasValidEntry = false;
                    let errors = [];

                    rows.forEach((row, index) => {
                        const date = row.querySelector('input[name*="[date]"]')?.value;
                        const description = row.querySelector('input[name*="[description]"]')?.value;
                        const pic = row.querySelector('input[name*="[pic]"]')?.value;
                        const cashIn = row.querySelector('input[name*="[cash_in]"]')?.value;
                        const cashOut = row.querySelector('input[name*="[cash_out]"]')?.value;
                        const cashflowId = row.querySelector('input[name*="[cashflow_id]"]')?.value;

                        const hasCashValue = (cashIn && parseFloat(cashIn) > 0) || (cashOut && parseFloat(cashOut) > 0);
                        
                        if (hasCashValue) {
                            hasValidEntry = true;
                            
                            if (!date) {
                                errors.push(`Baris ${index + 1}: Tanggal wajib diisi`);
                            }
                            if (!description || description.trim() === '') {
                                errors.push(`Baris ${index + 1}: Keterangan wajib diisi`);
                            }
                            if (!pic || pic.trim() === '') {
                                errors.push(`Baris ${index + 1}: PIC wajib diisi`);
                            }
                            if (!cashflowId) {
                                errors.push(`Baris ${index + 1}: Kode Cashflow wajib dipilih`);
                            }
                        }
                    });

                    if (!hasValidEntry) {
                        showAlert('error', 'Minimal satu baris harus diisi dengan nilai kas masuk atau keluar.');
                        return false;
                    }

                    if (errors.length > 0) {
                        showAlert('error', errors.join('<br>'));
                        return false;
                    }

                    return true;
                }
            </script>
        @endpush
    @endif
@endsection