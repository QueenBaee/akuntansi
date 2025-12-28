@extends('layouts.app')

@section('title', 'Tambah Trial Balance')

@section('page-header')
    <div class="page-pretitle">Master Data</div>
    <h2 class="page-title">Tambah Trial Balance</h2>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('trial-balance.store') }}" method="POST">
                    @csrf

                    {{-- Input Kode --}}
                    <div class="mb-3">
                        <label class="form-label">Kode</label>
                        <input type="text" name="kode" class="form-control" value="{{ old('kode') }}" required>
                    </div>

                    {{-- Input Keterangan --}}
                    <div class="mb-3">
                        <label class="form-label">Keterangan</label>
                        <input type="text" name="keterangan" class="form-control" value="{{ old('keterangan') }}" required>
                    </div>

                    {{-- Pilih Level --}}
                    <div class="mb-3">
                        <label class="form-label">Level</label>
                        <select name="level" class="form-control" required>
                            <option value="1" {{ old('level') == 1 ? 'selected' : '' }}>Level 1</option>
                            <option value="2" {{ old('level') == 2 ? 'selected' : '' }}>Level 2</option>
                            <option value="3" {{ old('level') == 3 ? 'selected' : '' }}>Level 3</option>
                            <option value="4" {{ old('level') == 4 ? 'selected' : '' }}>Level 4</option>
                        </select>
                    </div>

                    {{-- Parent Trial Balance --}}
                    <div class="mb-3">
                        <label class="form-label">Parent Trial Balance</label>
                        <select name="parent_id" class="form-control">
                            <option value="">-- Tidak ada parent --</option>
                            @foreach($parents as $p)
                                <option value="{{ $p->id }}" {{ old('parent_id') == $p->id ? 'selected' : '' }}>
                                    {{ $p->kode }} - {{ $p->keterangan }}
                                </option>
                            @endforeach
                        </select>
                        <small class="text-muted">Pilih parent jika level > 1</small>
                    </div>

                    {{-- Tahun 2024 --}}
                    <div class="mb-3">
                        <label class="form-label">Tahun 2024</label>
                        <input type="number" name="tahun_2024" class="form-control" value="{{ old('tahun_2024') }}">
                        <small class="text-muted">Saldo awal tahun 2024</small>
                    </div>

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
                    </div>

                    <div class="form-footer">
                        <button type="submit" class="btn btn-primary">Simpan</button>
                        <a href="{{ route('trial-balance.index') }}" class="btn btn-secondary">Kembali</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
