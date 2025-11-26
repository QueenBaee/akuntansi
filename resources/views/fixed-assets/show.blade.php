@extends('layouts.app')

@section('title', 'Detail Aset Tetap')

@section('page-header')
<div class="page-pretitle">Aset Tetap</div>
<h2 class="page-title">{{ $fixedAsset->name }}</h2>
@endsection

@section('page-actions')
<a href="{{ route('fixed-assets.edit', $fixedAsset) }}" class="btn btn-primary">
    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
        <path stroke="none" d="m0 0h24v24H0z" fill="none"/>
        <path d="m7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1"/>
        <path d="m20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z"/>
        <path d="m16 5l3 3"/>
    </svg>
    Edit Aset
</a>
@endsection

@section('content')
<div class="row">
    <!-- Asset Information -->
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Informasi Aset</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-12">
                        <div class="mb-3">
                            <label class="form-label">Kode Aset</label>
                            <div class="font-weight-medium">{{ $fixedAsset->code }}</div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="mb-3">
                            <label class="form-label">Nama Aset</label>
                            <div class="font-weight-medium">{{ $fixedAsset->name }}</div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="mb-3">
                            <label class="form-label">Tanggal Perolehan</label>
                            <div>{{ $fixedAsset->acquisition_date->format('d M Y') }}</div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="mb-3">
                            <label class="form-label">Umur Manfaat</label>
                            <div>{{ $fixedAsset->useful_life_months }} bulan</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Asset Value Summary -->
        <div class="card mt-3">
            <div class="card-header">
                <h3 class="card-title">Ringkasan Nilai</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-12">
                        <div class="mb-3">
                            <label class="form-label">Harga Perolehan</label>
                            <div class="font-weight-medium text-primary">{{ number_format($fixedAsset->acquisition_price, 0, ',', '.') }}</div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="mb-3">
                            <label class="form-label">Nilai Residual</label>
                            <div>{{ number_format($fixedAsset->residual_value, 0, ',', '.') }}</div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="mb-3">
                            <label class="form-label">Akumulasi Penyusutan</label>
                            <div class="text-danger">{{ number_format($fixedAsset->accumulated_depreciation, 0, ',', '.') }}</div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="mb-3">
                            <label class="form-label">Nilai Buku</label>
                            <div class="font-weight-medium text-success">{{ number_format($fixedAsset->current_book_value, 0, ',', '.') }}</div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="mb-3">
                            <label class="form-label">Penyusutan Bulanan</label>
                            <div>{{ number_format($fixedAsset->monthly_depreciation, 0, ',', '.') }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Depreciation Schedule -->
    <div class="col-md-8">
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

<!-- Account Mapping -->
<div class="row mt-3">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Mapping Akun</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">Asset Account</label>
                            <div>{{ $fixedAsset->assetAccount->kode }} - {{ $fixedAsset->assetAccount->keterangan }}</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">Accumulated Depreciation Account</label>
                            <div>{{ $fixedAsset->accumulatedAccount->kode }} - {{ $fixedAsset->accumulatedAccount->keterangan }}</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">Depreciation Expense Account</label>
                            <div>{{ $fixedAsset->expenseAccount->kode }} - {{ $fixedAsset->expenseAccount->keterangan }}</div>
                        </div>
                    </div>
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
</script>
@endpush