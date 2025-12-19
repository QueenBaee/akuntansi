@extends('layouts.app')

@section('content')

<div class="container mt-4">

    <h4>Laporan Trial Balance Tahun {{ $year ?? '' }}</h4>

    <form action="{{ route('trial.balance.report.show') }}" method="GET" class="mb-3">
        <div class="input-group" style="max-width: 300px;">
            <input type="number" name="year" value="{{ $year ?? date('Y') }}" class="form-control">
            <button class="btn btn-primary">Tampilkan</button>
        </div>
    </form>

    @isset($rows)
    <div class="table-responsive">
        <table class="table table-bordered table-sm">
            <thead class="table-light">
                <tr>
                    <th style="text-align:center">KODE</th>
                    <th style="text-align:center">KETERANGAN</th>
                    @foreach(range(1,12) as $m)
                        <th style="text-align:center">{{ strtoupper(date('M', mktime(0,0,0,$m,1))) }}</th>
                    @endforeach
                    <th style="text-align:center">TOTAL {{ $year }}</th>
                    <th style="text-align:center">TOTAL 2024</th>
                </tr>
            </thead>
            <tbody>
                @foreach($rows as $r)
                <tr>
                    <td>{{ $r['kode'] }}</td>
                    <td>{{ $r['keterangan'] }}</td>

                    @foreach($r['months'] as $v)
                        <td class="text-end">{{ number_format($v, 2) }}</td>
                    @endforeach

                    <td class="text-end fw-bold">{{ number_format($r['total_year'], 2) }}</td>
                    <td class="text-end fw-bold">{{ number_format($r['total_prev'], 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endisset

</div>

@endsection
