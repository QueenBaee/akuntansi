<?php $__env->startSection('content'); ?>
<div class="container">
    <h4>Tambah Cashflow</h4>

    <form action="<?php echo e(route('cashflow.store')); ?>" method="POST">
        <?php echo csrf_field(); ?>

        <div class="mb-3">
            <label>Kode</label>
            <input type="text" name="kode" class="form-control" required value="<?php echo e(old('kode')); ?>">
        </div>

        <div class="mb-3">
            <label>Keterangan</label>
            <input type="text" name="keterangan" class="form-control" required value="<?php echo e(old('keterangan')); ?>">
        </div>

        <div class="mb-3">
            <label>Pilih Akun Trial Balance</label>
            <select name="trial_balance_id" class="form-control" required>
                <option value="">-- Pilih Akun --</option>
                <?php $__currentLoopData = $accounts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $acc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($acc->id); ?>">
                        <?php echo e($acc->kode); ?> - <?php echo e($acc->keterangan); ?>

                    </option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>

        <button class="btn btn-primary">Simpan</button>
        <a href="<?php echo e(route('cashflow.index')); ?>" class="btn btn-secondary">Batal</a>
    </form>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/najibzulfan/project/akuntansi/resources/views/cashflow/create.blade.php ENDPATH**/ ?>