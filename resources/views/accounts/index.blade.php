@extends('layouts.app')

@section('title', 'Chart of Accounts')

@section('page-header')
    <div class="page-pretitle">Master Data</div>
    <h2 class="page-title">Chart of Accounts</h2>
@endsection

@section('page-actions')
    <a href="{{ route('accounts.create') }}" class="btn btn-primary">
        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="m0 0h24v24H0z" fill="none"/><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        Tambah Akun
    </a>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Daftar Akun</h3>
            </div>
            <div class="table-responsive">
                <table class="table table-vcenter card-table">
                    <thead>
                        <tr>
                            <th>Kode</th>
                            <th>Nama Akun</th>
                            <th>Tipe</th>
                            <th>Kategori</th>
                            <th>Parent</th>
                            <th>Status</th>
                            <th class="w-1">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($accounts as $account)
                        <tr>
                            <td><span class="text-muted">{{ $account->code }}</span></td>
                            <td>{{ $account->name }}</td>
                            <td>
                                <span class="badge bg-{{ $account->type === 'asset' ? 'success' : ($account->type === 'liability' ? 'danger' : ($account->type === 'equity' ? 'info' : ($account->type === 'revenue' ? 'primary' : 'warning'))) }}">
                                    {{ ucfirst($account->type) }}
                                </span>
                            </td>
                            <td>{{ $account->category }}</td>
                            <td>{{ $account->parent?->name ?? '-' }}</td>
                            <td>
                                <span class="badge bg-{{ $account->is_active ? 'success' : 'secondary' }}">
                                    {{ $account->is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>
                            <td>
                                <div class="btn-list flex-nowrap">
                                    <a href="{{ route('accounts.edit', $account) }}" class="btn btn-sm btn-outline-primary">
                                        Edit
                                    </a>
                                    <form method="POST" action="{{ route('accounts.destroy', $account) }}" class="d-inline">
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
                                Belum ada akun
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($accounts->hasPages())
            <div class="card-footer">
                {{ $accounts->links() }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection