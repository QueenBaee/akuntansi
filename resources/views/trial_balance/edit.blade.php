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
            <div class="form-label">Apakah akun ini termasuk Kas atau Bank?</div>
            <div class="row">
                <div class="col-md-6">
                    <label class="form-check form-check-inline">
                        <input class="form-check-input" type="checkbox" name="tipe_ledger" value="kas" 
                               {{ old('tipe_ledger', $trial_balance->tipe_ledger) == 'kas' ? 'checked' : '' }} 
                               onchange="toggleKasBank(this, 'kas')">
                        <span class="form-check-label">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon text-success me-1" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                <rect x="7" y="9" width="14" height="10" rx="2"/>
                                <circle cx="14" cy="14" r="2"/>
                                <path d="m4.5 12.5l8 -8a4.94 4.94 0 0 1 7 7l-8 8"/>
                            </svg>
                            Akun Kas
                        </span>
                    </label>
                </div>
                <div class="col-md-6">
                    <label class="form-check form-check-inline">
                        <input class="form-check-input" type="checkbox" name="tipe_ledger" value="bank" 
                               {{ old('tipe_ledger', $trial_balance->tipe_ledger) == 'bank' ? 'checked' : '' }} 
                               onchange="toggleKasBank(this, 'bank')">
                        <span class="form-check-label">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon text-primary me-1" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                <line x1="3" y1="21" x2="21" y2="21"/>
                                <line x1="3" y1="10" x2="21" y2="10"/>
                                <polyline points="5,6 12,3 19,6"/>
                                <line x1="4" y1="10" x2="4" y2="21"/>
                                <line x1="20" y1="10" x2="20" y2="21"/>
                                <line x1="8" y1="14" x2="8" y2="17"/>
                                <line x1="12" y1="14" x2="12" y2="17"/>
                                <line x1="16" y1="14" x2="16" y2="17"/>
                            </svg>
                            Akun Bank
                        </span>
                    </label>
                </div>
            </div>
            <small class="form-hint">Centang salah satu jika akun ini merupakan akun Kas atau Bank</small>
        </div>

        <script>
        function toggleKasBank(checkbox, type) {
            const allCheckboxes = document.querySelectorAll('input[name="tipe_ledger"]');
            
            if (checkbox.checked) {
                // Uncheck other checkboxes
                allCheckboxes.forEach(cb => {
                    if (cb !== checkbox) {
                        cb.checked = false;
                    }
                });
            }
        }
        </script>

        <div class="form-footer">
            <button type="submit" class="btn btn-primary">Update</button>
            <a href="{{ route('trial-balance.index') }}" class="btn btn-secondary">Batal</a>
        </div>
    </form>
</div>
@endsection
