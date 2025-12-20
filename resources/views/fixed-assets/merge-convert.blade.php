@extends('layouts.app')

@section('title', 'Merge & Convert Assets')

@php
use App\Helpers\AssetGroupHelper;
@endphp

@section('page-header')
<div class="page-pretitle">Aset Tetap</div>
<h2 class="page-title">Merge & Convert Assets to Regular</h2>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <form action="{{ route('fixed-assets.merge-convert') }}" method="POST" id="mergeConvertForm">
                @csrf
                <input type="hidden" name="asset_ids" value="{{ implode(',', $selectedAssets->pluck('id')->toArray()) }}">
                
                <div class="card-body">
                    <!-- Selected Assets -->
                    <div class="mb-4">
                        <h4 class="card-title mb-3">Selected Assets to Merge</h4>
                        <div class="row">
                            <div class="col-md-8">
                                <div class="mb-2">
                                    @foreach($selectedAssets as $asset)
                                        <span class="badge bg-primary me-2 mb-2 p-2">
                                            {{ $asset->name }} - {{ $asset->acquisition_date->format('d/m/Y') }} - Rp {{ number_format($asset->acquisition_price, 0, ',', '.') }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card bg-light">
                                    <div class="card-body p-3">
                                        <h5 class="card-title mb-1">Total Value</h5>
                                        <h3 class="text-success mb-0">Rp {{ number_format($totalPrice, 0, ',', '.') }}</h3>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- New Asset Identity -->
                    <div class="mb-4">
                        <h4 class="card-title mb-3">New Asset Identity</h4>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Asset Code <span class="text-danger">*</span></label>
                                    <input type="text" name="code" class="form-control @error('code') is-invalid @enderror" 
                                           value="{{ old('code', $suggestedCode) }}" required>
                                    @error('code')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Asset Name <span class="text-danger">*</span></label>
                                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" 
                                           value="{{ old('name', $suggestedName) }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Quantity <span class="text-danger">*</span></label>
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
                                    <label class="form-label">Group <span class="text-danger">*</span></label>
                                    <select name="group" id="assetGroup" class="form-select @error('group') is-invalid @enderror" required>
                                        <option value="">Pilih Kelompok</option>
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
                                            <label class="form-label">Condition <span class="text-danger">*</span></label>
                                            <select name="condition" class="form-select @error('condition') is-invalid @enderror" required>
                                                <option value="">Select Condition</option>
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
                                                <option value="">Select Status</option>
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

                    <!-- Acquisition Details -->
                    <div class="mb-4">
                        <h4 class="card-title mb-3">Acquisition Details</h4>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Acquisition Date <span class="text-danger">*</span></label>
                                    <input type="date" name="acquisition_date" class="form-control @error('acquisition_date') is-invalid @enderror" 
                                           value="{{ old('acquisition_date', $earliestDate) }}" required>
                                    @error('acquisition_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Total Acquisition Price <span class="text-danger">*</span></label>
                                    <input type="number" name="acquisition_price" class="form-control @error('acquisition_price') is-invalid @enderror" 
                                           value="{{ old('acquisition_price', $totalPrice) }}" step="0.01" min="0.01" required>
                                    @error('acquisition_price')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Residual Value</label>
                                    <input type="number" name="residual_value" class="form-control" 
                                           value="1" step="0.01" readonly>
                                    <small class="text-muted">Auto-filled with 1</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Depreciation -->
                    <div class="mb-4">
                        <h4 class="card-title mb-3">Depreciation</h4>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Depreciation Method <span class="text-danger">*</span></label>
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
                                    <label class="form-label">Depreciation Start Date <span class="text-danger">*</span></label>
                                    <input type="date" name="depreciation_start_date" class="form-control @error('depreciation_start_date') is-invalid @enderror" 
                                           value="{{ old('depreciation_start_date', date('Y-m-d')) }}" required>
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
                                            <label class="form-label">Useful Life (Years)</label>
                                            <input type="number" id="usefulLifeYears" name="useful_life_years" class="form-control" readonly>
                                            <small class="text-muted">Auto based on group</small>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="mb-3">
                                            <label class="form-label">Useful Life (Months)</label>
                                            <input type="number" id="usefulLifeMonths" name="useful_life_months" class="form-control" readonly>
                                            <small class="text-muted">Auto based on group</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Depreciation Rate</label>
                                    <input type="text" id="depreciationRate" name="depreciation_rate" class="form-control" readonly>
                                    <small class="text-muted">Auto based on group</small>
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
                                            <option value="{{ $account->id }}" data-code="{{ $account->kode }}" {{ old('asset_account_id', $firstAssetAccountId) == $account->id ? 'selected' : '' }}>
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
                        <a href="{{ route('fixed-assets.index') }}" class="btn btn-secondary">
                            <i class="ti ti-arrow-left"></i> Back
                        </a>
                        <button type="submit" class="btn btn-success">
                            <i class="ti ti-device-floppy"></i> Merge & Convert Assets
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded, initializing autofill...');
    
    const assetGroupSelect = document.getElementById('assetGroup');
    const assetAccountSelect = document.getElementById('assetAccount');
    const usefulLifeYears = document.getElementById('usefulLifeYears');
    const usefulLifeMonths = document.getElementById('usefulLifeMonths');
    const depreciationRate = document.getElementById('depreciationRate');

    console.log('Elements found:', {
        assetGroup: !!assetGroupSelect,
        assetAccount: !!assetAccountSelect,
        usefulLifeYears: !!usefulLifeYears,
        usefulLifeMonths: !!usefulLifeMonths,
        depreciationRate: !!depreciationRate
    });

    // Group asset data
    const groupData = {
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
    console.log('All accounts loaded:', allAccounts.length);

    // Update depreciation fields based on group
    function updateGroupData() {
        const group = assetGroupSelect.value;
        console.log('Group changed to:', group);
        
        if (group && groupData[group]) {
            const data = groupData[group];
            console.log('Setting group data:', data);
            
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
        console.log('Asset account changed to:', accountCode);
        
        const accumulatedAccount = document.getElementById('accumulatedAccount');
        const accumulatedAccountId = document.getElementById('accumulatedAccountId');
        const expenseAccount = document.getElementById('expenseAccount');
        const expenseAccountId = document.getElementById('expenseAccountId');
        
        if (accountCode && accountMapping[accountCode]) {
            const mapping = accountMapping[accountCode];
            console.log('Found mapping:', mapping);
            
            if (mapping.accumulated) {
                const accAccount = allAccounts.find(acc => acc.kode === mapping.accumulated);
                console.log('Found accumulated account:', accAccount);
                
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
                console.log('Found expense account:', expAccount);
                
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

    // Test function - run this in console
    window.testAutofill = function() {
        console.log('Testing autofill...');
        if (assetGroupSelect) {
            assetGroupSelect.value = 'Permanent';
            assetGroupSelect.dispatchEvent(new Event('change'));
        }
    };

    // Initialize on page load
    setTimeout(function() {
        if (assetGroupSelect && assetGroupSelect.value) {
            console.log('Initializing group on load:', assetGroupSelect.value);
            assetGroupSelect.dispatchEvent(new Event('change'));
        }
        if (assetAccountSelect && assetAccountSelect.value) {
            console.log('Initializing account on load:', assetAccountSelect.value);
            assetAccountSelect.dispatchEvent(new Event('change'));
        }
    }, 100);
});
</script>
@endsection