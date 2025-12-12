@extends('layouts.app')

@section('title', 'Laporan Posisi Keuangan')

@section('page-header')
    <div class="page-pretitle">Laporan</div>
    <h2 class="page-title">Laporan Posisi Keuangan Tahun {{ $year }}</h2>
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
                                    <th>{{ date('j M Y', mktime(0, 0, 0, $m+1, 0, $year)) }}</th>
                                @endfor
                                <th>{{ $year }}</th>
                                <th>{{ $year - 1 }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                function calculateGroupTotal($accountCodes, $items, $data) {
                                    $groupTotals = [];
                                    for ($m = 1; $m <= 12; $m++) {
                                        $groupTotals["month_$m"] = 0;
                                    }
                                    $groupTotals['total'] = 0;
                                    $groupTotals['opening'] = 0;
                                    
                                    foreach ($accountCodes as $code) {
                                        $item = $items->where('kode', $code)->first();
                                        if ($item) {
                                            $opening = abs($data[$item->id]['opening'] ?? 0);
                                            $groupTotals['opening'] += $opening;
                                            
                                            for ($m = 1; $m <= 12; $m++) {
                                                $monthVal = abs($data[$item->id]["month_$m"] ?? 0);
                                                // Only use month value if different from opening
                                                $groupTotals["month_$m"] += ($monthVal != $opening) ? $monthVal : $opening;
                                            }
                                            $groupTotals['total'] += abs($data[$item->id]['total'] ?? 0);
                                        }
                                    }
                                    return $groupTotals;
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
                                    'ASET TETAP - HARGA PEROLEHAN' => ['A23-01', 'A23-02', 'A23-03', 'A23-04', 'A23-99'],
                                    'ASET TETAP - AKUMULASI PENYUSUTAN' => ['A24-01', 'A24-02', 'A24-03'],
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
                                    'SALDO LABA / (AKUMULASI RUGI)' => ['C21-01'],
                                ];
                            @endphp
                            
                            <!-- ASET -->
                            <tr style="background-color: #e9ecef; font-weight: bold;">
                                <td colspan="16">ASET</td>
                            </tr>
                            
                            <!-- ASET LANCAR -->
                            <tr style="background-color: #f8f9fa; font-weight: bold; font-style: italic;">
                                <td></td>
                                <td>ASET LANCAR</td>
                                <td colspan="14"></td>
                            </tr>
                            
                            @php
                                $currentAssetGroups = [
                                    'Kas & Setara Kas' => ['1', '1. KAS & SETARA KAS'],
                                    'Piutang Usaha' => ['2', '2. PIUTANG USAHA'],
                                    'Piutang Lain-lain' => ['3', '3. PIUTANG LAIN-LAIN'],
                                    'Investasi Jangka Pendek' => ['', 'INVESTASI JANGKA PENDEK'],
                                    'Persediaan' => ['', 'PERSEDIAAN'],
                                    'Biaya Dibayar Di muka' => ['4', '4. BIAYA DIBAYAR DI MUKA'],
                                    'Uang Muka Pajak' => ['5', '5. UANG MUKA PAJAK'],
                                    'Aset Lancar Lainnya' => ['', 'ASET LANCAR LAINNYA'],
                                ];
                                
                                $currentAssetTotals = ['total' => 0, 'opening' => 0];
                                for ($m = 1; $m <= 12; $m++) {
                                    $currentAssetTotals["month_$m"] = 0;
                                }
                                
                                foreach ($currentAssetGroups as $displayName => [$noteNum, $groupKey]) {
                                    if (isset($accountGroups[$groupKey])) {
                                        $groupTotals = calculateGroupTotal($accountGroups[$groupKey], $items, $data);
                                        if ($groupTotals['total'] != 0 || $groupTotals['opening'] != 0) {
                                            echo '<tr>';
                                            echo '<td>' . $noteNum . '</td>';
                                            echo '<td style="padding-left: 40px;">' . $displayName . '</td>';
                                            for ($m = 1; $m <= 12; $m++) {
                                                echo '<td>' . formatAccounting($groupTotals["month_$m"]) . '</td>';
                                                $currentAssetTotals["month_$m"] += $groupTotals["month_$m"];
                                            }
                                            echo '<td>' . formatAccounting($groupTotals['total']) . '</td>';
                                            echo '<td>' . formatAccounting($groupTotals['opening']) . '</td>';
                                            echo '</tr>';
                                            $currentAssetTotals['total'] += $groupTotals['total'];
                                            $currentAssetTotals['opening'] += $groupTotals['opening'];
                                        }
                                    }
                                }
                            @endphp
                            
                            <!-- Total Aset Lancar -->
                            <tr style="background-color: #f8f9fa; font-weight: bold;">
                                <td></td>
                                <td>TOTAL ASET LANCAR</td>
                                @for ($m = 1; $m <= 12; $m++)
                                    <td>{{ formatAccounting($currentAssetTotals["month_$m"]) }}</td>
                                @endfor
                                <td>{{ formatAccounting($currentAssetTotals['total']) }}</td>
                                <td>{{ formatAccounting($currentAssetTotals['opening']) }}</td>
                            </tr>
                            
                            <!-- ASET TIDAK LANCAR -->
                            <tr style="background-color: #f8f9fa; font-weight: bold; font-style: italic;">
                                <td></td>
                                <td>ASET TIDAK LANCAR</td>
                                <td colspan="14"></td>
                            </tr>
                            
                            @php
                                $nonCurrentAssetTotals = ['total' => 0, 'opening' => 0];
                                for ($m = 1; $m <= 12; $m++) {
                                    $nonCurrentAssetTotals["month_$m"] = 0;
                                }
                                
                                $nonCurrentAssetGroups = [
                                    'Piutang Lain-lain - Jangka Panjang' => ['', 'PIUTANG LAIN-LAIN - JANGKA PANJANG'],
                                    'Investasi Jangka Panjang' => ['', 'INVESTASI JANGKA PANJANG'],
                                    'Aset Tidak Berwujud' => ['', 'ASET TIDAK BERWUJUD'],
                                    'Aset Tidak Lancar Lainnya' => ['7', '7. ASET TIDAK LANCAR LAINNYA'],
                                ];
                                
                                foreach ($nonCurrentAssetGroups as $displayName => [$noteNum, $groupKey]) {
                                    if (isset($accountGroups[$groupKey])) {
                                        $groupTotals = calculateGroupTotal($accountGroups[$groupKey], $items, $data);
                                        if ($groupTotals['total'] != 0 || $groupTotals['opening'] != 0) {
                                            echo '<tr>';
                                            echo '<td>' . $noteNum . '</td>';
                                            echo '<td style="padding-left: 40px;">' . $displayName . '</td>';
                                            for ($m = 1; $m <= 12; $m++) {
                                                echo '<td>' . formatAccounting($groupTotals["month_$m"]) . '</td>';
                                                $nonCurrentAssetTotals["month_$m"] += $groupTotals["month_$m"];
                                            }
                                            echo '<td>' . formatAccounting($groupTotals['total']) . '</td>';
                                            echo '<td>' . formatAccounting($groupTotals['opening']) . '</td>';
                                            echo '</tr>';
                                            $nonCurrentAssetTotals['total'] += $groupTotals['total'];
                                            $nonCurrentAssetTotals['opening'] += $groupTotals['opening'];
                                        }
                                    }
                                }
                                
                                // Aset Tetap - Net
                                $fixedAssetCost = calculateGroupTotal($accountGroups['ASET TETAP - HARGA PEROLEHAN'], $items, $data);
                                $fixedAssetDep = calculateGroupTotal($accountGroups['ASET TETAP - AKUMULASI PENYUSUTAN'], $items, $data);
                                
                                if ($fixedAssetCost['total'] != 0 || $fixedAssetCost['opening'] != 0 || $fixedAssetDep['total'] != 0 || $fixedAssetDep['opening'] != 0) {
                                    echo '<tr>';
                                    echo '<td>6</td>';
                                    echo '<td style="padding-left: 40px;">Aset Tetap - Bersih</td>';
                                    for ($m = 1; $m <= 12; $m++) {
                                        $netValue = $fixedAssetCost["month_$m"] - $fixedAssetDep["month_$m"];
                                        echo '<td>' . formatAccounting($netValue) . '</td>';
                                        $nonCurrentAssetTotals["month_$m"] += $netValue;
                                    }
                                    $netTotal = $fixedAssetCost['total'] - $fixedAssetDep['total'];
                                    $netOpening = $fixedAssetCost['opening'] - $fixedAssetDep['opening'];
                                    echo '<td>' . formatAccounting($netTotal) . '</td>';
                                    echo '<td>' . formatAccounting($netOpening) . '</td>';
                                    echo '</tr>';
                                    $nonCurrentAssetTotals['total'] += $netTotal;
                                    $nonCurrentAssetTotals['opening'] += $netOpening;
                                }
                            @endphp
                            
                            <!-- Total Aset Tidak Lancar -->
                            <tr style="background-color: #f8f9fa; font-weight: bold;">
                                <td></td>
                                <td>TOTAL ASET TIDAK LANCAR</td>
                                @for ($m = 1; $m <= 12; $m++)
                                    <td>{{ formatAccounting($nonCurrentAssetTotals["month_$m"]) }}</td>
                                @endfor
                                <td>{{ formatAccounting($nonCurrentAssetTotals['total']) }}</td>
                                <td>{{ formatAccounting($nonCurrentAssetTotals['opening']) }}</td>
                            </tr>
                            
                            <!-- TOTAL ASET -->
                            <tr style="background-color: #dee2e6; font-weight: bold; font-size: 16px;">
                                <td></td>
                                <td>TOTAL ASET</td>
                                @for ($m = 1; $m <= 12; $m++)
                                    <td>{{ formatAccounting($currentAssetTotals["month_$m"] + $nonCurrentAssetTotals["month_$m"]) }}</td>
                                @endfor
                                <td>{{ formatAccounting($currentAssetTotals['total'] + $nonCurrentAssetTotals['total']) }}</td>
                                <td>{{ formatAccounting($currentAssetTotals['opening'] + $nonCurrentAssetTotals['opening']) }}</td>
                            </tr>
                            
                            <!-- KEWAJIBAN -->
                            <tr style="background-color: #e9ecef; font-weight: bold;">
                                <td colspan="16">KEWAJIBAN</td>
                            </tr>
                            
                            <!-- KEWAJIBAN JANGKA PENDEK -->
                            <tr style="background-color: #f8f9fa; font-weight: bold; font-style: italic;">
                                <td></td>
                                <td>KEWAJIBAN JANGKA PENDEK</td>
                                <td colspan="14"></td>
                            </tr>
                            
                            @php
                                $currentLiabilityTotals = ['total' => 0, 'opening' => 0];
                                for ($m = 1; $m <= 12; $m++) {
                                    $currentLiabilityTotals["month_$m"] = 0;
                                }
                                
                                $currentLiabilityGroups = [
                                    'Utang Usaha' => ['8', '8. UTANG USAHA'],
                                    'Utang Lain-lain' => ['', 'UTANG LAIN-LAIN'],
                                    'Biaya Yang Harus Dibayar' => ['9', '9. BIAYA YANG HARUS DIBAYAR'],
                                    'Utang Pajak' => ['10', '10. UTANG PAJAK'],
                                    'Uang Muka Pendapatan' => ['', 'UANG MUKA PENDAPATAN'],
                                    'Pinjaman Jangka Pendek' => ['', 'PINJAMAN JANGKA PENDEK'],
                                    'Kewajiban Imbalan Pasca Kerja' => ['11', '11. KEWAJIBAN IMBALAN PASCA KERJA'],
                                ];
                                
                                foreach ($currentLiabilityGroups as $displayName => [$noteNum, $groupKey]) {
                                    if (isset($accountGroups[$groupKey])) {
                                        $groupTotals = calculateGroupTotal($accountGroups[$groupKey], $items, $data);
                                        if ($groupTotals['total'] != 0 || $groupTotals['opening'] != 0) {
                                            echo '<tr>';
                                            echo '<td>' . $noteNum . '</td>';
                                            echo '<td style="padding-left: 40px;">' . $displayName . '</td>';
                                            for ($m = 1; $m <= 12; $m++) {
                                                echo '<td>' . formatAccounting($groupTotals["month_$m"]) . '</td>';
                                                $currentLiabilityTotals["month_$m"] += $groupTotals["month_$m"];
                                            }
                                            echo '<td>' . formatAccounting($groupTotals['total']) . '</td>';
                                            echo '<td>' . formatAccounting($groupTotals['opening']) . '</td>';
                                            echo '</tr>';
                                            $currentLiabilityTotals['total'] += $groupTotals['total'];
                                            $currentLiabilityTotals['opening'] += $groupTotals['opening'];
                                        }
                                    }
                                }
                            @endphp
                            
                            <!-- Total Kewajiban Jangka Pendek -->
                            <tr style="background-color: #f8f9fa; font-weight: bold;">
                                <td></td>
                                <td>TOTAL KEWAJIBAN JANGKA PENDEK</td>
                                @for ($m = 1; $m <= 12; $m++)
                                    <td>{{ formatAccounting($currentLiabilityTotals["month_$m"]) }}</td>
                                @endfor
                                <td>{{ formatAccounting($currentLiabilityTotals['total']) }}</td>
                                <td>{{ formatAccounting($currentLiabilityTotals['opening']) }}</td>
                            </tr>
                            
                            <!-- KEWAJIBAN JANGKA PANJANG -->
                            <tr style="background-color: #f8f9fa; font-weight: bold; font-style: italic;">
                                <td></td>
                                <td>KEWAJIBAN JANGKA PANJANG</td>
                                <td colspan="14"></td>
                            </tr>
                            
                            @php
                                $longTermLiabilityTotals = ['total' => 0, 'opening' => 0];
                                for ($m = 1; $m <= 12; $m++) {
                                    $longTermLiabilityTotals["month_$m"] = 0;
                                }
                                
                                $longTermLiabilityGroups = [
                                    'Utang Usaha - Jk. Panjang' => ['', 'UTANG USAHA - JK. PANJANG'],
                                    'Utang Lain-lain - Jk. Panjang' => ['', 'UTANG LAIN-LAIN - JK. PANJANG'],
                                    'Pinjaman Jangka Panjang' => ['', 'PINJAMAN JANGKA PANJANG'],
                                    'Kewajiban Imbalan Pasca Kerja - Jk. Panjang' => ['', 'KEWAJIBAN IMBALAN PASCA KERJA - JK. PANJANG'],
                                    'Kewajiban Jangka Panjang Lainnya' => ['', 'KEWAJIBAN JANGKA PANJANG LAINNYA'],
                                ];
                                
                                foreach ($longTermLiabilityGroups as $displayName => [$noteNum, $groupKey]) {
                                    if (isset($accountGroups[$groupKey])) {
                                        $groupTotals = calculateGroupTotal($accountGroups[$groupKey], $items, $data);
                                        if ($groupTotals['total'] != 0 || $groupTotals['opening'] != 0) {
                                            echo '<tr>';
                                            echo '<td>' . $noteNum . '</td>';
                                            echo '<td style="padding-left: 40px;">' . $displayName . '</td>';
                                            for ($m = 1; $m <= 12; $m++) {
                                                echo '<td>' . formatAccounting($groupTotals["month_$m"]) . '</td>';
                                                $longTermLiabilityTotals["month_$m"] += $groupTotals["month_$m"];
                                            }
                                            echo '<td>' . formatAccounting($groupTotals['total']) . '</td>';
                                            echo '<td>' . formatAccounting($groupTotals['opening']) . '</td>';
                                            echo '</tr>';
                                            $longTermLiabilityTotals['total'] += $groupTotals['total'];
                                            $longTermLiabilityTotals['opening'] += $groupTotals['opening'];
                                        }
                                    }
                                }
                            @endphp
                            
                            <!-- Total Kewajiban Jangka Panjang -->
                            <tr style="background-color: #f8f9fa; font-weight: bold;">
                                <td></td>
                                <td>TOTAL KEWAJIBAN JANGKA PANJANG</td>
                                @for ($m = 1; $m <= 12; $m++)
                                    <td>{{ formatAccounting($longTermLiabilityTotals["month_$m"]) }}</td>
                                @endfor
                                <td>{{ formatAccounting($longTermLiabilityTotals['total']) }}</td>
                                <td>{{ formatAccounting($longTermLiabilityTotals['opening']) }}</td>
                            </tr>
                            
                            <!-- TOTAL KEWAJIBAN -->
                            <tr style="background-color: #dee2e6; font-weight: bold; font-size: 16px;">
                                <td></td>
                                <td>TOTAL KEWAJIBAN</td>
                                @for ($m = 1; $m <= 12; $m++)
                                    <td>{{ formatAccounting($currentLiabilityTotals["month_$m"] + $longTermLiabilityTotals["month_$m"]) }}</td>
                                @endfor
                                <td>{{ formatAccounting($currentLiabilityTotals['total'] + $longTermLiabilityTotals['total']) }}</td>
                                <td>{{ formatAccounting($currentLiabilityTotals['opening'] + $longTermLiabilityTotals['opening']) }}</td>
                            </tr>
                            
                            <!-- EKUITAS -->
                            <tr style="background-color: #e9ecef; font-weight: bold;">
                                <td colspan="16">EKUITAS</td>
                            </tr>
                            
                            @php
                                $equityTotals = ['total' => 0, 'opening' => 0];
                                for ($m = 1; $m <= 12; $m++) {
                                    $equityTotals["month_$m"] = 0;
                                }
                                
                                $equityGroups = [
                                    'Modal Disetor' => ['12', '12. MODAL DISETOR'],
                                    'Saldo Laba / (Akumulasi Rugi)' => ['', 'SALDO LABA / (AKUMULASI RUGI)'],
                                ];
                                
                                foreach ($equityGroups as $displayName => [$noteNum, $groupKey]) {
                                    if (isset($accountGroups[$groupKey])) {
                                        $groupTotals = calculateGroupTotal($accountGroups[$groupKey], $items, $data);
                                        echo '<tr>';
                                        echo '<td>' . $noteNum . '</td>';
                                        echo '<td style="padding-left: 20px;">' . $displayName . '</td>';
                                        for ($m = 1; $m <= 12; $m++) {
                                            echo '<td>' . formatAccounting($groupTotals["month_$m"]) . '</td>';
                                            $equityTotals["month_$m"] += $groupTotals["month_$m"];
                                        }
                                        echo '<td>' . formatAccounting($groupTotals['total']) . '</td>';
                                        echo '<td>' . formatAccounting($groupTotals['opening']) . '</td>';
                                        echo '</tr>';
                                        $equityTotals['total'] += $groupTotals['total'];
                                        $equityTotals['opening'] += $groupTotals['opening'];
                                    }
                                }
                            @endphp
                            
                            <!-- TOTAL EKUITAS -->
                            <tr style="background-color: #dee2e6; font-weight: bold; font-size: 16px;">
                                <td></td>
                                <td>TOTAL EKUITAS</td>
                                @for ($m = 1; $m <= 12; $m++)
                                    <td>{{ formatAccounting($equityTotals["month_$m"]) }}</td>
                                @endfor
                                <td>{{ formatAccounting($equityTotals['total']) }}</td>
                                <td>{{ formatAccounting($equityTotals['opening']) }}</td>
                            </tr>
                            
                            <!-- TOTAL KEWAJIBAN & EKUITAS -->
                            <tr style="background-color: #ced4da; font-weight: bold; font-size: 17px;">
                                <td></td>
                                <td>TOTAL KEWAJIBAN & EKUITAS</td>
                                @for ($m = 1; $m <= 12; $m++)
                                    <td>{{ formatAccounting($currentLiabilityTotals["month_$m"] + $longTermLiabilityTotals["month_$m"] + $equityTotals["month_$m"]) }}</td>
                                @endfor
                                <td>{{ formatAccounting($currentLiabilityTotals['total'] + $longTermLiabilityTotals['total'] + $equityTotals['total']) }}</td>
                                <td>{{ formatAccounting($currentLiabilityTotals['opening'] + $longTermLiabilityTotals['opening'] + $equityTotals['opening']) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection