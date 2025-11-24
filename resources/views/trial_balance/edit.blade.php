@extends('layouts.app')

@section('content')
<div class="container">
    <h4>Edit Trial Balance</h4>

    <form action="{{ route('trial-balance.update', $item->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label>Kode</label>
            <input type="text" name="kode" value="{{ $item->kode }}" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Keterangan</label>
            <input type="text" name="keterangan" value="{{ $item->keterangan }}" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Level</label>
            <input type="number" name="level" value="{{ $item->level }}" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Parent</label>
            <select name="parent_id" class="form-control">
                <option value="">Tidak Ada</option>

                @foreach ($parents as $p)
                    <option value="{{ $p->id }}" 
                        {{ $item->parent_id == $p->id ? 'selected' : '' }}>
                        {{ $p->kode }} - {{ $p->keterangan }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label>Tahun 2024</label>
            <input type="number" name="tahun_2024" value="{{ $item->tahun_2024 }}" class="form-control">
        </div>


        <div class="mb-3">
            <label>Kas / Bank</label>
            <select name="is_kas_bank" class="form-control">
                <option value="">-</option>
                <option value="kas" {{ $item->is_kas_bank == 'kas' ? 'selected' : '' }}>Kas</option>
                <option value="bank" {{ $item->is_kas_bank == 'bank' ? 'selected' : '' }}>Bank</option>
            </select>
        </div>

        <button class="btn btn-primary">Update</button>
    </form>
</div>
@endsection
