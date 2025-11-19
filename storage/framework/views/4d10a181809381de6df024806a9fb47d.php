<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover"/>
    <meta http-equiv="X-UA-Compatible" content="ie=edge"/>
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title><?php echo $__env->yieldContent('title', 'Dashboard'); ?> - Sistem Akuntansi</title>
    <link href="https://cdn.jsdelivr.net/npm/@tabler/core@1.0.0-beta17/dist/css/tabler.min.css" rel="stylesheet"/>
    <link href="https://cdn.jsdelivr.net/npm/@tabler/icons@2.44.0/icons-sprite.svg" rel="stylesheet"/>
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
                            <span class="avatar avatar-sm" style="background-image: url(https://ui-avatars.com/api/?name=<?php echo e(urlencode(auth()->user()->name)); ?>&background=206bc4&color=fff)"></span>
                            <div class="d-none d-xl-block ps-2">
                                <div><?php echo e(auth()->user()->name); ?></div>
                                <div class="mt-1 small text-muted"><?php echo e(auth()->user()->email); ?></div>
                            </div>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                            <form method="POST" action="<?php echo e(route('logout')); ?>" style="display: inline;">
                                <?php echo csrf_field(); ?>
                                <button type="submit" class="dropdown-item" style="border: none; background: none; width: 100%; text-align: left;">Logout</button>
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
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="m0 0h24v24H0z" fill="none"/><polyline points="5 12 3 12 12 3 21 12 19 12"/><path d="m5 12v7a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-7"/><path d="m9 21v-6a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v6"/></svg>
                                    </span>
                                    <span class="nav-link-title">Dashboard</span>
                                </a>
                            </li>
                            <li class="nav-item dropdown <?php echo e(request()->routeIs('accounts.*') || request()->routeIs('journals.*') || request()->routeIs('cash-transactions.*') ? 'active' : ''); ?>">
                                <a class="nav-link dropdown-toggle" href="#navbar-base" data-bs-toggle="dropdown" role="button">
                                    <span class="nav-link-icon d-md-none d-lg-inline-block">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="m0 0h24v24H0z" fill="none"/><rect x="3" y="4" width="18" height="4" rx="2"/><path d="m5 8v10a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-10"/><line x1="10" y1="12" x2="14" y2="12"/></svg>
                                    </span>
                                    <span class="nav-link-title">Akuntansi</span>
                                </a>
                                <div class="dropdown-menu">
                                    <a class="dropdown-item <?php echo e(request()->routeIs('accounts.*') ? 'active' : ''); ?>" href="<?php echo e(route('accounts.index')); ?>">Chart of Accounts</a>
                                    <a class="dropdown-item <?php echo e(request()->routeIs('journals.*') ? 'active' : ''); ?>" href="<?php echo e(route('journals.index')); ?>">Journal Entry</a>
                                    <a class="dropdown-item <?php echo e(request()->routeIs('cash-transactions.*') ? 'active' : ''); ?>" href="<?php echo e(route('cash-transactions.index')); ?>">Cash Transactions</a>
                                </div>
                            </li>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#navbar-extra" data-bs-toggle="dropdown" role="button">
                                    <span class="nav-link-icon d-md-none d-lg-inline-block">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="m0 0h24v24H0z" fill="none"/><path d="M9 7m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0"/><path d="M3 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/><path d="M21 21v-2a4 4 0 0 0 -3 -3.85"/></svg>
                                    </span>
                                    <span class="nav-link-title">Laporan</span>
                                </a>
                                <div class="dropdown-menu">
                                    <a class="dropdown-item" href="#trial-balance">Trial Balance</a>
                                    <a class="dropdown-item" href="#income-statement">Income Statement</a>
                                    <a class="dropdown-item" href="#balance-sheet">Balance Sheet</a>
                                    <a class="dropdown-item" href="#cash-flow">Cash Flow</a>
                                </div>
                            </li>
                            <li class="nav-item <?php echo e(request()->routeIs('trial-balance.index') ? 'active' : ''); ?>">
                                <a class="nav-link" href="<?php echo e(route('trial-balance.index')); ?>">
                                    <span class="nav-link-icon d-md-none d-lg-inline-block">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                            viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                            stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z"/>
                                            <path d="M3 3h18v4H3zM3 9h18v12H3z"/>
                                        </svg>
                                    </span>
                                    <span class="nav-link-title">Trial Balance</span>
                                </a>
                            </li>
                            <li class="nav-item <?php echo e(request()->routeIs('cashflow.index') ? 'active' : ''); ?>">
                                <a class="nav-link" href="<?php echo e(route('cashflow.index')); ?>">
                                    <span class="nav-link-icon d-md-none d-lg-inline-block">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                            viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                            stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z"/>
                                            <path d="M3 3h18v4H3zM3 9h18v12H3z"/>
                                        </svg>
                                    </span>
                                    <span class="nav-link-title">Cash Flow</span>
                                </a>
                            </li>
                            
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
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon alert-icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="m0 0h24v24H0z" fill="none"/><path d="M5 12l5 5l10 -10"/></svg>
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
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon alert-icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="m0 0h24v24H0z" fill="none"/><circle cx="12" cy="12" r="9"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
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
    <?php echo $__env->yieldPushContent('scripts'); ?>
</body>
</html><?php /**PATH D:\Akutansi\akuntansi\resources\views/layouts/app.blade.php ENDPATH**/ ?>