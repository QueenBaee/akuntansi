<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover"/>
    <meta http-equiv="X-UA-Compatible" content="ie=edge"/>
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title>Dashboard - Sistem Akuntansi</title>
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
                    <a href=".">Sistem Akuntansi</a>
                </h1>
                <div class="navbar-nav flex-row order-md-last">
                    <div class="nav-item dropdown">
                        <a href="#" class="nav-link d-flex lh-1 text-reset p-0" data-bs-toggle="dropdown">
                            <span class="avatar avatar-sm" style="background-image: url(https://ui-avatars.com/api/?name=Admin&background=206bc4&color=fff)"></span>
                            <div class="d-none d-xl-block ps-2">
                                <div id="user-name"><?php echo e(auth()->user()->name ?? 'User'); ?></div>
                                <div class="mt-1 small text-muted" id="user-email"><?php echo e(auth()->user()->email ?? 'user@example.com'); ?></div>
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
                            <li class="nav-item active">
                                <a class="nav-link" href="#dashboard">
                                    <span class="nav-link-icon d-md-none d-lg-inline-block">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="m0 0h24v24H0z" fill="none"/><polyline points="5 12 3 12 12 3 21 12 19 12"/><path d="m5 12v7a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-7"/><path d="m9 21v-6a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v6"/></svg>
                                    </span>
                                    <span class="nav-link-title">Dashboard</span>
                                </a>
                            </li>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#navbar-base" data-bs-toggle="dropdown" role="button">
                                    <span class="nav-link-icon d-md-none d-lg-inline-block">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="m0 0h24v24H0z" fill="none"/><rect x="3" y="4" width="18" height="4" rx="2"/><path d="m5 8v10a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-10"/><line x1="10" y1="12" x2="14" y2="12"/></svg>
                                    </span>
                                    <span class="nav-link-title">Akuntansi</span>
                                </a>
                                <div class="dropdown-menu">
                                    <a class="dropdown-item" href="<?php echo e(route('accounts.index')); ?>">Chart of Accounts</a>
                                    <a class="dropdown-item" href="<?php echo e(route('journals.index')); ?>">Journal Entry</a>
                                    <a class="dropdown-item" href="<?php echo e(route('cash-transactions.index')); ?>">Cash Transactions</a>
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
                            <div class="page-pretitle">Overview</div>
                            <h2 class="page-title">Dashboard</h2>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Page body -->
            <div class="page-body">
                <div class="container-xl">
                    <div class="row row-deck row-cards">
                        <div class="col-sm-6 col-lg-3">
                            <div class="card">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="subheader">Total Kas</div>
                                    </div>
                                    <div class="h1 mb-3" id="total-cash">Rp 0</div>
                                    <div class="d-flex mb-2">
                                        <div class="flex-fill">
                                            <div class="progress progress-sm">
                                                <div class="progress-bar bg-primary" style="width: 75%" role="progressbar"></div>
                                            </div>
                                        </div>
                                        <div class="ms-2">
                                            <small class="text-muted">75%</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-sm-6 col-lg-3">
                            <div class="card">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="subheader">Total Bank</div>
                                    </div>
                                    <div class="h1 mb-3" id="total-bank">Rp 0</div>
                                    <div class="d-flex mb-2">
                                        <div class="flex-fill">
                                            <div class="progress progress-sm">
                                                <div class="progress-bar bg-success" style="width: 60%" role="progressbar"></div>
                                            </div>
                                        </div>
                                        <div class="ms-2">
                                            <small class="text-muted">60%</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-sm-6 col-lg-3">
                            <div class="card">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="subheader">Pendapatan Bulan Ini</div>
                                    </div>
                                    <div class="h1 mb-3" id="monthly-revenue">Rp 0</div>
                                    <div class="d-flex mb-2">
                                        <div class="flex-fill">
                                            <div class="progress progress-sm">
                                                <div class="progress-bar bg-info" style="width: 45%" role="progressbar"></div>
                                            </div>
                                        </div>
                                        <div class="ms-2">
                                            <small class="text-muted">45%</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-sm-6 col-lg-3">
                            <div class="card">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="subheader">Beban Bulan Ini</div>
                                    </div>
                                    <div class="h1 mb-3" id="monthly-expense">Rp 0</div>
                                    <div class="d-flex mb-2">
                                        <div class="flex-fill">
                                            <div class="progress progress-sm">
                                                <div class="progress-bar bg-warning" style="width: 30%" role="progressbar"></div>
                                            </div>
                                        </div>
                                        <div class="ms-2">
                                            <small class="text-muted">30%</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Transaksi Terbaru</h3>
                                </div>
                                <div class="card-body">
                                    <div id="recent-transactions">
                                        <div class="text-center py-4">
                                            <div class="spinner-border" role="status">
                                                <span class="visually-hidden">Loading...</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/@tabler/core@1.0.0-beta17/dist/js/tabler.min.js"></script>
    <script src="<?php echo e(asset('js/app.js')); ?>"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (!requireAuth()) return;
            
            loadDashboardData();
        });
        
        async function loadDashboardData() {
            try {
                const data = await apiCall('/dashboard/stats');
                
                document.getElementById('total-cash').textContent = formatCurrency(data.data.total_cash || 0);
                document.getElementById('total-bank').textContent = formatCurrency(data.data.total_bank || 0);
                document.getElementById('monthly-revenue').textContent = formatCurrency(data.data.monthly_revenue || 0);
                document.getElementById('monthly-expense').textContent = formatCurrency(data.data.monthly_expense || 0);
                
                loadRecentTransactions();
            } catch (error) {
                console.error('Error loading dashboard:', error);
            }
        }
        
        async function loadRecentTransactions() {
            try {
                const data = await apiCall('/dashboard/recent-transactions');
                const container = document.getElementById('recent-transactions');
                
                if (data.data.length === 0) {
                    container.innerHTML = '<div class="text-center text-muted py-4">Belum ada transaksi</div>';
                    return;
                }
                
                let html = '<div class="table-responsive"><table class="table table-vcenter"><thead><tr><th>Tanggal</th><th>Deskripsi</th><th>Jumlah</th><th>Tipe</th></tr></thead><tbody>';
                
                data.data.forEach(transaction => {
                    html += `
                        <tr>
                            <td>${formatDate(transaction.date)}</td>
                            <td>${transaction.description}</td>
                            <td>${formatCurrency(transaction.amount)}</td>
                            <td><span class="badge bg-${transaction.type === 'in' ? 'success' : 'danger'}">${transaction.type === 'in' ? 'Masuk' : 'Keluar'}</span></td>
                        </tr>
                    `;
                });
                
                html += '</tbody></table></div>';
                container.innerHTML = html;
            } catch (error) {
                document.getElementById('recent-transactions').innerHTML = '<div class="text-center text-danger py-4">Error loading transactions</div>';
            }
        }
    </script>
</body>
</html><?php /**PATH C:\laragon\www\akuntansi\resources\views/dashboard.blade.php ENDPATH**/ ?>