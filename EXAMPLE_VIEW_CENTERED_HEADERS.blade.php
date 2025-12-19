@extends('layouts.app')

@push('styles')
<style>
    /* Scoped to this view only - center all table headers */
    .ledger-view thead th {
        text-align: center;
    }
</style>
@endpush

@section('title', 'Kelola Ledger')

@section('content')
<div class="ledger-view">
    <div class="card">
        <div class="table-responsive">
            <table class="table table-vcenter card-table">
                <thead>
                    <tr>
                        <th>Nama Ledger</th>
                        <th>Kode Ledger</th>
                        <th>Tipe</th>
                        <th>Status</th>
                        <th class="w-1">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Cash Account 1</td>
                        <td>KAS-001</td>
                        <td><span class="badge bg-primary">Kas</span></td>
                        <td><span class="badge bg-success">Aktif</span></td>
                        <td>
                            <button class="btn btn-sm">Edit</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
