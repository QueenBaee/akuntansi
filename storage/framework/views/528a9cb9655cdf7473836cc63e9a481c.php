<?php $__env->startSection('title', 'User Accounts'); ?>

<?php $__env->startSection('page-header'); ?>
<div class="page-pretitle">Management</div>
<h2 class="page-title">User Accounts</h2>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('page-actions'); ?>
<a href="<?php echo e(route('user-accounts.create')); ?>" class="btn btn-primary">
    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
        <path stroke="none" d="m0 0h24v24H0z" fill="none"/>
        <line x1="12" y1="5" x2="12" y2="19"/>
        <line x1="5" y1="12" x2="19" y2="12"/>
    </svg>
    Add User Account
</a>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">User Account List</h3>
            </div>
            <div class="table-responsive">
                <table class="table table-vcenter card-table">
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Account</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Created</th>
                            <th class="w-1">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $userAccounts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $userAccount): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td>
                                    <div class="d-flex py-1 align-items-center">
                                        <span class="avatar me-2" style="background-image: url(https://ui-avatars.com/api/?name=<?php echo e(urlencode($userAccount->user->name)); ?>&background=206bc4&color=fff)"></span>
                                        <div class="flex-fill">
                                            <div class="font-weight-medium"><?php echo e($userAccount->user->name); ?></div>
                                            <div class="text-muted"><?php echo e($userAccount->user->email); ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div>
                                        <div class="font-weight-medium"><?php echo e($userAccount->account->name); ?></div>
                                        <div class="text-muted"><?php echo e($userAccount->account->code); ?> - <?php echo e(ucfirst($userAccount->account->type)); ?></div>
                                    </div>
                                </td>
                                <td>
                                    <?php if($userAccount->role): ?>
                                        <span class="badge bg-blue-lt"><?php echo e($userAccount->role); ?></span>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge <?php echo e($userAccount->is_active ? 'bg-green-lt' : 'bg-red-lt'); ?>">
                                        <?php echo e($userAccount->is_active ? 'Active' : 'Inactive'); ?>

                                    </span>
                                </td>
                                <td class="text-muted">
                                    <?php echo e($userAccount->created_at->format('d M Y')); ?>

                                </td>
                                <td>
                                    <div class="btn-list flex-nowrap">
                                        <a href="<?php echo e(route('user-accounts.show', $userAccount)); ?>" class="btn btn-white btn-sm">View</a>
                                        <a href="<?php echo e(route('user-accounts.edit', $userAccount)); ?>" class="btn btn-white btn-sm">Edit</a>
                                        <form action="<?php echo e(route('user-accounts.destroy', $userAccount)); ?>" method="POST" style="display: inline;" 
                                              onsubmit="return confirm('Are you sure you want to delete this user account?')">
                                            <?php echo csrf_field(); ?>
                                            <?php echo method_field('DELETE'); ?>
                                            <button type="submit" class="btn btn-white btn-sm">Delete</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="6" class="text-center text-muted">No user accounts found</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <?php if($userAccounts->hasPages()): ?>
                <div class="card-footer d-flex align-items-center">
                    <div class="text-muted">
                        Showing <?php echo e($userAccounts->firstItem()); ?> to <?php echo e($userAccounts->lastItem()); ?> of <?php echo e($userAccounts->total()); ?> entries
                    </div>
                    <div class="ms-auto">
                        <?php echo e($userAccounts->links()); ?>

                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/najibzulfan/project/akuntansi/resources/views/user-accounts/index.blade.php ENDPATH**/ ?>