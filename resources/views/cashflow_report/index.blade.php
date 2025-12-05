@extends('layouts.app')

@section('title', 'Cashflow Report')

@section('page-header')
    <div class="page-pretitle">Laporan</div>
    <h2 class="page-title">Cashflow Report</h2>
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
                <div class="table-responsive">
                    <style>
                        .cf-text {
                            display: flex;
                            align-items: center;
                            font-size: 14px;
                        }

                        .no-equal-width th {
                            text-align: center !important;
                            vertical-align: middle !important;
                            font-weight: 600 !important;
                            background-color: #f8f9fa !important;
                        }

                        .no-equal-width td:nth-child(1),
                        .no-equal-width th:nth-child(1) {
                            min-width: 80px !important;
                            text-align: left !important;
                            font-weight: 600 !important;
                        }

                        .no-equal-width td:nth-child(2),
                        .no-equal-width th:nth-child(2) {
                            min-width: 300px !important;
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
                            background: #f8f9fa !important;
                        }

                        tr.level-1-row {
                            background: #ffffff !important;
                        }

                        tr.level-2-row {
                            background: #ffffff !important;
                        }

                        .total-row {
                            background: #f8f9fa !important;
                            font-weight: bold !important;
                        }
                        
                        .surplus-row {
                            background: #e3f2fd !important;
                            font-weight: bold !important;
                            border-top: 3px solid #1976d2 !important;
                        }
                    </style>

                    <table class="table table-bordered table-striped no-equal-width">
                        <thead>
                            <tr>
                                <th>Kode</th>
                                <th>Keterangan</th>
                                @for ($m = 1; $m <= 12; $m++)
                                    <th>{{ date('M', mktime(0, 0, 0, $m, 1, $year)) }} {{ substr($year, -2) }}</th>
                                @endfor
                                <th>{{ $year }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($flattenedData as $row)
                                @if(isset($row['is_header']) && $row['is_header'])
                                    {{-- Header row for parent categories --}}
                                    <tr class="level-{{ $row['depth'] }}-row">
                                        <td>{{ $row['code'] }}</td>
                                        <td>
                                            <div class="cf-text level-{{ $row['depth'] }}">
                                                {{ $row['name'] }}
                                            </div>
                                        </td>
                                        <td colspan="13"></td>
                                    </tr>
                                @else
                                    {{-- Data row (leaf) or Summary row --}}
                                    <tr class="level-{{ $row['depth'] }}-row {{ isset($row['is_summary']) && $row['is_summary'] ? 'total-row' : '' }}">
                                        <td>{{ isset($row['is_summary']) && $row['is_summary'] ? '' : $row['code'] }}</td>
                                        <td>
                                            <div class="cf-text level-{{ $row['depth'] }}">
                                                @if(isset($row['is_summary']) && $row['is_summary'])
                                                    <strong>{{ $row['name'] }}</strong>
                                                @else
                                                    {{ $row['name'] }}
                                                @endif
                                            </div>
                                        </td>
                                        @for ($m = 1; $m <= 12; $m++)
                                            <td>
                                                @if(isset($row['is_summary']) && $row['is_summary'])
                                                    <strong>{{ formatAccounting($data[$row['id']]["month_$m"] ?? 0) }}</strong>
                                                @else
                                                    {{ formatAccounting($data[$row['id']]["month_$m"] ?? 0) }}
                                                @endif
                                            </td>
                                        @endfor
                                        <td>
                                            @if(isset($row['is_summary']) && $row['is_summary'])
                                                <strong>{{ formatAccounting($data[$row['id']]['total'] ?? 0) }}</strong>
                                            @else
                                                {{ formatAccounting($data[$row['id']]['total'] ?? 0) }}
                                            @endif
                                        </td>
                                    </tr>
                                @endif
                            @endforeach
                            
                            <!-- SURPLUS/(DEFISIT) USAHA Row -->
                            <tr class="total-row" style="border-top: 3px solid #000;">
                                <td><strong>S/D</strong></td>
                                <td><strong>SURPLUS/(DEFISIT) USAHA</strong></td>
                                @for ($m = 1; $m <= 12; $m++)
                                    <td><strong>{{ formatAccounting($surplusDeficit["month_$m"] ?? 0) }}</strong></td>
                                @endfor
                                <td><strong>{{ formatAccounting($surplusDeficit['total'] ?? 0) }}</strong></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection