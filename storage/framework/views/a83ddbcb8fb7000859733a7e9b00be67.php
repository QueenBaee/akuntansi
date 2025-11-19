<?php $__env->startSection('title', 'Tambah Jurnal'); ?>

<?php $__env->startSection('page-header'); ?>
    <div class="page-pretitle">Transaksi</div>
    <h2 class="page-title" id="pageTitle">Jurnal Kas/Bank</h2>
    <div class="page-subtitle text-muted" id="pageSubtitle">Pilih akun dari menu untuk memulai</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <div class="row">
        <div class="col-12">
            <form method="POST" action="<?php echo e(route('journals.store')); ?>" id="journalForm" enctype="multipart/form-data">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="selected_cash_account_id" id="selectedCashAccountId">

                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="card-title">Jurnal Kas/Bank</h3>
                            <div class="text-muted" id="selectedAccountDisplay">Pilih akun kas/bank dari menu di atas</div>
                        </div>
                        <div class="text-end">
                            <small class="text-muted">Saldo Awal:</small><br>
                            <strong id="openingBalance">Rp 0</strong>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered" id="journalTable" style="border: 1px solid #dee2e6;">
                                <thead class="table-light">
                                    <tr style="border: 1px solid #dee2e6;">
                                        <th style="border: 1px solid #dee2e6; width: 80px;">Tanggal</th>
                                        <th style="border: 1px solid #dee2e6; width: 200px;">Deskripsi</th>
                                        <th style="border: 1px solid #dee2e6; width: 100px;">PIC</th>
                                        <th style="border: 1px solid #dee2e6; width: 100px;">File</th>
                                        <th style="border: 1px solid #dee2e6; width: 100px;">No. Bukti</th>
                                        <th style="border: 1px solid #dee2e6; width: 120px;">Kas Masuk</th>
                                        <th style="border: 1px solid #dee2e6; width: 120px;">Kas Keluar</th>
                                        <th style="border: 1px solid #dee2e6; width: 150px;">Akun Debit</th>
                                        <th style="border: 1px solid #dee2e6; width: 150px;">Akun Kredit</th>
                                        <th style="border: 1px solid #dee2e6; width: 120px;">Cashflow</th>
                                        <th style="border: 1px solid #dee2e6; width: 120px;">Saldo</th>
                                        <th style="border: 1px solid #dee2e6; width: 50px;">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="journalLines">
                                    <?php
                                        $selectedAccountId = session('selected_cash_account_id');
                                        $journals = $selectedAccountId ? \App\Models\Journal::with('details')->whereHas('details', function($q) use ($selectedAccountId) {
                                            $q->where('account_id', $selectedAccountId);
                                        })->orderBy('date', 'asc')->get() : collect();
                                        $runningBalance = $selectedAccountId ? \App\Models\Account::find($selectedAccountId)->opening_balance ?? 0 : 0;
                                    ?>
                                    <?php $__currentLoopData = $journals; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $journal): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <?php
                                            $cashDetail = $journal->details->where('account_id', $selectedAccountId)->first();
                                            $cashIn = $cashDetail ? $cashDetail->debit : 0;
                                            $cashOut = $cashDetail ? $cashDetail->credit : 0;
                                            $runningBalance += $cashIn - $cashOut;
                                        ?>
                                        <tr style="border: 1px solid #dee2e6; background-color: #f8f9fa;">
                                            <td style="border: 1px solid #dee2e6; padding: 4px; font-size: 12px;"><?php echo e($journal->date->format('Y-m-d')); ?></td>
                                            <td style="border: 1px solid #dee2e6; padding: 4px; font-size: 12px;"><?php echo e($journal->description); ?></td>
                                            <td style="border: 1px solid #dee2e6; padding: 4px; font-size: 12px;">-</td>
                                            <td style="border: 1px solid #dee2e6; padding: 4px; font-size: 12px;">-</td>
                                            <td style="border: 1px solid #dee2e6; padding: 4px; font-size: 12px;"><?php echo e($journal->reference ?? $journal->number); ?></td>
                                            <td style="border: 1px solid #dee2e6; padding: 4px; font-size: 12px; text-align: right;"><?php echo e($cashIn > 0 ? number_format($cashIn, 0, ',', '.') : ''); ?></td>
                                            <td style="border: 1px solid #dee2e6; padding: 4px; font-size: 12px; text-align: right;"><?php echo e($cashOut > 0 ? number_format($cashOut, 0, ',', '.') : ''); ?></td>
                                            <td style="border: 1px solid #dee2e6; padding: 4px; font-size: 12px;">-</td>
                                            <td style="border: 1px solid #dee2e6; padding: 4px; font-size: 12px;">-</td>
                                            <td style="border: 1px solid #dee2e6; padding: 4px; font-size: 12px;">-</td>
                                            <td style="border: 1px solid #dee2e6; padding: 4px; font-size: 12px; text-align: right; background: #e3f2fd;">Rp <?php echo e(number_format($runningBalance, 0, ',', '.')); ?></td>
                                            <td style="border: 1px solid #dee2e6; padding: 4px; text-align: center;">-</td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    <!-- New journal lines will be added here -->
                                </tbody>
                            </table>
                        </div>

                        <?php $__errorArgs = ['entries'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <div class="alert alert-danger"><?php echo e($message); ?></div>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                    <div class="card-footer text-end">
                        <a href="<?php echo e(route('journals.index')); ?>" class="btn btn-secondary me-2">Batal</a>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <?php $__env->startPush('scripts'); ?>
        <script>
            let lineIndex = 0;
            let openingBalance = 0;
            let selectedCashAccountId = null;

            const accountOptions = `
    <option value="">Pilih Akun</option>
    <?php $__currentLoopData = $accounts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $account): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <option value="<?php echo e($account->id); ?>"><?php echo e($account->code); ?> - <?php echo e($account->name); ?></option>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
`;

            const cashflowOptions = `
    <option value="">Pilih Cashflow</option>
    <?php $__currentLoopData = $cashflowCategories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <option value="<?php echo e($category->id); ?>"><?php echo e($category->keterangan); ?></option>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
`;

            document.addEventListener('DOMContentLoaded', function() {
                // Check if there's a selected account in sessionStorage
                const savedAccount = sessionStorage.getItem('selectedCashAccount');
                if (savedAccount) {
                    const account = JSON.parse(savedAccount);
                    selectAccount(account.id, account.name, account.balance);
                } else {
                    // Show message to select account
                    document.getElementById('selectedAccountDisplay').innerHTML =
                        '<span class="text-warning">⚠️ Pilih akun kas/bank dari menu di atas untuk memulai</span>';
                    document.getElementById('pageTitle').textContent = 'Jurnal Kas/Bank';
                    document.getElementById('pageSubtitle').textContent = 'Pilih akun dari menu untuk memulai';
                }

                addJournalLine();
            });

            function selectAccount(accountId, accountName, balance) {
                selectedCashAccountId = accountId;
                openingBalance = balance;

                document.getElementById('selectedCashAccountId').value = accountId;
                document.getElementById('selectedAccountDisplay').innerHTML =
                    `<strong>${accountName}</strong> <span class="badge bg-success ms-2">Terpilih</span>`;
                document.getElementById('openingBalance').textContent = 'Rp ' + balance.toLocaleString('id-ID');

                // Update page title and subtitle
                document.getElementById('pageTitle').textContent = `Jurnal ${accountName}`;
                document.getElementById('pageSubtitle').textContent = `Saldo: Rp ${balance.toLocaleString('id-ID')}`;

                // Show success message
                const alertDiv = document.createElement('div');
                alertDiv.className = 'alert alert-success alert-dismissible fade show';
                alertDiv.innerHTML = `
        <div class="d-flex">
            <div>
                <svg xmlns="http://www.w3.org/2000/svg" class="icon alert-icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="m0 0h24v24H0z" fill="none"/>
                    <path d="M5 12l5 5l10 -10"/>
                </svg>
            </div>
            <div>Akun <strong>${accountName}</strong> berhasil dipilih. Silakan mulai input transaksi.</div>
        </div>
        <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
    `;

                // Insert alert before the card
                const card = document.querySelector('.card');
                card.parentNode.insertBefore(alertDiv, card);

                // Auto dismiss after 3 seconds
                setTimeout(() => {
                    if (alertDiv.parentNode) {
                        alertDiv.remove();
                    }
                }, 3000);

                // Recalculate all balances
                calculateBalances();
            }

            function addJournalLine() {
                const tbody = document.getElementById('journalLines');
                const row = document.createElement('tr');
                row.style.border = '1px solid #dee2e6';

                row.innerHTML = `
        <td style="border: 1px solid #dee2e6; padding: 4px;">
            <input type="date" name="entries[${lineIndex}][date]" class="form-control form-control-sm" 
                   value="${new Date().toISOString().split('T')[0]}" style="border: none; font-size: 12px;">
        </td>
        <td style="border: 1px solid #dee2e6; padding: 4px;">
            <input type="text" name="entries[${lineIndex}][description]" class="form-control form-control-sm" 
                   placeholder="Deskripsi" style="border: none; font-size: 12px;">
        </td>
        <td style="border: 1px solid #dee2e6; padding: 4px;">
            <input type="text" name="entries[${lineIndex}][pic]" class="form-control form-control-sm" 
                   placeholder="PIC" style="border: none; font-size: 12px;">
        </td>
         <td style="border: 1px solid #dee2e6; padding: 4px;">
            <input type="file" name="entries[${lineIndex}][file]" class="form-control form-control-sm file-input" 
                   accept=".jpg,.jpeg,.png,.pdf" style="border: none; font-size: 11px;" onchange="generateProofNumber(this)">
        </td>
        <td style="border: 1px solid #dee2e6; padding: 4px;">
            <input type="text" name="entries[${lineIndex}][proof_number]" readonly class="form-control form-control-sm" 
                   placeholder="No. Bukti" style="border: none; font-size: 12px;">
        </td>
        <td style="border: 1px solid #dee2e6; padding: 4px;">
            <input type="number" name="entries[${lineIndex}][cash_in]" class="form-control form-control-sm cash-in" 
                   step="0.01" min="0" oninput="calculateBalances()" style="border: none; font-size: 12px; text-align: right;">
        </td>
        <td style="border: 1px solid #dee2e6; padding: 4px;">
            <input type="number" name="entries[${lineIndex}][cash_out]" class="form-control form-control-sm cash-out" 
                   step="0.01" min="0" oninput="calculateBalances()" style="border: none; font-size: 12px; text-align: right;">
        </td>
        <td style="border: 1px solid #dee2e6; padding: 4px;">
            <select name="entries[${lineIndex}][debit_account_id]" class="form-select form-select-sm" style="border: none; font-size: 12px;">
                ${accountOptions}
            </select>
        </td>
        <td style="border: 1px solid #dee2e6; padding: 4px;">
            <select name="entries[${lineIndex}][credit_account_id]" class="form-select form-select-sm" style="border: none; font-size: 12px;">
                ${accountOptions}
            </select>
        </td>
        <td style="border: 1px solid #dee2e6; padding: 4px;">
            <select name="entries[${lineIndex}][cashflow_id]" class="form-select form-select-sm" style="border: none; font-size: 12px;">
                ${cashflowOptions}
            </select>
        </td>
       
        <td style="border: 1px solid #dee2e6; padding: 4px;">
            <input type="text" class="form-control form-control-sm balance-display" readonly 
                   style="border: none; font-size: 12px; text-align: right; background: #f8f9fa;">
        </td>
        <td style="border: 1px solid #dee2e6; padding: 4px; text-align: center;">
            <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeJournalLine(this)" style="padding: 2px 6px;">
                ×
            </button>
        </td>
    `;

                tbody.appendChild(row);
                lineIndex++;

                // Add new row automatically when user starts typing in the last row
                const lastRow = tbody.lastElementChild;
                const inputs = lastRow.querySelectorAll('input, select');
                inputs.forEach(input => {
                    input.addEventListener('input', function() {
                        if (tbody.children.length === Array.from(tbody.children).indexOf(lastRow) + 1) {
                            addJournalLine();
                        }
                    });
                });

                calculateBalances();
            }

            function removeJournalLine(button) {
                const tbody = document.getElementById('journalLines');
                if (tbody.children.length > 1) {
                    button.closest('tr').remove();
                    calculateBalances();
                }
            }

            function calculateBalances() {
                const rows = document.querySelectorAll('#journalLines tr');
                let runningBalance = openingBalance;
                
                // Get last balance from existing data
                const existingRows = document.querySelectorAll('#journalLines tr[style*="background-color: #f8f9fa"]');
                if (existingRows.length > 0) {
                    const lastExistingBalance = existingRows[existingRows.length - 1].querySelector('td:nth-last-child(2)').textContent;
                    runningBalance = parseFloat(lastExistingBalance.replace(/[^0-9,-]/g, '').replace(',', '.')) || openingBalance;
                }

                rows.forEach((row, index) => {
                    // Skip existing data rows
                    if (row.style.backgroundColor === 'rgb(248, 249, 250)') return;
                    
                    const cashInInput = row.querySelector('.cash-in');
                    const cashOutInput = row.querySelector('.cash-out');
                    
                    if (cashInInput && cashOutInput) {
                        const cashIn = parseFloat(cashInInput.value) || 0;
                        const cashOut = parseFloat(cashOutInput.value) || 0;

                        runningBalance = runningBalance + cashIn - cashOut;

                        const balanceDisplay = row.querySelector('.balance-display');
                        if (balanceDisplay) {
                            balanceDisplay.value = 'Rp ' + runningBalance.toLocaleString('id-ID');
                        }
                    }
                });
            }

            function generateProofNumber(fileInput) {
                if (fileInput.files.length > 0 && selectedCashAccountId) {
                    const row = fileInput.closest('tr');
                    const proofNumberInput = row.querySelector('input[name*="[proof_number]"]');
                    const cashInInput = row.querySelector('.cash-in');
                    const cashOutInput = row.querySelector('.cash-out');

                    if (!proofNumberInput.value) {
                        const cashIn = parseFloat(cashInInput.value) || 0;
                        const cashOut = parseFloat(cashOutInput.value) || 0;
                        const transactionType = cashIn > 0 ? 'M' : (cashOut > 0 ? 'K' : 'M');

                        // Get account name from sessionStorage
                        const savedAccount = sessionStorage.getItem('selectedCashAccount');
                        if (savedAccount) {
                            const account = JSON.parse(savedAccount);
                            const accountName = account.name.replace(/\s+/g, '');

                            // Generate auto increment number (simple counter for demo)
                            const existingNumbers = Array.from(document.querySelectorAll('input[name*="[proof_number]"]'))
                                .map(input => input.value)
                                .filter(value => value.startsWith(`${accountName}/${transactionType}/`))
                                .map(value => parseInt(value.split('/')[2]) || 0);

                            const nextNumber = Math.max(0, ...existingNumbers) + 1;
                            const proofNumber = `${accountName}/${transactionType}/${String(nextNumber).padStart(3, '0')}`;

                            proofNumberInput.value = proofNumber;
                        }
                    }
                }
            }

            // Form validation before submit
            document.getElementById('journalForm').addEventListener('submit', function(e) {
                if (!selectedCashAccountId) {
                    e.preventDefault();

                    // Show alert in page instead of browser alert
                    const alertDiv = document.createElement('div');
                    alertDiv.className = 'alert alert-danger alert-dismissible fade show';
                    alertDiv.innerHTML = `
            <div class="d-flex">
                <div>
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon alert-icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="m0 0h24v24H0z" fill="none"/>
                        <circle cx="12" cy="12" r="9"/>
                        <line x1="12" y1="8" x2="12" y2="12"/>
                        <line x1="12" y1="16" x2="12.01" y2="16"/>
                    </svg>
                </div>
                <div><strong>Akun belum dipilih!</strong> Silakan pilih akun kas/bank dari menu di atas terlebih dahulu.</div>
            </div>
            <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
        `;

                    const card = document.querySelector('.card');
                    card.parentNode.insertBefore(alertDiv, card);

                    // Scroll to top to show the alert
                    window.scrollTo({
                        top: 0,
                        behavior: 'smooth'
                    });

                    return false;
                }

                // Check if at least one row has data
                const rows = document.querySelectorAll('#journalLines tr');
                let hasData = false;

                rows.forEach(row => {
                    const description = row.querySelector('input[name*="[description]"]').value;
                    const cashIn = parseFloat(row.querySelector('.cash-in').value) || 0;
                    const cashOut = parseFloat(row.querySelector('.cash-out').value) || 0;

                    if (description.trim() || cashIn > 0 || cashOut > 0) {
                        hasData = true;
                    }
                });

                if (!hasData) {
                    e.preventDefault();

                    const alertDiv = document.createElement('div');
                    alertDiv.className = 'alert alert-warning alert-dismissible fade show';
                    alertDiv.innerHTML = `
            <div class="d-flex">
                <div>
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon alert-icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="m0 0h24v24H0z" fill="none"/>
                        <path d="M12 9v2m0 4v.01"/>
                        <path d="M5 19h14a2 2 0 0 0 1.414 -1.414l-7 -7a2 2 0 0 0 -2.828 0l-7 7a2 2 0 0 0 1.414 1.414z"/>
                    </svg>
                </div>
                <div><strong>Data transaksi kosong!</strong> Silakan isi minimal satu baris transaksi dengan deskripsi dan nominal.</div>
            </div>
            <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
        `;

                    const card = document.querySelector('.card');
                    card.parentNode.insertBefore(alertDiv, card);

                    window.scrollTo({
                        top: 0,
                        behavior: 'smooth'
                    });

                    return false;
                }
            });
        </script>
    <?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/najibzulfan/project/akuntansi/resources/views/journals/create.blade.php ENDPATH**/ ?>