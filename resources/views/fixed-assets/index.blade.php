@extends('layouts.app')

@section('title', 'Kelola Aset Tetap')

@php
use App\Helpers\AssetGroupHelper;
@endphp

@section('page-header')
    <div class="page-pretitle">Master Data</div>
    <h2 class="page-title">Kelola Aset Tetap</h2>
@endsection

@section('page-actions')
    <div class="btn-list">
        <button type="button" class="btn btn-primary" onclick="showBatchDepreciationModal()">
            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="m0 0h24v24H0z" fill="none"/><path d="M12 3v18m9-9H3"/></svg>
            Mass Depreciation
        </button>
        <button type="button" class="btn btn-success" id="convertSelectedBtn" style="display: none;" onclick="showMergeConvertModal()">
            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="m0 0h24v24H0z" fill="none"/><path d="M16 4l4 4l-4 4" /><path d="M20 8H4" /></svg>
            Convert Selected (<span id="selectedCount">0</span>)
        </button>
    </div>
@endsection

@push('styles')
<style>
.table-vcenter {
    table-layout: fixed;
    width: 100%;
}

.table-vcenter th:nth-child(1) { width: 100px; }
.table-vcenter th:nth-child(2) { width: auto; min-width: 180px; }
.table-vcenter th:nth-child(3) { width: 80px; }
.table-vcenter th:nth-child(4) { width: 100px; }
.table-vcenter th:nth-child(5) { width: 80px; }
.table-vcenter th:nth-child(6) { width: 100px; }
.table-vcenter th:nth-child(7) { width: 120px; }
.table-vcenter th:nth-child(8) { width: 120px; }
.table-vcenter th:nth-child(9) { width: 120px; }
.table-vcenter th:nth-child(10) { width: 80px; }

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
                        <th style="text-align:center">Nomor Aset</th>
                        <th style="text-align:center">Nama Aset</th>
                        <th style="text-align:center">Qty</th>
                        <th style="text-align:center">Kelompok</th>
                        <th style="text-align:center">Kondisi</th>
                        <th style="text-align:center">Tgl Perolehan</th>
                        <th style="text-align:center">Harga Perolehan</th>
                        <th style="text-align:center">Akum. Penyusutan</th>
                        <th style="text-align:center">Nilai Buku</th>
                        <th class="w-1" style="text-align:center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($assets ?? [] as $asset)
                        <tr>
                            <td>{{ $asset->code ?? '-' }}</td>
                            <td>{{ $asset->name }}</td>
                            <td>{{ $asset->quantity ?? 1 }}</td>
                            <td>{{ $asset->group ? AssetGroupHelper::translateGroup($asset->group) : '-' }}</td>
                            <td>
                                @if($asset->condition === 'Baik')
                                    <span class="badge bg-success">Baik</span>
                                @elseif($asset->condition === 'Rusak')
                                    <span class="badge bg-danger">Rusak</span>
                                @else
                                    <span class="badge bg-secondary">-</span>
                                @endif
                            </td>
                            <td>{{ $asset->acquisition_date ? \Carbon\Carbon::parse($asset->acquisition_date)->format('d/m/Y') : '-' }}</td>
                            <td class="text-end">{{ $asset->acquisition_price ? number_format($asset->acquisition_price, 0, ',', '.') : '-' }}</td>
                            <td class="text-end text-danger">{{ number_format($asset->accumulated_depreciation ?? 0, 0, ',', '.') }}</td>
                            <td class="text-end text-success">{{ number_format($asset->current_book_value ?? $asset->acquisition_price, 0, ',', '.') }}</td>
                            <td>
                                <div class="btn-list flex-nowrap">
                                    <a href="/fixed-assets/{{ $asset->id }}" class="btn btn-sm btn-white">Detail</a>
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
                            <td colspan="11" class="text-center text-muted">Tidak ada data aset tetap</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Batch Depreciation Modal -->
    <div class="modal modal-blur fade" id="batchDepreciationModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Mass Depreciation Processing</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div id="step1" class="depreciation-step">
                        <h6>Select Depreciation Period</h6>
                        <div class="mb-3">
                            <label class="form-label">Period Month</label>
                            <input type="month" class="form-control" id="depreciationPeriod" required>
                        </div>
                        <button type="button" class="btn btn-primary" onclick="previewDepreciation()">Preview Eligible Assets</button>
                    </div>
                    
                    <div id="step2" class="depreciation-step" style="display: none;">
                        <h6>Eligible Assets Preview</h6>
                        <div id="previewResults"></div>
                        <div class="mt-3">
                            <button type="button" class="btn btn-secondary" onclick="backToStep1()">Back</button>
                            <button type="button" class="btn btn-success" onclick="processDepreciation()" id="processBtn">Process Depreciation</button>
                        </div>
                    </div>
                    
                    <div id="step3" class="depreciation-step" style="display: none;">
                        <h6>Processing Results</h6>
                        <div id="processResults"></div>
                        <div class="mt-3">
                            <button type="button" class="btn btn-primary" onclick="closeModal()">Close</button>
                        </div>
                    </div>
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
@endsection

@push('scripts')
<script>
function showBatchDepreciationModal() {
    document.getElementById('step1').style.display = 'block';
    document.getElementById('step2').style.display = 'none';
    document.getElementById('step3').style.display = 'none';
    
    const now = new Date();
    const currentMonth = now.getFullYear() + '-' + String(now.getMonth() + 1).padStart(2, '0');
    document.getElementById('depreciationPeriod').value = currentMonth;
    
    new bootstrap.Modal(document.getElementById('batchDepreciationModal')).show();
}

