@extends('layouts.app')

@section('content')
<div class="container">
    <h4>Tambah Trial Balance</h4>

    <form action="{{ route('trial-balance.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label class="form-label">Kode TB</label>
            <input type="text" name="kode" class="form-control">
        </div>

        <div class="mb-3">
            <label class="form-label">Akun TB</label>
            <input type="text" name="keterangan" class="form-control">
        </div>

        <div class="mb-3">
            <label class="form-label">Parent</label>
            <select name="parent_id" class="form-select">
                <option value="">-- Tidak Ada --</option>
                @foreach($parents as $p)
                    <option value="{{ $p->id }}">{{ $p->kode }} - {{ $p->keterangan }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Level</label>
            <input type="number" name="level" class="form-control">
        </div>

        <div class="mb-3">
            <label class="form-label">Kas / Bank</label>
            <select name="is_kas_bank" class="form-select">
                <option value="">-- Pilih --</option>
                <option value="kas">Kas</option>
                <option value="bank">Bank</option>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Tahun 2024</label>
            <input type="number" name="tahun_2024" class="form-control">
        </div>

        <button class="btn btn-primary">Simpan</button>
    </form>
</div>
@endsection
