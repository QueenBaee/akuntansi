@extends('layouts.app')

@section('title', 'Tambah Aset Tetap')

@section('page-header')
<div class="page-pretitle">Aset Tetap</div>
<h2 class="page-title">Tambah Aset Tetap</h2>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Informasi Aset Tetap</h3>
            </div>
            <form action="{{ route('fixed-assets.store') }}" method="POST">
                @csrf
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Kode Aset <span class="text-danger">*</span></label>
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
                                       value="{{ old('name') }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Tanggal Perolehan <span class="text-danger">*</span></label>
                                <input type="date" name="acquisition_date" class="form-control @error('acquisition_date') is-invalid @enderror" 
                                       value="{{ old('acquisition_date') }}" required>
                                @error('acquisition_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Harga Perolehan <span class="text-danger">*</span></label>
                                <input type="number" name="acquisition_price" class="form-control @error('acquisition_price') is-invalid @enderror" 
                                       value="{{ old('acquisition_price') }}" step="0.01" min="0.01" required>
                                @error('acquisition_price')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Nilai Residual</label>
                                <input type="number" name="residual_value" class="form-control @error('residual_value') is-invalid @enderror" 
                                       value="{{ old('residual_value', 0) }}" step="0.01" min="0">
                                @error('residual_value')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Umur Manfaat (bulan) <span class="text-danger">*</span></label>
                                <input type="number" name="useful_life_months" class="form-control @error('useful_life_months') is-invalid @enderror" 
                                       value="{{ old('useful_life_months') }}" min="1" max="600" required>
                                @error('useful_life_months')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Asset Account <span class="text-danger">*</span></label>
                                <select name="asset_account_id" class="form-select @error('asset_account_id') is-invalid @enderror" required>
                                    <option value="">Pilih Asset Account</option>
                                    @foreach($trialBalances as $account)
                                        <option value="{{ $account->id }}" {{ old('asset_account_id') == $account->id ? 'selected' : '' }}>
                                            {{ $account->kode }} - {{ $account->keterangan }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('asset_account_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Accumulated Depreciation Account <span class="text-danger">*</span></label>
                                <select name="accumulated_account_id" class="form-select @error('accumulated_account_id') is-invalid @enderror" required>
                                    <option value="">Pilih Accumulated Account</option>
                                    @foreach($trialBalances as $account)
                                        <option value="{{ $account->id }}" {{ old('accumulated_account_id') == $account->id ? 'selected' : '' }}>
                                            {{ $account->kode }} - {{ $account->keterangan }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('accumulated_account_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Depreciation Expense Account <span class="text-danger">*</span></label>
                                <select name="expense_account_id" class="form-select @error('expense_account_id') is-invalid @enderror" required>
                                    <option value="">Pilih Expense Account</option>
                                    @foreach($trialBalances as $account)
                                        <option value="{{ $account->id }}" {{ old('expense_account_id') == $account->id ? 'selected' : '' }}>
                                            {{ $account->kode }} - {{ $account->keterangan }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('expense_account_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    @if($parentAssets->count() > 0)
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Parent Asset (Optional)</label>
                                <select name="parent_id" class="form-select @error('parent_id') is-invalid @enderror">
                                    <option value="">Tidak ada parent</option>
                                    @foreach($parentAssets as $parent)
                                        <option value="{{ $parent->id }}" {{ old('parent_id') == $parent->id ? 'selected' : '' }}>
                                            {{ $parent->code }} - {{ $parent->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('parent_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
                <div class="card-footer text-end">
                    <a href="{{ route('fixed-assets.index') }}" class="btn btn-secondary me-2">Batal</a>
                    <button type="submit" class="btn btn-primary">Simpan Aset</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection