

<?php $__env->startSection('title', 'Jurnal Umum'); ?>

<?php $__env->startSection('page-header'); ?>
    <div class="page-pretitle">Transaksi</div>
    <h2 class="page-title">Jurnal Umum</h2>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('page-actions'); ?>
    <a href="<?php echo e(route('journals.create')); ?>" class="btn btn-primary">
        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="m0 0h24v24H0z" fill="none"/><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        Tambah Jurnal
    </a>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Daftar Jurnal</h3>
            </div>
            <div class="table-responsive">
                <table class="table table-vcenter card-table">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Nomor</th>
                            <th>Deskripsi</th>
                            <th>Referensi</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th class="w-1">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $journals; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $journal): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td><?php echo e($journal->date->format('d/m/Y')); ?></td>
                            <td><span class="text-muted"><?php echo e($journal->number); ?></span></td>
                            <td><?php echo e($journal->description); ?></td>
                            <td><?php echo e($journal->reference ?? '-'); ?></td>
                            <td>Rp <?php echo e(number_format($journal->total_amount, 0, ',', '.')); ?></td>
                            <td>
                                <span class="badge bg-<?php echo e($journal->is_posted ? 'success' : 'warning'); ?>">
                                    <?php echo e($journal->is_posted ? 'Posted' : 'Draft'); ?>

                                </span>
                            </td>
                            <td>
                                <div class="btn-list flex-nowrap">
                                    <a href="<?php echo e(route('journals.show', $journal)); ?>" class="btn btn-sm btn-outline-info">
                                        Detail
                                    </a>
                                    <a href="<?php echo e(route('journals.edit', $journal)); ?>" class="btn btn-sm btn-outline-primary">
                                        Edit
                                    </a>
                                    <form method="POST" action="<?php echo e(route('journals.destroy', $journal)); ?>" class="d-inline">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('DELETE'); ?>
                                        <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Yakin hapus?')">
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                Belum ada jurnal
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <?php if($journals->hasPages()): ?>
            <div class="card-footer">
                <?php echo e($journals->links()); ?>

            </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Akutansi\akuntansi\resources\views/journals/index.blade.php ENDPATH**/ ?>