<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') - Sistem Akuntansi</title>
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
                    <a href="{{ route('dashboard') }}">Sistem Akuntansi</a>
                </h1>
                <div class="navbar-nav flex-row order-md-last">
                    <div class="nav-item dropdown">
                        <a href="#" class="nav-link d-flex lh-1 text-reset p-0" data-bs-toggle="dropdown">
                            <span class="avatar avatar-sm"
                                style="background-image: url(https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&background=206bc4&color=fff)"></span>
                            <div class="d-none d-xl-block ps-2">
                                <div>{{ auth()->user()->name }}</div>
                                <div class="mt-1 small text-muted">{{ auth()->user()->email }}</div>
                            </div>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                            <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                                @csrf
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
                            <li class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                                <a class="nav-link" href="{{ route('dashboard') }}">
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
                            {{-- <li class="nav-item {{ request()->routeIs('ledgers.*') ? 'active' : '' }}">
                                <a class="nav-link" href="{{ route('ledgers.index') }}">
                                    <span class="nav-link-icon d-md-none d-lg-inline-block">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                            <path d="M6 4h11a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-11a1 1 0 0 1 -1 -1v-14a1 1 0 0 1 1 -1m3 0v18"/>
                                            <line x1="13" y1="8" x2="15" y2="8"/>
                                            <line x1="13" y1="12" x2="15" y2="12"/>
                                        </svg>
                                    </span>
                                    <span class="nav-link-title">Ledger</span>
                                </a>
                            </li> --}}
                            <li class="nav-item dropdown {{ $activeContext['active_section'] === 'cash-account' ? 'active' : '' }}" id="cash-account-nav">
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
                                    <h6 class="dropdown-header">List</h6>
                                    @forelse($cashAccounts as $ledger)
                                        <a class="dropdown-item cash-account-item" href="#" data-account-id="{{ $ledger->id }}" data-account-type="kas" onclick="selectCashAccount({{ $ledger->id }}, '{{ $ledger->nama_ledger }}', {{ $ledger->getCurrentBalance() }})">{{ $ledger->nama_ledger }}</a>
                                    @empty
                                        <span class="dropdown-item text-muted">No cash ledger available</span>
                                    @endforelse
                                    @role('admin')
                                        <div class="dropdown-divider"></div>
                                        <a class="dropdown-item {{ request()->routeIs('ledgers.cash') ? 'active' : '' }}" href="{{ route('ledgers.cash') }}">Add / Edit / Delete</a>
                                    @endrole
                                </div>
                            </li>

                            <li class="nav-item dropdown {{ $activeContext['active_section'] === 'bank-account' ? 'active' : '' }}" id="bank-account-nav">
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
                                    <span class="nav-link-title">Akun</span>
                                </a>
                                <div class="dropdown-menu">
                                    <h6 class="dropdown-header">List</h6>
                                    @forelse($bankAccounts as $ledger)
                                        <a class="dropdown-item bank-account-item" href="#" data-account-id="{{ $ledger->id }}" data-account-type="bank" onclick="selectCashAccount({{ $ledger->id }}, '{{ $ledger->nama_ledger }}', {{ $ledger->getCurrentBalance() }})">{{ $ledger->nama_ledger }}</a>
                                    @empty
                                        <span class="dropdown-item text-muted">No bank ledger available</span>
                                    @endforelse
                                    @role('admin')
                                        <div class="dropdown-divider"></div>
                                        <a class="dropdown-item {{ request()->routeIs('ledgers.bank') ? 'active' : '' }}" href="{{ route('ledgers.bank') }}">Add / Edit / Delete</a>
                                    @endrole
                                </div>
                            </li>

                            <!-- Memorial -->
                            <li class="nav-item {{ request()->routeIs('memorials.*') ? 'active' : '' }}">
                                <a class="nav-link" href="{{ route('memorials.create') }}">
                                    <span class="nav-link-icon d-md-none d-lg-inline-block">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                            <path d="M14 3v4a1 1 0 0 0 1 1h4"/>
                                            <path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z"/>
                                            <line x1="9" y1="9" x2="10" y2="9"/>
                                            <line x1="9" y1="13" x2="15" y2="13"/>
                                            <line x1="9" y1="17" x2="15" y2="17"/>
                                        </svg>
                                    </span>
                                    <span class="nav-link-title">Memorial</span>
                                </a>
                            </li>

                            <!-- Laporan -->
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown" role="button">
                                    <span class="nav-link-icon d-md-none d-lg-inline-block">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                            <path d="M9 5h-2a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-12a2 2 0 0 0 -2 -2h-2"/>
                                            <rect x="9" y="3" width="6" height="4" rx="2"/>
                                            <path d="M14 11h-2.5a1.5 1.5 0 0 0 0 3h1a1.5 1.5 0 0 1 0 3h-2.5"/>
                                            <path d="M12 17v1m0 -8v1"/>
                                        </svg>
                                    </span>
                                    <span class="nav-link-title">Laporan</span>
                                </a>
                                <div class="dropdown-menu">
                                    <a class="dropdown-item {{ request()->routeIs('cashflow.*') ? 'active' : '' }}" href="{{ route('cashflow.index') }}">Cashflow</a>
                                    <div class="dropdown-divider"></div>
                                    <h6 class="dropdown-header">Laporan Keuangan</h6>
                                    <a class="dropdown-item" href="#">Laporan Posisi Keuangan</a>
                                    <a class="dropdown-item" href="#">Laporan Penghasil Komprehensif & Laporan Laba Rugi</a>
                                    <a class="dropdown-item" href="#">Catatan Atas Laporan Keuangan</a>
                                    <div class="dropdown-divider"></div>
                                    <h6 class="dropdown-header">Pendukung</h6>
                                    <a class="dropdown-item {{ request()->routeIs('trial-balance.*') ? 'active' : '' }}" href="{{ route('trial-balance.index') }}">Trial Balance</a>
                                    <a class="dropdown-item" href="#">Asset</a>
                                    <a class="dropdown-item" href="#">Buku Besar</a>
                                </div>
                            </li>

                            @role('admin')
                                <li class="nav-item dropdown {{ request()->routeIs('accounts.*') || request()->routeIs('user-accounts.*') || request()->routeIs('user-ledgers.*') || request()->routeIs('users.*') || request()->routeIs('trial-balance.*') || request()->routeIs('cashflow.*') ? 'active' : '' }}">
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
                                        <a class="dropdown-item {{ request()->routeIs('users.*') ? 'active' : '' }}" href="{{ route('users.index') }}">User Management</a>
                                        <a class="dropdown-item {{ request()->routeIs('accounts.*') ? 'active' : '' }}" href="{{ route('accounts.index') }}">List Account</a>
                                        <a class="dropdown-item {{ request()->routeIs('user-accounts.*') ? 'active' : '' }}" href="{{ route('user-accounts.index') }}">User Accounts</a>
                                        <a class="dropdown-item {{ request()->routeIs('user-ledgers.*') ? 'active' : '' }}" href="{{ route('user-ledgers.index') }}">User Ledgers</a>
                                        <div class="dropdown-divider"></div>
                                        <a class="dropdown-item {{ request()->routeIs('trial-balance.*') ? 'active' : '' }}" href="{{ route('trial-balance.index') }}">Trial Balances</a>
                                        <a class="dropdown-item {{ request()->routeIs('cashflow.*') ? 'active' : '' }}" href="{{ route('cashflow.index') }}">Cash Flow</a>
                                    </div>
                                </li>
                            @endrole

                            
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
                            @yield('page-header')
                        </div>
                        @hasSection('page-actions')
                            <div class="col-auto ms-auto d-print-none">
                                <div class="btn-list">
                                    @yield('page-actions')
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Page body -->
            <div class="page-body">
                <div class="container-xl">
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible" role="alert">
                            <div class="d-flex">
                                <div>
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon alert-icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="m0 0h24v24H0z" fill="none"/>
                                        <path d="M5 12l5 5l10 -10"/>
                                    </svg>
                                </div>
                                <div>{{ session('success') }}</div>
                            </div>
                            <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
                        </div>
                    @endif

                    @if (session('error'))
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
                                <div>{{ session('error') }}</div>
                            </div>
                            <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
                        </div>
                    @endif

                    @yield('content')
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/@tabler/core@1.0.0-beta17/dist/js/tabler.min.js"></script>
    <script>
        function selectCashAccount(ledgerId, accountName, currentBalance) {
            // Get account type from the clicked element
            const clickedElement = document.querySelector(`[data-account-id="${ledgerId}"]`);
            const accountType = clickedElement.getAttribute('data-account-type');
            
            // Store selected account in sessionStorage
            sessionStorage.setItem('selectedCashAccount', JSON.stringify({
                id: ledgerId,
                name: accountName,
                balance: currentBalance,
                type: accountType
            }));
            
            // Navigate to journal create page with ledger_id parameter
            window.location.href = '{{ route("journals.create") }}?ledger_id=' + ledgerId;
        }
        
        function updateNavigationActiveState() {
            const activeContext = @json($activeContext);
            const urlParams = new URLSearchParams(window.location.search);
            const ledgerId = urlParams.get('ledger_id');
            
            // Handle dropdown item active states
            if (activeContext.route === 'journals.create' && ledgerId) {
                // Activate the specific account item in dropdown
                const accountItem = document.querySelector(`[data-account-id="${ledgerId}"]`);
                if (accountItem) {
                    accountItem.classList.add('active');
                }
                
                // Store in sessionStorage for consistency
                if (activeContext.type) {
                    const accountElement = document.querySelector(`[data-account-id="${ledgerId}"]`);
                    if (accountElement) {
                        const accountName = accountElement.textContent.trim();
                        sessionStorage.setItem('selectedCashAccount', JSON.stringify({
                            id: ledgerId,
                            name: accountName,
                            type: activeContext.type
                        }));
                    }
                }
            }
        }
        
        // Initialize navigation state on page load
        document.addEventListener('DOMContentLoaded', function() {
            updateNavigationActiveState();
        });
    </script>
    @stack('scripts')
</body>

</html>
