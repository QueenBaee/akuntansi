<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title><?php echo $__env->yieldContent('title', 'Dashboard'); ?> - Sistem Akuntansi</title>
    <link href="https://cdn.jsdelivr.net/npm/@tabler/core@1.0.0-beta17/dist/css/tabler.min.css" rel="stylesheet">
    <style>
        .dropdown-item.active {
            background-color: #206bc4;
            color: white;
        }
        .dropdown-item.active:hover {
            background-color: #1a5ba8;
            color: white;
        }
    </style>
</head>

<body>
    <div class="page">
        <!-- Navbar -->
        <header class="navbar navbar-expand-md navbar-light d-print-none">
            <div class="container-xl">
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbar-menu">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <h1 class="navbar-brand navbar-brand-autodark d-none-navbar-horizontal pe-0 pe-md-3">
                    <a href="<?php echo e(route('dashboard')); ?>">Sistem Akuntansi</a>
                </h1>
                <div class="navbar-nav flex-row order-md-last">
                    <div class="nav-item dropdown">
                        <a href="#" class="nav-link d-flex lh-1 text-reset p-0" data-bs-toggle="dropdown">
                            <span class="avatar avatar-sm"
                                style="background-image: url(https://ui-avatars.com/api/?name=<?php echo e(urlencode(auth()->user()->name)); ?>&background=206bc4&color=fff)"></span>
                            <div class="d-none d-xl-block ps-2">
                                <div><?php echo e(auth()->user()->name); ?></div>
                                <div class="mt-1 small text-muted"><?php echo e(auth()->user()->email); ?></div>
                            </div>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                            <form method="POST" action="<?php echo e(route('logout')); ?>" style="display: inline;">
                                <?php echo csrf_field(); ?>
                                <button type="submit" class="dropdown-item"
                                    style="border: none; background: none; width: 100%; text-align: left;">Logout</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <div class="navbar-expand-md">
            <div class="collapse navbar-collapse" id="navbar-menu">
                <div class="navbar navbar-light">
                    <div class="container-xl">
                        <ul class="navbar-nav">
                            <li class="nav-item <?php echo e(request()->routeIs('dashboard') ? 'active' : ''); ?>">
                                <a class="nav-link" href="<?php echo e(route('dashboard')); ?>">
                                    <span class="nav-link-icon d-md-none d-lg-inline-block">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="m0 0h24v24H0z" fill="none"/>
                                            <polyline points="5 12 3 12 12 3 21 12 19 12"/>
                                            <path d="m5 12v7a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-7"/>
                                            <path d="m9 21v-6a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v6"/>
                                        </svg>
                                    </span>
                                    <span class="nav-link-title">Dashboard</span>
                                </a>
                            </li>
                            <li class="nav-item dropdown <?php echo e(request()->routeIs('journals.*') && session('selected_account_type') == 'kas' ? 'active' : ''); ?>">
                                <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown" role="button">
                                    <span class="nav-link-icon d-md-none d-lg-inline-block">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                            <path d="M7 15h-3a1 1 0 0 1 -1 -1v-8a1 1 0 0 1 1 -1h12a1 1 0 0 1 1 1v3"/>
                                            <path d="M7 9m0 1a1 1 0 0 1 1 -1h12a1 1 0 0 1 1 1v8a1 1 0 0 1 -1 1h-12a1 1 0 0 1 -1 -1z"/>
                                            <path d="M12 14a2 2 0 1 0 4 0a2 2 0 0 0 -4 0"/>
                                        </svg>
                                    </span>
                                    <span class="nav-link-title">Kas</span>
                                </a>
                                <div class="dropdown-menu">
                                    <?php $__empty_1 = true; $__currentLoopData = $cashAccounts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $account): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                        <a class="dropdown-item cash-account-item" href="#" data-account-id="<?php echo e($account->id); ?>" onclick="selectCashAccount(<?php echo e($account->id); ?>, '<?php echo e($account->name); ?>', <?php echo e($account->getCurrentBalance()); ?>)"><?php echo e($account->name); ?></a>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                        <span class="dropdown-item text-muted">No cash accounts available</span>
                                    <?php endif; ?>
                                </div>
                            </li>

                            <li class="nav-item dropdown <?php echo e(request()->routeIs('journals.*') && session('selected_account_type') == 'bank' ? 'active' : ''); ?>">
                                <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown" role="button">
                                    <span class="nav-link-icon d-md-none d-lg-inline-block">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                            <path d="M3 21l18 0"/>
                                            <path d="M3 10l18 0"/>
                                            <path d="M5 6l7 -3l7 3"/>
                                            <path d="M4 10l0 11"/>
                                            <path d="M20 10l0 11"/>
                                            <path d="M8 14l0 3"/>
                                            <path d="M12 14l0 3"/>
                                            <path d="M16 14l0 3"/>
                                        </svg>
                                    </span>
                                    <span class="nav-link-title">Bank</span>
                                </a>
                                <div class="dropdown-menu">
                                    <?php $__empty_1 = true; $__currentLoopData = $bankAccounts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $account): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                        <a class="dropdown-item bank-account-item" href="#" data-account-id="<?php echo e($account->id); ?>" onclick="selectCashAccount(<?php echo e($account->id); ?>, '<?php echo e($account->name); ?>', <?php echo e($account->getCurrentBalance()); ?>)"><?php echo e($account->name); ?></a>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                        <span class="dropdown-item text-muted">No bank accounts available</span>
                                    <?php endif; ?>
                                </div>
                            </li>


                            <?php if(\Spatie\Permission\PermissionServiceProvider::bladeMethodWrapper('hasRole', 'admin')): ?>
                                <li class="nav-item dropdown <?php echo e(request()->routeIs('accounts.*') || request()->routeIs('user-accounts.*') || request()->routeIs('users.*') || request()->routeIs('trial-balance.*') || request()->routeIs('cashflow.*') ? 'active' : ''); ?>">
                                    <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown" role="button">
                                        <span class="nav-link-icon d-md-none d-lg-inline-block">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                <path stroke="none" d="m0 0h24v24H0z" fill="none"/>
                                                <rect x="3" y="4" width="18" height="4" rx="2"/>
                                                <path d="m5 8v10a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-10"/>
                                                <line x1="10" y1="12" x2="14" y2="12"/>
                                            </svg>
                                        </span>
                                        <span class="nav-link-title">Master Data</span>
                                    </a>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item <?php echo e(request()->routeIs('users.*') ? 'active' : ''); ?>" href="<?php echo e(route('users.index')); ?>">User Management</a>
                                        <a class="dropdown-item <?php echo e(request()->routeIs('accounts.*') ? 'active' : ''); ?>" href="<?php echo e(route('accounts.index')); ?>">List Account</a>
                                        <a class="dropdown-item <?php echo e(request()->routeIs('user-accounts.*') ? 'active' : ''); ?>" href="<?php echo e(route('user-accounts.index')); ?>">User Accounts</a>
                                        <a class="dropdown-item <?php echo e(request()->routeIs('trial-balance.*') ? 'active' : ''); ?>" href="<?php echo e(route('trial-balance.index')); ?>">Trial Balances</a>
                                        <a class="dropdown-item <?php echo e(request()->routeIs('cashflow.*') ? 'active' : ''); ?>" href="<?php echo e(route('cashflow.index')); ?>">Cash Flow</a>
                                    </div>
                                </li>
                            <?php endif; ?>

                            
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Page wrapper -->
        <div class="page-wrapper">
            <!-- Page header -->
            <div class="page-header d-print-none">
                <div class="container-xl">
                    <div class="row g-2 align-items-center">
                        <div class="col">
                            <?php echo $__env->yieldContent('page-header'); ?>
                        </div>
                        <?php if (! empty(trim($__env->yieldContent('page-actions')))): ?>
                            <div class="col-auto ms-auto d-print-none">
                                <div class="btn-list">
                                    <?php echo $__env->yieldContent('page-actions'); ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Page body -->
            <div class="page-body">
                <div class="container-xl">
                    <?php if(session('success')): ?>
                        <div class="alert alert-success alert-dismissible" role="alert">
                            <div class="d-flex">
                                <div>
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon alert-icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="m0 0h24v24H0z" fill="none"/>
                                        <path d="M5 12l5 5l10 -10"/>
                                    </svg>
                                </div>
                                <div><?php echo e(session('success')); ?></div>
                            </div>
                            <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
                        </div>
                    <?php endif; ?>

                    <?php if(session('error')): ?>
                        <div class="alert alert-danger alert-dismissible" role="alert">
                            <div class="d-flex">
                                <div>
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon alert-icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="m0 0h24v24H0z" fill="none"/>
                                        <circle cx="12" cy="12" r="9"/>
                                        <line x1="12" y1="8" x2="12" y2="12"/>
                                        <line x1="12" y1="16" x2="12.01" y2="16"/>
                                    </svg>
                                </div>
                                <div><?php echo e(session('error')); ?></div>
                            </div>
                            <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
                        </div>
                    <?php endif; ?>

                    <?php echo $__env->yieldContent('content'); ?>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/@tabler/core@1.0.0-beta17/dist/js/tabler.min.js"></script>
    <script>
        function selectCashAccount(accountId, accountName, currentBalance) {
            // Store selected account in sessionStorage
            sessionStorage.setItem('selectedCashAccount', JSON.stringify({
                id: accountId,
                name: accountName,
                balance: currentBalance
            }));
            
            // Remove active class from all account items
            document.querySelectorAll('.cash-account-item, .bank-account-item').forEach(item => {
                item.classList.remove('active');
            });
            
            // Add active class to selected account
            document.querySelector(`[data-account-id="${accountId}"]`).classList.add('active');
            
            // Navigate to journal create page
            // window.location.href = '<?php echo e(route("journals.create")); ?>';
            console.log(accountName)
        }
        
        // On page load, restore active state
        document.addEventListener('DOMContentLoaded', function() {
            const savedAccount = sessionStorage.getItem('selectedCashAccount');
            if (savedAccount) {
                const account = JSON.parse(savedAccount);
                const accountItem = document.querySelector(`[data-account-id="${account.id}"]`);
                if (accountItem) {
                    accountItem.classList.add('active');
                }
            }
        });
    </script>
    <?php echo $__env->yieldPushContent('scripts'); ?>
</body>

</html>
<?php /**PATH /home/najibzulfan/project/akuntansi/resources/views/layouts/app.blade.php ENDPATH**/ ?>