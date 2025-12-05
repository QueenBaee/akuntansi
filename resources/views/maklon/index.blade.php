@extends('layouts.app')

@section('title', 'Data Maklon')

@php
    $cacheBuster = time();
@endphp

@section('page-header')
    <div class="page-pretitle">Memorial</div>
    <h2 class="page-title">Data Maklon</h2>
@endsection

@section('page-actions')
    <button class="btn btn-primary" onclick="addMaklon()">
        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="m0 0h24v24H0z" fill="none"/><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        Tambah Maklon
    </button>
@endsection

@push('head')
<meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
<meta http-equiv="Pragma" content="no-cache">
<meta http-equiv="Expires" content="0">
@endpush

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Daftar Data Maklon</h3>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead class="table-light">
                            <tr>
                                <th class="text-center">Tanggal</th>
                                <th class="text-center">Keterangan</th>
                                <th class="text-center">PIC</th>
                                <th class="text-center">No Bukti</th>
                                <th class="text-center">Dokumen</th>
                                <th class="text-center">Batang</th>
                                <th class="text-center">DPP (Rp)</th>
                                <th class="text-center">PPN (%)</th>
                                <th class="text-center">PPh23 (%)</th>
                                <th class="text-center">Post</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="maklonTableBody">
                            @forelse($maklons ?? [] as $maklon)
                            <tr data-id="{{ $maklon->id }}" data-existing="1">
                                <td class="text-center">{{ $maklon->date->format('d/m/Y') }}</td>
                                <td>{{ $maklon->description }}</td>
                                <td class="text-center">{{ $maklon->pic ?? '-' }}</td>
                                <td class="text-center">{{ $maklon->proof_number ?? '-' }}</td>
                                <td class="text-center">
                                    @if($maklon->attachments && $maklon->attachments->count() > 0)
                                        @foreach($maklon->attachments as $attachment)
                                            <a href="{{ route('maklon.view-attachment', [$maklon->id, $attachment->id]) }}?v={{ $cacheBuster }}" target="_blank" class="btn btn-sm btn-outline-primary me-1 mb-1">
                                                {{ $attachment->original_name }}
                                            </a>
                                        @endforeach
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="text-center">{{ number_format($maklon->batang, 2, ',', '.') }}</td>
                                <td class="text-end">{{ number_format($maklon->dpp, 0, ',', '.') }}</td>
                                <td class="text-center">{{ $maklon->ppn }}%</td>
                                <td class="text-center">{{ $maklon->pph23 }}%</td>
                                <td class="text-center">
                                    @if($maklon->is_posted)
                                        <a href="{{ route('memorials.index') }}" class="btn btn-sm btn-success">Posted</a>
                                    @else
                                        <button class="btn btn-sm btn-outline-primary" onclick="postMaklon({{ $maklon->id }})">Post</button>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if(!$maklon->is_posted)
                                    <div class="btn-list flex-nowrap">
                                        <button class="btn btn-sm btn-warning me-1" onclick="editMaklon(this, {{ $maklon->id }})" title="Edit">✎</button>
                                        <button class="btn btn-sm btn-outline-danger" onclick="deleteMaklon({{ $maklon->id }})">
                                            Hapus
                                        </button>
                                    </div>
                                    @else
                                        -
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="11" class="text-center text-muted py-4">
                                    Belum ada data maklon
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

<!-- Add Maklon Modal -->
<div class="modal fade" id="addMaklonModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Tambah Data Maklon</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="addMaklonForm" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Tanggal</label>
                                <input type="date" class="form-control" name="date" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">PIC</label>
                                <input type="text" class="form-control" name="pic">
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Keterangan</label>
                        <input type="text" class="form-control" name="description" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">No Bukti</label>
                                <input type="text" class="form-control" name="proof_number">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Batang</label>
                                <input type="number" class="form-control" name="batang" step="0.01" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">DPP (Rp)</label>
                                <input type="number" class="form-control" name="dpp" step="0.01" required>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label">PPN (%)</label>
                                <input type="number" class="form-control" name="ppn" step="0.01" min="0" max="100" required>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label">PPh23 (%)</label>
                                <input type="number" class="form-control" name="pph23" step="0.01" min="0" max="100" required>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Dokumen</label>
                        <input type="file" class="form-control" name="attachments[]" multiple accept=".jpg,.jpeg,.png,.pdf">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
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
                <button type="button" class="btn btn-danger" id="confirmModalAction">Konfirmasi</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function addMaklon() {
    document.getElementById('addMaklonForm').reset();
    const modal = new bootstrap.Modal(document.getElementById('addMaklonModal'));
    modal.show();
}

document.getElementById('addMaklonForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    try {
        const response = await fetch('/maklon', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            },
            body: formData
        });
        
        const data = await response.json();
        
        if (data.success) {
            showAlert('Sukses', data.message, 'success');
            bootstrap.Modal.getInstance(document.getElementById('addMaklonModal')).hide();
            setTimeout(() => location.reload(), 1500);
        } else {
            showAlert('Error', data.message || 'Gagal menambahkan maklon', 'error');
        }
    } catch (error) {
        showAlert('Error', 'Terjadi kesalahan saat menambahkan data', 'error');
    }
});

function editMaklon(button, id) {
    const row = button.closest('tr');
    const isEditing = row.classList.contains('editing');
    
    // Check if already posted (additional safety check)
    const postCell = row.children[9];
    if (postCell && postCell.textContent.includes('Posted')) {
        showAlert('Error', 'Data yang sudah diposting tidak dapat diedit', 'error');
        return;
    }
    
    if (isEditing) {
        saveMaklon(row, id);
    } else {
        enterEditMode(row, button);
    }
}

