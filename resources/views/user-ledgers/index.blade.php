@extends('layouts.app')

@section('title', 'Kelola User Ledgers')

@section('page-header')
    <div class="page-pretitle">Master Data</div>
    <h2 class="page-title">Kelola User Ledgers</h2>
@endsection

@section('page-actions')
    <button type="button" class="btn btn-primary" onclick="toggleCreateForm()">
        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="m0 0h24v24H0z" fill="none"/><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        <span id="toggleText">Tambah User Ledger</span>
    </button>
@endsection

@section('content')
    <!-- Create Form -->
    <div class="card mb-3" id="createForm" style="display: none;">
        <div class="card-header">
            <h3 class="card-title">Tambah User Ledger Baru</h3>
        </div>
        <div class="card-body">
            <form id="userLedgerForm">
                <div class="row">
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label class="form-label">User</label>
                            <select class="form-select" id="userId" required>
                                <option value="">Pilih User</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label class="form-label">Ledger</label>
                            <select class="form-select" id="ledgerId" required>
                                <option value="">Pilih Ledger</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="mb-3">
                            <label class="form-label">Role</label>
                            <input type="text" class="form-control" id="role" placeholder="Role">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <select class="form-select" id="isActive">
                                <option value="1">Aktif</option>
                                <option value="0">Tidak Aktif</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="mb-3">
                            <label class="form-label">&nbsp;</label>
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">Simpan</button>
                                <button type="button" class="btn btn-secondary" onclick="toggleCreateForm()">Batal</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Table -->
    <div class="card">
        <div class="table-responsive">
            <table class="table table-vcenter card-table">
                <thead>
                    <tr>
                        <th style="text-align:center">User</th>
                        <th style="text-align:center">Ledger</th>
                        <th style="text-align:center">Role</th>
                        <th style="text-align:center">Status</th>
                        <th style="text-align:center">Dibuat</th>
                        <th class="w-1" style="text-align:center">Aksi</th>
                    </tr>
                </thead>
                <tbody id="userLedgerTableBody">
                    <tr>
                        <td colspan="6" class="text-center">Loading...</td>
                    </tr>
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

@push('scripts')
<script>
let originalData = {};
let users = [];
let ledgers = [];

function refreshTableData() {
    loadUserLedgers();
}

function toggleCreateForm() {
    const form = document.getElementById('createForm');
    const toggleText = document.getElementById('toggleText');
    
    if (form.style.display === 'none') {
        form.style.display = 'block';
        toggleText.textContent = 'Batal';
        document.getElementById('userLedgerForm').reset();
        loadFormData();
    } else {
        form.style.display = 'none';
        toggleText.textContent = 'Tambah User Ledger';
    }
}

