@extends('layouts.app')

@section('title', 'Edit Aset Tetap')

@section('page-header')
    <div class="page-pretitle">Master Data</div>
    <h2 class="page-title">Edit Aset Tetap</h2>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Edit Aset Tetap</h3>
    </div>
    <div class="card-body">
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        
        <form method="POST" action="{{ route('fixed-assets.update', $fixedAsset) }}" id="editAssetForm">
            @csrf
            @method('PUT')
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Kode Aset</label>
                        <input type="text" class="form-control" name="code" value="{{ $fixedAsset->code }}" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Nama Aset</label>
                        <input type="text" class="form-control" name="name" value="{{ $fixedAsset->name }}" required>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-4">
                    <div class="mb-3">
                        <label class="form-label">Kelompok</label>
                        <select class="form-select no-select2" name="group" required>
                            <option value="">Pilih Kelompok</option>
                            <option value="Permanent" {{ ($fixedAsset->group ?? 'Permanent') == 'Permanent' ? 'selected' : '' }}>Aset Permanen</option>
                            <option value="Non-permanent" {{ ($fixedAsset->group ?? '') == 'Non-permanent' ? 'selected' : '' }}>Aset Tidak Permanen</option>
                            <option value="Group 1" {{ ($fixedAsset->group ?? '') == 'Group 1' ? 'selected' : '' }}>Kelompok 1</option>
                            <option value="Group 2" {{ ($fixedAsset->group ?? '') == 'Group 2' ? 'selected' : '' }}>Kelompok 2</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                        <label class="form-label">Kondisi</label>
                        <select class="form-select no-select2" name="condition" required>
                            <option value="">Pilih Kondisi</option>
                            <option value="Baik" {{ ($fixedAsset->condition ?? 'Baik') == 'Baik' ? 'selected' : '' }}>Baik</option>
                            <option value="Rusak" {{ ($fixedAsset->condition ?? '') == 'Rusak' ? 'selected' : '' }}>Rusak</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select class="form-select no-select2" name="status" required>
                            <option value="">Pilih Status</option>
                            <option value="active" {{ $fixedAsset->is_active ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ !$fixedAsset->is_active ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-4">
                    <div class="mb-3">
                        <label class="form-label">Tanggal Perolehan</label>
                        <input type="date" class="form-control" name="acquisition_date" value="{{ $fixedAsset->acquisition_date->format('Y-m-d') }}" required>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                        <label class="form-label">Harga Perolehan</label>
                        <input type="number" class="form-control" name="acquisition_price" value="{{ $fixedAsset->acquisition_price }}" step="0.01" required>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                        <label class="form-label">Nilai Residual</label>
                        <input type="number" class="form-control" name="residual_value" value="{{ $fixedAsset->residual_value }}" step="0.01" required>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-4">
                    <div class="mb-3">
                        <label class="form-label">Metode Penyusutan</label>
                        <select class="form-select no-select2" name="depreciation_method" required>
                            <option value="">Pilih Metode</option>
                            <option value="garis lurus" {{ ($fixedAsset->depreciation_method ?? 'garis lurus') == 'garis lurus' ? 'selected' : '' }}>Garis Lurus</option>
                            <option value="saldo menurun" {{ ($fixedAsset->depreciation_method ?? '') == 'saldo menurun' ? 'selected' : '' }}>Saldo Menurun</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                        <label class="form-label">Umur Manfaat (tahun)</label>
                        <input type="number" class="form-control" name="useful_life_years" value="{{ $fixedAsset->useful_life_years ?? 5 }}" min="1" max="50" required>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                        <label class="form-label">Umur Manfaat (bulan)</label>
                        <input type="number" class="form-control" name="useful_life_months" value="{{ $fixedAsset->useful_life_months }}" min="1" required>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Tanggal Mulai Penyusutan</label>
                        <input type="date" class="form-control" name="depreciation_start_date" value="{{ $fixedAsset->depreciation_start_date ? $fixedAsset->depreciation_start_date->format('Y-m-d') : '' }}" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Lokasi</label>
                        <input type="text" class="form-control" name="location" value="{{ $fixedAsset->location }}">
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-4">
                    <div class="mb-3">
                        <label class="form-label">Asset Account</label>
                        <select class="form-select no-select2" name="asset_account_id" required>
                            @foreach($assetAccounts as $account)
                                <option value="{{ $account->id }}" {{ $fixedAsset->asset_account_id == $account->id ? 'selected' : '' }}>
                                    {{ $account->kode }} - {{ $account->keterangan }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                        <label class="form-label">Accumulated Account</label>
                        <select class="form-select no-select2" name="accumulated_account_id" required>
                            @foreach($assetAccounts as $account)
                                <option value="{{ $account->id }}" {{ $fixedAsset->accumulated_account_id == $account->id ? 'selected' : '' }}>
                                    {{ $account->kode }} - {{ $account->keterangan }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                        <label class="form-label">Expense Account</label>
                        <select class="form-select no-select2" name="expense_account_id" required>
                            @foreach($assetAccounts as $account)
                                <option value="{{ $account->id }}" {{ $fixedAsset->expense_account_id == $account->id ? 'selected' : '' }}>
                                    {{ $account->kode }} - {{ $account->keterangan }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            
            <div class="form-footer">
                <button type="submit" class="btn btn-primary">Update</button>
                <a href="{{ route('fixed-assets.show', $fixedAsset) }}" class="btn btn-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    $('#editAssetForm').on('submit', function(e) {
        // Debug: log all form data
        const formData = new FormData(this);
        console.log('Form data being submitted:');
        for (let [key, value] of formData.entries()) {
            console.log(key + ': ' + value);
        }
        // Let form submit normally
    });
});
</script>
@endpush