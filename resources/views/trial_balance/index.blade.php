@extends('layouts.app')

@section('title', 'Trial Balance')

@section('page-header')
    <div class="page-pretitle">Master Data</div>
    <h2 class="page-title">Daftar Trial Balance</h2>
@endsection

@section('page-actions')
    <form method="GET" class="d-flex gap-2 align-items-center me-2">
        <select name="filter_tipe_ledger" class="form-select" style="min-width: 200px;">
            <option value="">🔍 Semua Akun</option>
            <option value="kas" {{ request('filter_tipe_ledger') == 'kas' ? 'selected' : '' }}>💰 Akun Kas</option>
            <option value="bank" {{ request('filter_tipe_ledger') == 'bank' ? 'selected' : '' }}>🏦 Akun Bank</option>
        </select>
        <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Cari kode atau keterangan..." style="min-width: 200px;">
        <button class="btn btn-outline-primary" type="submit">
            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                <circle cx="10" cy="10" r="7"/>
                <path d="m21 21l-6 -6"/>
            </svg>
            Filter
        </button>
        @if(request('search') || request('filter_tipe_ledger'))
            <a href="{{ route('trial-balance.index') }}" class="btn btn-outline-secondary">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                    <line x1="18" y1="6" x2="6" y2="18"/>
                    <line x1="6" y1="6" x2="18" y2="18"/>
                </svg>
                Reset
            </a>
        @endif
    </form>
    <a href="{{ route('trial-balance.create') }}" class="btn btn-primary">
        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
            <line x1="12" y1="5" x2="12" y2="19"/>
            <line x1="5" y1="12" x2="19" y2="12"/>
        </svg>
        Tambah Akun Trial Balance
    </a>
@endsection

@section('content')
<style>
    .tb-text {
        display: flex;
        align-items: center;
        font-size: 14px;
    }

    /* Kolom kode dilebarkan */
    .table td:nth-child(1),
    .table th:nth-child(1) {
        min-width: 100px;
        width: 100px;
        white-space: nowrap;
        font-weight: 600;
    }

    .level-0 {
        margin-left: 0px;
        font-weight: 800;
    }

    .level-1 {
        margin-left: 15px;
        font-weight: 700;
    }

    .level-2 {
        margin-left: 30px;
        font-weight: 600;
    }

    .level-3 {
        margin-left: 45px;
    }

    .level-4 {
        margin-left: 60px;
    }

    tr.level-0-row {
        background: #eaf6ff !important;
    }

    tr.level-1-row {
        background: #f4fbff !important;
    }

    tr.level-2-row {
        background: #ffffff !important;
    }
    
    /* Make all card titles uppercase */
    .card-title {
        text-transform: uppercase !important;
    }
    
    /* Make table fill full width */
    .table {
        width: 100% !important;
        table-layout: auto !important;
    }
    
    .table-responsive {
        width: 100% !important;
    }
</style>
<div class="row">
    <div class="col-12">

        <div class="card">

            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Kode</th>
                            <th>Keterangan</th>
                            <th>Parent</th>
                            <th>Level</th>
                            <th class="text-center">Kas/Bank</th>
                            <th>2024 (Rp)</th>
                            <th class="w-1">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            function renderRowsTB($items, $prefix = '') {
                                foreach ($items as $item) {



                                    echo '<tr class="level-' . $item->level . '-row">';
                                    echo '<td>' . $item->kode . '</td>';
                                    echo '<td><div class="tb-text level-' . $item->level . '">' . $prefix . $item->keterangan . '</div></td>';
                                    echo '<td>' . ($item->parent?->kode ?? '-') . '</td>';
                                    echo '<td>' . $item->level . '</td>';

                                    // Tipe Ledger (Kas/Bank)
                                    echo '<td class="text-center">';
                                    if ($item->tipe_ledger) {
                                        if ($item->tipe_ledger == 'kas') {
                                            echo '<span class="badge bg-success-lt text-success">';
                                            echo '<svg xmlns="http://www.w3.org/2000/svg" class="icon icon-sm me-1" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">';
                                            echo '<path stroke="none" d="M0 0h24v24H0z" fill="none"/>';
                                            echo '<rect x="7" y="9" width="14" height="10" rx="2"/>';
                                            echo '<circle cx="14" cy="14" r="2"/>';
                                            echo '<path d="m4.5 12.5l8 -8a4.94 4.94 0 0 1 7 7l-8 8"/>';
                                            echo '</svg>';
                                            echo 'KAS</span>';
                                        } else {
                                            echo '<span class="badge bg-primary-lt text-primary">';
                                            echo '<svg xmlns="http://www.w3.org/2000/svg" class="icon icon-sm me-1" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">';
                                            echo '<path stroke="none" d="M0 0h24v24H0z" fill="none"/>';
                                            echo '<line x1="3" y1="21" x2="21" y2="21"/>';
                                            echo '<line x1="3" y1="10" x2="21" y2="10"/>';
                                            echo '<polyline points="5,6 12,3 19,6"/>';
                                            echo '<line x1="4" y1="10" x2="4" y2="21"/>';
                                            echo '<line x1="20" y1="10" x2="20" y2="21"/>';
                                            echo '<line x1="8" y1="14" x2="8" y2="17"/>';
                                            echo '<line x1="12" y1="14" x2="12" y2="17"/>';
                                            echo '<line x1="16" y1="14" x2="16" y2="17"/>';
                                            echo '</svg>';
                                            echo 'BANK</span>';
                                        }
                                    } else {
                                        echo '<span class="text-muted">-</span>';
                                    }
                                    echo '</td>';

                                    echo '<td>' . number_format($item->tahun_2024 ?? 0, 0, ',', '.') . '</td>';

                                    echo '<td>
                                            <div class="btn-list flex-nowrap">
                                                <a href="' . route('trial-balance.edit', $item->id) . '" class="btn btn-sm btn-outline-primary">Edit</a>
                                                <!-- <a href="' . route('trial-balance.create') . '?parent_id=' . $item->id . '" class="btn btn-sm btn-outline-success">Tambah</a> -->
                                                <form action="' . route('trial-balance.destroy', $item->id) . '" method="POST" class="d-inline">
                                                    ' . csrf_field() . '
                                                    ' . method_field('DELETE') . '
                                                    <button class="btn btn-sm btn-outline-danger" onclick="return confirm(\'Hapus?\')">Hapus</button>
                                                </form>
                                            </div>
                                          </td>';
                                    echo '</tr>';

                                    if ($item->children->count() > 0) {
                                        renderRowsTB($item->children, $prefix . '&nbsp;&nbsp;&nbsp;&nbsp;');
                                    }
                                }
                            }
                            renderRowsTB($items);
                        @endphp
                    </tbody>
                </table>
            </div>
        </div>



    </div>
</div>
@endsection
