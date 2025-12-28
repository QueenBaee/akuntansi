@extends('layouts.app')

@section('title', 'Company Profile')

@section('page-header')
    <div class="page-pretitle">Overview</div>
    <h2 class="page-title">Company Profile</h2>
@endsection

@section('content')
    <div class="row row-deck row-cards">
        
        <!-- Image Slider Section -->
        <div class="col-12">
            <div class="card">
                <div class="card-body p-0">
                    <div id="systemCarousel" class="carousel slide" data-bs-ride="carousel">
                        <div class="carousel-indicators">
                            <button type="button" data-bs-target="#systemCarousel" data-bs-slide-to="0" class="active"></button>
                            <button type="button" data-bs-target="#systemCarousel" data-bs-slide-to="1"></button>
                            <button type="button" data-bs-target="#systemCarousel" data-bs-slide-to="2"></button>
                        </div>
                        <div class="carousel-inner">
                            <div class="carousel-item active">
                                <img src="{{ asset('image/factory1.jpg') }}" class="d-block w-100" style="height:350px; object-fit:cover;">
                                <div class="carousel-caption d-none d-md-block">
                                    <h4>Dashboard Akuntansi</h4>
                                    <p>Ringkasan keuangan perusahaan</p>
                                </div>
                            </div>
                            <div class="carousel-item">
                                <img src="{{ asset('image/factory2.jpg') }}" class="d-block w-100" style="height:350px; object-fit:cover;">
                                <div class="carousel-caption d-none d-md-block">
                                    <h4>Laporan Keuangan</h4>
                                    <p>Laporan keuangan otomatis & real-time</p>
                                </div>
                            </div>
                            <div class="carousel-item">
                                <img src="{{ asset('image/factory3.jpg') }}" class="d-block w-100" style="height:350px; object-fit:cover;">
                                <div class="carousel-caption d-none d-md-block">
                                    <h4>Manajemen Aset</h4>
                                    <p>Manajemen aset & depresiasi</p>
                                </div>
                            </div>
                        </div>
                        <button class="carousel-control-prev" type="button" data-bs-target="#systemCarousel" data-bs-slide="prev">
                            <span class="carousel-control-prev-icon"></span>
                        </button>
                        <button class="carousel-control-next" type="button" data-bs-target="#systemCarousel" data-bs-slide="next">
                            <span class="carousel-control-next-icon"></span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Company Profile Header -->
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <div class="avatar avatar-xl" style="background-color: #206bc4;">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-lg text-white" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="m0 0h24v24H0z" fill="none"/>
                                    <path d="M9 5h-2a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-12a2 2 0 0 0 -2 -2h-2"/>
                                    <rect x="9" y="3" width="6" height="4" rx="2"/>
                                    <path d="M14 11h-2.5a1.5 1.5 0 0 0 0 3h1a1.5 1.5 0 0 1 0 3h-2.5"/>
                                    <path d="M12 17v1m0 -8v1"/>
                                </svg>
                            </div>
                        </div>
                        <div class="col">
                            <h2 class="mb-1">PT. Wahyu Manunggal Sejati</h2>
                            <div class="text-muted mb-2">Solusi Sistem Akuntansi</div>
                            <p class="text-muted mb-0">Sistem akuntansi komprehensif dengan pembukuan double-entry, manajemen aset, depresiasi otomatis, dan kemampuan pelaporan keuangan lengkap.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="col-12">
            <div class="row row-cards">
                <div class="col-sm-6 col-lg-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="subheader">Total Kas</div>
                            </div>
                            <div class="h1 mb-3" id="totalCash">Rp 0</div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="subheader">Total Akun</div>
                            </div>
                            <div class="h1 mb-3" id="totalAccounts">0</div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="subheader">Transaksi Bulan Ini</div>
                            </div>
                            <div class="h1 mb-3" id="monthlyTransactions">0</div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="subheader">Total Aset</div>
                            </div>
                            <div class="h1 mb-3" id="totalAssets">0</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Row -->
        <div class="col-12">
            <div class="row row-cards">
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Arus Kas 6 Bulan Terakhir</h3>
                        </div>
                        <div class="card-body" style="height: 300px;">
                            <canvas id="cashFlowChart"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Aset Berdasarkan Kategori</h3>
                        </div>
                        <div class="card-body" style="height: 300px;">
                            <canvas id="assetsCategoryChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- System Features Section -->
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Fitur & Kemampuan Sistem</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h5 class="mb-3">Fitur Akuntansi Utama</h5>
                            <ul class="list-unstyled">
                                <li class="mb-3">
                                    <h6 class="text-primary mb-1">• Pembukuan Double-Entry</h6>
                                    <p class="text-muted mb-0">Memastikan akurasi dengan validasi debit/kredit otomatis. Setiap transaksi mempertahankan persamaan akuntansi dasar.</p>
                                </li>
                                <li class="mb-3">
                                    <h6 class="text-primary mb-1">• Manajemen Kas & Bank</h6>
                                    <p class="text-muted mb-0">Melacak semua arus kas dengan transaksi terkategorisasi. Memantau beberapa rekening bank dan rekonsiliasi otomatis.</p>
                                </li>
                                <li class="mb-0">
                                    <h6 class="text-primary mb-1">• Jurnal & Buku Besar</h6>
                                    <p class="text-muted mb-0">Jejak audit lengkap dengan riwayat transaksi detail. Semua entri otomatis diposting ke akun masing-masing.</p>
                                </li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h5 class="mb-3">Kemampuan Lanjutan</h5>
                            <ul class="list-unstyled">
                                <li class="mb-3">
                                    <h6 class="text-success mb-1">• Neraca Saldo</h6>
                                    <p class="text-muted mb-0">Verifikasi saldo real-time dan deteksi kesalahan. Otomatis memastikan total debit sama dengan kredit sebelum penyusunan laporan keuangan.</p>
                                </li>
                                <li class="mb-0">
                                    <h6 class="text-success mb-1">• Laporan Keuangan Otomatis</h6>
                                    <p class="text-muted mb-0">Menghasilkan Neraca, Laporan Laba Rugi, dan Arus Kas secara otomatis. Laporan diperbarui real-time saat transaksi dicatat.</p>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Company & System Information -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Informasi Perusahaan</h3>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-5 text-muted">Alamat:</div>
                        <div class="col-7">Jl. Indrokilo No.Km. 5<br>Bolo, Dayurejo, Kec. Prigen<br>Pasuruan, Jawa Timur 67157</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-5 text-muted">Telepon:</div>
                        <div class="col-7">(0343) 635670</div>
                    </div>
                    <div class="row mb-0">
                        <div class="col-5 text-muted">Email:</div>
                        <div class="col-7">-</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Informasi Sistem</h3>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-5 text-muted">System Name:</div>
                        <div class="col-7">E-Pro</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-5 text-muted">Current Period:</div>
                        <div class="col-7">{{ date('F Y') }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-5 text-muted">Fiscal Year:</div>
                        <div class="col-7">{{ date('Y') }}</div>
                    </div>
                    <div class="row mb-0">
                        <div class="col-5 text-muted">Your Role:</div>
                        <div class="col-7">
                            <span class="badge bg-primary">{{ auth()->user()->roles->first()->name ?? 'User' }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        loadStats();
        loadCashFlowChart();
        loadAssetsCategoryChart();
    });

    function loadStats() {
        fetch('/api/dashboard/stats')
            .then(response => response.json())
            .then(data => {
                if (data && data.data) {
                    document.getElementById('totalCash').textContent = formatCurrency(data.data.totalCash);
                    document.getElementById('totalAccounts').textContent = data.data.totalAccounts;
                    document.getElementById('monthlyTransactions').textContent = data.data.monthlyTransactions;
                    document.getElementById('totalAssets').textContent = data.data.totalAssets;
                }
            })
            .catch(error => {
                document.getElementById('totalCash').textContent = 'Error';
            });
    }

    function loadCashFlowChart() {
        fetch('/api/dashboard/cash-flow-chart')
            .then(response => response.json())
            .then(data => {
                const ctx = document.getElementById('cashFlowChart').getContext('2d');
                new Chart(ctx, {
                    type: 'bar',
                    data: data,
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    callback: function(value) {
                                        return formatCurrency(value);
                                    }
                                }
                            }
                        }
                    }
                });
            })
            .catch(error => {});
    }

    function loadAssetsCategoryChart() {
        fetch('/api/dashboard/assets-by-category')
            .then(response => response.json())
            .then(data => {
                const ctx = document.getElementById('assetsCategoryChart').getContext('2d');
                new Chart(ctx, {
                    type: 'doughnut',
                    data: data,
                    options: {
                        responsive: true,
                        maintainAspectRatio: false
                    }
                });
            })
            .catch(error => {});
    }

    function formatCurrency(amount) {
        return new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0
        }).format(amount);
    }
</script>
@endpush