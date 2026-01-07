@extends('layouts.app')

@section('title', 'Memorial Entry')

@php
    $cacheBuster = time();
@endphp

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

@push('head')
<meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
<meta http-equiv="Pragma" content="no-cache">
<meta http-equiv="Expires" content="0">
@endpush

@section('content')
<div class="row m-0">
    <div class="col-12 p-0">
        <div class="card m-0">
            <div class="card-body p-0">
                <div class="table-responsive" style="max-height: calc(100vh - 250px); overflow-y: auto;">
                    <table class="table table-bordered table-striped m-0">
                        <thead class="table-light" style="position: sticky; top: 0; background-color: #f8f9fa; z-index: 10; border-bottom: 2px solid #dee2e6;">
                            <tr>
                                <th class="text-center" style="text-align:center">Tanggal</th>
                                <th class="text-center" style="text-align:center">Keterangan</th>
                                <th class="text-center" style="text-align:center">PIC</th>
                                <th class="text-center" style="text-align:center">No Bukti</th>
                                <th class="text-center" style="text-align:center">Dokumen</th>
                                <th class="text-center" style="text-align:center">Debit (Rp)</th>
                                <th class="text-center" style="text-align:center">Kredit (Rp)</th>
                                <th class="text-center" style="text-align:center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($memorials ?? [] as $memorial)
                            <tr>
                                <td class="text-center">{{ $memorial->date->format('d/m/Y') }}</td>
                                <td>{{ $memorial->description }}</td>
                                <td class="text-center">{{ $memorial->pic ?? '-' }}</td>
                                <td class="text-center">{{ $memorial->proof_number ?? '-' }}</td>
                                <td class="text-center">
                                    @if($memorial->attachments && $memorial->attachments->count() > 0)
                                        @foreach($memorial->attachments as $attachment)
                                            <a href="{{ route('memorials.view-attachment', [$memorial->id, $attachment->id]) }}?v={{ $cacheBuster }}" target="_blank" class="btn btn-sm btn-outline-primary me-1 mb-1">
                                                {{ $attachment->original_name }}
                                            </a>
                                        @endforeach
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="text-end">{{ number_format($memorial->cash_in ?? 0, 0, ',', '.') }}</td>
                                <td class="text-end">{{ number_format($memorial->cash_out ?? 0, 0, ',', '.') }}</td>
                                <td class="text-center">
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
                                <td colspan="8" class="text-center text-muted py-4">
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