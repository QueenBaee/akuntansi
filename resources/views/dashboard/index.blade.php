@extends('layouts.app')

@section('title', 'Company Profile')

@section('page-header')
    <div class="page-pretitle">Overview</div>
    <h2 class="page-title">Company Profile</h2>
@endsection

@section('page-actions')
    {{-- <a href="{{ route('accounts.create') }}" class="btn btn-primary">
        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2"
            stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
            <path stroke="none" d="m0 0h24v24H0z" fill="none" />
            <line x1="12" y1="5" x2="12" y2="19" />
            <line x1="5" y1="12" x2="19" y2="12" />
        </svg>
        Tambah Akun
    </a> --}}
@endsection

@section('content')
    <div class="row row-deck row-cards">
        
        <!-- Image Slider Section
        <div class="col-12">
            <div class="card">
                <div class="card-body p-0">
                    <div id="systemCarousel" class="carousel slide" data-bs-ride="carousel">
                        <div class="carousel-indicators">
                            <button type="button" data-bs-target="#systemCarousel" data-bs-slide-to="0" class="active"></button>
                            <button type="button" data-bs-target="#systemCarousel" data-bs-slide-to="1"></button>
                            <button type="button" data-bs-target="#systemCarousel" data-bs-slide-to="2"></button>
                            <button type="button" data-bs-target="#systemCarousel" data-bs-slide-to="3"></button>
                        </div>
                        <div class="carousel-inner">
                            <div class="carousel-item active">
                                <div class="d-flex align-items-center justify-content-center bg-primary-lt" style="height: 300px;">
                                    <div class="text-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-xl text-primary mb-3" width="64" height="64" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="m0 0h24v24H0z" fill="none"/>
                                            <rect x="4" y="4" width="6" height="6" rx="1"/>
                                            <rect x="14" y="4" width="6" height="6" rx="1"/>
                                            <rect x="4" y="14" width="6" height="6" rx="1"/>
                                            <rect x="14" y="14" width="6" height="6" rx="1"/>
                                        </svg>
                                        <h4 class="text-primary">Accounting Dashboard</h4>
                                        <p class="text-muted">Comprehensive overview of your financial data</p>
                                    </div>
                                </div>
                            </div>
                            <div class="carousel-item">
                                <div class="d-flex align-items-center justify-content-center bg-success-lt" style="height: 300px;">
                                    <div class="text-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-xl text-success mb-3" width="64" height="64" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="m0 0h24v24H0z" fill="none"/>
                                            <path d="M9 5h-2a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-12a2 2 0 0 0 -2 -2h-2"/>
                                            <rect x="9" y="3" width="6" height="4" rx="2"/>
                                            <path d="M9 12l2 2l4 -4"/>
                                        </svg>
                                        <h4 class="text-success">Financial Reports</h4>
                                        <p class="text-muted">Automated generation of balance sheets and income statements</p>
                                    </div>
                                </div>
                            </div>
                            <div class="carousel-item">
                                <div class="d-flex align-items-center justify-content-center bg-info-lt" style="height: 300px;">
                                    <div class="text-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-xl text-info mb-3" width="64" height="64" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="m0 0h24v24H0z" fill="none"/>
                                            <path d="M6 4h11a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-11a1 1 0 0 1 -1 -1v-14a1 1 0 0 1 1 -1m3 0v18"/>
                                            <line x1="13" y1="8" x2="15" y2="8"/>
                                            <line x1="13" y1="12" x2="15" y2="12"/>
                                        </svg>
                                        <h4 class="text-info">Journal & Ledger System</h4>
                                        <p class="text-muted">Complete double-entry bookkeeping with automated posting</p>
                                    </div>
                                </div>
                            </div>
                            <div class="carousel-item">
                                <div class="d-flex align-items-center justify-content-center bg-warning-lt" style="height: 300px;">
                                    <div class="text-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-xl text-warning mb-3" width="64" height="64" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="m0 0h24v24H0z" fill="none"/>
                                            <circle cx="12" cy="12" r="9"/>
                                            <path d="M12 3c.132 0 .263 0 .393 0a7.5 7.5 0 0 0 7.92 12.446a9 9 0 1 1 -8.313 -12.454z"/>
                                            <path d="M17 4a2 2 0 0 0 2 2a2 2 0 0 0 -2 2a2 2 0 0 0 -2 -2a2 2 0 0 0 2 -2"/>
                                            <path d="M19 11h2m-1 -1v2"/>
                                        </svg>
                                        <h4 class="text-warning">Asset Management</h4>
                                        <p class="text-muted">Fixed asset tracking with automatic depreciation calculation</p>
                                    </div>
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
        </div> -->

        <!-- Image Slider Section -->
        <div class="col-12">
            <div class="card">
                <div class="card-body p-0">

                    <div id="systemCarousel" class="carousel slide" data-bs-ride="carousel">

                        <!-- Indicators -->
                        <div class="carousel-indicators">
                            <button type="button" data-bs-target="#systemCarousel" data-bs-slide-to="0" class="active"></button>
                            <button type="button" data-bs-target="#systemCarousel" data-bs-slide-to="1"></button>
                            <button type="button" data-bs-target="#systemCarousel" data-bs-slide-to="2"></button>
                        </div>

                        <!-- Slides -->
                        <div class="carousel-inner">

                            <div class="carousel-item active">
                                <img src="{{ asset('image/factory1.jpg') }}"
                                    class="d-block w-100"
                                    style="height:350px; object-fit:cover;">
                                <div class="carousel-caption d-none d-md-block">
                                    <h4>Dashboard Akuntansi</h4>
                                    <p>Ringkasan keuangan perusahaan</p>
                                </div>
                            </div>

                            <div class="carousel-item">
                                <img src="{{ asset('image/factory2.jpg') }}"
                                    class="d-block w-100"
                                    style="height:350px; object-fit:cover;">
                                <div class="carousel-caption d-none d-md-block">
                                    <h4>Laporan Keuangan</h4>
                                    <p>Laporan keuangan otomatis & real-time</p>
                                </div>
                            </div>

                            <div class="carousel-item">
                                <img src="{{ asset('image/factory3.jpg') }}"
                                    class="d-block w-100"
                                    style="height:350px; object-fit:cover;">
                                <div class="carousel-caption d-none d-md-block">
                                    <h4>Manajemen Aset</h4>
                                    <p>Manajemen aset & depresiasi</p>
                                </div>
                            </div>

                        </div>

                        <!-- Controls -->
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



        <!-- System Explanation Section -->
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

        <!-- Company Information -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Informasi Perusahaan</h3>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-5 text-muted">Alamat:</div>
                        <div class="col-7">Jl.  Indrokilo No.Km. 5<br>Bolo, Dayurejo, Kec. Prigen<br>Pasuruan, Jawa Timur 67157</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-5 text-muted">Telepon:</div>
                        <div class="col-7">(0343) 635670</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-5 text-muted">Email:</div>
                        <!-- <div class="col-7">info@akuntansidigital.co.id</div> -->
                    </div>
                    <div class="row mb-3">
                        <div class="col-5 text-muted">NPWP:</div>
                        <!-- <div class="col-7">01.234.567.8-901.000</div> -->
                    </div>
                    <div class="row mb-0">
                        <div class="col-5 text-muted">Tahun Berdiri:</div>
                        <!-- <div class="col-7">2020</div> -->
                    </div>
                </div>
            </div>
        </div>

        <!-- System Information -->
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