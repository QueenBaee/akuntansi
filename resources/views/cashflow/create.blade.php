@extends('layouts.app')

@section('content')
<div class="container">
    <h4>Tambah Cashflow</h4>

    <form action="{{ route('cashflow.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label>Kode</label>
            <input type="text" name="kode" class="form-control" required value="{{ old('kode') }}">
        </div>

        <div class="mb-3">
            <label>Keterangan</label>
            <input type="text" name="keterangan" class="form-control" required value="{{ old('keterangan') }}">
        </div>

        <div class="mb-3">
            <label>Pilih Akun Trial Balance</label>
            <select name="trial_balance_id" class="form-control" required>
                <option value="">-- Pilih Akun --</option>
                @foreach($accounts as $acc)
                    <option value="{{ $acc->id }}">
                        {{ $acc->kode }} - {{ $acc->keterangan }}
                    </option>
                @endforeach
            </select>
        </div>

        <button class="btn btn-primary">Simpan</button>
        <a href="{{ route('cashflow.index') }}" class="btn btn-secondary">Batal</a>
    </form>
</div>
@endsection