async function loadFormData() {
    try {
        const response = await fetch('/user-ledgers/create', {
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const result = await response.json();
        const data = result.data;
        
        users = data.users;
        ledgers = data.ledgers;
        
        const userSelect = document.getElementById('userId');
        userSelect.innerHTML = '<option value="">Pilih User</option>' + 
            users.map(user => `<option value="${user.id}">${user.name} (${user.email})</option>`).join('');
            
        const ledgerSelect = document.getElementById('ledgerId');
        ledgerSelect.innerHTML = '<option value="">Pilih Ledger</option>' + 
            ledgers.map(ledger => `<option value="${ledger.id}">${ledger.kode_ledger} - ${ledger.nama_ledger}</option>`).join('');
    } catch (error) {
        console.error('Load form data error:', error);
        showAlert('error', 'Gagal memuat data form: ' + error.message);
    }
}

async function loadUserLedgers() {
    try {
        const response = await fetch('/user-ledgers/data', {
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const result = await response.json();
        const userLedgers = result.data.data;
        
        renderUserLedgers(userLedgers);
    } catch (error) {
        console.error('Load user ledgers error:', error);
        showAlert('error', 'Gagal memuat data: ' + error.message);
    }
}

function renderUserLedgers(userLedgers) {
    const tbody = document.getElementById('userLedgerTableBody');
    
    if (userLedgers.length === 0) {
        tbody.innerHTML = '<tr><td colspan="6" class="text-center text-muted">Tidak ada data user ledger</td></tr>';
        return;
    }
    
    tbody.innerHTML = userLedgers.map(userLedger => `
        <tr id="row-${userLedger.id}" data-id="${userLedger.id}">
            <td class="editable" data-field="user_id">
                <div class="d-flex py-1 align-items-center">
                    <span class="avatar me-2" style="background-image: url(https://ui-avatars.com/api/?name=${encodeURIComponent(userLedger.user.name)}&background=206bc4&color=fff)"></span>
                    <div class="flex-fill">
                        <div class="font-weight-medium">${userLedger.user.name}</div>
                        <div class="text-muted">${userLedger.user.email}</div>
                    </div>
                </div>
            </td>
            <td class="editable" data-field="ledger_id">
                <div>
                    <div class="font-weight-medium">${userLedger.ledger.nama_ledger}</div>
                    <div class="text-muted">${userLedger.ledger.kode_ledger} - ${userLedger.ledger.tipe_ledger}</div>
                </div>
            </td>
            <td class="editable" data-field="role">${userLedger.role || '-'}</td>
            <td class="editable" data-field="is_active">
                <span class="badge bg-${userLedger.is_active ? 'success' : 'danger'}">
                    ${userLedger.is_active ? 'Aktif' : 'Tidak Aktif'}
                </span>
            </td>
            <td class="text-muted">${new Date(userLedger.created_at).toLocaleDateString('id-ID')}</td>
            <td>
                <div class="btn-list flex-nowrap">
                    <button class="btn btn-sm edit-btn" onclick="editRow(${userLedger.id})">Edit</button>
                    <button class="btn btn-sm btn-success save-btn" onclick="saveRow(${userLedger.id})" style="display: none;">Simpan</button>
                    <button class="btn btn-sm btn-secondary cancel-btn" onclick="cancelEdit(${userLedger.id})" style="display: none;">Batal</button>
                    <button class="btn btn-sm btn-danger" onclick="deleteUserLedger(${userLedger.id})">Hapus</button>
                </div>
            </td>
        </tr>
    `).join('');
}

async function editRow(id) {
    const row = document.getElementById(`row-${id}`);
    const editables = row.querySelectorAll('.editable');
    
    // Get current data from API
    try {
        const response = await fetch(`/user-ledgers/${id}/edit`, {
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const result = await response.json();
        const data = result.data;
        const userLedger = data.userLedger;
        
        // Store original data
        originalData[id] = {
            user_id: userLedger.user_id,
            ledger_id: userLedger.ledger_id,
            role: userLedger.role || '',
            is_active: userLedger.is_active
        };
        
        editables.forEach(cell => {
            const field = cell.dataset.field;
            
            if (field === 'user_id') {
                let options = '<option value="">Pilih User</option>';
                data.users.forEach(user => {
                    const selected = user.id === userLedger.user_id ? 'selected' : '';
                    options += `<option value="${user.id}" ${selected}>${user.name} (${user.email})</option>`;
                });
                cell.innerHTML = `<select class="form-select form-select-sm">${options}</select>`;
            } else if (field === 'ledger_id') {
                let options = '<option value="">Pilih Ledger</option>';
                data.ledgers.forEach(ledger => {
                    const selected = ledger.id === userLedger.ledger_id ? 'selected' : '';
                    options += `<option value="${ledger.id}" ${selected}>${ledger.kode_ledger} - ${ledger.nama_ledger}</option>`;
                });
                cell.innerHTML = `<select class="form-select form-select-sm">${options}</select>`;
            } else if (field === 'is_active') {
                cell.innerHTML = `
                    <select class="form-select form-select-sm">
                        <option value="1" ${userLedger.is_active ? 'selected' : ''}>Aktif</option>
                        <option value="0" ${!userLedger.is_active ? 'selected' : ''}>Tidak Aktif</option>
                    </select>
                `;
            } else if (field === 'role') {
                cell.innerHTML = `<input type="text" class="form-control form-control-sm" value="${userLedger.role || ''}">`;
            }
        });
        
        // Toggle buttons
        row.querySelector('.edit-btn').style.display = 'none';
        row.querySelector('.save-btn').style.display = 'inline-block';
        row.querySelector('.cancel-btn').style.display = 'inline-block';
        
    } catch (error) {
        showAlert('error', 'Gagal memuat data edit: ' + error.message);
    }
}

function saveRow(id) {
    const row = document.getElementById(`row-${id}`);
    const editables = row.querySelectorAll('.editable');
    const formData = {};
    
    editables.forEach(cell => {
        const field = cell.dataset.field;
        const input = cell.querySelector('input, select');
        if (input) {
            formData[field] = input.value;
        }
    });
    
    fetch(`/user-ledgers/${id}`, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify(formData)
    })
    .then(async response => {
        if (!response.ok) {
            const errorData = await response.json();
            throw new Error(errorData.message || `HTTP error! status: ${response.status}`);
        }
        return await response.json();
    })
    .then(data => {
        if (data.success) {
            showAlert('success', data.message || 'Data berhasil diupdate');
            refreshTableData();
        } else {
            showAlert('error', data.message || 'Gagal mengupdate data');
            cancelEdit(id);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('error', error.message || 'Terjadi kesalahan saat mengupdate data');
        cancelEdit(id);
    });
}

function cancelEdit(id) {
    // Just refresh the table to restore original data
    refreshTableData();
}

function deleteUserLedger(id) {
    showAlert('warning', 'Apakah Anda yakin ingin menghapus user ledger ini?');
    
    const alertButton = document.getElementById('alertButton');
    alertButton.onclick = function() {
        bootstrap.Modal.getInstance(document.getElementById('alertModal')).hide();
        
        fetch(`/user-ledgers/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(async response => {
            if (!response.ok) {
                const errorData = await response.json();
                throw new Error(errorData.message || `HTTP error! status: ${response.status}`);
            }
            return await response.json();
        })
        .then(data => {
            if (data.success) {
                showAlert('success', data.message || 'Data berhasil dihapus');
                refreshTableData();
            } else {
                showAlert('error', data.message || 'Gagal menghapus data');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('error', error.message || 'Terjadi kesalahan saat menghapus data');
        });
        
        alertButton.onclick = null;
    };
}

document.getElementById('userLedgerForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = {
        user_id: document.getElementById('userId').value,
        ledger_id: document.getElementById('ledgerId').value,
        role: document.getElementById('role').value,
        is_active: document.getElementById('isActive').value
    };
    
    fetch('/user-ledgers', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify(formData)
    })
    .then(async response => {
        if (!response.ok) {
            const errorData = await response.json();
            throw new Error(errorData.message || `HTTP error! status: ${response.status}`);
        }
        return await response.json();
    })
    .then(data => {
        if (data.success) {
            showAlert('success', data.message || 'Data berhasil ditambahkan');
            document.getElementById('userLedgerForm').reset();
            toggleCreateForm();
            refreshTableData();
        } else {
            showAlert('error', data.message || 'Gagal menambahkan data');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('error', error.message || 'Terjadi kesalahan saat menambahkan data');
    });
});

function showAlert(type, message) {
    const modal = new bootstrap.Modal(document.getElementById('alertModal'));
    const icon = document.getElementById('alertIcon');
    const title = document.getElementById('alertTitle');
    const messageEl = document.getElementById('alertMessage');
    const button = document.getElementById('alertButton');
    
    if (type === 'success') {
        icon.innerHTML = `
            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-lg text-green" width="48" height="48" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                <path stroke="none" d="m0 0h24v24H0z" fill="none"/>
                <path d="M5 12l5 5l10 -10"/>
            </svg>
        `;
        title.textContent = 'Berhasil!';
        title.className = 'text-green';
        button.className = 'btn btn-success w-100';
    } else if (type === 'error') {
        icon.innerHTML = `
            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-lg text-red" width="48" height="48" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                <path stroke="none" d="m0 0h24v24H0z" fill="none"/>
                <circle cx="12" cy="12" r="9"/>
                <line x1="12" y1="8" x2="12" y2="12"/>
                <line x1="12" y1="16" x2="12.01" y2="16"/>
            </svg>
        `;
        title.textContent = 'Gagal!';
        title.className = 'text-red';
        button.className = 'btn btn-danger w-100';
    } else if (type === 'warning') {
        icon.innerHTML = `
            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-lg text-yellow" width="48" height="48" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                <path stroke="none" d="m0 0h24v24H0z" fill="none"/>
                <path d="M12 9v2m0 4v.01"/>
                <path d="M5 19h14a2 2 0 0 0 1.84 -2.75l-7.1 -12.25a2 2 0 0 0 -3.5 0l-7.1 12.25a2 2 0 0 0 1.75 2.75"/>
            </svg>
        `;
        title.textContent = 'Peringatan!';
        title.className = 'text-yellow';
        button.className = 'btn btn-warning w-100';
    }
    
    messageEl.textContent = message;
    modal.show();
}

// Initialize
document.addEventListener('DOMContentLoaded', () => {
    loadFormData();
    loadUserLedgers();
});
</script>
@endpush