function previewDepreciation() {
    const period = document.getElementById('depreciationPeriod').value;
    if (!period) {
        showAlert('error', 'Error', 'Please select a period');
        return;
    }
    
    fetch('/api/batch-depreciation/preview', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ period_month: period })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            displayPreview(data.data);
            document.getElementById('step1').style.display = 'none';
            document.getElementById('step2').style.display = 'block';
        } else {
            showAlert('error', 'Error', data.message || 'Failed to preview assets');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('error', 'Error', 'Network error occurred');
    });
}

function displayPreview(data) {
    const container = document.getElementById('previewResults');
    
    if (data.eligible_count === 0) {
        container.innerHTML = `
            <div class="alert alert-info">
                <h6>No Eligible Assets</h6>
                <p>No assets found for depreciation in ${data.period}</p>
            </div>
        `;
        document.getElementById('processBtn').disabled = true;
        return;
    }
    
    let html = `
        <div class="alert alert-success">
            <h6>Found ${data.eligible_count} eligible assets for ${data.period}</h6>
            <p><strong>Total Depreciation Amount:</strong> ${formatCurrency(data.total_depreciation)}</p>
        </div>
        <div class="table-responsive">
            <table class="table table-sm">
                <thead>
                    <tr>
                        <th>Code</th>
                        <th>Asset Name</th>
                        <th>Group</th>
                        <th>Monthly Depreciation</th>
                        <th>Book Value After</th>
                    </tr>
                </thead>
                <tbody>
    `;
    
    data.assets.forEach(asset => {
        html += `
            <tr>
                <td>${asset.code || '-'}</td>
                <td>${asset.name}</td>
                <td>${asset.group}</td>
                <td class="text-end">${formatCurrency(asset.monthly_depreciation)}</td>
                <td class="text-end">${formatCurrency(asset.book_value_after)}</td>
            </tr>
        `;
    });
    
    html += `
                </tbody>
            </table>
        </div>
    `;
    
    container.innerHTML = html;
    document.getElementById('processBtn').disabled = false;
}

function processDepreciation() {
    const period = document.getElementById('depreciationPeriod').value;
    document.getElementById('processBtn').disabled = true;
    document.getElementById('processBtn').textContent = 'Processing...';
    
    fetch('/api/batch-depreciation/process', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ period_month: period })
    })
    .then(response => response.json())
    .then(data => {
        displayResults(data);
        document.getElementById('step2').style.display = 'none';
        document.getElementById('step3').style.display = 'block';
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('error', 'Error', 'Processing failed');
        document.getElementById('processBtn').disabled = false;
        document.getElementById('processBtn').textContent = 'Process Depreciation';
    });
}

function displayResults(data) {
    const container = document.getElementById('processResults');
    
    let html = `
        <div class="alert ${data.success ? 'alert-success' : 'alert-danger'}">
            <h6>${data.message}</h6>
            <p><strong>Processed:</strong> ${data.processed_count} / ${data.total_eligible} assets</p>
        </div>
    `;
    
    if (data.results && data.results.length > 0) {
        html += `
            <div class="table-responsive">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>Asset Code</th>
                            <th>Asset Name</th>
                            <th>Status</th>
                            <th>Depreciation Amount</th>
                            <th>Journal Number</th>
                        </tr>
                    </thead>
                    <tbody>
        `;
        
        data.results.forEach(result => {
            const statusBadge = result.status === 'success' 
                ? '<span class="badge bg-success">Success</span>'
                : '<span class="badge bg-danger">Error</span>';
                
            html += `
                <tr>
                    <td>${result.asset_code || '-'}</td>
                    <td>${result.asset_name}</td>
                    <td>${statusBadge}</td>
                    <td class="text-end">${result.depreciation_amount ? formatCurrency(result.depreciation_amount) : '-'}</td>
                    <td>${result.journal_number || (result.error_message || '-')}</td>
                </tr>
            `;
        });
        
        html += `
                    </tbody>
                </table>
            </div>
        `;
    }
    
    container.innerHTML = html;
}

function backToStep1() {
    document.getElementById('step2').style.display = 'none';
    document.getElementById('step1').style.display = 'block';
}

function closeModal() {
    bootstrap.Modal.getInstance(document.getElementById('batchDepreciationModal')).hide();
    window.location.reload();
}

function formatCurrency(amount) {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0
    }).format(amount);
}

function showAlert(type, title, message) {
    const alertModal = document.getElementById('alertModal');
    const alertIcon = document.getElementById('alertIcon');
    const alertTitle = document.getElementById('alertTitle');
    const alertMessage = document.getElementById('alertMessage');
    const alertButton = document.getElementById('alertButton');
    
    alertTitle.textContent = title;
    alertMessage.textContent = message;
    
    if (type === 'success') {
        alertIcon.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-check text-success" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="m0 0h24v24H0z" fill="none"/><path d="M5 12l5 5l10 -10"/></svg>';
        alertButton.className = 'btn btn-success w-100';
    } else {
        alertIcon.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-alert-triangle text-danger" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="m0 0h24v24H0z" fill="none"/><path d="M12 9v2m0 4v.01M5 19h14a2 2 0 0 0 1.84 -2.75L13.74 4a2 2 0 0 0 -3.5 0L3.16 16.25A2 2 0 0 0 5 19"/></svg>';
        alertButton.className = 'btn btn-danger w-100';
    }
    
    new bootstrap.Modal(alertModal).show();
}
</script>
@endpush