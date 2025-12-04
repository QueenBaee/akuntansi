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
                                            <th style="border: 1px solid #dee2e6; width: 80px;">Tanggal</th>
                                            <th style="border: 1px solid #dee2e6; width: 200px;">Keterangan</th>
                                            <th style="border: 1px solid #dee2e6; width: 100px;">PIC</th>
                                            <th style="border: 1px solid #dee2e6; width: 100px;">Bukti</th>
                                            <th style="border: 1px solid #dee2e6; width: 100px;">No. Bukti</th>
                                            <th style="border: 1px solid #dee2e6; width: 120px;">Masuk</th>
                                            <th style="border: 1px solid #dee2e6; width: 120px;">Keluar</th>
                                            <th style="border: 1px solid #dee2e6; width: 120px;">Saldo</th>
                                            <th style="border: 1px solid #dee2e6; width: 230px;">Kode & Akun CF</th>
                                            <th style="border: 1px solid #dee2e6; width: 150px;">Debit</th>
                                            <th style="border: 1px solid #dee2e6; width: 150px;">Kredit</th>
                                            <th style="border: 1px solid #dee2e6; width: 50px;">Aksi</th>
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
                let selectedAccountName = {!! json_encode($selectedAccount ? $selectedAccount->keterangan : '') !!};
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
                            '<select name="entries[' + currentIndex + '][cashflow_id]" class="form-control form-control-sm cashflow-select" style="border: none; font-size: 12px;" onchange="updateCashflowDescription(this); updateTrialBalanceFromCashflow(this)">' +
                                cashflowOptions +
                            '</select>' +
                            '<input type="hidden" class="cashflow-desc">' +
                        '</td>' +
                        '<td style="border: 1px solid #dee2e6; padding: 2px;">' +
                            '<select name="entries[' + currentIndex + '][debit_account_id]" class="form-control form-control-sm debit-account" style="border: none; font-size: 12px;">' +
                                accountOptions +
                            '</select>' +
                        '</td>' +
                        '<td style="border: 1px solid #dee2e6; padding: 2px;">' +
                            '<select name="entries[' + currentIndex + '][credit_account_id]" class="form-control form-control-sm credit-account" style="border: none; font-size: 12px;">' +
                                accountOptions +
                            '</select>' +
                        '</td>' +
                        '<td style="border: 1px solid #dee2e6; padding: 2px; text-align: center;">' +
                            '-' +
                        '</td>';
                    
                    tbody.appendChild(row);
                    lineIndex++;
                }

                function handleCashInput(input, type) {
                    const row = input.closest('tr');
                    const cashInInput = row.querySelector('.cash-in');
                    const cashOutInput = row.querySelector('.cash-out');
                    const debitSelect = row.querySelector('select[name*="[debit_account_id]"]');
                    const creditSelect = row.querySelector('select[name*="[credit_account_id]"]');
                    const cashflowSelect = row.querySelector('.cashflow-select');

                    if (input.value && parseFloat(input.value) > 0) {
                        if (type === 'in') {
                            cashOutInput.value = '';
                            debitSelect.value = selectedCashAccountId;
                            debitSelect.disabled = true;
                            debitSelect.style.backgroundColor = '#e9ecef';
                            let hiddenDebit = row.querySelector('.hidden-debit');
                            if (!hiddenDebit) {
                                hiddenDebit = document.createElement('input');
                                hiddenDebit.type = 'hidden';
                                hiddenDebit.className = 'hidden-debit';
                                hiddenDebit.name = debitSelect.name;
                                row.appendChild(hiddenDebit);
                            }
                            hiddenDebit.value = selectedCashAccountId;
                            creditSelect.disabled = false;
                            creditSelect.style.backgroundColor = '';
                            const hiddenCredit = row.querySelector('.hidden-credit');
                            if (hiddenCredit) hiddenCredit.remove();
                            
                            // Auto-set trial balance account if cashflow is selected
                            if (cashflowSelect.value && cashflowData[cashflowSelect.value] && cashflowData[cashflowSelect.value].trial_balance_id) {
                                creditSelect.value = cashflowData[cashflowSelect.value].trial_balance_id;
                            }
                        } else {
                            cashInInput.value = '';
                            creditSelect.value = selectedCashAccountId;
                            creditSelect.disabled = true;
                            creditSelect.style.backgroundColor = '#e9ecef';
                            let hiddenCredit = row.querySelector('.hidden-credit');
                            if (!hiddenCredit) {
                                hiddenCredit = document.createElement('input');
                                hiddenCredit.type = 'hidden';
                                hiddenCredit.className = 'hidden-credit';
                                hiddenCredit.name = creditSelect.name;
                                row.appendChild(hiddenCredit);
                            }
                            hiddenCredit.value = selectedCashAccountId;
                            debitSelect.disabled = false;
                            debitSelect.style.backgroundColor = '';
                            const hiddenDebit = row.querySelector('.hidden-debit');
                            if (hiddenDebit) hiddenDebit.remove();
                            
                            // Auto-set trial balance account if cashflow is selected
                            if (cashflowSelect.value && cashflowData[cashflowSelect.value] && cashflowData[cashflowSelect.value].trial_balance_id) {
                                debitSelect.value = cashflowData[cashflowSelect.value].trial_balance_id;
                            }
                        }
                    } else {
                        debitSelect.disabled = false;
                        creditSelect.disabled = false;
                        debitSelect.style.backgroundColor = '';
                        creditSelect.style.backgroundColor = '';
                        debitSelect.value = '';
                        creditSelect.value = '';
                        const hiddenDebit = row.querySelector('.hidden-debit');
                        const hiddenCredit = row.querySelector('.hidden-credit');
                        if (hiddenDebit) hiddenDebit.remove();
                        if (hiddenCredit) hiddenCredit.remove();
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

                function updateTrialBalanceFromCashflow(selectElement) {
                    const row = selectElement.closest('tr');
                    const selectedId = selectElement.value;
                    
                    if (selectedId && cashflowData && cashflowData[selectedId] && cashflowData[selectedId].trial_balance_id) {
                        setTrialBalanceAccount(row, cashflowData[selectedId].trial_balance_id);
                    }
                }

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
                    cells[8].innerHTML = `<select class="form-control form-control-sm edit-cashflow cashflow-select" style="font-size: 12px; border: none;" onchange="updateCashflowDescription(this); updateTrialBalanceFromCashflow(this)">${cashflowOptions}</select>`;
                    cells[9].innerHTML = `<select class="form-control form-control-sm edit-debit debit-account" style="font-size: 12px; border: none;">${accountOptions}</select>`;
                    cells[10].innerHTML = `<select class="form-control form-control-sm edit-credit credit-account" style="font-size: 12px; border: none;">${accountOptions}</select>`;
                    
                    // Set current selections
                    setTimeout(() => {
                        // Set cashflow selection by code (now combined with description)
                        const cashflowSelect = row.querySelector('.edit-cashflow');
                        const combinedCashflowText = cells[8].textContent.trim();
                        for (let option of cashflowSelect.options) {
                            if (option.text === combinedCashflowText) {
                                cashflowSelect.value = option.value;
                                break;
                            }
                        }
                        
                        // Set account selections by text
                        const debitSelect = row.querySelector('.edit-debit');
                        const creditSelect = row.querySelector('.edit-credit');
                        
                        for (let option of debitSelect.options) {
                            if (option.text === debitAccount) {
                                debitSelect.value = option.value;
                                break;
                            }
                        }
                        
                        for (let option of creditSelect.options) {
                            if (option.text === creditAccount) {
                                creditSelect.value = option.value;
                                break;
                            }
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

                function handleEditCashInput(input, type) {
                    const row = input.closest('tr');
                    const cashInInput = row.querySelector('.edit-cash-in');
                    const cashOutInput = row.querySelector('.edit-cash-out');
                    const debitSelect = row.querySelector('.edit-debit');
                    const creditSelect = row.querySelector('.edit-credit');
                    const cashflowSelect = row.querySelector('.edit-cashflow');

                    if (input.value && parseFloat(input.value) > 0) {
                        if (type === 'in') {
                            cashOutInput.value = '';
                            debitSelect.value = selectedCashAccountId;
                            debitSelect.disabled = true;
                            debitSelect.style.backgroundColor = '#e9ecef';
                            creditSelect.disabled = false;
                            creditSelect.style.backgroundColor = '';
                            
                            // Auto-set trial balance account if cashflow is selected
                            if (cashflowSelect.value && cashflowData[cashflowSelect.value] && cashflowData[cashflowSelect.value].trial_balance_id) {
                                creditSelect.value = cashflowData[cashflowSelect.value].trial_balance_id;
                            }
                        } else {
                            cashInInput.value = '';
                            creditSelect.value = selectedCashAccountId;
                            creditSelect.disabled = true;
                            creditSelect.style.backgroundColor = '#e9ecef';
                            debitSelect.disabled = false;
                            debitSelect.style.backgroundColor = '';
                            
                            // Auto-set trial balance account if cashflow is selected
                            if (cashflowSelect.value && cashflowData[cashflowSelect.value] && cashflowData[cashflowSelect.value].trial_balance_id) {
                                debitSelect.value = cashflowData[cashflowSelect.value].trial_balance_id;
                            }
                        }
                    } else {
                        debitSelect.disabled = false;
                        creditSelect.disabled = false;
                        debitSelect.style.backgroundColor = '';
                        creditSelect.style.backgroundColor = '';
                        debitSelect.value = '';
                        creditSelect.value = '';
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
                        const cashflowId = row.querySelector('select[name*="[cashflow_id]"]')?.value;

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