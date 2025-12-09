@extends('layouts.app')

@section('content')
<div class="container">
    <h4>Tambah Trial Balance</h4>

    <form action="{{ route('trial-balance.store') }}" method="POST">
        @csrf

        @if($parent)
            <p><strong>Menambah Sub Akun:</strong> {{ $parent->kode }} - {{ $parent->keterangan }}</p>
            <input type="hidden" name="parent_id" value="{{ $parent->id }}">
        @endif

        <div class="mb-3">
            <label>Kode</label>
            <input type="text" name="kode" class="form-control"
                value="{{ old('kode') }}" required>
        </div>

        <div class="mb-3">
            <label>Keterangan</label>
            <input type="text" name="keterangan" class="form-control"
                value="{{ old('keterangan') }}" required>
        </div>

        <div class="mb-3">
            <label>Tahun 2024</label>
            <input type="number" name="tahun_2024" class="form-control"
                value="{{ old('tahun_2024') }}">
        </div>

        {{-- Tampilkan Kas/Bank jika level calon anak = 3 --}}
        @php
            $nextLevel = $parent ? $parent->level + 1 : 1;
        @endphp

        {{-- Checkbox untuk Aset --}}
        <div class="mb-3">
            <label class="form-check">
                <input class="form-check-input" type="checkbox" name="is_aset" value="1" 
                       {{ old('is_aset') ? 'checked' : '' }}>
                <span class="form-check-label">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon text-primary me-1" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                        <path d="M3 21h18"/>
                        <path d="M9 8h1"/>
                        <path d="M9 12h1"/>
                        <path d="M9 16h1"/>
                        <path d="M14 8h1"/>
                        <path d="M14 12h1"/>
                        <path d="M14 16h1"/>
                        <path d="M5 21v-16a2 2 0 0 1 2 -2h10a2 2 0 0 1 2 2v16"/>
                    </svg>
                    Apakah ini akun Aset?
                </span>
            </label>
            <small class="form-hint">Centang jika akun ini adalah Aset (Kas, Bank, Piutang, Persediaan)</small>
        </div>

        {{-- Checkbox untuk Kas/Bank --}}
        <div class="mb-3">
            <label class="form-check">
                <input class="form-check-input" type="checkbox" name="is_kas_bank" value="1" 
                       {{ old('is_kas_bank') ? 'checked' : '' }}>
                <span class="form-check-label">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon text-success me-1" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                        <rect x="7" y="9" width="14" height="10" rx="2"/>
                        <circle cx="14" cy="14" r="2"/>
                        <path d="m4.5 12.5l8 -8a4.94 4.94 0 0 1 7 7l-8 8"/>
                    </svg>
                    Apakah ini akun Kas/Bank?
                </span>
            </label>
            <small class="form-hint">Centang jika akun ini dapat digunakan untuk Ledger Kas/Bank</small>
        </div>

        <div class="form-footer">
            <button type="submit" class="btn btn-primary">Simpan</button>
            <a href="{{ route('trial-balance.index') }}" class="btn btn-secondary">Batal</a>
        </div>
    </form>
</div>
@endsection
