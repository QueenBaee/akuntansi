@extends('layouts.app')

@section('title', 'Edit Ledger')

@section('page-header')
    <div class="page-pretitle">Master Data</div>
    <h2 class="page-title">Edit Ledger</h2>
@endsection

@section('content')
    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('ledgers.update', $ledger) }}">
                @csrf
                @method('PUT')
                <div class="mb-3">
                    <label class="form-label">Nama Ledger</label>
                    <input type="text" class="form-control @error('nama_ledger') is-invalid @enderror" name="nama_ledger" value="{{ old('nama_ledger', $ledger->nama_ledger) }}" required>
                    @error('nama_ledger')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Kode Ledger</label>
                    <input type="text" class="form-control @error('kode_ledger') is-invalid @enderror" name="kode_ledger" value="{{ old('kode_ledger', $ledger->kode_ledger) }}" required>
                    @error('kode_ledger')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Tipe Ledger</label>
                    <select class="form-select @error('tipe_ledger') is-invalid @enderror" name="tipe_ledger" required>
                        <option value="">Pilih Tipe</option>
                        <option value="kas" {{ old('tipe_ledger', $ledger->tipe_ledger) == 'kas' ? 'selected' : '' }}>Kas</option>
                        <option value="bank" {{ old('tipe_ledger', $ledger->tipe_ledger) == 'bank' ? 'selected' : '' }}>Bank</option>
                    </select>
                    @error('tipe_ledger')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Deskripsi</label>
                    <textarea class="form-control" name="deskripsi" rows="3">{{ old('deskripsi', $ledger->deskripsi) }}</textarea>
                </div>
                
                <div class="mb-3">
                    <label class="form-check">
                        <input type="checkbox" class="form-check-input" name="is_active" value="1" {{ old('is_active', $ledger->is_active) ? 'checked' : '' }}>
                        <span class="form-check-label">Aktif</span>
                    </label>
                </div>
                
                <div class="form-footer">
                    <button type="submit" class="btn btn-primary">Update</button>
                    <a href="{{ route('ledgers.index') }}" class="btn btn-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
@endsection