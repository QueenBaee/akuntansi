

<?php $__env->startSection('content'); ?>
<div class="container">
    <h4>Cashflow</h4>

    
    <form method="GET" action="<?php echo e(route('cashflow.index')); ?>" class="mb-3">
        <div class="input-group">
            <input type="text" name="search" value="<?php echo e(request('search')); ?>" 
                class="form-control" placeholder="Search Kode / Keterangan">
            <button class="btn btn-outline-secondary" type="submit">Search</button>
        </div>
    </form>

    <a href="<?php echo e(route('cashflow.create')); ?>" class="btn btn-primary mb-3">Tambah Data</a>

    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>Kode</th>
                <th>Keterangan</th>
                <th>Kategori</th>
                <th>Tipe</th>
                <th>Nominal</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php $__empty_1 = true; $__currentLoopData = $data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                    <td><?php echo e($row->kode); ?></td>
                    <td><?php echo e($row->keterangan); ?></td>
                    <td><?php echo e(ucfirst($row->kategori)); ?></td>
                    <td><?php echo e(ucfirst($row->tipe)); ?></td>
                    <td>Rp <?php echo e(number_format($row->nominal, 0, ',', '.')); ?></td>

                    <td>
                        <a href="<?php echo e(route('cashflow.edit', $row->id)); ?>" 
                        class="btn btn-warning btn-sm">Edit</a>

                        <form action="<?php echo e(route('cashflow.destroy', $row->id)); ?>" 
                            method="POST" style="display:inline-block">
                            <?php echo csrf_field(); ?>
                            <?php echo method_field('DELETE'); ?>
                            <button onclick="return confirm('Hapus data ini?')" 
                                    class="btn btn-danger btn-sm">Hapus</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="6" class="text-center">Tidak ada data</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Akutansi\akuntansi\resources\views/cashflow/index.blade.php ENDPATH**/ ?>