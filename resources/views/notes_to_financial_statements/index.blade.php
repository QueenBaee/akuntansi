@extends('layouts.app')

@section('title', 'Notes to Financial Statements')

@section('page-header')
    <div class="page-pretitle">Laporan</div>
    <h2 class="page-title">Notes to Financial Statements Tahun {{ $year }}</h2>
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
                                        echo '<td><strong>Total Harga Perolehan</strong></td>';
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
                                        echo '<td><strong>Total Akumulasi Penyusutan</strong></td>';
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
                                        echo '<td><strong>Total ' . $groupName . '</strong></td>';
                                        for ($m = 1; $m <= 12; $m++) {
                                            echo '<td><strong>' . formatAccounting($groupTotals["month_$m"]) . '</strong></td>';
                                        }
                                        echo '<td><strong>' . formatAccounting($groupTotals['total']) . '</strong></td>';
                                        echo '<td><strong>' . formatAccounting($groupTotals['opening']) . '</strong></td>';
                                        echo '</tr>';
                                    }
                                }
                                
                                $accountGroups = [
                                    '1. KAS & SETARA KAS' => ['A11-01', 'A11-21', 'A11-22', 'A11-23'],
                                    '2. PIUTANG USAHA' => ['A12-01', 'A12-02', 'A12-03'],
                                    '3. PIUTANG LAIN-LAIN' => ['A13-01', 'A13-02', 'A13-03', 'A13-98', 'A13-99'],
                                    'INVESTASI JANGKA PENDEK' => ['A14-01', 'A14-02', 'A14-99'],
                                    'PERSEDIAAN' => ['A15-01', 'A15-02', 'A15-99'],
                                    '4. BIAYA DIBAYAR DI MUKA' => ['A16-01', 'A16-02'],
                                    '5. UANG MUKA PAJAK' => ['A17-01', 'A17-02', 'A17-03', 'A17-04', 'A17-11'],
                                    'ASET LANCAR LAINNYA' => ['A18-01', 'A18-02'],
                                    'PIUTANG LAIN-LAIN - JANGKA PANJANG' => ['A21-01', 'A21-02'],
                                    'INVESTASI JANGKA PANJANG' => ['A22-01', 'A22-02'],
                                    '6. ASET TETAP BERSIH - HARGA PEROLEHAN' => ['A23-01', 'A23-02', 'A23-03', 'A23-04', 'A23-99'],
                                    '6. ASET TETAP BERSIH - AKUMULASI PENYUSUTAN' => ['A24-01', 'A24-02', 'A24-03'],
                                    'ASET TIDAK BERWUJUD' => ['A25-01', 'A25-02'],
                                    '7. ASET TIDAK LANCAR LAINNYA' => ['A26-01', 'A26-02'],
                                    '8. UTANG USAHA' => ['L11-01', 'L11-99'],
                                    'UTANG LAIN-LAIN' => ['L12-01', 'L12-02'],
                                    '9. BIAYA YANG HARUS DIBAYAR' => ['L13-01', 'L13-02', 'L13-03', 'L13-04', 'L13-05', 'L13-06', 'L13-99'],
                                    '10. UTANG PAJAK' => ['L14-01', 'L14-02', 'L14-03', 'L14-04', 'L14-05', 'L14-11', 'L14-12'],
                                    'UANG MUKA PENDAPATAN' => ['L15-01', 'L15-02', 'L15-99'],
                                    'PINJAMAN JANGKA PENDEK' => ['L16-01', 'L16-02'],
                                    '11. KEWAJIBAN IMBALAN PASCA KERJA' => ['L17-01', 'L17-02'],
                                    'UTANG USAHA - JK. PANJANG' => ['L21-01', 'L21-02'],
                                    'UTANG LAIN-LAIN - JK. PANJANG' => ['L22-01', 'L22-02'],
                                    'PINJAMAN JANGKA PANJANG' => ['L23-01', 'L23-02'],
                                    'KEWAJIBAN IMBALAN PASCA KERJA - JK. PANJANG' => ['L24-01', 'L24-02'],
                                    'KEWAJIBAN JANGKA PANJANG LAINNYA' => ['L25-01', 'L25-02'],
                                    '12. MODAL DISETOR' => ['C11-01', 'C11-02', 'C11-03'],
                                    '13. PENDAPATAN' => ['R11-01', 'R11-02', 'R11-03'],
                                    '14. BEBAN PRODUKSI' => ['E11-01', 'E11-02', 'E11-03', 'E11-04', 'E11-05', 'E11-06', 'E11-07'],
                                    'PEMASARAN' => ['E21-01', 'E21-02'],
                                    '15. ADMINISTRASI & UMUM' => ['E22-01', 'E22-02', 'E22-03', 'E22-04', 'E22-05', 'E22-06', 'E22-07', 'E22-08', 'E22-09', 'E22-10', 'E22-11', 'E22-89', 'E22-96', 'E22-97', 'E22-98', 'E22-99'],
                                    '16. PENDAPATAN LAIN-LAIN' => ['R21-01', 'R21-02', 'R21-99'],
                                    '17. BEBAN LAIN-LAIN' => ['E31-01', 'E31-02', 'E31-03']
                                ];
                                
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