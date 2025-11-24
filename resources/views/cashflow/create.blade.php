@extends('layouts.app')

@section('content')
<div class="container">
    <h4>Tambah Cashflow</h4>

    <form action="{{ route('cashflow.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label>Level</label>
            <select name="level" id="level" class="form-control">
                <option value="1">Level 1</option>
                <option value="2">Level 2</option>
                <option value="3">Level 3 (harus punya akun TB)</option>
            </select>
        </div>

        <div class="mb-3">
            <label>Kode</label>
            <input type="text" name="kode" class="form-control">
        </div>

        <div class="mb-3">
            <label>Keterangan</label>
            <input type="text" name="keterangan" class="form-control">
        </div>

        <div class="mb-3">
            <label>Parent Cashflow (Level 1 & 2)</label>
            <select name="parent_id" class="form-control">
                <option value="">-- Tanpa Parent --</option>
                @foreach($cashflowParents as $cf)
                    <option value="{{ $cf->id }}">{{ $cf->kode }} - {{ $cf->keterangan }}</option>
                @endforeach
            </select>
        </div>

        {{-- HANYA MUNCUL KALAU LEVEL = 3 --}}
        <div class="mb-3" id="tbSelect">
            <label>Akun TB (Level 4)</label>
            <select name="trial_balance_id" class="form-control">
                <option value="">-- Pilih Akun TB Level 4 --</option>
                @foreach($parentsTB as $p)
                    <option value="{{ $p->id }}">{{ $p->kode }} - {{ $p->keterangan }}</option>
                @endforeach
            </select>
        </div>

        <button class="btn btn-primary">Simpan</button>
    </form>
</div>

<script>
document.getElementById('level').addEventListener('change', function() {
    document.getElementById('tbSelect').style.display = this.value == 3 ? 'block' : 'none';
});
</script>
@endsection
