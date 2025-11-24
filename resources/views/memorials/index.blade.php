@extends('layouts.app')

@section('title', 'Memorial Entry')

@section('page-header')
    <div class="page-pretitle">Memorial</div>
    <h2 class="page-title">Memorial Entry</h2>
@endsection

@section('page-actions')
    <a href="{{ route('memorials.create') }}" class="btn btn-primary">
        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="m0 0h24v24H0z" fill="none"/><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        Tambah Memorial
    </a>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Daftar Memorial</h3>
            </div>
            <div class="table-responsive">
                <table class="table table-vcenter card-table">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Referensi</th>
                            <th>Deskripsi</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th class="w-1">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($memorials ?? [] as $memorial)
                        <tr>
                            <td>{{ $memorial->date }}</td>
                            <td>{{ $memorial->reference ?? '-' }}</td>
                            <td>{{ $memorial->description }}</td>
                            <td>{{ number_format($memorial->details->sum('debit'), 0, ',', '.') }}</td>
                            <td>
                                <span class="badge bg-{{ $memorial->is_posted ? 'success' : 'warning' }}">
                                    {{ $memorial->is_posted ? 'Posted' : 'Draft' }}
                                </span>
                            </td>
                            <td>
                                <div class="btn-list flex-nowrap">
                                    <button class="btn btn-sm btn-outline-primary" onclick="viewMemorial({{ $memorial->id }})">
                                        View
                                    </button>
                                    @if(!$memorial->is_posted)
                                    <button class="btn btn-sm btn-outline-danger" onclick="deleteMemorial({{ $memorial->id }})">
                                        Hapus
                                    </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">
                                Belum ada memorial
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Alert Modal -->
<div class="modal fade" id="alertModal" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="alertModalTitle">Info</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p id="alertModalMessage"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">OK</button>
            </div>
        </div>
    </div>
</div>

<!-- Confirm Modal -->
<div class="modal fade" id="confirmModal" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Konfirmasi</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p id="confirmModalMessage"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-danger" id="confirmModalAction">Hapus</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
async function deleteMemorial(id) {
    showConfirm('Apakah Anda yakin ingin menghapus memorial ini?', async () => {
        try {
            const response = await fetch(`/memorials/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                }
            });
            
            const data = await response.json();
            
            if (data.success) {
                showAlert('Sukses', 'Memorial berhasil dihapus', 'success');
                setTimeout(() => location.reload(), 1500);
            } else {
                showAlert('Error', data.message || 'Gagal menghapus memorial', 'error');
            }
        } catch (error) {
            showAlert('Error', 'Terjadi kesalahan saat menghapus data', 'error');
        }
    });
}

function showAlert(title, message, type = 'info') {
    document.getElementById('alertModalTitle').textContent = title;
    document.getElementById('alertModalMessage').textContent = message;
    const modal = new bootstrap.Modal(document.getElementById('alertModal'));
    modal.show();
}

function showConfirm(message, callback) {
    document.getElementById('confirmModalMessage').textContent = message;
    const modal = new bootstrap.Modal(document.getElementById('confirmModal'));
    document.getElementById('confirmModalAction').onclick = () => {
        modal.hide();
        callback();
    };
    modal.show();
}

function viewMemorial(id) {
    // Implement view memorial details
    alert('View memorial ' + id);
}
</script>
@endpush