@extends('layouts.app')

@section('title', 'Buat Aset Tetap dari Transaksi')

@php
use App\Helpers\AssetGroupHelper;
@endphp

@section('page-header')
<div class="page-pretitle">Aset Tetap</div>
<h2 class="page-title">Buat Aset Tetap dari Transaksi</h2>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <!-- Transaction Info Card -->
        <div class="card mb-3">
            <div class="card-header">
                <h4 class="card-title">Informasi Transaksi</h4>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <strong>Nomor Bukti:</strong><br>
                        {{ $journal->proof_number ?: '-' }}
                    </div>
                    <div class="col-md-3">
                        <strong>Tanggal:</strong><br>
                        {{ $journal->date->format('d/m/Y') }}
                    </div>
                    <div class="col-md-3">
                        <strong>Nilai:</strong><br>
                        Rp {{ number_format($prefillData['acquisition_price'], 0, ',', '.') }}
                    </div>
                    <div class="col-md-3">
                        <strong>Deskripsi:</strong><br>
                        {{ $journal->description }}
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <form action="{{ route('fixed-assets.store-from-transaction') }}" method="POST" id="assetForm">
                @csrf
                <input type="hidden" name="journal_id" value="{{ $journal->id }}">
                
                <div class="card-body">
                    <!-- Identitas Aset -->
                    <div class="mb-4">
                        <h4 class="card-title mb-3">Identitas Aset</h4>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Nomor Aset <span class="text-danger">*</span></label>
                                    <input type="text" name="code" class="form-control @error('code') is-invalid @enderror" 
                                           value="{{ old('code') }}" required>
                                    @error('code')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Nama Aset <span class="text-danger">*</span></label>
                                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" 
                                           value="{{ old('name', $prefillData['description']) }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Jumlah Unit <span class="text-danger">*</span></label>
                                    <input type="number" name="quantity" class="form-control @error('quantity') is-invalid @enderror" 
                                           value="{{ old('quantity', 1) }}" min="1" required>
                                    @error('quantity')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Location</label>
                                    <input type="text" name="location" class="form-control @error('location') is-invalid @enderror" 
                                           value="{{ old('location') }}">
                                    @error('location')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Kelompok <span class="text-danger">*</span></label>
                                    <select name="group" id="assetGroup" class="form-select @error('group') is-invalid @enderror" required>
                                        <option value="">Pilih Kelompok</option>
                                        <option value="Aset Dalam Penyelesaian" {{ old('group') == 'Aset Dalam Penyelesaian' ? 'selected' : '' }}>Aset Dalam Penyelesaian</option>
                                        <option value="Tanah" {{ old('group') == 'Tanah' ? 'selected' : '' }}>Tanah</option>
                                        @foreach(AssetGroupHelper::getAllGroups() as $value => $label)
                                            <option value="{{ $value }}" {{ old('group') == $value ? 'selected' : '' }}>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                    @error('group')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-6">
                                        <div class="mb-3">
                                            <label class="form-label">Kondisi <span class="text-danger">*</span></label>
                                            <select name="condition" class="form-select @error('condition') is-invalid @enderror" required>
                                                <option value="">Pilih Kondisi</option>
                                                <option value="Baik" {{ old('condition', 'Baik') == 'Baik' ? 'selected' : '' }}>Baik</option>
                                                <option value="Rusak" {{ old('condition') == 'Rusak' ? 'selected' : '' }}>Rusak</option>
                                            </select>
                                            @error('condition')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="mb-3">
                                            <label class="form-label">Status <span class="text-danger">*</span></label>
                                            <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                                                <option value="">Pilih Status</option>
                                                <option value="active" {{ old('status', 'active') == 'active' ? 'selected' : '' }}>Active</option>
                                                <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                            </select>
                                            @error('status')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
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
                                    <label class="form-label">Tanggal Perolehan <span class="text-danger">*</span></label>
                                    <input type="date" name="acquisition_date" class="form-control @error('acquisition_date') is-invalid @enderror" 
                                           value="{{ old('acquisition_date', $prefillData['acquisition_date']) }}" required>
                                    @error('acquisition_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Harga Perolehan <span class="text-danger">*</span></label>
                                    <input type="number" name="acquisition_price" class="form-control @error('acquisition_price') is-invalid @enderror" 
                                           value="{{ old('acquisition_price', $prefillData['acquisition_price']) }}" step="0.01" min="0.01" required>
                                    @error('acquisition_price')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Nilai Residu</label>
                                    <input type="number" name="residual_value" class="form-control" 
                                           value="1" step="0.01" readonly>
                                    <small class="text-muted">Otomatis diisi 1</small>
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
                                    <label class="form-label">Metode Penyusutan <span class="text-danger">*</span></label>
                                    <select name="depreciation_method" class="form-select @error('depreciation_method') is-invalid @enderror" required>
                                        <option value="">Pilih Metode</option>
                                        <option value="garis lurus" {{ old('depreciation_method', 'garis lurus') == 'garis lurus' ? 'selected' : '' }}>Garis Lurus</option>
                                        <option value="saldo menurun" {{ old('depreciation_method') == 'saldo menurun' ? 'selected' : '' }}>Saldo Menurun</option>
                                    </select>
                                    @error('depreciation_method')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Tanggal Mulai Penyusutan <span class="text-danger">*</span></label>
                                    <input type="date" name="depreciation_start_date" class="form-control @error('depreciation_start_date') is-invalid @enderror" 
                                           value="{{ old('depreciation_start_date', $prefillData['acquisition_date']) }}" required>
                                    @error('depreciation_start_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-6">
                                        <div class="mb-3">
                                            <label class="form-label">Umur Manfaat (Tahun)</label>
                                            <input type="number" id="usefulLifeYears" name="useful_life_years" class="form-control" readonly>
                                            <small class="text-muted">Otomatis berdasarkan kelompok</small>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="mb-3">
                                            <label class="form-label">Umur Manfaat (Bulan)</label>
                                            <input type="number" id="usefulLifeMonths" name="useful_life_months" class="form-control" readonly>
                                            <small class="text-muted">Otomatis berdasarkan kelompok</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Prosentase Penyusutan</label>
                                    <input type="text" id="depreciationRate" name="depreciation_rate" class="form-control" readonly>
                                    <small class="text-muted">Otomatis berdasarkan kelompok</small>
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
                                    <label class="form-label">Harga Perolehan <span class="text-danger">*</span></label>
                                    <select name="asset_account_id" id="assetAccount" class="form-select @error('asset_account_id') is-invalid @enderror" required>
                                        <option value="">Pilih Akun Harga Perolehan</option>
                                        @foreach($assetAccounts as $account)
                                            <option value="{{ $account->id }}" data-code="{{ $account->kode }}" 
                                                {{ old('asset_account_id', $prefillData['asset_account_id']) == $account->id ? 'selected' : '' }}>
                                                {{ $account->kode }} - {{ $account->keterangan }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('asset_account_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Akumulasi Penyusutan</label>
                                    <input type="text" id="accumulatedAccount" class="form-control" readonly>
                                    <input type="hidden" name="accumulated_account_id" id="accumulatedAccountId">
                                    <small class="text-muted">Otomatis berdasarkan harga perolehan</small>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Beban Penyusutan</label>
                                    <input type="text" id="expenseAccount" class="form-control" readonly>
                                    <input type="hidden" name="expense_account_id" id="expenseAccountId">
                                    <small class="text-muted">Otomatis berdasarkan harga perolehan</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="card-footer">
                    <div class="d-flex justify-content-between">
                        <a href="{{ url()->previous() }}" class="btn btn-secondary">
                            <i class="ti ti-arrow-left"></i> Kembali
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="ti ti-device-floppy"></i> Buat Aset Tetap
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const assetGroupSelect = document.getElementById('assetGroup');
    const assetAccountSelect = document.getElementById('assetAccount');
    const usefulLifeYears = document.getElementById('usefulLifeYears');
    const usefulLifeMonths = document.getElementById('usefulLifeMonths');
    const depreciationRate = document.getElementById('depreciationRate');
    const accumulatedAccount = document.getElementById('accumulatedAccount');
    const accumulatedAccountId = document.getElementById('accumulatedAccountId');
    const expenseAccount = document.getElementById('expenseAccount');
    const expenseAccountId = document.getElementById('expenseAccountId');

    // Group asset data
    const groupData = {
        'Aset Dalam Penyelesaian': { years: 0, months: 0, rate: 0 },
        'Tanah': { years: 0, months: 0, rate: 0 },
        'Permanent': { years: 20, months: 240, rate: 5 },
        'Non-permanent': { years: 10, months: 120, rate: 10 },
        'Group 1': { years: 4, months: 48, rate: 25 },
        'Group 2': { years: 8, months: 96, rate: 12.5 }
    };

    // Account mapping
    const accountMapping = {
        'A23-01': { accumulated: null, expense: null },
        'A23-02': { accumulated: 'A24-01', expense: 'E22-96' },
        'A23-03': { accumulated: 'A24-02', expense: 'E22-97' },
        'A23-04': { accumulated: 'A24-03', expense: 'E22-98' },
        'A23-05': { accumulated: 'A24-04', expense: 'E22-99' },
        'A23-06': { accumulated: null, expense: null }
    };

    // All accounts data from backend
    const allAccounts = {!! json_encode($allAccounts ?? []) !!};

    // Update depreciation fields based on group
    function updateGroupData() {
        const group = assetGroupSelect.value;
        
        if (group && groupData[group]) {
            const data = groupData[group];
            if (usefulLifeYears) usefulLifeYears.value = data.years;
            if (usefulLifeMonths) usefulLifeMonths.value = data.months;
            if (depreciationRate) depreciationRate.value = data.rate;
        } else {
            if (usefulLifeYears) usefulLifeYears.value = '';
            if (usefulLifeMonths) usefulLifeMonths.value = '';
            if (depreciationRate) depreciationRate.value = '';
        }
    }

    // Update related accounts based on asset account
    function updateAccountData() {
        const selectedOption = assetAccountSelect.options[assetAccountSelect.selectedIndex];
        const accountCode = selectedOption ? selectedOption.getAttribute('data-code') : null;
        
        if (accountCode && accountMapping[accountCode]) {
            const mapping = accountMapping[accountCode];
            
            if (mapping.accumulated) {
                const accAccount = allAccounts.find(acc => acc.kode === mapping.accumulated);
                if (accAccount && accumulatedAccount) {
                    accumulatedAccount.value = accAccount.kode + ' - ' + accAccount.keterangan;
                    if (accumulatedAccountId) accumulatedAccountId.value = accAccount.id;
                } else {
                    if (accumulatedAccount) accumulatedAccount.value = 'Akun tidak ditemukan';
                    if (accumulatedAccountId) accumulatedAccountId.value = '';
                }
            } else {
                if (accumulatedAccount) accumulatedAccount.value = 'Tidak Ada';
                if (accumulatedAccountId) accumulatedAccountId.value = '';
            }
            
            if (mapping.expense) {
                const expAccount = allAccounts.find(acc => acc.kode === mapping.expense);
                if (expAccount && expenseAccount) {
                    expenseAccount.value = expAccount.kode + ' - ' + expAccount.keterangan;
                    if (expenseAccountId) expenseAccountId.value = expAccount.id;
                } else {
                    if (expenseAccount) expenseAccount.value = 'Akun tidak ditemukan';
                    if (expenseAccountId) expenseAccountId.value = '';
                }
            } else {
                if (expenseAccount) expenseAccount.value = 'Tidak Ada';
                if (expenseAccountId) expenseAccountId.value = '';
            }
        } else {
            if (accumulatedAccount) accumulatedAccount.value = '';
            if (accumulatedAccountId) accumulatedAccountId.value = '';
            if (expenseAccount) expenseAccount.value = '';
            if (expenseAccountId) expenseAccountId.value = '';
        }
    }

    // Add event listeners
    if (assetGroupSelect) {
        assetGroupSelect.addEventListener('change', updateGroupData);
        assetGroupSelect.addEventListener('input', updateGroupData);
        assetGroupSelect.onchange = updateGroupData;
    }

    if (assetAccountSelect) {
        assetAccountSelect.addEventListener('change', updateAccountData);
        assetAccountSelect.addEventListener('input', updateAccountData);
        assetAccountSelect.onchange = updateAccountData;
    }

    // Initialize autofill on page load
    setTimeout(function() {
        if (assetGroupSelect && assetGroupSelect.value) {
            assetGroupSelect.dispatchEvent(new Event('change'));
        }
        if (assetAccountSelect && assetAccountSelect.value) {
            assetAccountSelect.dispatchEvent(new Event('change'));
        }
    }, 100);
});
</script>
@endsection