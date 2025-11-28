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
        <form method="POST" action="{{ route('fixed-assets.update', $fixedAsset) }}">
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
                        <label class="form-label">Umur Manfaat (bulan)</label>
                        <input type="number" class="form-control" name="useful_life_months" value="{{ $fixedAsset->useful_life_months }}" min="1" required>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-3">
                    <div class="mb-3">
                        <label class="form-label">Asset Account</label>
                        <select class="form-select" name="asset_account_id" required>
                            @foreach($trialBalances as $account)
                                <option value="{{ $account->id }}" {{ $fixedAsset->asset_account_id == $account->id ? 'selected' : '' }}>
                                    {{ $account->kode }} - {{ $account->keterangan }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="mb-3">
                        <label class="form-label">Accumulated Account</label>
                        <select class="form-select" name="accumulated_account_id" required>
                            @foreach($trialBalances as $account)
                                <option value="{{ $account->id }}" {{ $fixedAsset->accumulated_account_id == $account->id ? 'selected' : '' }}>
                                    {{ $account->kode }} - {{ $account->keterangan }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="mb-3">
                        <label class="form-label">Expense Account</label>
                        <select class="form-select" name="expense_account_id" required>
                            @foreach($trialBalances as $account)
                                <option value="{{ $account->id }}" {{ $fixedAsset->expense_account_id == $account->id ? 'selected' : '' }}>
                                    {{ $account->kode }} - {{ $account->keterangan }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="mb-3">
                        <label class="form-label">Nilai Residual</label>
                        <input type="number" class="form-control" name="residual_value" value="{{ $fixedAsset->residual_value }}" step="0.01">
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-12">
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select class="form-select" name="is_active">
                            <option value="1" {{ $fixedAsset->is_active ? 'selected' : '' }}>Aktif</option>
                            <option value="0" {{ !$fixedAsset->is_active ? 'selected' : '' }}>Tidak Aktif</option>
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