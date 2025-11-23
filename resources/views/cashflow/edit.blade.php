@extends('layouts.app')

@section('title', 'Edit Cashflow')

@section('page-header')
    <div class="page-pretitle">Master Data</div>
    <h2 class="page-title">Edit Cashflow</h2>
@endsection

@section('content')
<div class="row">
    <div class="col-12">

        <div class="card">
            <div class="card-body">

                <form action="{{ route('cashflow.update', $cashflow->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label class="form-label">Kode</label>
                        <input type="text" name="kode" class="form-control"
                               value="{{ $cashflow->kode }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Keterangan</label>
                        <input type="text" name="keterangan" class="form-control"
                               value="{{ $cashflow->keterangan }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Level</label>
                        <select name="level" class="form-control">
                            <option value="1" {{ $cashflow->level == 1 ? 'selected' : '' }}>Level 1</option>
                            <option value="2" {{ $cashflow->level == 2 ? 'selected' : '' }}>Level 2</option>
                            <option value="3" {{ $cashflow->level == 3 ? 'selected' : '' }}>Level 3</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Parent Cashflow</label>
                        <select name="parent_id" class="form-control">
                            <option value="">-- Tidak ada parent --</option>
                            @foreach($cashflowParents as $cf)
                                <option value="{{ $cf->id }}"
                                    {{ $cashflow->parent_id == $cf->id ? 'selected' : '' }}>
                                    {{ $cf->kode }} - {{ $cf->keterangan }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Trial Balance (Level 4)</label>
                        <select name="trial_balance_id" class="form-control">
                            <option value="">-- Pilih akun --</option>
                            @foreach($parentsTB as $tb)
                                <option value="{{ $tb->id }}"
                                    {{ $cashflow->trial_balance_id == $tb->id ? 'selected' : '' }}>
                                    {{ $tb->kode }} - {{ $tb->keterangan }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <button class="btn btn-primary">Update</button>
                    <a href="{{ route('cashflow.index') }}" class="btn btn-secondary">Kembali</a>
                </form>

            </div>
        </div>

    </div>
</div>
@endsection
