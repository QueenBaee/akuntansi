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
            <input type="text" name="kode" class="form-control" required value="{{ old('kode') }}">
        </div>

        <div class="mb-3">
            <label>Keterangan</label>
            <input type="text" name="keterangan" class="form-control" required value="{{ old('keterangan') }}">
        </div>

        <button class="btn btn-primary">Simpan</button>
        <a href="{{ route('trial-balance.index') }}" class="btn btn-secondary">Batal</a>
    </form>
</div>
@endsection
