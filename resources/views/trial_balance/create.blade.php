@extends('layouts.app')

@section('content')
<div class="container">
    <h4>Tambah Trial Balance</h4>

    <form action="{{ route('trial-balance.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label>Kode</label>
            <input type="text" name="kode" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Keterangan</label>
            <input type="text" name="keterangan" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Level</label>
            <input type="number" name="level" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Parent</label>
            <select name="parent_id" class="form-control">
                <option value="">Tidak Ada</option>
                @foreach ($parents as $p)
                    <option value="{{ $p->id }}">{{ $p->kode }} - {{ $p->keterangan }}</option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label>Tahun 2024</label>
            <input type="number" name="tahun_2024" class="form-control">
        </div>

        <div class="mb-3">
            <label>Kas / Bank</label>
            <select name="is_kas_bank" class="form-control">
                <option value="">-</option>
                <option value="kas">Kas</option>
                <option value="bank">Bank</option>
            </select>
        </div>

        <button class="btn btn-primary">Simpan</button>
    </form>
</div>
@endsection
