<?php $__env->startSection('content'); ?>
<div class="container">
    <h4>Tambah Trial Balance</h4>

    <form action="<?php echo e(route('trial-balance.store')); ?>" method="POST">
        <?php echo csrf_field(); ?>

        <?php if($parent): ?>
            <p><strong>Menambah Sub Akun:</strong> <?php echo e($parent->kode); ?> - <?php echo e($parent->keterangan); ?></p>
            <input type="hidden" name="parent_id" value="<?php echo e($parent->id); ?>">
        <?php endif; ?>

        <div class="mb-3">
            <label>Kode</label>
            <input type="text" name="kode" class="form-control" required value="<?php echo e(old('kode')); ?>">
        </div>

        <div class="mb-3">
            <label>Keterangan</label>
            <input type="text" name="keterangan" class="form-control" required value="<?php echo e(old('keterangan')); ?>">
        </div>

        <button class="btn btn-primary">Simpan</button>
        <a href="<?php echo e(route('trial-balance.index')); ?>" class="btn btn-secondary">Batal</a>
    </form>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/najibzulfan/project/akuntansi/resources/views/trial_balance/create.blade.php ENDPATH**/ ?>