@extends('layouts.app')

@section('title', 'Trial Balance Report')

@section('page-header')
    <div class="page-pretitle">Laporan</div>
    <h2 class="page-title">Trial Balance Report Tahun {{ $year }}</h2>
@endsection

@section('page-actions')
    <form method="GET" class="d-flex">
        <input type="number" name="year" value="{{ $year }}" class="form-control me-2" placeholder="Tahun">
        <button class="btn btn-outline-primary">Tampilkan</button>
    </form>
@endsection

@section('content')
<div class="row">
    <div class="col-12">

        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Daftar Trial Balance</h3>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Kode</th>
                            <th>Keterangan</th>
                            @for ($m = 1; $m <= 12; $m++)
                                <th>{{ date('M', mktime(0,0,0,$m,1,$year)) }}</th>
                            @endfor
                            <th>Total {{ $year }}</th>
                            <th>Total 2024</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($items as $item)
                            <tr>
                                <td>{{ $item->kode }}</td>
                                <td>{{ $item->keterangan }}</td>

                                @for ($m = 1; $m <= 12; $m++)
                                    <td>{{ number_format($data[$item->id]['month_'.$m] ?? 0, 0, ',', '.') }}</td>
                                @endfor

                                <td>{{ number_format($data[$item->id]['total_this_year'] ?? 0, 0, ',', '.') }}</td>
                                <td>{{ number_format($item->tahun_2024 ?? 0, 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>
@endsection
