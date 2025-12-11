@extends('layouts.app')

@section('title', 'Detail Aset Tetap')

@section('page-header')
<div class="page-pretitle">Aset Tetap</div>
<h2 class="page-title">{{ $fixedAsset->name }}</h2>
@endsection

@section('page-actions')
<div class="btn-list">
    <button class="btn btn-primary edit-btn" onclick="editAsset()">
        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
            <path stroke="none" d="m0 0h24v24H0z" fill="none"/>
            <path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1"/>
            <path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z"/>
            <path d="M16 5l3 3"/>
        </svg>
        Edit
    </button>
    <button class="btn btn-success save-btn" onclick="saveAsset()" style="display: none;">
        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
            <path stroke="none" d="m0 0h24v24H0z" fill="none"/>
            <path d="M5 12l5 5l10 -10"/>
        </svg>
        Simpan
    </button>
    <button class="btn btn-secondary cancel-btn" onclick="cancelEdit()" style="display: none;">
        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
            <path stroke="none" d="m0 0h24v24H0z" fill="none"/>
            <path d="M18 6l-12 12"/>
            <path d="M6 6l12 12"/>
        </svg>
        Batal
    </button>
    <a href="{{ route('fixed-assets.index') }}" class="btn btn-secondary">
        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
            <path stroke="none" d="m0 0h24v24H0z" fill="none"/>
            <path d="M9 11l-4 4l4 4m-4 -4h11a4 4 0 0 0 0 -8h-1"/>
        </svg>
        Kembali ke Daftar
    </a>
