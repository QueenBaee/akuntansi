@extends('layouts.app')

@section('title', 'Cashflow Report')

@section('page-header')
    <div class="page-pretitle">Laporan</div>
    <h2 class="page-title">Arus Kas Tahun {{ $year }}</h2>
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
                    <h3 class="card-title">Daftar Cashflow Report</h3>
                </div>

                <div class="table-responsive">
                    <style>
                        .cf-text {
                            display: flex;
                            align-items: center;
                            font-size: 14px;
                        }

                        /* Kolom kode dilebarkan */
                        .table td:nth-child(1),
                        .table th:nth-child(1) {
                            min-width: 100px;
                            width: 100px;
                            white-space: nowrap;
                            font-weight: 600;
                        }

                        .level-0 {
                            margin-left: 0px;
                            font-weight: 800;
                        }

                        .level-1 {
                            margin-left: 15px;
                            font-weight: 700;
                        }

                        .level-2 {
                            margin-left: 30px;
                            font-weight: 600;
                        }

                        .level-3 {
                            margin-left: 45px;
                        }

                        .level-4 {
                            margin-left: 60px;
                        }

                        tr.level-0-row {
                            background: #eaf6ff !important;
                        }

                        tr.level-1-row {
                            background: #f4fbff !important;
                        }

                        tr.level-2-row {
                            background: #ffffff !important;
                        }


                    </style>

                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Kode</th>
                                <th>Keterangan</th>
                                @for ($m = 1; $m <= 12; $m++)
                                    <th>{{ date('M', mktime(0, 0, 0, $m, 1, $year)) }} {{ substr($year, -2) }}</th>
                                @endfor
                                <th>{{ $year }}</th>
                                <th>{{ $year - 1 }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($enhancedItems as $item)
                                @php
                                    $displayLevel = $item->level;
                                    if (isset($item->is_subtotal)) {
                                        $displayLevel = 1;
                                    } elseif (isset($item->is_total)) {
                                        $displayLevel = 0;
                                    } elseif (isset($item->is_surplus)) {
                                        $displayLevel = 0;
                                    }
                                @endphp
                                <tr class="level-{{ $displayLevel }}-row">

                                    <td>{{ $item->kode }}</td>

                                    <td>
                                        <div class="cf-text level-{{ $displayLevel }}">
                                            {{ $item->keterangan }}
                                        </div>
                                    </td>

                                    {{-- Monthly values --}}
                                    @for ($m = 1; $m <= 12; $m++)
                                        @php
                                            $val = $data[$item->id]["month_$m"] ?? 0;
                                        @endphp
                                        <td>{{ $val == 0 ? '' : number_format($val, 0, ',', '.') }}</td>
                                    @endfor

                                    {{-- Total current year --}}
                                    @php
                                        $total = $data[$item->id]['total'] ?? 0;
                                    @endphp
                                    <td>{{ $total == 0 ? '' : number_format($total, 0, ',', '.') }}</td>

                                    {{-- Opening balance --}}
                                    @php
                                        $opening = $data[$item->id]['opening'] ?? 0;
                                    @endphp
                                    <td>{{ $opening == 0 ? '' : number_format($opening, 0, ',', '.') }}</td>

                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
@endsection