@extends('layouts.app')

@section('title', 'List of Accounts')

@section('page-header')
    <div class="page-pretitle">Jurnal</div>
    <h2 class="page-title">List of Accounts</h2>
@endsection

@section('page-actions')
    <button class="btn btn-primary" onclick="showCreateForm()">
        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="m0 0h24v24H0z" fill="none"/><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        Tambah Akun
    </button>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <!-- Create Form -->
        <div class="card mb-3" id="create-form" style="display: none;">
            <div class="card-header">
                <h3 class="card-title">Tambah Akun Baru</h3>
            </div>
            <form id="account-form">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <label class="form-label">Kode Akun</label>
                            <input type="text" class="form-control" id="code" name="code" placeholder="Contoh: 1100" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Nama Akun</label>
                            <input type="text" class="form-control" id="name" name="name" placeholder="Contoh: Kas Besar" required>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Tipe</label>
                            <select class="form-select" id="type" name="type" required>
                                <option value="">Pilih Tipe</option>
                                <option value="kas">Kas</option>
                                <option value="bank">Bank</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Saldo Awal</label>
                            <input type="number" class="form-control" id="opening_balance" name="opening_balance" placeholder="0" value="0" step="0.01">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">&nbsp;</label>
                            <div>
                                <button type="submit" class="btn btn-success me-2">Simpan</button>
                                <button type="button" class="btn btn-secondary" onclick="hideCreateForm()">Batal</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <!-- Accounts List -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Daftar Akun</h3>
            </div>
            <div class="table-responsive">
                <table class="table table-vcenter card-table">
                    <thead>
                        <tr>
                            <th style="text-align:center">Kode</th>
                            <th style="text-align:center">Nama Akun</th>
                            <th style="text-align:center">Tipe</th>
                            <th style="text-align:center">Saldo</th>
                            <th style="text-align:center">Status</th>
                            <th class="w-1" style="text-align:center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($accounts as $account)
                        <tr id="row-{{ $account->id }}">
                            <td>
                                <span class="view-mode">{{ $account->code }}</span>
                                <input type="text" class="form-control edit-mode" style="display:none" value="{{ $account->code }}" data-field="code">
                            </td>
                            <td>
                                <span class="view-mode">{{ $account->name }}</span>
                                <input type="text" class="form-control edit-mode" style="display:none" value="{{ $account->name }}" data-field="name">
                            </td>
                            <td>
                                <span class="view-mode">
                                    <span class="badge bg-{{ $account->type === 'kas' ? 'success' : 'primary' }}">
                                        {{ ucfirst($account->type) }}
                                    </span>
                                </span>
                                <select class="form-select edit-mode" style="display:none" data-field="type">
                                    <option value="kas" {{ $account->type === 'kas' ? 'selected' : '' }}>Kas</option>
                                    <option value="bank" {{ $account->type === 'bank' ? 'selected' : '' }}>Bank</option>
                                </select>
                            </td>
                            <td>
                                <span class="text-end">{{ number_format($account->getCurrentBalance(), 0, ',', '.') }}</span>
                            </td>
                            <td>
                                <span class="badge bg-{{ $account->is_active ? 'success' : 'secondary' }}">
                                    {{ $account->is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>
                            <td>
                                <div class="btn-list flex-nowrap">
                                    <button class="btn btn-sm btn-outline-primary view-mode" onclick="editAccount({{ $account->id }})">
                                        Edit
                                    </button>
                                    <button class="btn btn-sm btn-success edit-mode" style="display:none" onclick="saveAccount({{ $account->id }})">
                                        Simpan
                                    </button>
                                    <button class="btn btn-sm btn-secondary edit-mode" style="display:none" onclick="cancelEdit({{ $account->id }})">
                                        Batal
                                    </button>
                                    <button class="btn btn-sm btn-outline-danger view-mode" onclick="deleteAccount({{ $account->id }})">
                                        Hapus
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">
                                Belum ada akun
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($accounts->hasPages())
            <div class="card-footer">
                {{ $accounts->links('pagination.tabler') }}
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Confirm Modal -->
<div class="modal modal-blur fade" id="confirm-modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-body text-center py-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon mb-2 text-warning" width="48" height="48" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="m0 0h24v24H0z" fill="none"/>
                    <path d="M12 9v2m0 4v.01"/>
                    <path d="M5 19h14a2 2 0 0 0 1.84 -2.75l-7.1 -12.25a2 2 0 0 0 -3.5 0l-7.1 12.25a2 2 0 0 0 1.75 2.75"/>
                </svg>
                <h3>Konfirmasi</h3>
                <div class="text-muted" id="confirm-message">Apakah Anda yakin?</div>
            </div>
            <div class="modal-footer">
                <div class="w-100">
                    <div class="row">
                        <div class="col">
                            <button type="button" class="btn btn-secondary w-100" data-bs-dismiss="modal">Batal</button>
                        </div>
                        <div class="col">
                            <button type="button" class="btn btn-warning w-100" id="confirm-yes">Ya, Lanjutkan</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Alert Modal -->
<div class="modal modal-blur fade" id="alert-modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-body text-center py-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon mb-2" id="alert-icon" width="48" height="48" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="m0 0h24v24H0z" fill="none"/>
                    <path d="M5 12l5 5l10 -10"/>
                </svg>
                <h3 id="alert-title">Berhasil</h3>
                <div class="text-muted" id="alert-message">Operasi berhasil dilakukan</div>
            </div>
            <div class="modal-footer">
                <div class="w-100">
                    <button type="button" class="btn btn-primary w-100" data-bs-dismiss="modal">OK</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function showCreateForm() {
    document.getElementById('create-form').style.display = 'block';
}

function hideCreateForm() {
    document.getElementById('create-form').style.display = 'none';
    document.getElementById('account-form').reset();
}

document.getElementById('account-form').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const formData = new FormData(e.target);
    formData.set('is_active', '1');
    
    try {
        const response = await fetch('/accounts', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: formData
        });
        
        const data = await response.json();
        
        if (data.success) {
            location.reload();
        } else {
            showAlert('error', data.message || 'Terjadi kesalahan');
        }
    } catch (error) {
        showAlert('error', 'Terjadi kesalahan saat menyimpan data');
    }
});

