@extends('layouts.app')

@section('title', 'Tambah Cashflow')

@section('page-header')
    <div class="page-pretitle">Master Data</div>
    <h2 class="page-title">Tambah Cashflow</h2>
@endsection

@section('content')
<div class="row">
    <div class="col-12">

        <div class="card">
            <div class="card-body">

                <form action="{{ route('cashflow.store') }}" method="POST">
                    @csrf

                    {{-- Input Kode --}}
                    <div class="mb-3">
                        <label class="form-label">Kode Cash Flow</label>
                        <input type="text" name="kode" class="form-control" required>
                    </div>

                    {{-- Input Keterangan --}}
                    <div class="mb-3">
                        <label class="form-label">Akun Cash Flow</label>
                        <input type="text" name="keterangan" class="form-control" required>
                    </div>

                    {{-- Pilih Level --}}
                    <div class="mb-3">
                        <label class="form-label">Level</label>
                        <select name="level" class="form-control" required>
                            <option value="1">Level 1</option>
                            <option value="2">Level 2</option>
                            <option value="3">Level 3</option>
                        </select>
                    </div>

                    {{-- Parent Cashflow (Level 1 & 2) --}}
                    <div class="mb-3">
                        <label class="form-label">Parent Cashflow</label>
                        <select name="parent_id" class="form-control">
                            <option value="">-- Tidak ada parent --</option>

                            @foreach($cashflowParents as $cf)
                                <option 
                                    value="{{ $cf->id }}"
                                    {{ request('parent_id') == $cf->id ? 'selected' : '' }}
                                >
                                    {{ $cf->kode }} - {{ $cf->keterangan }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Trial Balance (hanya level 4 untuk Level 3) --}}
                    <div class="mb-3">
                        <label class="form-label">Trial Balance (Level 4)</label>
                        <select name="trial_balance_id" class="form-control">
                            <option value="">-- Pilih akun TB level 4 --</option>

                            @foreach($parentsTB as $tb)
                                <option value="{{ $tb->id }}">
                                    {{ $tb->kode }} - {{ $tb->keterangan }}
                                </option>
                            @endforeach
                        </select>
                        <small class="text-muted">Hanya diisi jika Level = 3</small>
                    </div>

                    <button class="btn btn-primary">Simpan</button>
                    <a href="{{ route('cashflow.index') }}" class="btn btn-secondary">Kembali</a>
                </form>

            </div>
        </div>

    </div>
</div>
@endsection
