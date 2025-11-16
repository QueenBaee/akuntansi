<?php $__env->startSection('title', 'Transaksi Kas'); ?>

<?php $__env->startSection('page-header'); ?>
    <div class="page-pretitle">Transaksi</div>
    <h2 class="page-title">Transaksi Kas</h2>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('page-actions'); ?>
    <a href="<?php echo e(route('cash-transactions.create')); ?>" class="btn btn-primary">
        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="m0 0h24v24H0z" fill="none"/><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        Tambah Transaksi
    </a>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Daftar Transaksi Kas</h3>
                <div class="card-actions">
                    <form method="GET" class="d-flex">
                        <input type="date" name="date_from" class="form-control me-2" value="<?php echo e(request('date_from')); ?>" placeholder="Dari Tanggal">
                        <input type="date" name="date_to" class="form-control me-2" value="<?php echo e(request('date_to')); ?>" placeholder="Sampai Tanggal">
                        <button type="submit" class="btn btn-outline-primary">Filter</button>
                    </form>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-vcenter card-table">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Tipe</th>
                            <th>Akun Kas</th>
                            <th>Akun Lawan</th>
                            <th>Jumlah</th>
                            <th>Deskripsi</th>
                            <th class="w-1">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $transactions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $transaction): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td><?php echo e($transaction->date->format('d/m/Y')); ?></td>
                            <td>
                                <span class="badge bg-<?php echo e($transaction->type === 'in' ? 'success' : 'danger'); ?>">
                                    <?php echo e($transaction->type === 'in' ? 'Masuk' : 'Keluar'); ?>

                                </span>
                            </td>
                            <td><?php echo e($transaction->cashAccount->name ?? '-'); ?></td>
                            <td><?php echo e($transaction->contraAccount->name ?? '-'); ?></td>
                            <td>Rp <?php echo e(number_format($transaction->amount, 0, ',', '.')); ?></td>
                            <td><?php echo e($transaction->description); ?></td>
                            <td>
                                <div class="btn-list flex-nowrap">
                                    <a href="<?php echo e(route('cash-transactions.edit', $transaction)); ?>" class="btn btn-sm btn-outline-primary">
                                        Edit
                                    </a>
                                    <form method="POST" action="<?php echo e(route('cash-transactions.destroy', $transaction)); ?>" class="d-inline">
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
                                Belum ada transaksi kas
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <?php if($transactions->hasPages()): ?>
            <div class="card-footer">
                <?php echo e($transactions->links()); ?>

            </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\laragon\www\akuntansi\resources\views/transactions/cash/index.blade.php ENDPATH**/ ?>