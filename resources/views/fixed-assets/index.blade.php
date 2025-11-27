@extends('layouts.app')

@section('title', 'Kelola Aset Tetap')

@section('page-header')
    <div class="page-pretitle">Master Data</div>
    <h2 class="page-title">Kelola Aset Tetap</h2>
@endsection

@section('page-actions')
    <button type="button" class="btn btn-primary" onclick="toggleCreateForm()">
        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="m0 0h24v24H0z" fill="none"/><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        <span id="toggleText">Tambah Aset Tetap</span>
    </button>
@endsection

@section('content')
    <!-- Create Form -->
    <div class="card mb-3" id="createForm" style="display: none;">
        <div class="card-header">
            <h3 class="card-title">Tambah Aset Tetap Baru</h3>
        </div>
        <div class="card-body">
            <form id="assetForm">
                <div class="row">
                    <div class="col-md-2">
                        <div class="mb-3">
                            <label class="form-label">Kode Aset</label>
                            <input type="text" class="form-control" id="code" required>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label class="form-label">Nama Aset</label>
                            <input type="text" class="form-control" id="name" required>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="mb-3">
                            <label class="form-label">Tanggal Perolehan</label>
                            <input type="date" class="form-control" id="acquisitionDate" required>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="mb-3">
                            <label class="form-label">Harga Perolehan (Rp)</label>
                            <input type="text" class="form-control" id="acquisitionPrice" placeholder="1,000,000" required>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="mb-3">
                            <label class="form-label">Umur Manfaat (bulan)</label>
                            <input type="number" class="form-control" id="usefulLifeMonths" min="1" required>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label class="form-label">Asset Account</label>
                            <select class="form-select" id="assetAccountId" required>
                                <option value="">Pilih Asset Account</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label class="form-label">Accumulated Account</label>
                            <select class="form-select" id="accumulatedAccountId" required>
                                <option value="">Pilih Accumulated Account</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label class="form-label">Expense Account</label>
                            <select class="form-select" id="expenseAccountId" required>
                                <option value="">Pilih Expense Account</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label class="form-label">Nilai Residual</label>
                            <input type="text" class="form-control" id="residualValue" placeholder="0 atau 0.1 (10%)" value="0">
                            <small class="form-hint">Masukkan dalam rupiah (1000000) atau desimal (0.1 = 10%)</small>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <div class="mb-3">
                            <button type="submit" class="btn btn-primary me-2">Simpan</button>
                            <button type="button" class="btn btn-secondary" onclick="toggleCreateForm()">Batal</button>
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
                        <th>Kode</th>
                        <th>Nama Aset</th>
                        <th>Tanggal Perolehan</th>
                        <th>Harga Perolehan</th>
                        <th>Nilai Buku</th>
                        <th>Status</th>
                        <th class="w-1">Aksi</th>
                    </tr>
                </thead>
                <tbody id="assetTableBody">
                    <tr>
                        <td colspan="7" class="text-center">Loading...</td>
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
let trialBalances = [];

function refreshTableData() {
    loadAssets();
}

function toggleCreateForm() {
    const form = document.getElementById('createForm');
    const toggleText = document.getElementById('toggleText');
    
    if (form.style.display === 'none') {
        form.style.display = 'block';
        toggleText.textContent = 'Batal';
        document.getElementById('assetForm').reset();
        loadFormData();
    } else {
        form.style.display = 'none';
        toggleText.textContent = 'Tambah Aset Tetap';
    }
}

