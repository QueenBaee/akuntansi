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

        @if ($nextLevel == 3)
            <div class="mb-3">
                <label>Jenis (Kas / Bank)</label>
                <select name="is_kas_bank" class="form-control">
                    <option value="">-- Pilih --</option>
                    <option value="kas" {{ old('is_kas_bank') == 'kas' ? 'selected' : '' }}>Kas</option>
                    <option value="bank" {{ old('is_kas_bank') == 'bank' ? 'selected' : '' }}>Bank</option>
                </select>
                <small class="text-muted">
                    Hanya muncul untuk akun level 3 (rekening kas & bank).
                </small>
            </div>
        @endif

        <button class="btn btn-primary">Simpan</button>
        <a href="{{ route('trial-balance.index') }}" class="btn btn-secondary">Batal</a>
    </form>
</div>
@endsection
