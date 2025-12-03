@extends('layouts.app')

@section('content')
<div class="container">
    <h4>Edit Trial Balance</h4>

    <form action="{{ route('trial-balance.update', $trial_balance->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label>Kode</label>
            <input type="text" name="kode" class="form-control"
                required value="{{ old('kode', $trial_balance->kode) }}">
        </div>

        <div class="mb-3">
            <label>Keterangan</label>
            <input type="text" name="keterangan" class="form-control"
                required value="{{ old('keterangan', $trial_balance->keterangan) }}">
        </div>

        <div class="mb-3">
            <label>Tahun 2024</label>
            <input type="number" name="tahun_2024" class="form-control"
                value="{{ old('tahun_2024', $trial_balance->tahun_2024 ?? '') }}">
        </div>

        {{-- Checkbox untuk Kas/Bank --}}
        <div class="mb-3">
            <label class="form-check">
                <input class="form-check-input" type="checkbox" name="is_kas_bank" value="1" 
                       {{ old('is_kas_bank', $trial_balance->is_kas_bank) ? 'checked' : '' }}>
                <span class="form-check-label">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon text-success me-1" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                        <rect x="7" y="9" width="14" height="10" rx="2"/>
                        <circle cx="14" cy="14" r="2"/>
                        <path d="m4.5 12.5l8 -8a4.94 4.94 0 0 1 7 7l-8 8"/>
                    </svg>
                    Is this a Cash/Bank account?
                </span>
            </label>
            <small class="form-hint">Check this if the account is a Cash or Bank account</small>
        </div>

        <div class="form-footer">
            <button type="submit" class="btn btn-primary">Update</button>
            <a href="{{ route('trial-balance.index') }}" class="btn btn-secondary">Batal</a>
        </div>
    </form>
</div>
@endsection
