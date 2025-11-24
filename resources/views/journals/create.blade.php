@extends('layouts.app')

@section('title', 'Tambah Jurnal')

@section('page-header')
    <div class="page-pretitle">Transaksi</div>
    <h2 class="page-title" id="pageTitle">
        @if ($selectedAccount)
            Jurnal {{ $selectedAccount->keterangan }}
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

@section('content')
    <div id="errorAlert" class="alert alert-danger" style="display: none;"></div>

    @if ($errors->any())
        <div class="alert alert-danger">
            @foreach ($errors->all() as $error)
                {{ $error }}<br>
            @endforeach
        </div>
    @endif

    @if (!$selectedAccount)
        <div class="alert alert-warning">
            <strong>Perhatian!</strong> Pilih akun kas/bank dari menu di atas untuk memulai membuat jurnal.
        </div>
    @endif

    <div class="row">
        <div class="col-12">
            <form method="POST" action="{{ route('journals.store') }}" id="journalForm" enctype="multipart/form-data">
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
                                            <th style="border: 1px solid #dee2e6; width: 80px;">Kode CF</th>
                                            <th style="border: 1px solid #dee2e6; width: 150px;">Akun Arus Kas</th>
                                            <th style="border: 1px solid #dee2e6; width: 150px;">Akun Debit</th>
                                            <th style="border: 1px solid #dee2e6; width: 150px;">Akun Kredit</th>
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
                                                    {{ $history['cashflow_code'] ?? '-' }}</td>
                                                <td style="border: 1px solid #dee2e6; padding: 4px; font-size: 12px;">
                                                    {{ $history['cashflow_desc'] ?? '-' }}</td>
                                                <td style="border: 1px solid #dee2e6; padding: 4px; font-size: 12px;">
                                                    {{ $history['debit_account'] }}</td>
                                                <td style="border: 1px solid #dee2e6; padding: 4px; font-size: 12px;">
                                                    {{ $history['credit_account'] }}</td>
                                                <td style="border: 1px solid #dee2e6; padding: 4px; text-align: center;">
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

                const accountOptions = `
                    <option value="">Pilih Akun</option>
                    @foreach ($accounts as $account)
                        <option value="{{ $account->id }}">{{ $account->kode }} - {{ $account->keterangan }}</option>
                    @endforeach
                `;

                const cashflowOptions = `
                    <option value="">Pilih Kode</option>
                    @foreach ($cashflows as $cashflow)
                        <option value="{{ $cashflow->id }}" data-description="{{ $cashflow->keterangan }}">{{ $cashflow->kode }}</option>
                    @endforeach
                `;

                const cashflowData = {
                    @foreach ($cashflows as $cashflow)
                        {{ $cashflow->id }}: {
                            code: '{{ $cashflow->kode }}',
                            description: '{{ $cashflow->keterangan }}'
                        },
                    @endforeach
                };

                document.addEventListener('DOMContentLoaded', function() {
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
                    row.innerHTML = `
                        <td style="border: 1px solid #dee2e6; padding: 2px;">
                            <input type="date" name="entries[\${lineIndex}][date]" class="form-control form-control-sm" style="border: none; font-size: 12px;" value="{{ date('Y-m-d') }}">
                        </td>
                        <td style="border: 1px solid #dee2e6; padding: 2px;">
                            <input type="text" name="entries[\${lineIndex}][description]" class="form-control form-control-sm" style="border: none; font-size: 12px;" placeholder="Deskripsi">
                        </td>
                        <td style="border: 1px solid #dee2e6; padding: 2px;">
                            <input type="text" name="entries[\${lineIndex}][pic]" class="form-control form-control-sm" style="border: none; font-size: 12px;" placeholder="PIC">
                        </td>
                        <td style="border: 1px solid #dee2e6; padding: 2px;">
                            <input type="file" name="entries[\${lineIndex}][attachments][]" class="form-control form-control-sm" style="border: none; font-size: 11px;" accept=".jpg,.jpeg,.png,.pdf" multiple onchange="generateProofNumber(this, \${lineIndex})">
                        </td>
                        <td style="border: 1px solid #dee2e6; padding: 2px;">
                            <input type="text" name="entries[\${lineIndex}][proof_number]" class="form-control form-control-sm proof-number" style="border: none; font-size: 12px;" placeholder="Auto" readonly>
                        </td>
                        <td style="border: 1px solid #dee2e6; padding: 2px;">
                            <input type="number" name="entries[\${lineIndex}][cash_in]" class="form-control form-control-sm cash-in" style="border: none; font-size: 12px; text-align: right;" placeholder="0" min="0" step="1" onchange="calculateBalance(this); updateProofNumber(this)" oninput="handleCashInput(this, 'in')">
                        </td>
                        <td style="border: 1px solid #dee2e6; padding: 2px;">
                            <input type="number" name="entries[\${lineIndex}][cash_out]" class="form-control form-control-sm cash-out" style="border: none; font-size: 12px; text-align: right;" placeholder="0" min="0" step="1" onchange="calculateBalance(this); updateProofNumber(this)" oninput="handleCashInput(this, 'out')">
                        </td>
                        <td style="border: 1px solid #dee2e6; padding: 2px; text-align: right; background: #e8f5e8;">
                            <span class="balance-display" style="font-size: 12px; font-weight: bold;">${formatter.format(currentBalance)}</span>
                        </td>
                        <td style="border: 1px solid #dee2e6; padding: 2px;">
                            <select name="entries[\${lineIndex}][cashflow_id]" class="form-control form-control-sm cashflow-select" style="border: none; font-size: 12px;" onchange="updateCashflowDescription(this)">
                                \${cashflowOptions}
                            </select>
                        </td>
                        <td style="border: 1px solid #dee2e6; padding: 2px;">
                            <input type="text" class="form-control form-control-sm cashflow-desc" style="border: none; font-size: 12px; background-color: #f8f9fa;" placeholder="Keterangan otomatis" readonly>
                        </td>
                        <td style="border: 1px solid #dee2e6; padding: 2px;">
                            <select name="entries[\${lineIndex}][debit_account_id]" class="form-control form-control-sm debit-account" style="border: none; font-size: 12px;">
                                \${accountOptions}
                            </select>
                        </td>
                        <td style="border: 1px solid #dee2e6; padding: 2px;">
                            <select name="entries[\${lineIndex}][credit_account_id]" class="form-control form-control-sm credit-account" style="border: none; font-size: 12px;">
                                \${accountOptions}
                            </select>
                        </td>
                        <td style="border: 1px solid #dee2e6; padding: 2px; text-align: center;">
                            <button type="button" class="btn btn-sm btn-success" onclick="addJournalLine()" style="font-size: 10px; padding: 2px 6px;">+</button>
                        </td>
                    `;
                    tbody.appendChild(row);
                    lineIndex++;
                }

                function handleCashInput(input, type) {
                    const row = input.closest('tr');
                    const cashInInput = row.querySelector('.cash-in');
                    const cashOutInput = row.querySelector('.cash-out');
                    const debitSelect = row.querySelector('select[name*="[debit_account_id]"]');
                    const creditSelect = row.querySelector('select[name*="[credit_account_id]"]');

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
                    const descInput = row.querySelector('.cashflow-desc');
                    const selectedId = selectElement.value;

                    if (selectedId && cashflowData[selectedId]) {
                        descInput.value = cashflowData[selectedId].description;
                    } else {
                        descInput.value = '';
                    }
                }

                function deleteTransaction(journalId) {
                    if (confirm('Apakah Anda yakin ingin menghapus transaksi ini?')) {
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
                            alert('Transaksi berhasil dihapus!');
                            window.location.reload();
                        })
                        .catch(error => {
                            alert('Error: Gagal menghapus transaksi');
                        });
                    }
                }
            </script>
        @endpush
    @endif
@endsection