@extends('layouts.app')

@section('title', 'Kelola Ledger')

@section('page-header')
    <div class="page-pretitle">Master Data</div>
    <h2 class="page-title">Kelola Ledger {{ $type ? '- ' . ucfirst($type) : '' }}</h2>
@endsection

@section('page-actions')
    <button type="button" class="btn btn-primary" onclick="toggleCreateForm()">
        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="m0 0h24v24H0z" fill="none"/><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        <span id="toggleText">Tambah Ledger</span>
    </button>

@endsection

@section('content')
    <!-- Create Form -->
    <div class="card mb-3" id="createForm" style="display: none;">
        <div class="card-header">
            <h3 class="card-title">Tambah Ledger Baru</h3>
        </div>
        <div class="card-body">
            <form id="ledgerForm">
                <div class="row">
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label class="form-label">Nama Ledger</label>
                            <input type="text" class="form-control" id="namaLedger" required>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="mb-3">
                            <label class="form-label">Kode Ledger</label>
                            <input type="text" class="form-control" id="kodeLedger" required>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="mb-3">
                            <label class="form-label">Tipe</label>
                            @if($type)
                                <input type="text" class="form-control" value="{{ ucfirst($type) }}" readonly>
                                <input type="hidden" id="tipeLedger" value="{{ $type }}">
                            @else
                                <select class="form-select" id="tipeLedger" required>
                                    <option value="">Pilih</option>
                                    <option value="kas">Kas</option>
                                    <option value="bank">Bank</option>
                                </select>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="mb-3">
                            <label class="form-label">Trial Balance</label>
                            <select class="form-select" id="trialBalanceId">
                                <option value="">Pilih</option>
                                @foreach($trialBalances as $trialBalance)
                                    <option value="{{ $trialBalance->id }}">{{ $trialBalance->kode }} - {{ $trialBalance->keterangan }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="mb-3">
                            <label class="form-label">Deskripsi</label>
                            <input type="text" class="form-control" id="deskripsi">
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
                        <th>Nama Ledger</th>
                        <th>Kode Ledger</th>
                        <th>Tipe</th>
                        <th>Trial Balance</th>
                        <th>Deskripsi</th>
                        <th>Status</th>
                        <th class="w-1">Aksi</th>
                    </tr>
                </thead>
                <tbody id="ledgerTableBody">
                    @forelse($ledgers as $ledger)
                    <tr id="row-{{ $ledger->id }}" data-id="{{ $ledger->id }}">
                        <td class="editable" data-field="nama_ledger">{{ $ledger->nama_ledger }}</td>
                        <td class="editable" data-field="kode_ledger">{{ $ledger->kode_ledger }}</td>
                        <td class="editable" data-field="tipe_ledger">
                            <span class="badge bg-{{ $ledger->tipe_ledger == 'kas' ? 'primary' : 'success' }}">
                                {{ ucfirst($ledger->tipe_ledger) }}
                            </span>
                        </td>
                        <td class="editable" data-field="trial_balance_id">{{ $ledger->trialBalance ? $ledger->trialBalance->kode . ' - ' . $ledger->trialBalance->keterangan : '-' }}</td>
                        <td class="editable" data-field="deskripsi">{{ $ledger->deskripsi }}</td>
                        <td class="editable" data-field="is_active">
                            <span class="badge bg-{{ $ledger->is_active ? 'success' : 'danger' }}">
                                {{ $ledger->is_active ? 'Aktif' : 'Tidak Aktif' }}
                            </span>
                        </td>
                        <td>
                            <div class="btn-list flex-nowrap">
                                <button class="btn btn-sm edit-btn" onclick="editRow({{ $ledger->id }})">Edit</button>

                                <button class="btn btn-sm btn-success save-btn" onclick="saveRow({{ $ledger->id }})" style="display: none;">Simpan</button>
                                <button class="btn btn-sm btn-secondary cancel-btn" onclick="cancelEdit({{ $ledger->id }})" style="display: none;">Batal</button>
                                <button class="btn btn-sm btn-danger" onclick="deleteLedger({{ $ledger->id }})">Hapus</button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5">
                            <div class="empty">
                                <div class="empty-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="48" height="48" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                        <path d="M9 5h-2a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-12a2 2 0 0 0 -2 -2h-2"/>
                                        <rect x="9" y="3" width="6" height="4" rx="2"/>
                                    </svg>
                                </div>
                                <p class="empty-title">No ledgers found</p>
                                <p class="empty-subtitle text-muted">Click "Tambah Ledger" button above to create your first ledger.</p>
                            </div>
                        </td>
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

@push('scripts')
<script>
let originalData = {};
const currentType = '{{ $type ?? "" }}';

function refreshTableData() {
    const currentUrl = window.location.pathname;
    fetch(currentUrl, {
        headers: {
            'Accept': 'text/html',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.text())
    .then(html => {
        const parser = new DOMParser();
        const doc = parser.parseFromString(html, 'text/html');
        const newTableBody = doc.querySelector('#ledgerTableBody');
        if (newTableBody) {
            document.getElementById('ledgerTableBody').innerHTML = newTableBody.innerHTML;
        }
        // Also refresh the menu by reloading the page
        setTimeout(() => {
            window.location.reload();
        }, 500);
    })
    .catch(error => {
        console.error('Error refreshing table:', error);
        window.location.reload();
    });
}

function toggleCreateForm() {
    const form = document.getElementById('createForm');
    const toggleText = document.getElementById('toggleText');
    
    if (form.style.display === 'none') {
        form.style.display = 'block';
        toggleText.textContent = 'Batal';
        document.getElementById('ledgerForm').reset();
    } else {
        form.style.display = 'none';
        toggleText.textContent = 'Tambah Ledger';
    }
}

function editRow(id) {
    const row = document.getElementById(`row-${id}`);
    const editables = row.querySelectorAll('.editable');
    
    // Store original data
    originalData[id] = {};
    
    editables.forEach(cell => {
        const field = cell.dataset.field;
        const currentValue = cell.textContent.trim();
        originalData[id][field] = currentValue;
        
        if (field === 'tipe_ledger') {
            const currentType = currentValue.toLowerCase();
            const isTypeLocked = {{ $type ? 'true' : 'false' }};
            if (isTypeLocked) {
                cell.innerHTML = `<span class="badge bg-${currentType === 'kas' ? 'primary' : 'success'}">${currentValue}</span>`;
            } else {
                cell.innerHTML = `
                    <select class="form-select form-select-sm">
                        <option value="kas" ${currentType === 'kas' ? 'selected' : ''}>Kas</option>
                        <option value="bank" ${currentType === 'bank' ? 'selected' : ''}>Bank</option>
                    </select>
                `;
            }
        } else if (field === 'trial_balance_id') {
            const trialBalances = @json($trialBalances);
            let options = '<option value="">Pilih</option>';
            trialBalances.forEach(tb => {
                const selected = currentValue.includes(tb.kode) ? 'selected' : '';
                options += `<option value="${tb.id}" ${selected}>${tb.kode} - ${tb.keterangan}</option>`;
            });
            cell.innerHTML = `<select class="form-select form-select-sm">${options}</select>`;
        } else if (field === 'is_active') {
            const isActive = currentValue === 'Aktif';
            cell.innerHTML = `
                <select class="form-select form-select-sm">
                    <option value="1" ${isActive ? 'selected' : ''}>Aktif</option>
                    <option value="0" ${!isActive ? 'selected' : ''}>Tidak Aktif</option>
                </select>
            `;
        } else {
            cell.innerHTML = `<input type="text" class="form-control form-control-sm" value="${currentValue}">`;
        }
    });
    
    // Toggle buttons
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
        } else if (field === 'tipe_ledger') {
            // For locked type, get from original data
            const badgeText = cell.querySelector('.badge')?.textContent?.toLowerCase();
            formData[field] = badgeText === 'kas' ? 'kas' : 'bank';
        }
    });
    
    fetch(`/ledgers/${id}`, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
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
    const row = document.getElementById(`row-${id}`);
    const editables = row.querySelectorAll('.editable');
    
    editables.forEach(cell => {
        const field = cell.dataset.field;
        const originalValue = originalData[id][field];
        
        if (field === 'tipe_ledger') {
            const badgeClass = originalValue.toLowerCase() === 'kas' ? 'primary' : 'success';
            cell.innerHTML = `<span class="badge bg-${badgeClass}">${originalValue}</span>`;
        } else if (field === 'is_active') {
            const badgeClass = originalValue === 'Aktif' ? 'success' : 'danger';
            cell.innerHTML = `<span class="badge bg-${badgeClass}">${originalValue}</span>`;
        } else {
            cell.textContent = originalValue;
        }
    });
    
    // Toggle buttons
    row.querySelector('.edit-btn').style.display = 'inline-block';
    row.querySelector('.save-btn').style.display = 'none';
    row.querySelector('.cancel-btn').style.display = 'none';
}

function updateRowDisplay(id, data) {
    const row = document.getElementById(`row-${id}`);
    const editables = row.querySelectorAll('.editable');
    
    editables.forEach(cell => {
        const field = cell.dataset.field;
        const value = data[field];
        
        if (field === 'tipe_ledger') {
            const badgeClass = value === 'kas' ? 'primary' : 'success';
            cell.innerHTML = `<span class="badge bg-${badgeClass}">${value.charAt(0).toUpperCase() + value.slice(1)}</span>`;
        } else if (field === 'is_active') {
            const isActive = value == '1';
            const badgeClass = isActive ? 'success' : 'danger';
            const text = isActive ? 'Aktif' : 'Tidak Aktif';
            cell.innerHTML = `<span class="badge bg-${badgeClass}">${text}</span>`;
        } else if (field === 'trial_balance_id') {
            if (value) {
                const trialBalances = @json($trialBalances);
                const selectedTB = trialBalances.find(tb => tb.id == value);
                cell.textContent = selectedTB ? `${selectedTB.kode} - ${selectedTB.keterangan}` : '-';
            } else {
                cell.textContent = '-';
            }
        } else {
            cell.textContent = value;
        }
    });
    
    // Toggle buttons
    row.querySelector('.edit-btn').style.display = 'inline-block';
    row.querySelector('.save-btn').style.display = 'none';
    row.querySelector('.cancel-btn').style.display = 'none';
}

function deleteLedger(id) {
    // Show warning modal instead of browser confirm
    showAlert('warning', 'Apakah Anda yakin ingin menghapus ledger ini?');
    
    // Add event listener to OK button for confirmation
    const alertButton = document.getElementById('alertButton');
    alertButton.onclick = function() {
        // Close modal first
        bootstrap.Modal.getInstance(document.getElementById('alertModal')).hide();
        
        // Then proceed with deletion
        fetch(`/ledgers/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
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
        
        // Reset onclick to prevent multiple bindings
        alertButton.onclick = null;
    };
}

document.getElementById('ledgerForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const tipeLedgerElement = document.getElementById('tipeLedger');
    const formData = {
        nama_ledger: document.getElementById('namaLedger').value,
        kode_ledger: document.getElementById('kodeLedger').value,
        tipe_ledger: tipeLedgerElement.tagName === 'INPUT' ? tipeLedgerElement.value : tipeLedgerElement.value,
        trial_balance_id: document.getElementById('trialBalanceId').value || null,
        deskripsi: document.getElementById('deskripsi').value
    };
    
    fetch('/ledgers', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
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
            document.getElementById('ledgerForm').reset();
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
</script>
@endpush