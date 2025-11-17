<?php $__env->startSection('title', 'List of Accounts'); ?>

<?php $__env->startSection('page-header'); ?>
    <div class="page-pretitle">Master Data</div>
    <h2 class="page-title">List of Accounts</h2>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('page-actions'); ?>
    <a href="<?php echo e(route('accounts.create')); ?>" class="btn btn-primary">
        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="m0 0h24v24H0z" fill="none"/><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        Tambah Akun
    </a>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Daftar Akun</h3>
            </div>
            <div class="table-responsive">
                <table class="table table-vcenter card-table">
                    <thead>
                        <tr>
                            <th>Kode</th>
                            <th>Nama Akun</th>
                            <th>Tipe</th>
                            <th>Kategori</th>
                            <th>Parent</th>
                            <th>Status</th>
                            <th class="w-1">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $accounts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $account): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td><span class="text-muted"><?php echo e($account->code); ?></span></td>
                            <td><?php echo e($account->name); ?></td>
                            <td>
                                <span class="badge bg-<?php echo e($account->type === 'asset' ? 'success' : ($account->type === 'liability' ? 'danger' : ($account->type === 'equity' ? 'info' : ($account->type === 'revenue' ? 'primary' : 'warning')))); ?>">
                                    <?php echo e(ucfirst($account->type)); ?>

                                </span>
                            </td>
                            <td><?php echo e($account->category); ?></td>
                            <td><?php echo e($account->parent?->name ?? '-'); ?></td>
                            <td>
                                <span class="badge bg-<?php echo e($account->is_active ? 'success' : 'secondary'); ?>">
                                    <?php echo e($account->is_active ? 'Aktif' : 'Nonaktif'); ?>

                                </span>
                            </td>
                            <td>
                                <div class="btn-list flex-nowrap">
                                    <a href="<?php echo e(route('accounts.edit', $account)); ?>" class="btn btn-sm btn-outline-primary">
                                        Edit
                                    </a>
                                    <form method="POST" action="<?php echo e(route('accounts.destroy', $account)); ?>" class="d-inline">
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
                                Belum ada akun
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <?php if($accounts->hasPages()): ?>
            <div class="card-footer">
                <?php echo e($accounts->links()); ?>

            </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/najibzulfan/project/akuntansi/resources/views/accounts/index.blade.php ENDPATH**/ ?>