</div>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <!-- Identitas Aset -->
                <div class="mb-4">
                    <h4 class="card-title mb-3">Identitas Aset</h4>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Nomor Aset</label>
                                <div class="font-weight-medium editable" data-field="code">{{ $fixedAsset->code }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Nama Aset</label>
                                <div class="font-weight-medium editable" data-field="name">{{ $fixedAsset->name }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Jumlah Unit</label>
                                <div class="editable" data-field="quantity">{{ $fixedAsset->quantity ?? 1 }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Location</label>
                                <div class="editable" data-field="location">{{ $fixedAsset->location ?? '-' }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Kelompok</label>
                                <div class="editable" data-field="group">{{ $fixedAsset->group ?? '-' }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-6">
                                    <div class="mb-3">
                                        <label class="form-label">Kondisi</label>
                                        <div class="editable" data-field="condition">{{ $fixedAsset->condition ?? '-' }}</div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="mb-3">
                                        <label class="form-label">Status</label>
                                        <div>
                                            @if($fixedAsset->is_active)
                                                <span class="badge bg-success">Active</span>
                                            @else
                                                <span class="badge bg-secondary">Inactive</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Detail Perolehan -->
                <div class="mb-4">
                    <h4 class="card-title mb-3">Detail Perolehan</h4>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Tanggal Perolehan</label>
                                <div class="editable" data-field="acquisition_date">{{ $fixedAsset->acquisition_date->format('d M Y') }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Harga Perolehan</label>
                                <div class="font-weight-medium text-primary editable" data-field="acquisition_price">{{ number_format($fixedAsset->acquisition_price, 0, ',', '.') }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Nilai Residu</label>
                                <div class="editable" data-field="residual_value">{{ number_format($fixedAsset->residual_value, 0, ',', '.') }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Penyusutan -->
                <div class="mb-4">
                    <h4 class="card-title mb-3">Penyusutan</h4>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Metode Penyusutan</label>
                                <div>{{ $fixedAsset->depreciation_method ?? 'garis lurus' }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Tanggal Mulai Penyusutan</label>
                                <div>{{ $fixedAsset->depreciation_start_date ? $fixedAsset->depreciation_start_date->format('d M Y') : '-' }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-6">
                                    <div class="mb-3">
                                        <label class="form-label">Umur Manfaat (Tahun)</label>
                                        <div>{{ $fixedAsset->useful_life_years ?? round($fixedAsset->useful_life_months / 12, 1) }}</div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="mb-3">
                                        <label class="form-label">Umur Manfaat (Bulan)</label>
                                        <div class="editable" data-field="useful_life_months">{{ $fixedAsset->useful_life_months }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Prosentase Penyusutan</label>
                                <div>{{ $fixedAsset->depreciation_rate ?? round(100 / ($fixedAsset->useful_life_months / 12), 2) }}%</div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Akumulasi Penyusutan</label>
                                <div class="text-danger">{{ number_format($fixedAsset->accumulated_depreciation, 0, ',', '.') }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Nilai Buku</label>
                                <div class="font-weight-medium text-success">{{ number_format($fixedAsset->current_book_value, 0, ',', '.') }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Penyusutan Bulanan</label>
                                <div>{{ number_format($fixedAsset->monthly_depreciation, 0, ',', '.') }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Akun Terkait -->
                <div class="mb-4">
                    <h4 class="card-title mb-3">Akun Terkait</h4>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Harga Perolehan</label>
                                <div>{{ $fixedAsset->assetAccount->kode }} - {{ $fixedAsset->assetAccount->keterangan }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Akumulasi Penyusutan</label>
                                <div>{{ $fixedAsset->accumulatedAccount->kode }} - {{ $fixedAsset->accumulatedAccount->keterangan }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Beban Penyusutan</label>
                                <div>{{ $fixedAsset->expenseAccount->kode }} - {{ $fixedAsset->expenseAccount->keterangan }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Depreciation Schedule -->
<div class="row mt-3">
    <div class="col-12">

        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Simulasi Penyusutan Bulanan</h3>
            </div>
            <div class="table-responsive">
                <table class="table table-vcenter card-table">
                    <thead>
                        <tr>
                            <th>Periode</th>
                            <th class="text-end">Beban Penyusutan</th>
                            <th class="text-end">Akumulasi Penyusutan</th>
                            <th class="text-end">Nilai Buku</th>
                            <th>Status</th>
                            <th class="w-1">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($depreciationSchedule as $row)
                            <tr>
                                <td>{{ $row['period_formatted'] }}</td>
                                <td class="text-end">{{ number_format($row['depreciation_amount'], 0, ',', '.') }}</td>
                                <td class="text-end">{{ number_format($row['accumulated_depreciation'], 0, ',', '.') }}</td>
                                <td class="text-end">{{ number_format($row['book_value'], 0, ',', '.') }}</td>
                                <td>
                                    @if($row['is_posted'])
                                        <span class="badge bg-success">Posted</span>
                                    @else
                                        <span class="badge bg-secondary">Simulation</span>
                                    @endif
                                </td>
                                <td>
                                    @if($row['is_posted'])
                                        @if($row['posted_data'] && $row['posted_data']->journal)
                                            <a href="/memorials" class="btn btn-sm btn-outline-primary" title="Lihat di Memorial">
                                                {{ $row['posted_data']->journal->number }}
                                            </a>
                                        @else
                                            <span class="badge bg-success">Posted</span>
                                        @endif
                                    @else
                                        <button type="button" class="btn btn-sm btn-primary" 
                                                onclick="showPostModal('{{ $fixedAsset->id }}', '{{ $row['period'] }}', '{{ $row['period_formatted'] }}')">
                                            Insert to Memorial
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted">Tidak ada data penyusutan</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
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

<!-- Post Confirmation Modal -->
<div class="modal modal-blur fade" id="postModal" tabindex="-1">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center py-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-lg text-blue mb-2" width="48" height="48" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="m0 0h24v24H0z" fill="none"/>
                    <circle cx="12" cy="12" r="9"/>
                    <path d="m9 12l2 2l4 -4"/>
                </svg>
                <h3>Konfirmasi Posting</h3>
                <div class="text-muted" id="postMessage">Posting penyusutan untuk periode ini?</div>
            </div>
            <div class="modal-footer">
                <div class="w-100">
                    <div class="row">
                        <div class="col">
                            <button type="button" class="btn w-100" data-bs-dismiss="modal">Batal</button>
                        </div>
                        <div class="col">
                            <button type="button" class="btn btn-primary w-100" id="confirmPostBtn">Ya, Posting</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let originalData = {};

function editAsset() {
    const editables = document.querySelectorAll('.editable');
    
    editables.forEach(element => {
        const field = element.dataset.field;
        const currentValue = element.textContent.trim();
        originalData[field] = currentValue;
        
        if (field === 'acquisition_date') {
            const dateValue = new Date(currentValue.split(' ').reverse().join('-')).toISOString().split('T')[0];
            element.innerHTML = `<input type="date" class="form-control" value="${dateValue}">`;
        } else if (field === 'acquisition_price' || field === 'residual_value') {
            const numericValue = currentValue.replace(/\./g, '');
            element.innerHTML = `<input type="number" class="form-control" value="${numericValue}" step="0.01">`;
        } else if (field === 'useful_life_months') {
            const numericValue = currentValue.replace(' bulan', '');
            element.innerHTML = `<input type="number" class="form-control" value="${numericValue}" min="1">`;
        } else if (field === 'residual_value') {
            const percentValue = parseFloat(currentValue.replace('%', ''));
            element.innerHTML = `<input type="number" class="form-control" value="${percentValue}" min="0" max="100" step="0.01">`;
        } else {
            element.innerHTML = `<input type="text" class="form-control" value="${currentValue}">`;
        }
    });
    
    document.querySelector('.edit-btn').style.display = 'none';
    document.querySelector('.save-btn').style.display = 'inline-block';
    document.querySelector('.cancel-btn').style.display = 'inline-block';
}

function saveAsset() {
    const editables = document.querySelectorAll('.editable');
    const formData = {};
    
    editables.forEach(element => {
        const field = element.dataset.field;
        const input = element.querySelector('input');
        if (input) {
            if (field === 'residual_value') {
                formData[field] = parseFloat(input.value) / 100 || 0;
            } else {
                formData[field] = input.value;
            }
        }
    });
    
    fetch(`/fixed-assets/{{ $fixedAsset->id }}`, {
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
            setTimeout(() => location.reload(), 1500);
        } else {
            showAlert('error', data.message || 'Gagal mengupdate data');
            cancelEdit();
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('error', error.message || 'Terjadi kesalahan saat mengupdate data');
        cancelEdit();
    });
}

function cancelEdit() {
    const editables = document.querySelectorAll('.editable');
    
    editables.forEach(element => {
        const field = element.dataset.field;
        element.textContent = originalData[field];
    });
    
    document.querySelector('.edit-btn').style.display = 'inline-block';
    document.querySelector('.save-btn').style.display = 'none';
    document.querySelector('.cancel-btn').style.display = 'none';
}

function showPostModal(assetId, period, periodFormatted) {
    document.getElementById('postMessage').textContent = `Posting penyusutan untuk ${periodFormatted}?`;
    
    const confirmBtn = document.getElementById('confirmPostBtn');
    confirmBtn.onclick = function() {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/fixed-assets/${assetId}/depreciation/${period}/post`;
        
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = document.querySelector('meta[name="csrf-token"]').content;
        
        form.appendChild(csrfToken);
        document.body.appendChild(form);
        form.submit();
    };
    
    new bootstrap.Modal(document.getElementById('postModal')).show();
}

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
    }
    
    messageEl.textContent = message;
    modal.show();
}
</script>
@endpush