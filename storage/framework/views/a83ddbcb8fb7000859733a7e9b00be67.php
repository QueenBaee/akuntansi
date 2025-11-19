<?php $__env->startSection('title', 'Tambah Jurnal'); ?>

<?php $__env->startSection('page-header'); ?>
    <div class="page-pretitle">Transaksi</div>
    <h2 class="page-title" id="pageTitle">
        <?php if($selectedAccount): ?>
            Jurnal <?php echo e($selectedAccount->name); ?>

        <?php else: ?>
            Jurnal Kas/Bank
        <?php endif; ?>
    </h2>
    <div class="page-subtitle text-muted" id="pageSubtitle">
        <?php if($selectedAccount): ?>
            Saldo Awal: <?php echo e(number_format($openingBalance, 0, ',', '.')); ?>

        <?php else: ?>
            Pilih akun dari menu untuk memulai
        <?php endif; ?>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <?php if(!$selectedAccount): ?>
        <div class="alert alert-warning">
            <strong>Perhatian!</strong> Pilih akun kas/bank dari menu di atas untuk memulai membuat jurnal.
        </div>
    <?php endif; ?>

    <div class="row">
        <div class="col-12">
            <form method="POST" action="<?php echo e(route('journals.store')); ?>" id="journalForm" enctype="multipart/form-data">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="selected_cash_account_id" value="<?php echo e($selectedAccount->id ?? ''); ?>">

                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="card-title">
                                <?php if($selectedAccount): ?>
                                    <?php echo e($selectedAccount->code); ?> - <?php echo e($selectedAccount->name); ?>

                                <?php else: ?>
                                    Jurnal Kas/Bank
                                <?php endif; ?>
                            </h3>
                        </div>
                        <?php if($selectedAccount): ?>
                            <div class="text-end">
                                <small class="text-muted">Saldo Awal:</small><br>
                                <strong><?php echo e(number_format($openingBalance, 0, ',', '.')); ?></strong>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="card-body">
                        <?php if($selectedAccount): ?>
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
                                        <?php $__currentLoopData = $journalsHistory; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $history): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <tr data-existing="1" data-balance="<?php echo e($history['balance']); ?>" style="border: 1px solid #dee2e6; background-color: #f8f9fa;">
                                                <td style="border: 1px solid #dee2e6; padding: 4px; font-size: 12px;"><?php echo e($history['date']); ?></td>
                                                <td style="border: 1px solid #dee2e6; padding: 4px; font-size: 12px;"><?php echo e($history['description']); ?></td>
                                                <td style="border: 1px solid #dee2e6; padding: 4px; font-size: 12px;">-</td>
                                                <td style="border: 1px solid #dee2e6; padding: 4px; font-size: 12px;">-</td>
                                                <td style="border: 1px solid #dee2e6; padding: 4px; font-size: 12px;"><?php echo e($history['proof_number']); ?></td>
                                                <td style="border: 1px solid #dee2e6; padding: 4px; font-size: 12px; text-align: right;"><?php echo e($history['cash_in'] > 0 ? number_format($history['cash_in'], 0, ',', '.') : ''); ?></td>
                                                <td style="border: 1px solid #dee2e6; padding: 4px; font-size: 12px; text-align: right;"><?php echo e($history['cash_out'] > 0 ? number_format($history['cash_out'], 0, ',', '.') : ''); ?></td>
                                                <td style="border: 1px solid #dee2e6; padding: 4px; font-size: 12px;">-</td>
                                                <td style="border: 1px solid #dee2e6; padding: 4px; font-size: 12px;">-</td>
                                                <td style="border: 1px solid #dee2e6; padding: 4px; font-size: 12px;">-</td>
                                                <td style="border: 1px solid #dee2e6; padding: 4px; font-size: 12px; text-align: right; background: #e3f2fd;"><?php echo e(number_format($history['balance'], 0, ',', '.')); ?></td>
                                                <td style="border: 1px solid #dee2e6; padding: 4px; text-align: center;">-</td>
                                            </tr>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="text-center py-5">
                                <p class="text-muted">Pilih akun kas/bank dari menu di atas untuk memulai membuat jurnal.</p>
                            </div>
                        <?php endif; ?>

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
                        <?php if($selectedAccount): ?>
                            <button type="submit" class="btn btn-primary">Simpan</button>
                        <?php endif; ?>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <?php if($selectedAccount): ?>
        <?php $__env->startPush('scripts'); ?>
            <script>
                let lineIndex = 0;
                let openingBalance = <?php echo e($openingBalance); ?>;
                let selectedCashAccountId = <?php echo e($selectedAccount->id); ?>;
                let currentBalance = openingBalance;
                const formatter = new Intl.NumberFormat('id-ID');

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
                    // Calculate current balance from history
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
                            <input type="date" name="entries[${lineIndex}][date]" class="form-control form-control-sm" style="border: none; font-size: 12px;" value="<?php echo e(date('Y-m-d')); ?>">
                        </td>
                        <td style="border: 1px solid #dee2e6; padding: 2px;">
                            <input type="text" name="entries[${lineIndex}][description]" class="form-control form-control-sm" style="border: none; font-size: 12px;" placeholder="Deskripsi">
                        </td>
                        <td style="border: 1px solid #dee2e6; padding: 2px;">
                            <input type="text" name="entries[${lineIndex}][pic]" class="form-control form-control-sm" style="border: none; font-size: 12px;" placeholder="PIC">
                        </td>
                        <td style="border: 1px solid #dee2e6; padding: 2px;">
                            <input type="file" name="entries[${lineIndex}][file]" class="form-control form-control-sm" style="border: none; font-size: 11px;" accept=".jpg,.jpeg,.png,.pdf" onchange="generateProofNumber(this, ${lineIndex})">
                        </td>
                        <td style="border: 1px solid #dee2e6; padding: 2px;">
                            <input type="text" name="entries[${lineIndex}][proof_number]" class="form-control form-control-sm proof-number" style="border: none; font-size: 12px;" placeholder="Auto" readonly>
                        </td>
                        <td style="border: 1px solid #dee2e6; padding: 2px;">
                            <input type="number" name="entries[${lineIndex}][cash_in]" class="form-control form-control-sm cash-in" style="border: none; font-size: 12px; text-align: right;" placeholder="0" min="0" step="1" onchange="calculateBalance(this)" oninput="handleInput(this)">
                        </td>
                        <td style="border: 1px solid #dee2e6; padding: 2px;">
                            <input type="number" name="entries[${lineIndex}][cash_out]" class="form-control form-control-sm cash-out" style="border: none; font-size: 12px; text-align: right;" placeholder="0" min="0" step="1" onchange="calculateBalance(this)" oninput="handleInput(this)">
                        </td>
                        <td style="border: 1px solid #dee2e6; padding: 2px;">
                            <select name="entries[${lineIndex}][debit_account_id]" class="form-control form-control-sm" style="border: none; font-size: 12px;">
                                ${accountOptions}
                            </select>
                        </td>
                        <td style="border: 1px solid #dee2e6; padding: 2px;">
                            <select name="entries[${lineIndex}][credit_account_id]" class="form-control form-control-sm" style="border: none; font-size: 12px;">
                                ${accountOptions}
                            </select>
                        </td>
                        <td style="border: 1px solid #dee2e6; padding: 2px;">
                            <select name="entries[${lineIndex}][cashflow_id]" class="form-control form-control-sm" style="border: none; font-size: 12px;">
                                ${cashflowOptions}
                            </select>
                        </td>
                        <td style="border: 1px solid #dee2e6; padding: 2px; text-align: right; background: #e8f5e8;">
                            <span class="balance-display" style="font-size: 12px; font-weight: bold;">${formatter.format(currentBalance)}</span>
                        </td>
                        <td style="border: 1px solid #dee2e6; padding: 2px; text-align: center;">
                            <button type="button" class="btn btn-sm btn-danger" onclick="removeRow(this)" style="font-size: 10px; padding: 2px 6px;">×</button>
                        </td>
                    `;
                    
                    tbody.appendChild(row);
                    lineIndex++;
                }

                function handleInput(input) {
                    const row = input.closest('tr');
                    const inputs = row.querySelectorAll('input[type="text"], input[type="number"], select');
                    let hasValue = false;
                    
                    inputs.forEach(inp => {
                        if (inp.value && inp.value.trim() !== '') {
                            hasValue = true;
                        }
                    });
                    
                    // If this is the last row and user started typing, add new row
                    const allRows = document.querySelectorAll('#journalLines tr:not([data-existing="1"])');
                    const isLastRow = row === allRows[allRows.length - 1];
                    
                    if (hasValue && isLastRow) {
                        addJournalLine();
                    }
                }

                function calculateBalance(input) {
                    const row = input.closest('tr');
                    const cashInInput = row.querySelector('.cash-in');
                    const cashOutInput = row.querySelector('.cash-out');
                    const balanceDisplay = row.querySelector('.balance-display');
                    
                    // Get previous balance
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
                    
                    // Recalculate all subsequent rows
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
                        const now = new Date();
                        const timestamp = now.getFullYear().toString() + 
                                        (now.getMonth() + 1).toString().padStart(2, '0') + 
                                        now.getDate().toString().padStart(2, '0') + 
                                        now.getHours().toString().padStart(2, '0') + 
                                        now.getMinutes().toString().padStart(2, '0');
                        const proofNumber = 'PROOF-' + timestamp + '-' + (index + 1);
                        
                        const proofInput = input.closest('tr').querySelector('.proof-number');
                        proofInput.value = proofNumber;
                    }
                }

                function removeRow(button) {
                    const row = button.closest('tr');
                    const allEditableRows = document.querySelectorAll('#journalLines tr:not([data-existing="1"])');
                    
                    if (allEditableRows.length > 1) {
                        row.remove();
                        
                        // Recalculate balances for remaining rows
                        const remainingRows = document.querySelectorAll('#journalLines tr:not([data-existing="1"])');
                        remainingRows.forEach((r, index) => {
                            calculateBalance(r.querySelector('.cash-in'));
                        });
                    }
                }
            </script>
        <?php $__env->stopPush(); ?>
    <?php endif; ?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/najibzulfan/project/akuntansi/resources/views/journals/create.blade.php ENDPATH**/ ?>