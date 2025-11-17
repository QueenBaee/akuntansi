@extends('layouts.app')

@section('title', 'Dashboard')

@section('page-header')
    <div class="page-pretitle">Overview</div>
    <h2 class="page-title">Dashboard</h2>
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

        {{-- <div class="col-12">
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
        </div> --}}
    </div>
@endsection
