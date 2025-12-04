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
                    <style>
                        .tb-text {
                            display: flex;
                            align-items: center;
                            font-size: 14px;
                        }

                        .no-equal-width th {
                            text-align: center !important;
                            vertical-align: middle !important;
                            font-weight: 600 !important;
                            background-color: #f8f9fa !important;
                            width: auto !important;
                        }

                        .no-equal-width td:nth-child(1),
                        .no-equal-width th:nth-child(1) {
                            min-width: 80px !important;
                            width: 80px !important;
                            text-align: center !important;
                            font-weight: 600 !important;
                        }

                        .no-equal-width td:nth-child(2),
                        .no-equal-width th:nth-child(2) {
                            min-width: 200px !important;
                            width: 200px !important;
                            text-align: left !important;
                        }

                        .no-equal-width td:not(:nth-child(1)):not(:nth-child(2)),
                        .no-equal-width th:not(:nth-child(1)):not(:nth-child(2)) {
                            text-align: right !important;
                            min-width: 80px !important;
                            width: 80px !important;
                        }

                        .level-0 {
                            margin-left: 0px;
                            font-weight: 800;
                        }

                        .level-1 {
                            margin-left: 20px;
                            font-weight: 700;
                        }

                        .level-2 {
                            margin-left: 40px;
                            font-weight: 600;
                        }

                        .level-3 {
                            margin-left: 60px;
                            font-weight: 500;
                        }

                        .level-4 {
                            margin-left: 80px;
                            font-weight: 400;
                        }

                        tr.level-0-row {
                            background: #e3f2fd !important;
                        }

                        tr.level-1-row {
                            background: #f1f8ff !important;
                        }

                        tr.level-2-row {
                            background: #ffffff !important;
                        }
                    </style>



                    <table class="table table-bordered table-striped no-equal-width">
                        <thead>
                            <tr>
                                <th>Kode</th>
                                <th>Keterangan</th>
                                @for ($m = 1; $m <= 12; $m++)
                                    <th>{{ date('M', mktime(0, 0, 0, $m, 1, $year)) }}</th>
                                @endfor
                                <th>Total {{ $year }}</th>
                                <th>Total {{ $year - 1 }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($items as $item)
                                <tr class="level-{{ $item->level }}-row">

                                    <td>{{ $item->kode }}</td>

                                    <td>
                                        <div class="tb-text level-{{ $item->level }}">
                                            {{ $item->keterangan }}
                                        </div>
                                    </td>

                                    {{-- Nilai bulan --}}
                                    @for ($m = 1; $m <= 12; $m++)
                                        @php
                                            $val = $data[$item->id]["month_$m"] ?? 0;
                                        @endphp
                                        <td>{{ $val == 0 ? '' : number_format($val, 0, ',', '.') }}</td>
                                    @endfor

                                    {{-- Total tahun berjalan --}}
                                    @php
                                        $total = $data[$item->id]['total'] ?? 0;
                                    @endphp
                                    <td>{{ $total == 0 ? '' : number_format($total, 0, ',', '.') }}</td>

                                    {{-- Saldo awal --}}
                                    @php
                                        $start =  $data[$item->id]['opening'] ?? 0;
                                    @endphp
                                    <td>{{ $start == 0 ? '' : number_format($start, 0, ',', '.') }}</td>

                                </tr>
                            @endforeach
                        </tbody>



                    </table>
                </div>
            </div>

        </div>
    </div>
@endsection
