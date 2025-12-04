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
                                    <th>{{ date('M', mktime(0, 0, 0, $m, 1, $year)) }} {{ substr($year, -2) }}</th>
                                @endfor
                                <th>{{ $year }}</th>
                                <th>{{ $year - 1 }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                function renderRowsReport($items, $data, $prefix = '') {
                                    foreach ($items as $item) {
                                        if($item->kode == 'PB') continue;
                                        
                                        echo '<tr class="level-' . $item->level . '-row">';
                                        echo '<td>' . $item->kode . '</td>';
                                        echo '<td><div class="tb-text level-' . $item->level . '">' . $prefix . $item->keterangan . '</div></td>';
                                        
                                        // Nilai bulan
                                        for ($m = 1; $m <= 12; $m++) {
                                            $val = $data[$item->id]["month_$m"] ?? 0;
                                            echo '<td>' . formatAccounting($val) . '</td>';
                                        }
                                        
                                        // Total tahun berjalan
                                        $total = $data[$item->id]['total'] ?? 0;
                                        echo '<td>' . formatAccounting($total) . '</td>';
                                        
                                        // Saldo awal
                                        $start = $data[$item->id]['opening'] ?? 0;
                                        echo '<td>' . formatAccounting($start) . '</td>';
                                        
                                        echo '</tr>';
                                        
                                        if ($item->children->count() > 0) {
                                            renderRowsReport($item->children, $data, $prefix . '&nbsp;&nbsp;&nbsp;&nbsp;');
                                        }
                                    }
                                }
                                
                                $rootItems = $items->whereNull('parent_id');
                                renderRowsReport($rootItems, $data);
                            @endphp
                            
                            {{-- Bagian Pindah Buku --}}
                            @php
                                $pbItem = $items->where('kode', 'PB')->first();
                            @endphp
                            @if($pbItem)
                                <tr class="level-{{ $pbItem->level }}-row">
                                    <td>{{ $pbItem->kode }}</td>
                                    <td><div class="tb-text level-{{ $pbItem->level }}">{{ $pbItem->keterangan }}</div></td>
                                    @for ($m = 1; $m <= 12; $m++)
                                        @php
                                            $val = $data[$pbItem->id]["month_$m"] ?? 0;
                                        @endphp
                                        <td>{{ formatAccounting($val) }}</td>
                                    @endfor
                                    @php
                                        $total = $data[$pbItem->id]['total'] ?? 0;
                                        $start = $data[$pbItem->id]['opening'] ?? 0;
                                    @endphp
                                    <td>{{ formatAccounting($total) }}</td>
                                    <td>{{ formatAccounting($start) }}</td>
                                </tr>
                            @endif

                            {{-- TOTAL ASSETS --}}
                            @php
                                $totalAssets = [];
                                foreach ($items as $item) {
                                    if (str_starts_with($item->kode, 'A')) {
                                        for ($m = 1; $m <= 12; $m++) {
                                            $totalAssets["month_$m"] = ($totalAssets["month_$m"] ?? 0) + ($data[$item->id]["month_$m"] ?? 0);
                                        }
                                        $totalAssets['total'] = ($totalAssets['total'] ?? 0) + ($data[$item->id]['total'] ?? 0);
                                        $totalAssets['opening'] = ($totalAssets['opening'] ?? 0) + ($data[$item->id]['opening'] ?? 0);
                                    }
                                }
                            @endphp
                            <tr class="total-row">
                                <td><strong>A</strong></td>
                                <td><strong>TOTAL ASSETS (Aktiva)</strong></td>
                                @for ($m = 1; $m <= 12; $m++)
                                    @php $val = $totalAssets["month_$m"] ?? 0; @endphp
                                    <td><strong>{{ formatAccounting($val) }}</strong></td>
                                @endfor
                                @php $total = $totalAssets['total'] ?? 0; @endphp
                                <td><strong>{{ formatAccounting($total) }}</strong></td>
                                @php $opening = $totalAssets['opening'] ?? 0; @endphp
                                <td><strong>{{ formatAccounting($opening) }}</strong></td>
                            </tr>

                            {{-- TOTAL LIABILITIES --}}
                            @php
                                $totalLiabilities = [];
                                foreach ($items as $item) {
                                    if (str_starts_with($item->kode, 'L')) {
                                        for ($m = 1; $m <= 12; $m++) {
                                            $totalLiabilities["month_$m"] = ($totalLiabilities["month_$m"] ?? 0) + ($data[$item->id]["month_$m"] ?? 0);
                                        }
                                        $totalLiabilities['total'] = ($totalLiabilities['total'] ?? 0) + ($data[$item->id]['total'] ?? 0);
                                        $totalLiabilities['opening'] = ($totalLiabilities['opening'] ?? 0) + ($data[$item->id]['opening'] ?? 0);
                                    }
                                }
                            @endphp
                            <tr class="total-row">
                                <td><strong>L</strong></td>
                                <td><strong>TOTAL LIABILITIES (Kewajiban)</strong></td>
                                @for ($m = 1; $m <= 12; $m++)
                                    @php $val = $totalLiabilities["month_$m"] ?? 0; @endphp
                                    <td><strong>{{ formatAccounting($val) }}</strong></td>
                                @endfor
                                @php $total = $totalLiabilities['total'] ?? 0; @endphp
                                <td><strong>{{ formatAccounting($total) }}</strong></td>
                                @php $opening = $totalLiabilities['opening'] ?? 0; @endphp
                                <td><strong>{{ formatAccounting($opening) }}</strong></td>
                            </tr>

                            {{-- TOTAL EQUITY --}}
                            @php
                                $totalEquity = [];
                                foreach ($items as $item) {
                                    if (str_starts_with($item->kode, 'C')) {
                                        for ($m = 1; $m <= 12; $m++) {
                                            $totalEquity["month_$m"] = ($totalEquity["month_$m"] ?? 0) + ($data[$item->id]["month_$m"] ?? 0);
                                        }
                                        $totalEquity['total'] = ($totalEquity['total'] ?? 0) + ($data[$item->id]['total'] ?? 0);
                                        $totalEquity['opening'] = ($totalEquity['opening'] ?? 0) + ($data[$item->id]['opening'] ?? 0);
                                    }
                                }
                            @endphp
                            <tr class="total-row">
                                <td><strong>C</strong></td>
                                <td><strong>TOTAL EQUITY (Ekuitas)</strong></td>
                                @for ($m = 1; $m <= 12; $m++)
                                    @php $val = $totalEquity["month_$m"] ?? 0; @endphp
                                    <td><strong>{{ formatAccounting($val) }}</strong></td>
                                @endfor
                                @php $total = $totalEquity['total'] ?? 0; @endphp
                                <td><strong>{{ formatAccounting($total) }}</strong></td>
                                @php $opening = $totalEquity['opening'] ?? 0; @endphp
                                <td><strong>{{ formatAccounting($opening) }}</strong></td>
                            </tr>

                            {{-- SELISIH (A - L - C) --}}
                            @php
                                $selisih = [];
                                for ($m = 1; $m <= 12; $m++) {
                                    // Assets are positive, Liabilities & Equity are negative in accounting
                                    $selisih["month_$m"] = ($totalAssets["month_$m"] ?? 0) - abs($totalLiabilities["month_$m"] ?? 0) - abs($totalEquity["month_$m"] ?? 0);
                                }
                                $selisih['total'] = ($totalAssets['total'] ?? 0) - abs($totalLiabilities['total'] ?? 0) - abs($totalEquity['total'] ?? 0);
                                $selisih['opening'] = ($totalAssets['opening'] ?? 0) - abs($totalLiabilities['opening'] ?? 0) - abs($totalEquity['opening'] ?? 0);
                            @endphp
                            <tr class="total-row">
                                <td><strong></strong></td>
                                <td><strong>SELISIH</strong></td>
                                @for ($m = 1; $m <= 12; $m++)
                                    @php $val = $selisih["month_$m"] ?? 0; @endphp
                                    <td><strong>{{ formatAccounting($val) }}</strong></td>
                                @endfor
                                @php $total = $selisih['total'] ?? 0; @endphp
                                <td><strong>{{ formatAccounting($total) }}</strong></td>
                                @php $opening = $selisih['opening'] ?? 0; @endphp
                                <td><strong>{{ formatAccounting($opening) }}</strong></td>
                            </tr>

                            {{-- R1 GROUP --}}
                            @php
                                $r1Group = [];
                                foreach ($items as $item) {
                                    if (str_starts_with($item->kode, 'R1')) {
                                        for ($m = 1; $m <= 12; $m++) {
                                            $r1Group["month_$m"] = ($r1Group["month_$m"] ?? 0) + ($data[$item->id]["month_$m"] ?? 0);
                                        }
                                        $r1Group['total'] = ($r1Group['total'] ?? 0) + ($data[$item->id]['total'] ?? 0);
                                        $r1Group['opening'] = ($r1Group['opening'] ?? 0) + ($data[$item->id]['opening'] ?? 0);
                                    }
                                }
                            @endphp
                            <tr class="total-row">
                                <td><strong>R1</strong></td>
                                <td><strong>Pendapatan</strong></td>
                                @for ($m = 1; $m <= 12; $m++)
                                    @php $val = $r1Group["month_$m"] ?? 0; @endphp
                                    <td><strong>{{ formatAccounting($val) }}</strong></td>
                                @endfor
                                @php $total = $r1Group['total'] ?? 0; @endphp
                                <td><strong>{{ formatAccounting($total) }}</strong></td>
                                @php $opening = $r1Group['opening'] ?? 0; @endphp
                                <td><strong>{{ formatAccounting($opening) }}</strong></td>
                            </tr>

                            {{-- R2 GROUP --}}
                            @php
                                $r2Group = [];
                                foreach ($items as $item) {
                                    if (str_starts_with($item->kode, 'R2')) {
                                        for ($m = 1; $m <= 12; $m++) {
                                            $r2Group["month_$m"] = ($r2Group["month_$m"] ?? 0) + ($data[$item->id]["month_$m"] ?? 0);
                                        }
                                        $r2Group['total'] = ($r2Group['total'] ?? 0) + ($data[$item->id]['total'] ?? 0);
                                        $r2Group['opening'] = ($r2Group['opening'] ?? 0) + ($data[$item->id]['opening'] ?? 0);
                                    }
                                }
                            @endphp
                            <tr class="total-row">
                                <td><strong>R2</strong></td>
                                <td><strong>Pendapatan Lain-Lain</strong></td>
                                @for ($m = 1; $m <= 12; $m++)
                                    @php $val = $r2Group["month_$m"] ?? 0; @endphp
                                    <td><strong>{{ formatAccounting($val) }}</strong></td>
                                @endfor
                                @php $total = $r2Group['total'] ?? 0; @endphp
                                <td><strong>{{ formatAccounting($total) }}</strong></td>
                                @php $opening = $r2Group['opening'] ?? 0; @endphp
                                <td><strong>{{ formatAccounting($opening) }}</strong></td>
                            </tr>

                            {{-- TOTAL REVENUE --}}
                            @php
                                $totalRevenue = [];
                                for ($m = 1; $m <= 12; $m++) {
                                    $totalRevenue["month_$m"] = ($r1Group["month_$m"] ?? 0) + ($r2Group["month_$m"] ?? 0);
                                }
                                $totalRevenue['total'] = ($r1Group['total'] ?? 0) + ($r2Group['total'] ?? 0);
                                $totalRevenue['opening'] = ($r1Group['opening'] ?? 0) + ($r2Group['opening'] ?? 0);
                            @endphp
                            <tr class="total-row">
                                <td><strong></strong></td>
                                <td><strong>TOTAL REVENUE</strong></td>
                                @for ($m = 1; $m <= 12; $m++)
                                    @php $val = $totalRevenue["month_$m"] ?? 0; @endphp
                                    <td><strong>{{ formatAccounting($val) }}</strong></td>
                                @endfor
                                @php $total = $totalRevenue['total'] ?? 0; @endphp
                                <td><strong>{{ formatAccounting($total) }}</strong></td>
                                @php $opening = $totalRevenue['opening'] ?? 0; @endphp
                                <td><strong>{{ formatAccounting($opening) }}</strong></td>
                            </tr>

                            {{-- E11 GROUP --}}
                            @php
                                $e11Group = [];
                                foreach ($items as $item) {
                                    if (str_starts_with($item->kode, 'E11')) {
                                        for ($m = 1; $m <= 12; $m++) {
                                            $e11Group["month_$m"] = ($e11Group["month_$m"] ?? 0) + ($data[$item->id]["month_$m"] ?? 0);
                                        }
                                        $e11Group['total'] = ($e11Group['total'] ?? 0) + ($data[$item->id]['total'] ?? 0);
                                        $e11Group['opening'] = ($e11Group['opening'] ?? 0) + ($data[$item->id]['opening'] ?? 0);
                                    }
                                }
                            @endphp
                            <tr class="total-row">
                                <td><strong>E11</strong></td>
                                <td><strong>Beban Produksi</strong></td>
                                @for ($m = 1; $m <= 12; $m++)
                                    @php $val = $e11Group["month_$m"] ?? 0; @endphp
                                    <td><strong>{{ formatAccounting($val) }}</strong></td>
                                @endfor
                                @php $total = $e11Group['total'] ?? 0; @endphp
                                <td><strong>{{ formatAccounting($total) }}</strong></td>
                                @php $opening = $e11Group['opening'] ?? 0; @endphp
                                <td><strong>{{ formatAccounting($opening) }}</strong></td>
                            </tr>

                            {{-- E2 GROUP --}}
                            @php
                                $e2Group = [];
                                foreach ($items as $item) {
                                    if (str_starts_with($item->kode, 'E2')) {
                                        for ($m = 1; $m <= 12; $m++) {
                                            $e2Group["month_$m"] = ($e2Group["month_$m"] ?? 0) + ($data[$item->id]["month_$m"] ?? 0);
                                        }
                                        $e2Group['total'] = ($e2Group['total'] ?? 0) + ($data[$item->id]['total'] ?? 0);
                                        $e2Group['opening'] = ($e2Group['opening'] ?? 0) + ($data[$item->id]['opening'] ?? 0);
                                    }
                                }
                            @endphp
                            <tr class="total-row">
                                <td><strong>E2</strong></td>
                                <td><strong>Beban Usaha</strong></td>
                                @for ($m = 1; $m <= 12; $m++)
                                    @php $val = $e2Group["month_$m"] ?? 0; @endphp
                                    <td><strong>{{ formatAccounting($val) }}</strong></td>
                                @endfor
                                @php $total = $e2Group['total'] ?? 0; @endphp
                                <td><strong>{{ formatAccounting($total) }}</strong></td>
                                @php $opening = $e2Group['opening'] ?? 0; @endphp
                                <td><strong>{{ formatAccounting($opening) }}</strong></td>
                            </tr>

                            {{-- E3 GROUP --}}
                            @php
                                $e3Group = [];
                                foreach ($items as $item) {
                                    if (str_starts_with($item->kode, 'E3')) {
                                        for ($m = 1; $m <= 12; $m++) {
                                            $e3Group["month_$m"] = ($e3Group["month_$m"] ?? 0) + ($data[$item->id]["month_$m"] ?? 0);
                                        }
                                        $e3Group['total'] = ($e3Group['total'] ?? 0) + ($data[$item->id]['total'] ?? 0);
                                        $e3Group['opening'] = ($e3Group['opening'] ?? 0) + ($data[$item->id]['opening'] ?? 0);
                                    }
                                }
                            @endphp
                            <tr class="total-row">
                                <td><strong>E3</strong></td>
                                <td><strong>Beban Lain-lain</strong></td>
                                @for ($m = 1; $m <= 12; $m++)
                                    @php $val = $e3Group["month_$m"] ?? 0; @endphp
                                    <td><strong>{{ formatAccounting($val) }}</strong></td>
                                @endfor
                                @php $total = $e3Group['total'] ?? 0; @endphp
                                <td><strong>{{ formatAccounting($total) }}</strong></td>
                                @php $opening = $e3Group['opening'] ?? 0; @endphp
                                <td><strong>{{ formatAccounting($opening) }}</strong></td>
                            </tr>

                            {{-- TOTAL EXPENSES (excluding E9) --}}
                            @php
                                $totalExpenses = [];
                                for ($m = 1; $m <= 12; $m++) {
                                    $totalExpenses["month_$m"] = ($e11Group["month_$m"] ?? 0) + ($e2Group["month_$m"] ?? 0) + ($e3Group["month_$m"] ?? 0);
                                }
                                $totalExpenses['total'] = ($e11Group['total'] ?? 0) + ($e2Group['total'] ?? 0) + ($e3Group['total'] ?? 0);
                                $totalExpenses['opening'] = ($e11Group['opening'] ?? 0) + ($e2Group['opening'] ?? 0) + ($e3Group['opening'] ?? 0);
                            @endphp
                            <tr class="total-row">
                                <td><strong></strong></td>
                                <td><strong>TOTAL EXPENSES</strong></td>
                                @for ($m = 1; $m <= 12; $m++)
                                    @php $val = $totalExpenses["month_$m"] ?? 0; @endphp
                                    <td><strong>{{ formatAccounting($val) }}</strong></td>
                                @endfor
                                @php $total = $totalExpenses['total'] ?? 0; @endphp
                                <td><strong>{{ formatAccounting($total) }}</strong></td>
                                @php $opening = $totalExpenses['opening'] ?? 0; @endphp
                                <td><strong>{{ formatAccounting($opening) }}</strong></td>
                            </tr>

                            {{-- (SURPLUS) / DEFICIT --}}
                            @php
                                $surplusDeficit = [];
                                for ($m = 1; $m <= 12; $m++) {
                                    // Revenue is credit (negative), Expenses are debit (positive)
                                    $surplusDeficit["month_$m"] = abs($totalRevenue["month_$m"] ?? 0) - abs($totalExpenses["month_$m"] ?? 0);
                                }
                                $surplusDeficit['total'] = abs($totalRevenue['total'] ?? 0) - abs($totalExpenses['total'] ?? 0);
                                $surplusDeficit['opening'] = abs($totalRevenue['opening'] ?? 0) - abs($totalExpenses['opening'] ?? 0);
                            @endphp
                            <tr class="total-row">
                                <td><strong></strong></td>
                                <td><strong>(SURPLUS) / DEFICIT</strong></td>
                                @for ($m = 1; $m <= 12; $m++)
                                    @php $val = $surplusDeficit["month_$m"] ?? 0; @endphp
                                    <td><strong>{{ formatAccounting($val) }}</strong></td>
                                @endfor
                                @php $total = $surplusDeficit['total'] ?? 0; @endphp
                                <td><strong>{{ formatAccounting($total) }}</strong></td>
                                @php $opening = $surplusDeficit['opening'] ?? 0; @endphp
                                <td><strong>{{ formatAccounting($opening) }}</strong></td>
                            </tr>

                            {{-- E9 GROUP --}}
                            @php
                                $e9Group = [];
                                foreach ($items as $item) {
                                    if (str_starts_with($item->kode, 'E9')) {
                                        for ($m = 1; $m <= 12; $m++) {
                                            $e9Group["month_$m"] = ($e9Group["month_$m"] ?? 0) + ($data[$item->id]["month_$m"] ?? 0);
                                        }
                                        $e9Group['total'] = ($e9Group['total'] ?? 0) + ($data[$item->id]['total'] ?? 0);
                                        $e9Group['opening'] = ($e9Group['opening'] ?? 0) + ($data[$item->id]['opening'] ?? 0);
                                    }
                                }
                            @endphp
                            <tr class="total-row">
                                <td><strong>E9</strong></td>
                                <td><strong>Beban Pajak Penghasilan</strong></td>
                                @for ($m = 1; $m <= 12; $m++)
                                    @php $val = $e9Group["month_$m"] ?? 0; @endphp
                                    <td><strong>{{ formatAccounting($val) }}</strong></td>
                                @endfor
                                @php $total = $e9Group['total'] ?? 0; @endphp
                                <td><strong>{{ formatAccounting($total) }}</strong></td>
                                @php $opening = $e9Group['opening'] ?? 0; @endphp
                                <td><strong>{{ formatAccounting($opening) }}</strong></td>
                            </tr>

                            {{-- NET (SURPLUS) / DEFICIT --}}
                            @php
                                $netSurplusDeficit = [];
                                for ($m = 1; $m <= 12; $m++) {
                                    $netSurplusDeficit["month_$m"] = ($surplusDeficit["month_$m"] ?? 0) - abs($e9Group["month_$m"] ?? 0);
                                }
                                $netSurplusDeficit['total'] = ($surplusDeficit['total'] ?? 0) - abs($e9Group['total'] ?? 0);
                                $netSurplusDeficit['opening'] = ($surplusDeficit['opening'] ?? 0) - abs($e9Group['opening'] ?? 0);
                            @endphp
                            <tr class="total-row">
                                <td><strong></strong></td>
                                <td><strong>NET (SURPLUS) / DEFICIT</strong></td>
                                @for ($m = 1; $m <= 12; $m++)
                                    @php $val = $netSurplusDeficit["month_$m"] ?? 0; @endphp
                                    <td><strong>{{ formatAccounting($val) }}</strong></td>
                                @endfor
                                @php $total = $netSurplusDeficit['total'] ?? 0; @endphp
                                <td><strong>{{ formatAccounting($total) }}</strong></td>
                                @php $opening = $netSurplusDeficit['opening'] ?? 0; @endphp
                                <td><strong>{{ formatAccounting($opening) }}</strong></td>
                            </tr>

                        </tbody>



                    </table>
                </div>
            </div>

        </div>
    </div>
@endsection
