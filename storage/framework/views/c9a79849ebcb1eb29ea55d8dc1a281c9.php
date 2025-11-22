<?php $__env->startSection('content'); ?>
<div class="container">
    <h4>Edit Trial Balance</h4>

    <form action="<?php echo e(route('trial-balance.update', $trial_balance->id)); ?>" method="POST">
        <?php echo csrf_field(); ?>
        <?php echo method_field('PUT'); ?>

        <div class="mb-3">
            <label>Kode</label>
            <input type="text" name="kode" class="form-control" required value="<?php echo e(old('kode', $trial_balance->kode)); ?>">
        </div>

        <div class="mb-3">
            <label>Keterangan</label>
            <input type="text" name="keterangan" class="form-control" required value="<?php echo e(old('keterangan', $trial_balance->keterangan)); ?>">
        </div>

        <button class="btn btn-primary">Update</button>
        <a href="<?php echo e(route('trial-balance.index')); ?>" class="btn btn-secondary">Batal</a>
    </form>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/najibzulfan/project/akuntansi/resources/views/trial_balance/edit.blade.php ENDPATH**/ ?>