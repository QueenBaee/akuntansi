@extends('layouts.app')

@section('title', 'Kelola Aset Tetap')

@section('page-header')
    <div class="page-pretitle">Master Data</div>
    <h2 class="page-title">Kelola Aset Tetap</h2>
@endsection

@section('page-actions')
    <div class="btn-list">
        <button type="button" class="btn btn-success" id="convertSelectedBtn" style="display: none;" onclick="showMergeConvertModal()">
            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="m0 0h24v24H0z" fill="none"/><path d="M16 4l4 4l-4 4" /><path d="M20 8H4" /></svg>
            Convert Selected (<span id="selectedCount">0</span>)
        </button>
        <a href="/fixed-assets/create" class="btn btn-primary">
            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="m0 0h24v24H0z" fill="none"/><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Tambah Aset Tetap
        </a>
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
                        <th class="w-1">
                            <input type="checkbox" id="selectAll" onchange="toggleSelectAll()">
                        </th>
                        <th>Nomor Aset</th>
                        <th>Nama Aset</th>
                        <th>Qty</th>
                        <th>Kelompok</th>
                        <th>Kondisi</th>
                        <th>Tgl Perolehan</th>
                        <th>Harga Perolehan</th>
                        <th>Akum. Penyusutan</th>
                        <th>Nilai Buku</th>
                        <th class="w-1">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($assets ?? [] as $asset)
                        <tr>
                            <td>
                                @if($asset->group === 'Aset Dalam Penyelesaian')
                                    <input type="checkbox" class="asset-checkbox" value="{{ $asset->id }}" 
                                           data-name="{{ $asset->name }}" 
                                           data-price="{{ $asset->acquisition_price }}" 
                                           onchange="updateSelectedAssets()">
                                @endif
                            </td>
                            <td>{{ $asset->code ?? '-' }}</td>
                            <td>{{ $asset->name }}</td>
                            <td>{{ $asset->quantity ?? 1 }}</td>
                            <td>{{ $asset->group ?? '-' }}</td>
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
function toggleSelectAll() {
    const selectAll = document.getElementById('selectAll');
    const checkboxes = document.querySelectorAll('.asset-checkbox');
    
    checkboxes.forEach(checkbox => {
        checkbox.checked = selectAll.checked;
    });
    
    updateSelectedAssets();
}

function updateSelectedAssets() {
    const checkboxes = document.querySelectorAll('.asset-checkbox:checked');
    const count = checkboxes.length;
    
    document.getElementById('selectedCount').textContent = count;
    document.getElementById('convertSelectedBtn').style.display = count > 0 ? 'inline-block' : 'none';
    
    // Update select all checkbox
    const allCheckboxes = document.querySelectorAll('.asset-checkbox');
    const selectAll = document.getElementById('selectAll');
    selectAll.checked = allCheckboxes.length > 0 && checkboxes.length === allCheckboxes.length;
}

function showMergeConvertModal() {
    const checkboxes = document.querySelectorAll('.asset-checkbox:checked');
    
    if (checkboxes.length === 0) {
        showAlert('error', 'Error', 'Please select at least one asset');
        return;
    }
    
    const assetIds = [];
    checkboxes.forEach(checkbox => {
        assetIds.push(checkbox.value);
    });
    
    // Redirect to merge-convert page with selected asset IDs
    window.location.href = `/fixed-assets/merge-convert?assets=${assetIds.join(',')}`;
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
        alertIcon.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-check text-success" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="m0 0h24v24H0z" fill="none"/><path d="m5 12l5 5l10 -10" /></svg>';
        alertButton.className = 'btn btn-success w-100';
    } else {
        alertIcon.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-alert-triangle text-danger" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="m0 0h24v24H0z" fill="none"/><path d="m12 9v2m0 4v.01" /><path d="m5 19h14a2 2 0 0 0 1.84 -2.75l-7.1 -12.25a2 2 0 0 0 -3.5 0l-7.1 12.25a2 2 0 0 0 1.75 2.75" /></svg>';
        alertButton.className = 'btn btn-danger w-100';
    }
    
    const modal = new bootstrap.Modal(alertModal);
    modal.show();
}
</script>
@endpush