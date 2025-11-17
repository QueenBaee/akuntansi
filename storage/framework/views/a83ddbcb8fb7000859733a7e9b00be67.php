<?php $__env->startSection('title', 'Tambah Jurnal'); ?>

<?php $__env->startSection('page-header'); ?>
    <div class="page-pretitle">Transaksi</div>
    <h2 class="page-title">Tambah Jurnal Umum</h2>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="row">
    <div class="col-12">
        <form method="POST" action="<?php echo e(route('journals.store')); ?>" id="journalForm">
            <?php echo csrf_field(); ?>
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Form Jurnal Umum</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label required">Tanggal</label>
                                <input type="date" name="date" class="form-control <?php $__errorArgs = ['date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                       value="<?php echo e(old('date', date('Y-m-d'))); ?>" required>
                                <?php $__errorArgs = ['date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <div class="invalid-feedback"><?php echo e($message); ?></div>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Referensi</label>
                                <input type="text" name="reference" class="form-control <?php $__errorArgs = ['reference'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                       value="<?php echo e(old('reference')); ?>" placeholder="Nomor bukti">
                                <?php $__errorArgs = ['reference'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <div class="invalid-feedback"><?php echo e($message); ?></div>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label required">Deskripsi</label>
                                <input type="text" name="description" class="form-control <?php $__errorArgs = ['description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                       value="<?php echo e(old('description')); ?>" required>
                                <?php $__errorArgs = ['description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <div class="invalid-feedback"><?php echo e($message); ?></div>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4>Detail Jurnal</h4>
                        <button type="button" class="btn btn-outline-primary btn-sm" onclick="addJournalLine()">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="m0 0h24v24H0z" fill="none"/><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                            Tambah Baris
                        </button>
                    </div>
                    
                    <div class="table-responsive">
                        <table class="table table-bordered" id="journalTable">
                            <thead>
                                <tr>
                                    <th width="40%">Akun</th>
                                    <th width="25%">Deskripsi</th>
                                    <th width="15%">Debit</th>
                                    <th width="15%">Kredit</th>
                                    <th width="5%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="journalLines">
                                <!-- Journal lines will be added here -->
                            </tbody>
                            <tfoot>
                                <tr class="table-active">
                                    <td colspan="2"><strong>Total</strong></td>
                                    <td><strong id="totalDebit">0</strong></td>
                                    <td><strong id="totalCredit">0</strong></td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    
                    <?php $__errorArgs = ['details'];
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

function addJournalLine() {
    const tbody = document.getElementById('journalLines');
    const row = document.createElement('tr');
    row.innerHTML = `
        <td>
            <select name="details[${lineIndex}][account_id]" class="form-select" required>
                <option value="">Pilih Akun</option>
                <?php $__currentLoopData = $accounts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $account): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($account->id); ?>"><?php echo e($account->code); ?> - <?php echo e($account->name); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </td>
        <td>
            <input type="text" name="details[${lineIndex}][description]" class="form-control" placeholder="Deskripsi">
        </td>
        <td>
            <input type="number" name="details[${lineIndex}][debit]" class="form-control debit-input" step="0.01" min="0" onchange="calculateTotals()">
        </td>
        <td>
            <input type="number" name="details[${lineIndex}][credit]" class="form-control credit-input" step="0.01" min="0" onchange="calculateTotals()">
        </td>
        <td>
            <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeJournalLine(this)">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="m0 0h24v24H0z" fill="none"/><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
        </td>
    `;
    tbody.appendChild(row);
    lineIndex++;
}

function removeJournalLine(button) {
    button.closest('tr').remove();
    calculateTotals();
}

function calculateTotals() {
    let totalDebit = 0;
    let totalCredit = 0;
    
    document.querySelectorAll('.debit-input').forEach(input => {
        totalDebit += parseFloat(input.value) || 0;
    });
    
    document.querySelectorAll('.credit-input').forEach(input => {
        totalCredit += parseFloat(input.value) || 0;
    });
    
    document.getElementById('totalDebit').textContent = totalDebit.toLocaleString('id-ID');
    document.getElementById('totalCredit').textContent = totalCredit.toLocaleString('id-ID');
}

// Add initial lines
document.addEventListener('DOMContentLoaded', function() {
    addJournalLine();
    addJournalLine();
});
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/najibzulfan/project/akuntansi/resources/views/journals/create.blade.php ENDPATH**/ ?>