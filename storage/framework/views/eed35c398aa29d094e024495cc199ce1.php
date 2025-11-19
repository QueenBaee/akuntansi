<?php $__env->startSection('content'); ?>
<div class="container">
    <h4>Trial Balance</h4>

    
    <form method="GET" action="<?php echo e(route('trial-balance.index')); ?>" class="mb-3">
        <div class="input-group">
            <input type="text" name="search" value="<?php echo e(request('search')); ?>" class="form-control" placeholder="Search Kode / Keterangan">
            <button class="btn btn-outline-secondary" type="submit">Search</button>
        </div>
    </form>

    <a href="<?php echo e(route('trial-balance.create')); ?>" class="btn btn-primary mb-3">Tambah Root</a>

    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>Kode</th>
                <th>Keterangan</th>
                <th>Parent</th>
                <th>Level</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php
                function renderRows($items, $prefix = '') {
                    foreach ($items as $item) {
                        // Skip Beban (E) karena nanti digroup
                        if(substr($item->kode,0,1) == 'E') continue;

                        echo '<tr>';
                        echo '<td>' . $item->kode . '</td>';
                        echo '<td>' . $prefix . $item->keterangan . '</td>';
                        echo '<td>' . ($item->parent?->kode ?? '-') . '</td>';
                        echo '<td>' . $item->level . '</td>';
                        echo '<td>
                                <a href="' . route('trial-balance.edit', $item->id) . '" class="btn btn-warning btn-sm">Edit</a>
                                <a href="' . route('trial-balance.create') . '?parent_id=' . $item->id . '" class="btn btn-success btn-sm">Tambah Sub-Akun</a>
                                <form action="' . route('trial-balance.destroy', $item->id) . '" method="POST" style="display:inline-block">
                                    ' . csrf_field() . '
                                    ' . method_field('DELETE') . '
                                    <button onclick="return confirm(\'Hapus?\')" class="btn btn-danger btn-sm">Hapus</button>
                                </form>
                              </td>';
                        echo '</tr>';
                        if ($item->children->count() > 0) {
                            renderRows($item->children, $prefix . '    ');
                        }
                    }
                }
                renderRows($items);
            ?>
        </tbody>
    </table>

    
    <h5 class="mt-5">Beban (E) Group</h5>
    <?php $__currentLoopData = $bebanItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $group => $groupItems): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <h6><?php echo e($group); ?></h6>
        <table class="table table-bordered table-sm mb-3">
            <thead>
                <tr>
                    <th>Kode</th>
                    <th>Keterangan</th>
                    <th>Parent</th>
                    <th>Level</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php $__currentLoopData = $groupItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td><?php echo e($item->kode); ?></td>
                        <td><?php echo e($item->keterangan); ?></td>
                        <td><?php echo e($item->parent?->kode ?? '-'); ?></td>
                        <td><?php echo e($item->level); ?></td>
                        <td>
                            <a href="<?php echo e(route('trial-balance.edit', $item->id)); ?>" class="btn btn-warning btn-sm">Edit</a>
                            <a href="<?php echo e(route('trial-balance.create')); ?>?parent_id=<?php echo e($item->id); ?>" class="btn btn-success btn-sm">Tambah Sub-Akun</a>
                            <form action="<?php echo e(route('trial-balance.destroy', $item->id)); ?>" method="POST" style="display:inline-block">
                                <?php echo csrf_field(); ?>
                                <?php echo method_field('DELETE'); ?>
                                <button onclick="return confirm('Hapus?')" class="btn btn-danger btn-sm">Hapus</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/najibzulfan/project/akuntansi/resources/views/trial_balance/index.blade.php ENDPATH**/ ?>