<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Sistem Akuntansi</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
    <div id="app">
        <!-- Navigation -->
        <nav class="navbar">
            <div class="container">
                <div class="nav-content">
                    <div class="nav-brand">Sistem Akuntansi</div>
                    <ul class="nav-menu">
                        {{-- <li><a href="/" class="nav-link">Dashboard</a></li> --}}
                        <li><a href="/accounts" class="nav-link">Chart of Accounts</a></li>
                        <li><a href="/cash-transactions" class="nav-link">Transaksi Kas</a></li>
                        <li><a href="/reports" class="nav-link">Laporan</a></li>
                        <li><a href="/trial_balance" class="nav-link">Laporan</a></li>
                        <li><button class="btn btn-secondary logout-btn">Logout</button></li>
                    </ul>
                </div>
            </div>
        </nav>

        <!-- Main Content -->
        <main class="container" style="padding-top: 2rem;">
            <div id="content">
                <!-- Content will be loaded here -->
            </div>
        </main>
    </div>

    <script src="{{ asset('js/app.js') }}"></script>
    <script src="{{ asset('js/router.js') }}"></script>
</body>
</html>