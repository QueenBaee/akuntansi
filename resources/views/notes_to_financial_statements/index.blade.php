@extends('layouts.app')

@section('title', 'Notes to Financial Statements')

@section('page-header')
    <div class="page-pretitle">Laporan</div>
    <h2 class="page-title">Catatan Atas Laporan Keuangan {{ $year }}</h2>
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
                            text-align: left !important;
                            font-weight: 600 !important;
                        }

                        .no-equal-width td:nth-child(2),
                        .no-equal-width th:nth-child(2) {
                            min-width: 200px !important;
                            width: auto !important;
                            text-align: left !important;
                            white-space: nowrap !important;
                        }

                        .no-equal-width td:not(:nth-child(1)):not(:nth-child(2)),
                        .no-equal-width th:not(:nth-child(1)):not(:nth-child(2)) {
                            text-align: right !important;
                            min-width: 80px !important;
                            width: 80px !important;
                        }

                        .group-header {
                           
                            font-weight: bold;
                        }

                        .group-total {
                  
                            font-weight: bold;
                        }
                    </style>

                    <table class="table table-bordered table-striped no-equal-width">
                        <thead>
                            <tr>
                                <th style="text-align:center">Kode</th>
                                <th style="text-align:center">Keterangan</th>
                                @for ($m = 1; $m <= 12; $m++)
                                    <th style="text-align:center">{{ date('j M Y', mktime(0, 0, 0, $m+1, 0, $year)) }}</th>
                                @endfor
                                <th style="text-align:center">{{ $year }}</th>
                                <th style="text-align:center">{{ $year - 1 }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                function renderNotesGroup($groupName, $accountCodes, $items, $data) {
                                    $groupTotals = [];
                                    $hasAccounts = false;
                                    
                                    // Check if group has any accounts
                                    foreach ($accountCodes as $code) {
                                        $item = $items->where('kode', $code)->first();
                                        if ($item) {
                                            $hasAccounts = true;
                                            break;
                                        }
                                    }
                                    
                                    if (!$hasAccounts) return;
                                    
                                    // Group header
                                    echo '<tr class="group-header">';
                                    echo '<td colspan="16">' . $groupName . '</td>';
                                    echo '</tr>';
                                    
                                    // Initialize group totals
                                    for ($m = 1; $m <= 12; $m++) {
                                        $groupTotals["month_$m"] = 0;
                                    }
                                    $groupTotals['total'] = 0;
                                    $groupTotals['opening'] = 0;
                                    
                                    // Special handling for Net Fixed Assets
                                    if (str_contains($groupName, 'HARGA PEROLEHAN')) {
                                        foreach ($accountCodes as $code) {
                                            $item = $items->where('kode', $code)->first();
                                            if ($item) {
                                                $val = $data[$item->id]['total'] ?? 0;
                                                echo '<tr>';
                                                echo '<td>' . $item->kode . '</td>';
                                                echo '<td><div class="tb-text" style="margin-left: 20px;">' . $item->keterangan . '</div></td>';
                                                
                                                for ($m = 1; $m <= 12; $m++) {
                                                    $monthVal = $data[$item->id]["month_$m"] ?? 0;
                                                    echo '<td>' . formatAccounting(abs($monthVal)) . '</td>';
                                                    $groupTotals["month_$m"] += abs($monthVal);
                                                }
                                                
                                                echo '<td>' . formatAccounting(abs($val)) . '</td>';
                                                echo '<td>' . formatAccounting(abs($data[$item->id]['opening'] ?? 0)) . '</td>';
                                                echo '</tr>';
                                                
                                                $groupTotals['total'] += abs($val);
                                                $groupTotals['opening'] += abs($data[$item->id]['opening'] ?? 0);
                                            }
                                        }
                                        
                                        // Subtotal for acquisition cost
                                        echo '<tr class="group-total">';
                                        echo '<td></td>';
                                        echo '<td>&nbsp;</td>';
                                        for ($m = 1; $m <= 12; $m++) {
                                            echo '<td><strong>' . formatAccounting($groupTotals["month_$m"]) . '</strong></td>';
                                        }
                                        echo '<td><strong>' . formatAccounting($groupTotals['total']) . '</strong></td>';
                                        echo '<td><strong>' . formatAccounting($groupTotals['opening']) . '</strong></td>';
                                        echo '</tr>';
                                    } elseif (str_contains($groupName, 'AKUMULASI PENYUSUTAN')) {
                                        foreach ($accountCodes as $code) {
                                            $item = $items->where('kode', $code)->first();
                                            if ($item) {
                                                $val = $data[$item->id]['total'] ?? 0;
                                                echo '<tr>';
                                                echo '<td>' . $item->kode . '</td>';
                                                echo '<td><div class="tb-text" style="margin-left: 20px;">' . $item->keterangan . '</div></td>';
                                                
                                                for ($m = 1; $m <= 12; $m++) {
                                                    $monthVal = $data[$item->id]["month_$m"] ?? 0;
                                                    echo '<td>(' . formatAccounting(abs($monthVal)) . ')</td>';
                                                    $groupTotals["month_$m"] += abs($monthVal);
                                                }
                                                
                                                echo '<td>(' . formatAccounting(abs($val)) . ')</td>';
                                                echo '<td>(' . formatAccounting(abs($data[$item->id]['opening'] ?? 0)) . ')</td>';
                                                echo '</tr>';
                                                
                                                $groupTotals['total'] += abs($val);
                                                $groupTotals['opening'] += abs($data[$item->id]['opening'] ?? 0);
                                            }
                                        }
                                        
                                        // Subtotal for accumulated depreciation
                                        echo '<tr class="group-total">';
                                        echo '<td></td>';
                                        echo '<td>&nbsp;</td>';
                                        for ($m = 1; $m <= 12; $m++) {
                                            echo '<td><strong>(' . formatAccounting($groupTotals["month_$m"]) . ')</strong></td>';
                                        }
                                        echo '<td><strong>(' . formatAccounting($groupTotals['total']) . ')</strong></td>';
                                        echo '<td><strong>(' . formatAccounting($groupTotals['opening']) . ')</strong></td>';
                                        echo '</tr>';
                                    } else {
                                        // Regular groups
                                        foreach ($accountCodes as $code) {
                                            $item = $items->where('kode', $code)->first();
                                            if ($item) {
                                                $val = $data[$item->id]['total'] ?? 0;
                                                echo '<tr>';
                                                echo '<td>' . $item->kode . '</td>';
                                                echo '<td><div class="tb-text" style="margin-left: 20px;">' . $item->keterangan . '</div></td>';
                                                
                                                for ($m = 1; $m <= 12; $m++) {
                                                    $monthVal = $data[$item->id]["month_$m"] ?? 0;
                                                    echo '<td>' . formatAccounting(abs($monthVal)) . '</td>';
                                                    $groupTotals["month_$m"] += abs($monthVal);
                                                }
                                                
                                                echo '<td>' . formatAccounting(abs($val)) . '</td>';
                                                echo '<td>' . formatAccounting(abs($data[$item->id]['opening'] ?? 0)) . '</td>';
                                                echo '</tr>';
                                                
                                                $groupTotals['total'] += abs($val);
                                                $groupTotals['opening'] += abs($data[$item->id]['opening'] ?? 0);
                                            }
                                        }
                                        
                                        // Group total
                                        echo '<tr class="group-total">';
                                        echo '<td></td>';
                                        echo '<td>&nbsp;</td>';
                                        for ($m = 1; $m <= 12; $m++) {
                                            echo '<td><strong>' . formatAccounting($groupTotals["month_$m"]) . '</strong></td>';
                                        }
                                        echo '<td><strong>' . formatAccounting($groupTotals['total']) . '</strong></td>';
                                        echo '<td><strong>' . formatAccounting($groupTotals['opening']) . '</strong></td>';
                                        echo '</tr>';
                                    }
                                }
                                
                                // Use account groups from controller
                                
                                foreach ($accountGroups as $groupName => $accountCodes) {
                                    renderNotesGroup($groupName, $accountCodes, $items, $data);
                                }
                                
                                // Calculate Net Book Value for Fixed Assets
                                $acquisitionTotals = [];
                                $depreciationTotals = [];
                                
                                for ($m = 1; $m <= 12; $m++) {
                                    $acquisitionTotals["month_$m"] = 0;
                                    $depreciationTotals["month_$m"] = 0;
                                }
                                $acquisitionTotals['total'] = 0;
                                $acquisitionTotals['opening'] = 0;
                                $depreciationTotals['total'] = 0;
                                $depreciationTotals['opening'] = 0;
                                
                                foreach (['A23-01', 'A23-02', 'A23-03', 'A23-04', 'A23-99'] as $code) {
                                    $item = $items->where('kode', $code)->first();
                                    if ($item && isset($data[$item->id])) {
                                        for ($m = 1; $m <= 12; $m++) {
                                            $acquisitionTotals["month_$m"] += abs($data[$item->id]["month_$m"] ?? 0);
                                        }
                                        $acquisitionTotals['total'] += abs($data[$item->id]['total'] ?? 0);
                                        $acquisitionTotals['opening'] += abs($data[$item->id]['opening'] ?? 0);
                                    }
                                }
                                
                                foreach (['A24-01', 'A24-02', 'A24-03'] as $code) {
                                    $item = $items->where('kode', $code)->first();
                                    if ($item && isset($data[$item->id])) {
                                        for ($m = 1; $m <= 12; $m++) {
                                            $depreciationTotals["month_$m"] += abs($data[$item->id]["month_$m"] ?? 0);
                                        }
                                        $depreciationTotals['total'] += abs($data[$item->id]['total'] ?? 0);
                                        $depreciationTotals['opening'] += abs($data[$item->id]['opening'] ?? 0);
                                    }
                                }
                                
                                if ($acquisitionTotals['total'] > 0 || $depreciationTotals['total'] > 0) {
                                    echo '<tr class="group-total">';
                                    echo '<td></td>';
                                    echo '<td><strong>NILAI BUKU</strong></td>';
                                    for ($m = 1; $m <= 12; $m++) {
                                        $netBookValue = $acquisitionTotals["month_$m"] - $depreciationTotals["month_$m"];
                                        echo '<td><strong>' . formatAccounting($netBookValue) . '</strong></td>';
                                    }
                                    $netBookValueTotal = $acquisitionTotals['total'] - $depreciationTotals['total'];
                                    $netBookValueOpening = $acquisitionTotals['opening'] - $depreciationTotals['opening'];
                                    echo '<td><strong>' . formatAccounting($netBookValueTotal) . '</strong></td>';
                                    echo '<td><strong>' . formatAccounting($netBookValueOpening) . '</strong></td>';
                                    echo '</tr>';
                                }
                            @endphp
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection