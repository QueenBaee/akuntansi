@extends('layouts.app')

@section('title', 'Jurnal Umum')

@section('page-header')
    <div class="page-pretitle">Transaksi</div>
    <h2 class="page-title">Jurnal Umum</h2>
@endsection

@section('page-actions')
    <a href="{{ route('journals.create') }}" class="btn btn-primary">
        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="m0 0h24v24H0z" fill="none"/><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        Tambah Jurnal
    </a>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Daftar Jurnal</h3>
            </div>
            <div class="table-responsive">
                <table class="table table-vcenter card-table">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Nomor</th>
                            <th>Deskripsi</th>
                            <th>Referensi</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th class="w-1">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($journals as $journal)
                        <tr>
                            <td>{{ $journal->date->format('d/m/Y') }}</td>
                            <td><span class="text-muted">{{ $journal->number }}</span></td>
                            <td>{{ $journal->description }}</td>
                            <td>{{ $journal->reference ?? '-' }}</td>
                            <td>Rp {{ number_format($journal->total_amount, 0, ',', '.') }}</td>
                            <td>
                                <span class="badge bg-{{ $journal->is_posted ? 'success' : 'warning' }}">
                                    {{ $journal->is_posted ? 'Posted' : 'Draft' }}
                                </span>
                            </td>
                            <td>
                                <div class="btn-list flex-nowrap">
                                    <a href="{{ route('journals.show', $journal) }}" class="btn btn-sm btn-outline-info">
                                        Detail
                                    </a>
                                    <a href="{{ route('journals.edit', $journal) }}" class="btn btn-sm btn-outline-primary">
                                        Edit
                                    </a>
                                    <form method="POST" action="{{ route('journals.destroy', $journal) }}" class="d-inline">
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
                                Belum ada jurnal
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($journals->hasPages())
            <div class="card-footer">
                {{ $journals->links() }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection