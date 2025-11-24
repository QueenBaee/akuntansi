@extends('layouts.app')

@section('content')
<div class="container">
    <h4>Edit Trial Balance</h4>

    <form action="{{ route('trial-balance.update', $item->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label class="form-label">Kode TB</label>
            <input type="text" name="kode" class="form-control" value="{{ $item->kode }}">
        </div>

        <div class="mb-3">
            <label class="form-label">Akun TB</label>
            <input type="text" name="keterangan" class="form-control" value="{{ $item->keterangan }}">
        </div>

        <div class="mb-3">
            <label class="form-label">Parent</label>
            <select name="parent_id" class="form-select">
                <option value="">-- Tidak Ada --</option>
                @foreach($parents as $p)
                    <option value="{{ $p->id }}" {{ $item->parent_id == $p->id ? 'selected' : '' }}>
                        {{ $p->kode }} - {{ $p->keterangan }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Level</label>
            <input type="number" name="level" class="form-control" value="{{ $item->level }}">
        </div>

        <div class="mb-3">
            <label class="form-label">Kas / Bank</label>
            <select name="is_kas_bank" class="form-select">
                <option value="">-- Pilih --</option>
                <option value="kas"  {{ $item->is_kas_bank == 'kas' ? 'selected' : '' }}>Kas</option>
                <option value="bank" {{ $item->is_kas_bank == 'bank' ? 'selected' : '' }}>Bank</option>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Tahun 2024</label>
            <input type="number" name="tahun_2024" class="form-control" value="{{ $item->tahun_2024 }}">
        </div>

        <button class="btn btn-primary">Update</button>
    </form>
</div>
@endsection
