@extends('layouts.app')

@section('title', 'Transaksi Kas')

@section('page-header')
    <div class="page-pretitle">Transaksi</div>
    <h2 class="page-title">Transaksi Kas</h2>
@endsection

@section('page-actions')
    <a href="{{ route('cash-transactions.create') }}" class="btn btn-primary">
        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="m0 0h24v24H0z" fill="none"/><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        Tambah Transaksi
    </a>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Daftar Transaksi Kas</h3>
                <div class="card-actions">
                    <form method="GET" class="d-flex">
                        <input type="date" name="date_from" class="form-control me-2" value="{{ request('date_from') }}" placeholder="Dari Tanggal">
                        <input type="date" name="date_to" class="form-control me-2" value="{{ request('date_to') }}" placeholder="Sampai Tanggal">
                        <button type="submit" class="btn btn-outline-primary">Filter</button>
                    </form>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-vcenter card-table">
                    <thead>
                        <tr>
                            <th style="text-align:center">Tanggal</th>
                            <th style="text-align:center">Tipe</th>
                            <th style="text-align:center">Akun Kas</th>
                            <th style="text-align:center">Akun Lawan</th>
                            <th style="text-align:center">Jumlah</th>
                            <th style="text-align:center">Deskripsi</th>
                            <th class="w-1" style="text-align:center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($transactions as $transaction)
                        <tr>
                            <td>{{ $transaction->date->format('d/m/Y') }}</td>
                            <td>
                                <span class="badge bg-{{ $transaction->type === 'in' ? 'success' : 'danger' }}">
                                    {{ $transaction->type === 'in' ? 'Masuk' : 'Keluar' }}
                                </span>
                            </td>
                            <td>{{ $transaction->cashAccount->name ?? '-' }}</td>
                            <td>{{ $transaction->contraAccount->name ?? '-' }}</td>
                            <td>Rp {{ number_format($transaction->amount, 0, ',', '.') }}</td>
                            <td>{{ $transaction->description }}</td>
                            <td>
                                <div class="btn-list flex-nowrap">
                                    <a href="{{ route('cash-transactions.edit', $transaction) }}" class="btn btn-sm btn-outline-primary">
                                        Edit
                                    </a>
                                    <form method="POST" action="{{ route('cash-transactions.destroy', $transaction) }}" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Yakin hapus?')">
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                Belum ada transaksi kas
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($transactions->hasPages())
            <div class="card-footer">
                {{ $transactions->links() }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection