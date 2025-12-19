@extends('layouts.app')

@section('title', 'Cashflow')

@section('page-header')
    <div class="page-pretitle">Master Data</div>
    <h2 class="page-title">Daftar Cashflow</h2>
@endsection

@section('page-actions')
    <form method="GET" class="d-flex gap-2 align-items-center me-2">
        <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Cari kode atau keterangan..." style="min-width: 200px;">
        <button class="btn btn-outline-primary" type="submit">
            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                <circle cx="10" cy="10" r="7"/>
                <path d="m21 21l-6 -6"/>
            </svg>
            Cari
        </button>
    </form>
    <a href="{{ route('cashflow.create') }}" class="btn btn-primary">
        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
            viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
            fill="none" stroke-linecap="round" stroke-linejoin="round">
            <path stroke="none" d="M0 0h24v24H0z" />
            <line x1="12" y1="5" x2="12" y2="19" />
            <line x1="5" y1="12" x2="19" y2="12" />
        </svg>
        Tambah Akun Cashflow
    </a>
@endsection

@section('content')
<style>
body {
    overflow-x: hidden;
}
.card {
    max-width: 100%;
    box-sizing: border-box;
}
.table {
    width: 100%;
    table-layout: fixed;
}
.table td, .table th {
    vertical-align: middle;
    word-wrap: break-word;
}
.card {
    overflow: hidden;
}
.btn-list {
    justify-content: center;
    gap: 0.25rem;
}
</style>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div style="overflow-x: auto;">
                <table class="table table-vcenter card-table mb-0" style="width: auto; table-layout: auto;">
                    <thead>
                        <tr>
                            <th style="text-align:center">Kode</th>
                            <th style="text-align:center">Keterangan</th>
                            <th style="text-align:center">Akun TB</th>
                            <th style="text-align:center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="cashflow-tbody">
                        <tr id="loading-row">
                            <td colspan="4" class="text-center py-4">
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
    loadCashflowData();
});

function loadCashflowData() {
    console.log('FETCH CALLED');
    
    fetch('/api/cashflow/get-data')
        .then(response => {
            console.log('RESPONSE RECEIVED', response);
            return response.json();
        })
        .then(data => {
            console.log('Full response data:', data);
            if (data.status === 'success') {
                console.log('Items array:', data.data.items);
                renderCashflowTable(data.data.items);
            } else {
                throw new Error(data.message || 'Failed to load data');
            }
        })
        .catch(error => {
            console.error('Error loading cashflow data:', error);
            alert('Error loading data: ' + error.message);
            document.getElementById('cashflow-tbody').innerHTML = 
                '<tr><td colspan="4" class="text-center text-danger">Error loading data</td></tr>';
        });
}

function renderCashflowTable(items) {
    const tbody = document.getElementById('cashflow-tbody');
    let html = '';
    
    // Debug: log first item to see structure
    if (items.length > 0) {
        console.log('First item structure:', items[0]);
    }
    
    items.forEach(item => {
        html += renderCashflowRow(item);
    });
    
    tbody.innerHTML = html;
}

function renderCashflowRow(item) {
    let html = '<tr>';
    html += `<td>${item.kode}</td>`;
    
    // Indentation based on level
    let indentation = '';
    for(let i = 1; i < item.level; i++) {
        indentation += '&nbsp;&nbsp;&nbsp;&nbsp;';
    }
    html += `<td>${indentation}${item.keterangan}</td>`;
    
    // Trial Balance info
    if (item.trial_balance_id && item.trial_balance) {
        html += `<td>${item.trial_balance.kode} - ${item.trial_balance.keterangan}</td>`;
    } else {
        html += '<td>-</td>';
    }
    
    html += `<td class="text-center">
        <div class="btn-list">
            <a href="/cashflow/${item.id}/edit" class="btn btn-sm btn-outline-primary">Edit</a>
            <form action="/cashflow/${item.id}" method="POST" class="d-inline">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
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