function editAccount(id) {
    const row = document.getElementById(`row-${id}`);
    row.querySelectorAll('.view-mode').forEach(el => el.style.display = 'none');
    row.querySelectorAll('.edit-mode').forEach(el => el.style.display = 'inline-block');
}

function cancelEdit(id) {
    const row = document.getElementById(`row-${id}`);
    row.querySelectorAll('.view-mode').forEach(el => el.style.display = 'inline-block');
    row.querySelectorAll('.edit-mode').forEach(el => el.style.display = 'none');
}

async function saveAccount(id) {
    document.getElementById('confirm-message').textContent = 'Apakah Anda yakin data sudah sesuai?';
    const modal = new bootstrap.Modal(document.getElementById('confirm-modal'));
    modal.show();
    
    document.getElementById('confirm-yes').onclick = function() {
        modal.hide();
        doSaveAccount(id);
    };
}

async function doSaveAccount(id) {
    const row = document.getElementById(`row-${id}`);
    const formData = new FormData();
    
    formData.append('_method', 'PUT');
    
    row.querySelectorAll('.edit-mode[data-field]').forEach(input => {
        formData.append(input.dataset.field, input.value);
    });
    
    try {
        const response = await fetch(`/accounts/${id}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: formData
        });
        
        const data = await response.json();
        
        if (data.success) {
            location.reload();
        } else {
            showAlert('error', data.message || 'Terjadi kesalahan');
        }
    } catch (error) {
        showAlert('error', 'Terjadi kesalahan saat menyimpan data');
    }
}

async function deleteAccount(id) {
    if (confirm('Apakah Anda yakin ingin menghapus akun ini?')) {
        try {
            const response = await fetch(`/accounts/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                }
            });
            
            const data = await response.json();
            
            if (data.success) {
                location.reload();
            } else {
                showAlert('error', data.message || 'Gagal menghapus akun');
            }
        } catch (error) {
            showAlert('error', 'Terjadi kesalahan saat menghapus data');
        }
    }
}

function showAlert(type, message) {
    document.getElementById('alert-title').textContent = type === 'success' ? 'Berhasil' : 'Error';
    document.getElementById('alert-message').textContent = message;
    document.getElementById('alert-icon').className = type === 'success' ? 'text-success' : 'text-danger';
    new bootstrap.Modal(document.getElementById('alert-modal')).show();
}
</script>
@endpush