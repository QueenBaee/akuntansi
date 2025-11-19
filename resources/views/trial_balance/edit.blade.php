@extends('layouts.app')

@section('content')
<div class="container">
    <h4>Edit Trial Balance</h4>

    <form action="{{ route('trial-balance.update', $trial_balance->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label>Kode</label>
            <input type="text" name="kode" class="form-control" required value="{{ old('kode', $trial_balance->kode) }}">
        </div>

        <div class="mb-3">
            <label>Keterangan</label>
            <input type="text" name="keterangan" class="form-control" required value="{{ old('keterangan', $trial_balance->keterangan) }}">
        </div>

        <button class="btn btn-primary">Update</button>
        <a href="{{ route('trial-balance.index') }}" class="btn btn-secondary">Batal</a>
    </form>
</div>
@endsection
