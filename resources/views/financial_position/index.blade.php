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
                <div class="table-responsive" style="max-height: calc(100vh - 250px); overflow-y: auto;">
                    <style>
                        .tb-text {
                            display: flex;
                            align-items: center;
                            font-size: 14px;
                        }

                        .no-equal-width thead th:nth-child(1) {
                            text-align: center !important;
                            vertical-align: middle !important;
                            font-weight: 600 !important;
                            background-color: #f8f9fa !important;
                            width: auto !important;
                            position: sticky;
                            top: 0;
                            left: 0;
                            z-index: 12;
                            border-bottom: 2px solid #dee2e6;
                        }

                        .no-equal-width thead th:nth-child(2) {
                            text-align: center !important;
                            vertical-align: middle !important;
                            font-weight: 600 !important;
                            background-color: #f8f9fa !important;
                            width: auto !important;
                            position: sticky;
                            top: 0;
                            left: 80px;
                            z-index: 12;
                            border-bottom: 2px solid #dee2e6;
                        }

                        .no-equal-width thead th {
                            text-align: center !important;
                            vertical-align: middle !important;
                            font-weight: 600 !important;
                            background-color: #f8f9fa !important;
                            width: auto !important;
                            position: sticky;
                            top: 0;
                            z-index: 10;
                            border-bottom: 2px solid #dee2e6;
                        }

                        .no-equal-width tbody td:nth-child(1) {
                            min-width: 80px !important;
                            width: 80px !important;
                            text-align: left !important;
                            font-weight: 600 !important;
                            position: sticky;
                            left: 0;
                            background-color: #f8f9fa;
                            z-index: 11;
                        }

                        .no-equal-width tbody td:nth-child(2) {
                            min-width: 200px !important;
                            width: auto !important;
                            text-align: left !important;
                            white-space: nowrap !important;
                            position: sticky;
                            left: 80px;
                            background-color: #f8f9fa;
                            z-index: 11;
                        }

                        .no-equal-width tbody td:not(:nth-child(1)):not(:nth-child(2)) {
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
                                <th style="text-align:center">Note</th>
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
                                
                                // Use account groups from controller
                                // $accountGroups is passed from controller
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
                                    'Kas & Setara Kas' => ['1', 'Kas & Setara Kas'],
                                    'Piutang Usaha' => ['2', 'Piutang Usaha'],
                                    'Piutang Lain-lain' => ['3', 'Piutang Lain-lain'],
                                    'Investasi Jangka Pendek' => ['', 'Investasi Jangka Pendek'],
                                    'Persediaan' => ['', 'Persediaan'],
                                    'Biaya Dibayar Di muka' => ['4', 'Biaya Dibayar Di muka'],
                                    'Uang Muka Pajak' => ['5', 'Uang Muka Pajak'],
                                    'Aset Lancar Lainnya' => ['', 'Aset Lancar Lainnya'],
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
                                <td>&nbsp;</td>
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
                                    'Piutang Lain-lain - Jangka Panjang' => ['', 'Piutang Lain-lain - Jangka Panjang'],
                                    'Investasi Jangka Panjang' => ['', 'Investasi Jangka Panjang'],
                                    'Aset Tidak Berwujud' => ['', 'Aset Tidak Berwujud'],
                                    'Aset Tidak Lancar Lainnya' => ['8', 'Aset Tidak Lancar Lainnya'],
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
                                $fixedAssetCost = calculateGroupTotal($accountGroups['Aset Tetap Bersih'], $items, $data);
                                $fixedAssetDep = [];
                                
                                // Extract depreciation accounts from Aset Tetap Bersih group
                                $depAccounts = array_filter($accountGroups['Aset Tetap Bersih'], function($code) {
                                    return strpos($code, 'A24-') === 0;
                                });
                                $costAccounts = array_filter($accountGroups['Aset Tetap Bersih'], function($code) {
                                    return strpos($code, 'A23-') === 0;
                                });
                                
                                $fixedAssetCost = calculateGroupTotal($costAccounts, $items, $data);
                                $fixedAssetDep = calculateGroupTotal($depAccounts, $items, $data);
                                
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
                                <td>&nbsp;</td>
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
                                    'Utang Usaha' => ['9', 'Utang Usaha'],
                                    'Utang Lain-lain' => ['10', 'Utang Lain-lain'],
                                    'Biaya Yang Harus Dibayar' => ['11', 'Biaya yang Harus Dibayar'],
                                    'Utang Pajak' => ['12', 'Utang Pajak'],
                                    'Uang Muka Pendapatan' => ['', 'Uang Muka Pendapatan'],
                                    'Pinjaman Jangka Pendek' => ['', 'Pinjaman Jangka Pendek'],
                                    'Kewajiban Imbalan Pasca Kerja' => ['13', 'Kewajiban Imbalan Pasca Kerja'],
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
                                <td>&nbsp;</td>
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
                                    'Utang Usaha - Jk. Panjang' => ['', 'Utang Usaha - Jk. Panjang'],
                                    'Utang Lain-lain - Jk. Panjang' => ['', 'Utang Lain-lain - Jk. Panjang'],
                                    'Pinjaman Jangka Panjang' => ['', 'Pinjaman Jangka Panjang'],
                                    'Kewajiban Imbalan Pasca Kerja - Jk. Panjang' => ['', 'Kewajiban Imbalan Pasca Kerja - Jk. Panjang'],
                                    'Kewajiban Jangka Panjang Lainnya' => ['', 'Kewajiban Jangka Panjang Lainnya'],
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
                                <td>&nbsp;</td>
                                @for ($m = 1; $m <= 12; $m++)
                                    <td>{{ formatAccounting($longTermLiabilityTotals["month_$m"]) }}</td>
                                @endfor
                                <td>{{ formatAccounting($longTermLiabilityTotals['total']) }}</td>
                                <td>{{ formatAccounting($longTermLiabilityTotals['opening']) }}</td>
                            </tr>
                            
                            <!-- TOTAL KEWAJIBAN -->
                            <tr style="background-color: #dee2e6; font-weight: bold; font-size: 16px;">
                                <td></td>
                                <td>&nbsp;</td>
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
                                    'Modal Disetor' => ['14', 'Modal Disetor'],
                                    'Saldo (Laba)/Rugi' => ['', 'Saldo (Laba)/Rugi'],
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
                                <td>&nbsp;</td>
                                @for ($m = 1; $m <= 12; $m++)
                                    <td>{{ formatAccounting($equityTotals["month_$m"]) }}</td>
                                @endfor
                                <td>{{ formatAccounting($equityTotals['total']) }}</td>
                                <td>{{ formatAccounting($equityTotals['opening']) }}</td>
                            </tr>
                            
                            <!-- TOTAL KEWAJIBAN & EKUITAS -->
                            <tr style="background-color: #ced4da; font-weight: bold; font-size: 17px;">
                                <td></td>
                                <td>TOTAL KEWAJIBAN DAN ASET BERSIH</td>
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