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
                            <label class="form-label">Kategori</label>
                            <select class="form-select" id="categoryKode">
                                <option value="">Pilih Kategori</option>
                            </select>
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
                            <label class="form-label">Nilai Residual (%)</label>
                            <input type="number" class="form-control" id="residualValue" placeholder="10" value="0" min="0" max="100" step="0.01">
                            <small class="form-hint">Masukkan dalam persen (contoh: 10 untuk 10%)</small>
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
                        <th>Umur Manfaat</th>
                        <th>Nilai Residual</th>
                        <th>Asset Account</th>
                        <th>Accumulated Account</th>
                        <th>Expense Account</th>
                        <th>Nilai Buku</th>
                        <th>Status</th>
                        <th class="w-1">Aksi</th>
                    </tr>
                </thead>
                <tbody id="assetTableBody">
                    <tr>
                        <td colspan="12" class="text-center">Loading...</td>
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
        
        const categorySelect = document.getElementById('categoryKode');
        const categoryOptions = '<option value="">Pilih Kategori</option>' + 
            result.data.categories.map(cat => {
                const indent = cat.level === 2 ? '&nbsp;&nbsp;' : '';
                const style = cat.level === 1 ? 'font-weight: bold;' : '';
                return `<option value="${cat.kode}" style="${style}">${indent}${cat.nama}</option>`;
            }).join('');
        categorySelect.innerHTML = categoryOptions;
        
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
        tbody.innerHTML = '<tr><td colspan="12" class="text-center text-muted">Tidak ada data aset tetap</td></tr>';
        return;
    }
    
    // Group assets by category
    const categories = {
        '1': { name: 'Bangunan', assets: [] },
        '2': { name: 'Kendaraan', assets: [] },
        '3': { name: 'Peralatan', assets: [] },
        '4': { name: 'Tanah', assets: [] },
        'uncategorized': { name: 'Tidak Berkategori', assets: [] }
    };
    
    assets.forEach(asset => {
        const categoryCode = asset.category_kode ? asset.category_kode.split('.')[0] : 'uncategorized';
        if (categories[categoryCode]) {
            categories[categoryCode].assets.push(asset);
        } else {
            categories['uncategorized'].assets.push(asset);
        }
    });
    
    let html = '';
    
    Object.keys(categories).forEach(categoryCode => {
        const category = categories[categoryCode];
        if (category.assets.length > 0) {
            // Category header
            html += `
                <tr class="table-active">
                    <td colspan="12" class="fw-bold text-primary">${category.name}</td>
                </tr>
            `;
            
            // Assets in category
            category.assets.forEach(asset => {
                html += `
                    <tr id="row-${asset.id}" data-id="${asset.id}">
                        <td class="editable ps-4" data-field="code">${asset.code}</td>
                        <td class="editable" data-field="name">${asset.name}</td>
                        <td class="editable" data-field="acquisition_date">${new Date(asset.acquisition_date).toLocaleDateString('id-ID')}</td>
                        <td class="editable" data-field="acquisition_price">${new Intl.NumberFormat('id-ID').format(asset.acquisition_price)}</td>
                        <td class="editable" data-field="useful_life_months">${asset.useful_life_months} bulan</td>
                        <td class="editable" data-field="residual_value">${(asset.residual_value * 100).toFixed(2)}%</td>
                        <td class="editable text-muted small" data-field="asset_account_id">${asset.asset_account ? asset.asset_account.kode + ' - ' + asset.asset_account.keterangan : '-'}</td>
                        <td class="editable text-muted small" data-field="accumulated_account_id">${asset.accumulated_account ? asset.accumulated_account.kode + ' - ' + asset.accumulated_account.keterangan : '-'}</td>
                        <td class="editable text-muted small" data-field="expense_account_id">${asset.expense_account ? asset.expense_account.kode + ' - ' + asset.expense_account.keterangan : '-'}</td>
                        <td class="text-success">${new Intl.NumberFormat('id-ID').format(asset.current_book_value)}</td>
                        <td class="editable" data-field="is_active">
                            <span class="badge bg-${asset.is_active ? 'success' : 'danger'}">
                                ${asset.is_active ? 'Aktif' : 'Tidak Aktif'}
                            </span>
                        </td>
                        <td>
                            <div class="btn-list flex-nowrap">
                                <a href="/fixed-assets/${asset.id}" class="btn btn-sm btn-white">Detail</a>
                                <button class="btn btn-sm btn-primary edit-btn" onclick="editRow(${asset.id})">Edit</button>
                                <button class="btn btn-sm btn-success save-btn" onclick="saveRow(${asset.id})" style="display: none;">Simpan</button>
                                <button class="btn btn-sm btn-secondary cancel-btn" onclick="cancelEdit(${asset.id})" style="display: none;">Batal</button>
                                <button class="btn btn-sm btn-danger" onclick="deleteAsset(${asset.id})">Hapus</button>
                            </div>
                        </td>
                    </tr>
                `;
            });
        }
    });
    
    tbody.innerHTML = html;
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
        } else if (field === 'useful_life_months') {
            const numericValue = currentValue.replace(' bulan', '');
            cell.innerHTML = `<input type="number" class="form-control form-control-sm" value="${numericValue}" min="1">`;
        } else if (field === 'residual_value') {
            const percentValue = parseFloat(currentValue.replace('%', ''));
            cell.innerHTML = `<input type="number" class="form-control form-control-sm" value="${percentValue}" min="0" max="100" step="0.01">`;
        } else if (field === 'asset_account_id' || field === 'accumulated_account_id' || field === 'expense_account_id') {
            const options = '<option value="">Pilih Account</option>' + 
                trialBalances.map(account => `<option value="${account.id}">${account.kode} - ${account.keterangan}</option>`).join('');
            cell.innerHTML = `<select class="form-select form-select-sm">${options}</select>`;
            
            // Set selected value
            const select = cell.querySelector('select');
            if (currentValue !== '-') {
                const accountCode = currentValue.split(' - ')[0];
                const account = trialBalances.find(acc => acc.kode === accountCode);
                if (account) select.value = account.id;
            }
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
            if (field === 'residual_value') {
                formData[field] = parseFloat(input.value) / 100 || 0;
            } else {
                formData[field] = input.value;
            }
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
        category_kode: document.getElementById('categoryKode').value,
        acquisition_date: document.getElementById('acquisitionDate').value,
        acquisition_price: parseCurrency(document.getElementById('acquisitionPrice').value),
        residual_value: parseFloat(document.getElementById('residualValue').value) / 100 || 0,
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

function createSampleData() {
    const sampleAssets = [
        {code: 'BNG-001', name: 'Gedung Kantor Pusat', category_kode: '1.1', price: 2500000000},
        {code: 'BNG-002', name: 'Gudang Penyimpanan', category_kode: '1.2', price: 800000000},
        {code: 'KND-001', name: 'Mobil Toyota Avanza', category_kode: '2.1', price: 250000000},
        {code: 'KND-002', name: 'Motor Honda Vario', category_kode: '2.2', price: 25000000},
        {code: 'PRL-001', name: 'Laptop Dell Latitude', category_kode: '3.1', price: 15000000},
        {code: 'PRL-002', name: 'Mesin Produksi A1', category_kode: '3.2', price: 500000000},
    ];
    
    let created = 0;
    
    sampleAssets.forEach(async (asset, index) => {
        setTimeout(async () => {
            try {
                const response = await fetch('/fixed-assets', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({
                        code: asset.code,
                        name: asset.name,
                        category_kode: asset.category_kode,
                        acquisition_date: '2023-01-15',
                        acquisition_price: asset.price,
                        residual_value: 0.10,
                        useful_life_months: 60,
                        asset_account_id: trialBalances[0]?.id,
                        accumulated_account_id: trialBalances[1]?.id,
                        expense_account_id: trialBalances[2]?.id
                    })
                });
                
                created++;
                if (created === sampleAssets.length) {
                    showAlert('success', 'Sample data berhasil dibuat!');
                    refreshTableData();
                }
            } catch (error) {
                console.error('Error creating sample:', error);
            }
        }, index * 500);
    });
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