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
                                            echo '<td>' . ($val == 0 ? '' : number_format($val, 0, ',', '.')) . '</td>';
                                        }
                                        
                                        // Total tahun berjalan
                                        $total = $data[$item->id]['total'] ?? 0;
                                        echo '<td>' . ($total == 0 ? '' : number_format($total, 0, ',', '.')) . '</td>';
                                        
                                        // Saldo awal
                                        $start = $data[$item->id]['opening'] ?? 0;
                                        echo '<td>' . ($start == 0 ? '' : number_format($start, 0, ',', '.')) . '</td>';
                                        
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
                                        <td>{{ $val == 0 ? '' : number_format($val, 0, ',', '.') }}</td>
                                    @endfor
                                    @php
                                        $total = $data[$pbItem->id]['total'] ?? 0;
                                        $start = $data[$pbItem->id]['opening'] ?? 0;
                                    @endphp
                                    <td>{{ $total == 0 ? '' : number_format($total, 0, ',', '.') }}</td>
                                    <td>{{ $start == 0 ? '' : number_format($start, 0, ',', '.') }}</td>
                                </tr>
                            @endif
                            @endforeach

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
                                    <td><strong>{{ $val == 0 ? '' : number_format($val, 0, ',', '.') }}</strong></td>
                                @endfor
                                @php $total = $totalAssets['total'] ?? 0; @endphp
                                <td><strong>{{ $total == 0 ? '' : number_format($total, 0, ',', '.') }}</strong></td>
                                @php $opening = $totalAssets['opening'] ?? 0; @endphp
                                <td><strong>{{ $opening == 0 ? '' : number_format($opening, 0, ',', '.') }}</strong></td>
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
                                    <td><strong>{{ $val == 0 ? '' : number_format($val, 0, ',', '.') }}</strong></td>
                                @endfor
                                @php $total = $totalLiabilities['total'] ?? 0; @endphp
                                <td><strong>{{ $total == 0 ? '' : number_format($total, 0, ',', '.') }}</strong></td>
                                @php $opening = $totalLiabilities['opening'] ?? 0; @endphp
                                <td><strong>{{ $opening == 0 ? '' : number_format($opening, 0, ',', '.') }}</strong></td>
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
                                    <td><strong>{{ $val == 0 ? '' : number_format($val, 0, ',', '.') }}</strong></td>
                                @endfor
                                @php $total = $totalEquity['total'] ?? 0; @endphp
                                <td><strong>{{ $total == 0 ? '' : number_format($total, 0, ',', '.') }}</strong></td>
                                @php $opening = $totalEquity['opening'] ?? 0; @endphp
                                <td><strong>{{ $opening == 0 ? '' : number_format($opening, 0, ',', '.') }}</strong></td>
                            </tr>

                            {{-- SELISIH (A - L - C) --}}
                            @php
                                $selisih = [];
                                for ($m = 1; $m <= 12; $m++) {
                                    $selisih["month_$m"] = ($totalAssets["month_$m"] ?? 0) - ($totalLiabilities["month_$m"] ?? 0) - ($totalEquity["month_$m"] ?? 0);
                                }
                                $selisih['total'] = ($totalAssets['total'] ?? 0) - ($totalLiabilities['total'] ?? 0) - ($totalEquity['total'] ?? 0);
                                $selisih['opening'] = ($totalAssets['opening'] ?? 0) - ($totalLiabilities['opening'] ?? 0) - ($totalEquity['opening'] ?? 0);
                            @endphp
                            <tr class="total-row">
                                <td><strong></strong></td>
                                <td><strong>SELISIH</strong></td>
                                @for ($m = 1; $m <= 12; $m++)
                                    @php $val = $selisih["month_$m"] ?? 0; @endphp
                                    <td><strong>{{ $val == 0 ? '' : number_format($val, 0, ',', '.') }}</strong></td>
                                @endfor
                                @php $total = $selisih['total'] ?? 0; @endphp
                                <td><strong>{{ $total == 0 ? '' : number_format($total, 0, ',', '.') }}</strong></td>
                                @php $opening = $selisih['opening'] ?? 0; @endphp
                                <td><strong>{{ $opening == 0 ? '' : number_format($opening, 0, ',', '.') }}</strong></td>
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
                                    <td><strong>{{ $val == 0 ? '' : number_format($val, 0, ',', '.') }}</strong></td>
                                @endfor
                                @php $total = $r1Group['total'] ?? 0; @endphp
                                <td><strong>{{ $total == 0 ? '' : number_format($total, 0, ',', '.') }}</strong></td>
                                @php $opening = $r1Group['opening'] ?? 0; @endphp
                                <td><strong>{{ $opening == 0 ? '' : number_format($opening, 0, ',', '.') }}</strong></td>
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
                                    <td><strong>{{ $val == 0 ? '' : number_format($val, 0, ',', '.') }}</strong></td>
                                @endfor
                                @php $total = $r2Group['total'] ?? 0; @endphp
                                <td><strong>{{ $total == 0 ? '' : number_format($total, 0, ',', '.') }}</strong></td>
                                @php $opening = $r2Group['opening'] ?? 0; @endphp
                                <td><strong>{{ $opening == 0 ? '' : number_format($opening, 0, ',', '.') }}</strong></td>
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
                                    <td><strong>{{ $val == 0 ? '' : number_format($val, 0, ',', '.') }}</strong></td>
                                @endfor
                                @php $total = $totalRevenue['total'] ?? 0; @endphp
                                <td><strong>{{ $total == 0 ? '' : number_format($total, 0, ',', '.') }}</strong></td>
                                @php $opening = $totalRevenue['opening'] ?? 0; @endphp
                                <td><strong>{{ $opening == 0 ? '' : number_format($opening, 0, ',', '.') }}</strong></td>
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
                                    <td><strong>{{ $val == 0 ? '' : number_format($val, 0, ',', '.') }}</strong></td>
                                @endfor
                                @php $total = $e11Group['total'] ?? 0; @endphp
                                <td><strong>{{ $total == 0 ? '' : number_format($total, 0, ',', '.') }}</strong></td>
                                @php $opening = $e11Group['opening'] ?? 0; @endphp
                                <td><strong>{{ $opening == 0 ? '' : number_format($opening, 0, ',', '.') }}</strong></td>
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
                                    <td><strong>{{ $val == 0 ? '' : number_format($val, 0, ',', '.') }}</strong></td>
                                @endfor
                                @php $total = $e2Group['total'] ?? 0; @endphp
                                <td><strong>{{ $total == 0 ? '' : number_format($total, 0, ',', '.') }}</strong></td>
                                @php $opening = $e2Group['opening'] ?? 0; @endphp
                                <td><strong>{{ $opening == 0 ? '' : number_format($opening, 0, ',', '.') }}</strong></td>
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
                                    <td><strong>{{ $val == 0 ? '' : number_format($val, 0, ',', '.') }}</strong></td>
                                @endfor
                                @php $total = $e3Group['total'] ?? 0; @endphp
                                <td><strong>{{ $total == 0 ? '' : number_format($total, 0, ',', '.') }}</strong></td>
                                @php $opening = $e3Group['opening'] ?? 0; @endphp
                                <td><strong>{{ $opening == 0 ? '' : number_format($opening, 0, ',', '.') }}</strong></td>
                            </tr>

                            {{-- TOTAL EXPENSES --}}
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
                                    <td><strong>{{ $val == 0 ? '' : number_format($val, 0, ',', '.') }}</strong></td>
                                @endfor
                                @php $total = $totalExpenses['total'] ?? 0; @endphp
                                <td><strong>{{ $total == 0 ? '' : number_format($total, 0, ',', '.') }}</strong></td>
                                @php $opening = $totalExpenses['opening'] ?? 0; @endphp
                                <td><strong>{{ $opening == 0 ? '' : number_format($opening, 0, ',', '.') }}</strong></td>
                            </tr>

                            {{-- (SURPLUS) / DEFICIT --}}
                            @php
                                $surplusDeficit = [];
                                for ($m = 1; $m <= 12; $m++) {
                                    $surplusDeficit["month_$m"] = ($totalRevenue["month_$m"] ?? 0) - ($totalExpenses["month_$m"] ?? 0);
                                }
                                $surplusDeficit['total'] = ($totalRevenue['total'] ?? 0) - ($totalExpenses['total'] ?? 0);
                                $surplusDeficit['opening'] = ($totalRevenue['opening'] ?? 0) - ($totalExpenses['opening'] ?? 0);
                            @endphp
                            <tr class="total-row">
                                <td><strong></strong></td>
                                <td><strong>(SURPLUS) / DEFICIT</strong></td>
                                @for ($m = 1; $m <= 12; $m++)
                                    @php $val = $surplusDeficit["month_$m"] ?? 0; @endphp
                                    <td><strong>{{ $val == 0 ? '' : number_format($val, 0, ',', '.') }}</strong></td>
                                @endfor
                                @php $total = $surplusDeficit['total'] ?? 0; @endphp
                                <td><strong>{{ $total == 0 ? '' : number_format($total, 0, ',', '.') }}</strong></td>
                                @php $opening = $surplusDeficit['opening'] ?? 0; @endphp
                                <td><strong>{{ $opening == 0 ? '' : number_format($opening, 0, ',', '.') }}</strong></td>
                            </tr>

                            {{-- E9 - BEBAN PAJAK PENGHASILAN --}}
                            @foreach ($items as $item)
                                @if ($item->kode == 'E9')
                                    <tr class="level-{{ $item->level }}-row">
                                        <td>{{ $item->kode }}</td>
                                        <td>
                                            <div class="tb-text level-{{ $item->level }}">
                                                {{ $item->keterangan }}
                                            </div>
                                        </td>
                                        @for ($m = 1; $m <= 12; $m++)
                                            @php $val = $data[$item->id]["month_$m"] ?? 0; @endphp
                                            <td>{{ $val == 0 ? '' : number_format($val, 0, ',', '.') }}</td>
                                        @endfor
                                        @php $total = $data[$item->id]['total'] ?? 0; @endphp
                                        <td>{{ $total == 0 ? '' : number_format($total, 0, ',', '.') }}</td>
                                        @php $opening = $data[$item->id]['opening'] ?? 0; @endphp
                                        <td>{{ $opening == 0 ? '' : number_format($opening, 0, ',', '.') }}</td>
                                    </tr>
                                @endif
                            @endforeach

                            {{-- NET (SURPLUS) / DEFICIT --}}
                            @php
                                $netSurplusDeficit = [];
                                $totalE9 = [];
                                foreach ($items as $item) {
                                    if ($item->kode == 'E9') {
                                        for ($m = 1; $m <= 12; $m++) {
                                            $totalE9["month_$m"] = $data[$item->id]["month_$m"] ?? 0;
                                        }
                                        $totalE9['total'] = $data[$item->id]['total'] ?? 0;
                                        $totalE9['opening'] = $data[$item->id]['opening'] ?? 0;
                                        break;
                                    }
                                }
                                for ($m = 1; $m <= 12; $m++) {
                                    $netSurplusDeficit["month_$m"] = ($surplusDeficit["month_$m"] ?? 0) - ($totalE9["month_$m"] ?? 0);
                                }
                                $netSurplusDeficit['total'] = ($surplusDeficit['total'] ?? 0) - ($totalE9['total'] ?? 0);
                                $netSurplusDeficit['opening'] = ($surplusDeficit['opening'] ?? 0) - ($totalE9['opening'] ?? 0);
                            @endphp
                            <tr class="total-row">
                                <td><strong></strong></td>
                                <td><strong>NET (SURPLUS) / DEFICIT</strong></td>
                                @for ($m = 1; $m <= 12; $m++)
                                    @php $val = $netSurplusDeficit["month_$m"] ?? 0; @endphp
                                    <td><strong>{{ $val == 0 ? '' : number_format($val, 0, ',', '.') }}</strong></td>
                                @endfor
                                @php $total = $netSurplusDeficit['total'] ?? 0; @endphp
                                <td><strong>{{ $total == 0 ? '' : number_format($total, 0, ',', '.') }}</strong></td>
                                @php $opening = $netSurplusDeficit['opening'] ?? 0; @endphp
                                <td><strong>{{ $opening == 0 ? '' : number_format($opening, 0, ',', '.') }}</strong></td>
                            </tr>

                        </tbody>



                    </table>
                </div>
            </div>

        </div>
    </div>
@endsection
