@extends('layouts.app')

@section('title', 'Memorial Entry')

@section('page-header')
    <div class="page-pretitle">Memorial</div>
    <h2 class="page-title">Memorial Entry</h2>
@endsection

@section('content')
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



    <div class="row">
        <div class="col-12">
            <form method="POST" action="{{ route('memorials.store') }}" id="memorialForm" enctype="multipart/form-data"
                onsubmit="return validateForm()">
                @csrf

                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="card-title">Memorial Entry</h3>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered" id="memorialTable" style="border: 1px solid #dee2e6;">
                                <thead class="table-light">
                                    <tr style="border: 1px solid #dee2e6;">
                                        <th style="border: 1px solid #dee2e6; width: 80px;">Tanggal</th>
                                        <th style="border: 1px solid #dee2e6; width: 200px;">Keterangan</th>
                                        <th style="border: 1px solid #dee2e6; width: 100px;">PIC</th>
                                        <th style="border: 1px solid #dee2e6; width: 100px;">Bukti</th>
                                        <th style="border: 1px solid #dee2e6; width: 100px;">No. Bukti</th>
                                        <th style="border: 1px solid #dee2e6; width: 150px;">Akun Debit</th>
                                        <th style="border: 1px solid #dee2e6; width: 120px;">Debit</th>
                                        <th style="border: 1px solid #dee2e6; width: 150px;">Akun Kredit</th>
                                        <th style="border: 1px solid #dee2e6; width: 120px;">Kredit</th>
                                        <th style="border: 1px solid #dee2e6; width: 50px;">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="memorialLines">
                                    @foreach ($memorialsHistory as $history)
                                        <tr data-existing="1" data-journal-id="{{ $history['journal_id'] }}"
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
                                            <td style="border: 1px solid #dee2e6; padding: 4px; font-size: 12px;">
                                                {{ $history['debit_account'] }}</td>
                                            <td
                                                style="border: 1px solid #dee2e6; padding: 4px; font-size: 12px; text-align: right;">
                                                {{ $history['debit_amount'] > 0 ? number_format($history['debit_amount'], 0, ',', '.') : '' }}
                                            </td>
                                            <td style="border: 1px solid #dee2e6; padding: 4px; font-size: 12px;">
                                                {{ $history['credit_account'] }}</td>
                                            <td
                                                style="border: 1px solid #dee2e6; padding: 4px; font-size: 12px; text-align: right;">
                                                {{ $history['credit_amount'] > 0 ? number_format($history['credit_amount'], 0, ',', '.') : '' }}
                                            </td>


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

                        @error('entries')
                            <div class="alert alert-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="card-footer text-end">
                        <button type="button" class="btn btn-success me-2" onclick="addMemorialLine()">+ Tambah
                            Baris</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
        <script>
            let lineIndex = 0;
            const formatter = new Intl.NumberFormat('id-ID');

            // Build account options
            let accountOptions = '<option value="">Pilih Akun</option>';
            @foreach ($accounts as $account)
                accountOptions +=
                    '<option value="{{ $account->id }}">{{ str_replace(["'", '"'], ["\'", '\"'], $account->kode . ' - ' . $account->keterangan) }}</option>';
            @endforeach

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
                addMemorialLine();
            });

            function addMemorialLine() {
                const tbody = document.getElementById('memorialLines');
                const row = document.createElement('tr');
                row.style.border = '1px solid #dee2e6';

                const currentDate = '{{ date('Y-m-d') }}';
                const currentIndex = lineIndex;

                row.innerHTML =
                    '<td style="border: 1px solid #dee2e6; padding: 2px;">' +
                    '<input type="date" name="entries[' + currentIndex +
                    '][date]" class="form-control form-control-sm" style="border: none; font-size: 12px;" value="' +
                    currentDate + '">' +
                    '</td>' +
                    '<td style="border: 1px solid #dee2e6; padding: 2px;">' +
                    '<input type="text" name="entries[' + currentIndex +
                    '][description]" class="form-control form-control-sm" style="border: none; font-size: 12px;" placeholder="Deskripsi">' +
                    '</td>' +
                    '<td style="border: 1px solid #dee2e6; padding: 2px;">' +
                    '<input type="text" name="entries[' + currentIndex +
                    '][pic]" class="form-control form-control-sm" style="border: none; font-size: 12px;" placeholder="PIC">' +
                    '</td>' +
                    '<td style="border: 1px solid #dee2e6; padding: 2px;">' +
                    '<input type="file" name="entries[' + currentIndex +
                    '][attachments][]" class="form-control form-control-sm" style="border: none; font-size: 11px;" accept=".jpg,.jpeg,.png,.pdf" multiple>' +
                    '</td>' +
                    '<td style="border: 1px solid #dee2e6; padding: 2px;">' +
                    '<input type="text" name="entries[' + currentIndex +
                    '][proof_number]" class="form-control form-control-sm proof-number" style="border: none; font-size: 12px;" placeholder="Auto" readonly>' +
                    '</td>' +
                    '<td style="border: 1px solid #dee2e6; padding: 2px;">' +
                    '<select name="entries[' + currentIndex +
                    '][debit_account_id]" class="form-control form-control-sm debit-account" style="border: none; font-size: 12px;">' +
                    accountOptions +
                    '</select>' +
                    '</td>' +
                    '<td style="border: 1px solid #dee2e6; padding: 2px;">' +
                    '<input type="number" name="entries[' + currentIndex +
                    '][debit_amount]" class="form-control form-control-sm debit-amount" style="border: none; font-size: 12px; text-align: right;" placeholder="0" min="0" step="1" oninput="handleAmountInput(this, \'debit\')">' +
                    '</td>' +
                    '<td style="border: 1px solid #dee2e6; padding: 2px;">' +
                    '<select name="entries[' + currentIndex +
                    '][credit_account_id]" class="form-control form-control-sm credit-account" style="border: none; font-size: 12px;">' +
                    accountOptions +
                    '</select>' +
                    '</td>' +
                    '<td style="border: 1px solid #dee2e6; padding: 2px;">' +
                    '<input type="number" name="entries[' + currentIndex +
                    '][credit_amount]" class="form-control form-control-sm credit-amount" style="border: none; font-size: 12px; text-align: right;" placeholder="0" min="0" step="1" oninput="handleAmountInput(this, \'credit\')">' +
                    '</td>' +

                    '<td style="border: 1px solid #dee2e6; padding: 2px; text-align: center;">' +
                    '-' +
                    '</td>';

                tbody.appendChild(row);
                lineIndex++;
            }

            function handleAmountInput(input, type) {
                const row = input.closest('tr');
                const debitAccountSelect = row.querySelector('.debit-account');
                const creditAccountSelect = row.querySelector('.credit-account');
                const debitInput = row.querySelector('.debit-amount');
                const creditInput = row.querySelector('.credit-amount');
                const currentValue = parseFloat(input.value) || 0;

                // Check if both accounts are selected
                if (currentValue > 0 && (!debitAccountSelect.value || !creditAccountSelect.value)) {
                    showAlert('error', 'Pilih akun debit dan kredit terlebih dahulu sebelum mengisi nominal');
                    input.value = '';
                    return;
                }

                // Auto-fill the opposite field with the same amount
                if (currentValue > 0) {
                    if (type === 'debit') {
                        creditInput.value = currentValue;
                    } else if (type === 'credit') {
                        debitInput.value = currentValue;
                    }
                } else {
                    // Clear both fields if current value is 0 or empty
                    if (type === 'debit') {
                        creditInput.value = '';
                    } else if (type === 'credit') {
                        debitInput.value = '';
                    }
                }

                updateProofNumber(input);
            }

            function updateProofNumber(input) {
                const row = input.closest('tr');
                const debitAmount = parseFloat(row.querySelector('.debit-amount').value) || 0;
                const creditAmount = parseFloat(row.querySelector('.credit-amount').value) || 0;
                const proofInput = row.querySelector('.proof-number');

                if (debitAmount > 0 || creditAmount > 0) {
                    // Count existing memorial entries to get next number
                    let memorialCount = 1;
                    const historyRows = document.querySelectorAll('tr[data-existing="1"]');
                    historyRows.forEach(historyRow => {
                        const historyProofCell = historyRow.children[4];
                        if (historyProofCell && historyProofCell.textContent.includes('MEM-')) {
                            memorialCount++;
                        }
                    });

                    const currentRows = document.querySelectorAll('input[name*="[proof_number]"]');
                    currentRows.forEach(inp => {
                        if (inp !== proofInput && inp.value && inp.value.includes('MEM-')) {
                            memorialCount++;
                        }
                    });

                    const proofNumber = `MEM-${memorialCount.toString().padStart(3, '0')}`;
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
                        document.getElementById('attachmentsList').innerHTML =
                            '<p class="text-danger">Error loading attachments</p>';
                    });

                modal.addEventListener('hidden.bs.modal', () => {
                    document.body.removeChild(modal);
                });
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
                const debitAmount = cells[5].textContent.replace(/[^0-9]/g, '') || '0';
                const creditAmount = cells[6].textContent.replace(/[^0-9]/g, '') || '0';
                const debitAccount = cells[7].textContent.trim();
                const creditAccount = cells[8].textContent.trim();

                // Convert date format from d/m/Y to Y-m-d
                const dateParts = currentDate.split('/');
                const formattedDate = dateParts.length === 3 ?
                    `${dateParts[2]}-${dateParts[1].padStart(2, '0')}-${dateParts[0].padStart(2, '0')}` : currentDate;

                // Convert to editable fields
                cells[0].innerHTML =
                    `<input type="date" class="form-control form-control-sm edit-date" value="${formattedDate}" style="font-size: 12px; border: none;">`;
                cells[1].innerHTML =
                    `<input type="text" class="form-control form-control-sm edit-description" value="${description}" style="font-size: 12px; border: none;">`;
                cells[2].innerHTML =
                    `<input type="text" class="form-control form-control-sm edit-pic" value="${pic}" style="font-size: 12px; border: none;">`;
                cells[3].innerHTML =
                    `<input type="file" class="form-control form-control-sm edit-attachments" accept=".jpg,.jpeg,.png,.pdf" multiple style="font-size: 11px; border: none;">`;
                cells[4].innerHTML =
                    `<input type="text" class="form-control form-control-sm edit-proof" value="${proofNumber}" style="font-size: 12px; border: none;">`;
                cells[5].innerHTML =
                    `<input type="number" class="form-control form-control-sm edit-debit debit-amount" value="${debitAmount}" style="font-size: 12px; text-align: right; border: none;" min="0" oninput="handleAmountInput(this, 'debit')">`;
                cells[6].innerHTML =
                    `<input type="number" class="form-control form-control-sm edit-credit credit-amount" value="${creditAmount}" style="font-size: 12px; text-align: right; border: none;" min="0" oninput="handleAmountInput(this, 'credit')">`;
                cells[7].innerHTML =
                    `<select class="form-control form-control-sm edit-debit-account debit-account" style="font-size: 12px; border: none;">${accountOptions}</select>`;
                cells[8].innerHTML =
                    `<select class="form-control form-control-sm edit-credit-account credit-account" style="font-size: 12px; border: none;">${accountOptions}</select>`;

                // Set current selections
                setTimeout(() => {
                    // Set account selections by text
                    const debitSelect = row.querySelector('.edit-debit-account');
                    const creditSelect = row.querySelector('.edit-credit-account');

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
                formData.append('debit_amount', row.querySelector('.edit-debit').value || 0);
                formData.append('credit_amount', row.querySelector('.edit-credit').value || 0);
                formData.append('debit_account_id', row.querySelector('.edit-debit-account').value);
                formData.append('credit_account_id', row.querySelector('.edit-credit-account').value);
                formData.append('_method', 'PUT');

                // Handle file attachments
                const fileInput = row.querySelector('.edit-attachments');
                if (fileInput.files.length > 0) {
                    for (let i = 0; i < fileInput.files.length; i++) {
                        formData.append('attachments[]', fileInput.files[i]);
                    }
                }

                fetch(`/memorials/${journalId}`, {
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
                            showAlert('success', 'Memorial berhasil diperbarui!');
                            setTimeout(() => window.location.reload(), 1500);
                        } else {
                            showAlert('error', data.message || 'Gagal memperbarui memorial');
                        }
                    })
                    .catch(error => {
                        showAlert('error', 'Gagal memperbarui memorial');
                    });
            }

            function deleteTransaction(journalId) {
                showConfirmModal(
                    'Konfirmasi Hapus',
                    'Apakah Anda yakin ingin menghapus memorial ini?',
                    () => {
                        fetch(`/memorials/${journalId}`, {
                                method: 'DELETE',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                        'content'),
                                    'Accept': 'application/json'
                                }
                            })
                            .then(response => response.json())
                            .then(data => {
                                showAlert('success', 'Memorial berhasil dihapus!');
                                setTimeout(() => window.location.reload(), 1500);
                            })
                            .catch(error => {
                                showAlert('error', 'Gagal menghapus memorial');
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
                const rows = document.querySelectorAll('#memorialLines tr:not([data-existing])');
                let hasValidEntry = false;
                let errors = [];
                let totalDebit = 0;
                let totalCredit = 0;

                rows.forEach((row, index) => {
                    const date = row.querySelector('input[name*="[date]"]')?.value;
                    const description = row.querySelector('input[name*="[description]"]')?.value;
                    const pic = row.querySelector('input[name*="[pic]"]')?.value;
                    const debitAmount = parseFloat(row.querySelector('input[name*="[debit_amount]"]')?.value) || 0;
                    const creditAmount = parseFloat(row.querySelector('input[name*="[credit_amount]"]')?.value) || 0;
                    const debitAccountId = row.querySelector('select[name*="[debit_account_id]"]')?.value;
                    const creditAccountId = row.querySelector('select[name*="[credit_account_id]"]')?.value;

                    const hasAmount = debitAmount > 0 || creditAmount > 0;

                    if (hasAmount) {
                        hasValidEntry = true;
                        totalDebit += debitAmount;
                        totalCredit += creditAmount;

                        if (!date) {
                            errors.push(`Baris ${index + 1}: Tanggal wajib diisi`);
                        }
                        if (!description || description.trim() === '') {
                            errors.push(`Baris ${index + 1}: Keterangan wajib diisi`);
                        }
                        if (!pic || pic.trim() === '') {
                            errors.push(`Baris ${index + 1}: PIC wajib diisi`);
                        }
                        if (debitAmount > 0 && !debitAccountId) {
                            errors.push(`Baris ${index + 1}: Akun debit wajib dipilih`);
                        }
                        if (creditAmount > 0 && !creditAccountId) {
                            errors.push(`Baris ${index + 1}: Akun kredit wajib dipilih`);
                        }

                    }
                });

                if (!hasValidEntry) {
                    showAlert('error', 'Minimal satu baris harus diisi dengan nilai debit atau kredit.');
                    return false;
                }

                if (Math.abs(totalDebit - totalCredit) > 0.01) {
                    showAlert('error',
                        `Total debit (${formatter.format(totalDebit)}) harus sama dengan total kredit (${formatter.format(totalCredit)}).`
                    );
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
@endsection