function enterEditMode(row, editButton) {
    row.classList.add('editing');
    row.style.backgroundColor = '#fff3cd';
    
    const cells = row.children;
    const id = row.getAttribute('data-id');
    
    // Get current values
    const currentDate = cells[0].textContent.trim();
    const description = cells[1].textContent.trim();
    const pic = cells[2].textContent.trim() === '-' ? '' : cells[2].textContent.trim();
    const proofNumber = cells[3].textContent.trim() === '-' ? '' : cells[3].textContent.trim();
    const batang = cells[5].textContent.replace(/[^0-9.,]/g, '').replace(',', '.');
    const dpp = cells[6].textContent.replace(/[^0-9]/g, '');
    const ppn = cells[7].textContent.replace('%', '');
    const pph23 = cells[8].textContent.replace('%', '');
    
    // Convert date format from d/m/Y to Y-m-d
    const dateParts = currentDate.split('/');
    const formattedDate = dateParts.length === 3 ? `${dateParts[2]}-${dateParts[1].padStart(2, '0')}-${dateParts[0].padStart(2, '0')}` : currentDate;
    
    // Convert to editable fields
    cells[0].innerHTML = `<input type="date" class="form-control form-control-sm edit-date" value="${formattedDate}" style="font-size: 12px;">`;
    cells[1].innerHTML = `<input type="text" class="form-control form-control-sm edit-description" value="${description}" style="font-size: 12px;">`;
    cells[2].innerHTML = `<input type="text" class="form-control form-control-sm edit-pic" value="${pic}" style="font-size: 12px;">`;
    cells[3].innerHTML = `<input type="text" class="form-control form-control-sm edit-proof" value="${proofNumber}" style="font-size: 12px;">`;
    cells[4].innerHTML = `<input type="file" class="form-control form-control-sm edit-attachments" accept=".jpg,.jpeg,.png,.pdf" multiple style="font-size: 11px;">`;
    cells[5].innerHTML = `<input type="number" class="form-control form-control-sm edit-batang" value="${batang}" step="0.01" style="font-size: 12px;">`;
    cells[6].innerHTML = `<input type="number" class="form-control form-control-sm edit-dpp" value="${dpp}" step="0.01" style="font-size: 12px; text-align: right;">`;
    cells[7].innerHTML = `<div class="input-group input-group-sm"><input type="number" class="form-control edit-ppn" value="${ppn}" step="0.01" min="0" max="100" style="font-size: 12px;"><span class="input-group-text">%</span></div>`;
    cells[8].innerHTML = `<div class="input-group input-group-sm"><input type="number" class="form-control edit-pph23" value="${pph23}" step="0.01" min="0" max="100" style="font-size: 12px;"><span class="input-group-text">%</span></div>`;
    
    // Change button to save
    editButton.innerHTML = '✓';
    editButton.className = 'btn btn-sm btn-success me-1';
    editButton.title = 'Simpan';
}

function saveMaklon(row, id) {
    const formData = new FormData();
    
    formData.append('date', row.querySelector('.edit-date').value);
    formData.append('description', row.querySelector('.edit-description').value);
    formData.append('pic', row.querySelector('.edit-pic').value);
    formData.append('proof_number', row.querySelector('.edit-proof').value);
    formData.append('batang', row.querySelector('.edit-batang').value);
    formData.append('dpp', row.querySelector('.edit-dpp').value);
    formData.append('ppn', row.querySelector('.edit-ppn').value);
    formData.append('pph23', row.querySelector('.edit-pph23').value);
    formData.append('_method', 'PUT');
    
    // Handle file attachments
    const fileInput = row.querySelector('.edit-attachments');
    if (fileInput.files.length > 0) {
        for (let i = 0; i < fileInput.files.length; i++) {
            formData.append('attachments[]', fileInput.files[i]);
        }
    }
    
    fetch(`/maklon/${id}`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('Sukses', 'Maklon berhasil diperbarui', 'success');
            setTimeout(() => location.reload(), 1500);
        } else {
            showAlert('Error', data.message || 'Gagal memperbarui maklon', 'error');
        }
    })
    .catch(error => {
        showAlert('Error', 'Gagal memperbarui maklon', 'error');
    });
}

async function postMaklon(id) {
    showConfirm('Apakah Anda yakin ingin memposting maklon ini? Setelah diposting, data tidak dapat diubah lagi.', async () => {
        try {
            const response = await fetch(`/maklon/${id}/post`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                }
            });
            
            const data = await response.json();
            
            if (data.success) {
                showAlert('Sukses', data.message, 'success');
                setTimeout(() => location.reload(), 1500);
            } else {
                showAlert('Error', data.message || 'Gagal memposting maklon', 'error');
            }
        } catch (error) {
            showAlert('Error', 'Terjadi kesalahan saat memposting data', 'error');
        }
    });
}

async function deleteMaklon(id) {
    showConfirm('Apakah Anda yakin ingin menghapus maklon ini?', async () => {
        try {
            const response = await fetch(`/maklon/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                }
            });
            
            const data = await response.json();
            
            if (data.success) {
                showAlert('Sukses', data.message, 'success');
                setTimeout(() => location.reload(), 1500);
            } else {
                showAlert('Error', data.message || 'Gagal menghapus maklon', 'error');
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
</script>
@endpush