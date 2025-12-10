@extends('layouts.app')

@section('title', 'Kelola Aset Tetap')

@section('page-header')
    <div class="page-pretitle">Master Data</div>
    <h2 class="page-title">Kelola Aset Tetap</h2>
@endsection

@section('page-actions')
    <a href="/fixed-assets/create" class="btn btn-primary">
        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="m0 0h24v24H0z" fill="none"/><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        Tambah Aset Tetap
    </a>
@endsection

@push('styles')
<style>
.table-vcenter {
    table-layout: fixed;
    width: 100%;
}

.table-vcenter th:nth-child(1) { width: 120px; }
.table-vcenter th:nth-child(2) { width: auto; min-width: 200px; }
.table-vcenter th:nth-child(3) { width: 100px; }
.table-vcenter th:nth-child(4) { width: 120px; }
.table-vcenter th:nth-child(5) { width: 150px; }
.table-vcenter th:nth-child(6) { width: 150px; }
.table-vcenter th:nth-child(7) { width: 80px; }

.table-vcenter td {
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.table-vcenter td:nth-child(2) {
    white-space: normal;
    word-wrap: break-word;
}
</style>
@endpush

@section('content')
    <div class="card">
        <div class="table-responsive">
            <table class="table table-vcenter card-table">
                <thead>
                    <tr>
                        <th>Nomor Aset</th>
                        <th>Nama Aset</th>
                        <th>Status</th>
                        <th>Tanggal Perolehan</th>
                        <th>Harga Perolehan</th>
                        <th>Nilai Buku</th>
                        <th class="w-1">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($assets ?? [] as $asset)
                        <tr>
                            <td>{{ $asset->asset_number ?? $asset->code ?? '-' }}</td>
                            <td>{{ $asset->asset_name ?? $asset->name }}</td>
                            <td>
                                <span class="badge bg-{{ $asset->status === 'Active' ? 'success' : 'danger' }}">
                                    {{ $asset->status === 'Active' ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>
                            <td>{{ $asset->acquisition_date ? \Carbon\Carbon::parse($asset->acquisition_date)->format('d/m/Y') : '-' }}</td>
                            <td>{{ $asset->acquisition_price ? number_format($asset->acquisition_price, 0, ',', '.') : '-' }}</td>
                            <td class="text-success">{{ ($asset->current_book_value ?? $asset->acquisition_price) ? number_format($asset->current_book_value ?? $asset->acquisition_price, 0, ',', '.') : '-' }}</td>
                            <td>
                                <div class="btn-list flex-nowrap">
                                    <a href="/fixed-assets/{{ $asset->id }}" class="btn btn-sm btn-white">Detail</a>
                                    <a href="/fixed-assets/{{ $asset->id }}/edit" class="btn btn-sm btn-primary">Edit</a>
                                    <form method="POST" action="/fixed-assets/{{ $asset->id }}" style="display: inline;" onsubmit="return confirm('Yakin ingin menghapus?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted">Tidak ada data aset tetap</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Alert Modal -->
    <div class="modal modal-blur fade" id="alertModal" tabindex="-1">
        <div class="modal-dialog modal-sm modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body text-center py-4">
                    <div id="alertIcon" class="mb-2"></div>
                    <h3 id="alertTitle">Alert</h3>
                    <div class="text-muted" id="alertMessage">Pesan alert</div>
                </div>
                <div class="modal-footer">
                    <div class="w-100">
                        <button type="button" class="btn w-100" id="alertButton" data-bs-dismiss="modal">OK</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

