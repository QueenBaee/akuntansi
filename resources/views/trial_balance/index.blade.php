@extends('layouts.app')

@section('title', 'Trial Balance')

@section('page-header')
    <div class="page-pretitle">Master Data</div>
    <h2 class="page-title">Daftar Trial Balance</h2>
@endsection

@section('page-actions')
    <form method="GET" class="d-flex gap-2 align-items-center me-2">
        <select name="filter_kas_bank" class="form-select" style="min-width: 200px;">
            <option value="">🔍 Semua Akun</option>
            <option value="1" {{ request('filter_kas_bank') == '1' ? 'selected' : '' }}>💰 Akun Kas/Bank</option>
            <option value="0" {{ request('filter_kas_bank') == '0' ? 'selected' : '' }}>📄 Akun Lainnya</option>
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
        @if(request('search') || request('filter_kas_bank'))
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
                            <th class="text-center">Kas/Bank</th>
                            <th>2024 (Rp)</th>
                            <th class="w-1">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="trial-balance-tbody">
                        <tr id="loading-row">
                            <td colspan="5" class="text-center py-4">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                                <div class="mt-2">Loading...</div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>



    </div>
</div>

<script>
console.log('SCRIPT LOADED');

document.addEventListener('DOMContentLoaded', function() {
    loadTrialBalanceData();
});

function loadTrialBalanceData() {
    console.log('FETCH CALLED');
    
    fetch('/api/trial-balance/get-data')
        .then(response => {
            console.log('RESPONSE RECEIVED', response);
            return response.json();
        })
        .then(data => {
            if (data.status === 'success') {
                renderTrialBalanceTable(data.data.items);
            } else {
                throw new Error(data.message || 'Failed to load data');
            }
        })
        .catch(error => {
            console.error('Error loading trial balance data:', error);
            alert('Error loading data: ' + error.message);
            document.getElementById('trial-balance-tbody').innerHTML = 
                '<tr><td colspan="5" class="text-center text-danger">Error loading data</td></tr>';
        });
}

function renderTrialBalanceTable(items) {
    const tbody = document.getElementById('trial-balance-tbody');
    let html = '';
    
    items.forEach(item => {
        html += renderTrialBalanceRow(item, '');
    });
    
    tbody.innerHTML = html;
}

function renderTrialBalanceRow(item, prefix) {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    
    let html = `<tr class="level-${item.level}-row">`;
    html += `<td>${item.kode}</td>`;
    html += `<td><div class="tb-text level-${item.level}">${prefix}${item.keterangan}</div></td>`;
    
    // Kas/Bank Status
    html += '<td class="text-center">';
    if (item.is_kas_bank) {
        html += '<span class="badge bg-success-lt text-success">';
        html += '<svg xmlns="http://www.w3.org/2000/svg" class="icon icon-sm me-1" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">';
        html += '<path stroke="none" d="M0 0h24v24H0z" fill="none"/>';
        html += '<rect x="7" y="9" width="14" height="10" rx="2"/>';
        html += '<circle cx="14" cy="14" r="2"/>';
        html += '<path d="m4.5 12.5l8 -8a4.94 4.94 0 0 1 7 7l-8 8"/>';
        html += '</svg>Yes</span>';
    } else {
        html += '<span class="text-muted">No</span>';
    }
    html += '</td>';
    
    html += `<td>${new Intl.NumberFormat('id-ID').format(item.tahun_2024 || 0)}</td>`;
    html += `<td>
        <div class="btn-list flex-nowrap">
            <a href="/trial-balance/${item.id}/edit" class="btn btn-sm btn-outline-primary">Edit</a>
            <form action="/trial-balance/${item.id}" method="POST" class="d-inline">
                <input type="hidden" name="_token" value="${csrfToken}">
                <input type="hidden" name="_method" value="DELETE">
                <button class="btn btn-sm btn-outline-danger" onclick="return confirm('Hapus?')">Hapus</button>
            </form>
        </div>
    </td>`;
    html += '</tr>';
    
    return html;
}
</script>
@endsection
