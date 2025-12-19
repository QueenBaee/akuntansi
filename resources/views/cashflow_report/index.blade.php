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
                <div style="overflow-x: auto;">
                    <style>
                        .no-equal-width td,
                        .no-equal-width th {
                            white-space: nowrap !important;
                        }
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
                            text-align: left !important;
                            font-weight: 600 !important;
                        }

                        .no-equal-width td:nth-child(2),
                        .no-equal-width th:nth-child(2) {
                            text-align: left !important;
                        }

                        .no-equal-width td:nth-child(n+3):nth-child(-n+14),
                        .no-equal-width th:nth-child(n+3):nth-child(-n+14) {
                            text-align: right !important;
                            width: 80px !important;
                            min-width: 80px !important;
                        }

                        .no-equal-width td:nth-child(15),
                        .no-equal-width th:nth-child(15) {
                            text-align: left !important;
                        }

                        .no-equal-width td:nth-child(16),
                        .no-equal-width th:nth-child(16) {
                            text-align: left !important;
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
                            background: #f0f0f0 !important;
                            font-weight: bold !important;
                            border-top: 3px solid #000 !important;
                        }
                        
                        .net-surplus-row {
                            background: #e8e8e8 !important;
                            font-weight: bold !important;
                            border-top: 3px solid #000 !important;
                        }
                        
                        .cash-bank-opening-row {
                            background: #f5f5f5 !important;
                            font-weight: bold !important;
                            border-top: 2px solid #000 !important;
                        }
                        
                        .cash-bank-closing-row {
                            background: #eeeeee !important;
                            font-weight: bold !important;
                            border-top: 2px solid #000 !important;
                        }
                        
                        .cash-bank-detail-row {
                            background: #f9f9f9 !important;
                            font-style: italic;
                        }
                        
                        .cash-bank-detail-total-row {
                            background: #e0e0e0 !important;
                            font-weight: bold !important;
                            border-top: 2px solid #000 !important;
                        }
                    </style>

                    <table class="table table-bordered table-striped no-equal-width" style="table-layout: auto; width: max-content; min-width: 100%;">
                        <thead>
                            <tr>
                                <th style="text-align:center">Kode</th>
                                <th style="text-align:center">Keterangan</th>
                                @for ($m = 1; $m <= 12; $m++)
                                    <th style="text-align:center">{{ date('M', mktime(0, 0, 0, $m, 1, $year)) }} {{ substr($year, -2) }}</th>
                                @endfor
                                <th style="text-align:center">{{ $year }}</th>
                                <th style="text-align:center">Kode TB</th>
                                <th style="text-align:center">Akun Laporan Keuangan</th>
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
                                        <td>{{ $row['trial_balance_code'] ?? '' }}</td>
                                        <td>{{ $row['trial_balance_name'] ?? '' }}</td>
                                    </tr>
                                @else
                                    {{-- Data row (leaf) or Summary row --}}
                                    <tr class="level-{{ $row['depth'] }}-row {{ isset($row['is_summary']) && $row['is_summary'] ? 'total-row' : '' }} {{ isset($row['is_surplus_deficit']) && $row['is_surplus_deficit'] ? 'surplus-row' : '' }} {{ isset($row['is_net_surplus_deficit']) && $row['is_net_surplus_deficit'] ? 'net-surplus-row' : '' }} {{ isset($row['is_cash_bank_opening']) && $row['is_cash_bank_opening'] ? 'cash-bank-opening-row' : '' }} {{ isset($row['is_cash_bank_closing']) && $row['is_cash_bank_closing'] ? 'cash-bank-closing-row' : '' }} {{ isset($row['is_cash_bank_detail']) && $row['is_cash_bank_detail'] ? 'cash-bank-detail-row' : '' }} {{ isset($row['is_cash_bank_detail_total']) && $row['is_cash_bank_detail_total'] ? 'cash-bank-detail-total-row' : '' }}" 
                                        @if(isset($row['is_surplus_deficit']) && $row['is_surplus_deficit']) style="border-top: 3px solid #000;" @endif
                                        @if(isset($row['is_net_surplus_deficit']) && $row['is_net_surplus_deficit']) style="border-top: 3px solid #000;" @endif
                                        @if(isset($row['is_cash_bank_opening']) && $row['is_cash_bank_opening']) style="border-top: 2px solid #000;" @endif
                                        @if(isset($row['is_cash_bank_closing']) && $row['is_cash_bank_closing']) style="border-top: 2px solid #000;" @endif
                                        @if(isset($row['is_cash_bank_detail_total']) && $row['is_cash_bank_detail_total']) style="border-top: 2px solid #000;" @endif>
                                        <td>{{ isset($row['is_summary']) && $row['is_summary'] ? $row['code'] : $row['code'] }}</td>
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
                                        <td>{{ $row['trial_balance_code'] ?? '' }}</td>
                                        <td>{{ $row['trial_balance_name'] ?? '' }}</td>
                                    </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection