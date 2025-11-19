<?php $__env->startSection('title', 'User Management'); ?>

<?php $__env->startSection('page-header'); ?>
    <div class="page-pretitle">Master Data</div>
    <h2 class="page-title">User Management</h2>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('page-actions'); ?>
    <a href="<?php echo e(route('users.create')); ?>" class="btn btn-primary">
        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="m0 0h24v24H0z" fill="none"/><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        Add New User
    </a>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">User List</h3>
                <div class="card-actions">
                    <form method="GET" class="row g-2">
                        <div class="col-auto">
                            <input type="text" name="search" value="<?php echo e(request('search')); ?>" placeholder="Search users..." class="form-control form-control-sm">
                        </div>
                        <div class="col-auto">
                            <select name="role" class="form-select form-select-sm">
                                <option value="">All Roles</option>
                                <option value="admin" <?php echo e(request('role') === 'admin' ? 'selected' : ''); ?>>Admin</option>
                                <option value="user" <?php echo e(request('role') === 'user' ? 'selected' : ''); ?>>User</option>
                            </select>
                        </div>
                        <div class="col-auto">
                            <select name="is_active" class="form-select form-select-sm">
                                <option value="">All Status</option>
                                <option value="1" <?php echo e(request('is_active') === '1' ? 'selected' : ''); ?>>Active</option>
                                <option value="0" <?php echo e(request('is_active') === '0' ? 'selected' : ''); ?>>Inactive</option>
                            </select>
                        </div>
                        <div class="col-auto">
                            <button type="submit" class="btn btn-outline-primary btn-sm">Filter</button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-vcenter card-table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th class="w-1">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td><?php echo e($user->name); ?></td>
                            <td><?php echo e($user->email); ?></td>
                            <td><?php echo e($user->roles->pluck('name')->join(', ')); ?></td>
                            <td>
                                <span class="badge bg-<?php echo e($user->is_active ? 'success' : 'secondary'); ?>">
                                    <?php echo e($user->is_active ? 'Active' : 'Inactive'); ?>

                                </span>
                            </td>
                            <td>
                                <div class="btn-list flex-nowrap">
                                    <a href="<?php echo e(route('users.edit', $user)); ?>" class="btn btn-sm btn-outline-primary">Edit</a>
                                    <form method="POST" action="<?php echo e(route('users.destroy', $user)); ?>" class="d-inline">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('DELETE'); ?>
                                        <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure?')">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">
                                No users found
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <?php if($users->hasPages()): ?>
            <div class="card-footer">
                <?php echo e($users->links()); ?>

            </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/najibzulfan/project/akuntansi/resources/views/users/index.blade.php ENDPATH**/ ?>