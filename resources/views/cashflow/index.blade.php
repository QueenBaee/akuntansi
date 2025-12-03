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
<div class="row">
    <div class="col-12">

        <div class="card">

            <div class="table-responsive">
                <table class="table table-vcenter card-table">
                    <thead>
                        <tr>
                            <th>Kode</th>
                            <th>Keterangan</th>
                            <th>Parent</th>
                            <th>Akun TB</th>
                            <th>Level</th>
                            <th class="w-1">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($cashflows as $cashflow)
                            <tr>
                                <td>{{ $cashflow->kode }}</td>
                                <td>
                                    @for($i = 1; $i < $cashflow->level; $i++)
                                        &nbsp;&nbsp;&nbsp;&nbsp;
                                    @endfor
                                    {{ $cashflow->keterangan }}
                                </td>
                                <td>{{ $cashflow->parent->keterangan ?? '-' }}</td>
                                <td>
                                    @if($cashflow->level == 3 && $cashflow->trialBalance)
                                        {{ $cashflow->trialBalance->kode }} - {{ $cashflow->trialBalance->keterangan }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>{{ $cashflow->level }}</td>
                                <td>
                                    <div class="btn-list flex-nowrap">
                                        <a href="{{ route('cashflow.edit', $cashflow->id) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                        <form action="{{ route('cashflow.destroy', $cashflow->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-sm btn-outline-danger" onclick="return confirm('Hapus?')">Hapus</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            

        </div>

    </div>
</div>
@endsection