async function loadFormData() {
    try {
        const response = await fetch('/fixed-assets/create', {
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
        trialBalances = result.data.trialBalances;
        
        const assetSelect = document.getElementById('assetAccountId');
        const accumulatedSelect = document.getElementById('accumulatedAccountId');
        const expenseSelect = document.getElementById('expenseAccountId');
        
        const options = '<option value="">Pilih Account</option>' + 
            trialBalances.map(account => `<option value="${account.id}">${account.kode} - ${account.keterangan}</option>`).join('');
            
        assetSelect.innerHTML = options;
        accumulatedSelect.innerHTML = options;
        expenseSelect.innerHTML = options;
        
    } catch (error) {
        showAlert('error', 'Gagal memuat data form: ' + error.message);
    }
}

async function loadAssets() {
    try {
        const response = await fetch('/fixed-assets', {
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
        const assets = result.data.data;
        
        renderAssets(assets);
    } catch (error) {
        showAlert('error', 'Gagal memuat data: ' + error.message);
    }
}

function renderAssets(assets) {
    const tbody = document.getElementById('assetTableBody');
    
    if (assets.length === 0) {
        tbody.innerHTML = '<tr><td colspan="7" class="text-center text-muted">Tidak ada data aset tetap</td></tr>';
        return;
    }
    
    tbody.innerHTML = assets.map(asset => `
        <tr id="row-${asset.id}" data-id="${asset.id}">
            <td class="editable" data-field="code">${asset.code}</td>
            <td class="editable" data-field="name">${asset.name}</td>
            <td class="editable" data-field="acquisition_date">${new Date(asset.acquisition_date).toLocaleDateString('id-ID')}</td>
            <td class="editable" data-field="acquisition_price">${new Intl.NumberFormat('id-ID').format(asset.acquisition_price)}</td>
            <td class="text-success">${new Intl.NumberFormat('id-ID').format(asset.current_book_value)}</td>
            <td class="editable" data-field="is_active">
                <span class="badge bg-${asset.is_active ? 'success' : 'danger'}">
                    ${asset.is_active ? 'Aktif' : 'Tidak Aktif'}
                </span>
            </td>
            <td>
                <div class="btn-list flex-nowrap">
                    <a href="/fixed-assets/${asset.id}" class="btn btn-sm btn-white">Detail</a>
                    <button class="btn btn-sm edit-btn" onclick="editRow(${asset.id})">Edit</button>
                    <button class="btn btn-sm btn-success save-btn" onclick="saveRow(${asset.id})" style="display: none;">Simpan</button>
                    <button class="btn btn-sm btn-secondary cancel-btn" onclick="cancelEdit(${asset.id})" style="display: none;">Batal</button>
                    <button class="btn btn-sm btn-danger" onclick="deleteAsset(${asset.id})">Hapus</button>
                </div>
            </td>
        </tr>
    `).join('');
}

function editRow(id) {
    const row = document.getElementById(`row-${id}`);
    const editables = row.querySelectorAll('.editable');
    
    originalData[id] = {};
    
    editables.forEach(cell => {
        const field = cell.dataset.field;
        const currentValue = cell.textContent.trim();
        originalData[id][field] = currentValue;
        
        if (field === 'is_active') {
            const isActive = currentValue === 'Aktif';
            cell.innerHTML = `
                <select class="form-select form-select-sm">
                    <option value="1" ${isActive ? 'selected' : ''}>Aktif</option>
                    <option value="0" ${!isActive ? 'selected' : ''}>Tidak Aktif</option>
                </select>
            `;
        } else if (field === 'acquisition_date') {
            const dateValue = new Date(currentValue.split('/').reverse().join('-')).toISOString().split('T')[0];
            cell.innerHTML = `<input type="date" class="form-control form-control-sm" value="${dateValue}">`;
        } else if (field === 'acquisition_price') {
            const numericValue = currentValue.replace(/\./g, '');
            cell.innerHTML = `<input type="number" class="form-control form-control-sm" value="${numericValue}" step="0.01">`;
        } else {
            cell.innerHTML = `<input type="text" class="form-control form-control-sm" value="${currentValue}">`;
        }
    });
    
    row.querySelector('.edit-btn').style.display = 'none';
    row.querySelector('.save-btn').style.display = 'inline-block';
    row.querySelector('.cancel-btn').style.display = 'inline-block';
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
    
    fetch(`/fixed-assets/${id}`, {
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
    refreshTableData();
}

function deleteAsset(id) {
    showAlert('warning', 'Apakah Anda yakin ingin menghapus aset tetap ini?');
    
    const alertButton = document.getElementById('alertButton');
    alertButton.onclick = function() {
        bootstrap.Modal.getInstance(document.getElementById('alertModal')).hide();
        
        fetch(`/fixed-assets/${id}`, {
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

document.getElementById('assetForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = {
        code: document.getElementById('code').value,
        name: document.getElementById('name').value,
        acquisition_date: document.getElementById('acquisitionDate').value,
        acquisition_price: parseCurrency(document.getElementById('acquisitionPrice').value),
        residual_value: document.getElementById('residualValue').value || 0,
        useful_life_months: document.getElementById('usefulLifeMonths').value,
        asset_account_id: document.getElementById('assetAccountId').value,
        accumulated_account_id: document.getElementById('accumulatedAccountId').value,
        expense_account_id: document.getElementById('expenseAccountId').value
    };
    
    fetch('/fixed-assets', {
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
            document.getElementById('assetForm').reset();
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

// Format currency input
function formatCurrency(input) {
    let value = input.value.replace(/[^\d]/g, '');
    if (value) {
        value = parseInt(value).toLocaleString('id-ID');
    }
    input.value = value;
}

// Parse currency to number
function parseCurrency(value) {
    return parseInt(value.replace(/[^\d]/g, '')) || 0;
}

// Initialize
document.addEventListener('DOMContentLoaded', () => {
    loadFormData();
    loadAssets();
    
    // Add currency formatting to acquisition price
    document.getElementById('acquisitionPrice').addEventListener('input', function() {
        formatCurrency(this);
    });
});
</script>
@endpush