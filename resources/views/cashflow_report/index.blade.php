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
                            @php
                                function renderCashflowRows($flattenedData, $data) {
                                    $levelGroups = [];
                                    
                                    // Group by level 0 (top level)
                                    $currentGroup = null;
                                    foreach ($flattenedData as $row) {
                                        if ($row['depth'] == 0) {
                                            if ($currentGroup) {
                                                $levelGroups[] = $currentGroup;
                                            }
                                            $currentGroup = ['parent' => $row, 'children' => []];
                                        } else {
                                            $currentGroup['children'][] = $row;
                                        }
                                    }
                                    if ($currentGroup) {
                                        $levelGroups[] = $currentGroup;
                                    }
                                    
                                    // Render each group with summary
                                    foreach ($levelGroups as $group) {
                                        $parent = $group['parent'];
                                        
                                        // Render parent header
                                        if ($parent['is_leaf']) {
                                            echo '<tr class="level-' . $parent['depth'] . '-row">';
                                            echo '<td>' . $parent['code'] . '</td>';
                                            echo '<td><div class="cf-text level-' . $parent['depth'] . '">' . $parent['name'] . '</div></td>';
                                            
                                            for ($m = 1; $m <= 12; $m++) {
                                                $val = $data[$parent['id']]["month_$m"] ?? 0;
                                                echo '<td>' . formatAccounting($val) . '</td>';
                                            }
                                            
                                            $total = $data[$parent['id']]['total'] ?? 0;
                                            echo '<td>' . formatAccounting($total) . '</td>';
                                            echo '</tr>';
                                        } else {
                                            echo '<tr class="level-' . $parent['depth'] . '-row">';
                                            echo '<td>' . $parent['code'] . '</td>';
                                            echo '<td><div class="cf-text level-' . $parent['depth'] . '">' . $parent['name'] . '</div></td>';
                                            echo '<td colspan="13"></td>';
                                            echo '</tr>';
                                        }
                                        
                                        // Group level 1 children by level 1 parents
                                        $level1Groups = [];
                                        $currentLevel1 = null;
                                        
                                        foreach ($group['children'] as $child) {
                                            if ($child['depth'] == 1) {
                                                if ($currentLevel1) {
                                                    $level1Groups[] = $currentLevel1;
                                                }
                                                $currentLevel1 = ['parent' => $child, 'children' => []];
                                            } else {
                                                $currentLevel1['children'][] = $child;
                                            }
                                        }
                                        if ($currentLevel1) {
                                            $level1Groups[] = $currentLevel1;
                                        }
                                        
                                        // Render level 1 groups
                                        foreach ($level1Groups as $level1Group) {
                                            $level1Parent = $level1Group['parent'];
                                            
                                            // Render level 1 parent
                                            if ($level1Parent['is_leaf']) {
                                                echo '<tr class="level-' . $level1Parent['depth'] . '-row">';
                                                echo '<td>' . $level1Parent['code'] . '</td>';
                                                echo '<td><div class="cf-text level-' . $level1Parent['depth'] . '">' . $level1Parent['name'] . '</div></td>';
                                                
                                                for ($m = 1; $m <= 12; $m++) {
                                                    $val = $data[$level1Parent['id']]["month_$m"] ?? 0;
                                                    echo '<td>' . formatAccounting($val) . '</td>';
                                                }
                                                
                                                $total = $data[$level1Parent['id']]['total'] ?? 0;
                                                echo '<td>' . formatAccounting($total) . '</td>';
                                                echo '</tr>';
                                            } else {
                                                echo '<tr class="level-' . $level1Parent['depth'] . '-row">';
                                                echo '<td>' . $level1Parent['code'] . '</td>';
                                                echo '<td><div class="cf-text level-' . $level1Parent['depth'] . '">' . $level1Parent['name'] . '</div></td>';
                                                echo '<td colspan="13"></td>';
                                                echo '</tr>';
                                            }
                                            
                                            // Render level 1 children (level 2+)
                                            foreach ($level1Group['children'] as $child) {
                                                echo '<tr class="level-' . $child['depth'] . '-row">';
                                                echo '<td>' . $child['code'] . '</td>';
                                                echo '<td><div class="cf-text level-' . $child['depth'] . '">' . $child['name'] . '</div></td>';
                                                
                                                for ($m = 1; $m <= 12; $m++) {
                                                    $val = $data[$child['id']]["month_$m"] ?? 0;
                                                    echo '<td>' . formatAccounting($val) . '</td>';
                                                }
                                                
                                                $total = $data[$child['id']]['total'] ?? 0;
                                                echo '<td>' . formatAccounting($total) . '</td>';
                                                echo '</tr>';
                                            }
                                            
                                            // Add level 1 summary
                                            if (!$level1Parent['is_leaf']) {
                                                echo '<tr class="total-row">';
                                                echo '<td><strong>' . $level1Parent['code'] . '</strong></td>';
                                                echo '<td><strong>TOTAL ' . $level1Parent['name'] . '</strong></td>';
                                                
                                                for ($m = 1; $m <= 12; $m++) {
                                                    $val = $data[$level1Parent['id']]["month_$m"] ?? 0;
                                                    echo '<td><strong>' . formatAccounting($val) . '</strong></td>';
                                                }
                                                
                                                $total = $data[$level1Parent['id']]['total'] ?? 0;
                                                echo '<td><strong>' . formatAccounting($total) . '</strong></td>';
                                                echo '</tr>';
                                            }
                                        }
                                        
                                        // Add level 0 summary
                                        if (!$parent['is_leaf']) {
                                            echo '<tr class="total-row">';
                                            echo '<td><strong>' . $parent['code'] . '</strong></td>';
                                            echo '<td><strong>TOTAL ' . $parent['name'] . '</strong></td>';
                                            
                                            for ($m = 1; $m <= 12; $m++) {
                                                $val = $data[$parent['id']]["month_$m"] ?? 0;
                                                echo '<td><strong>' . formatAccounting($val) . '</strong></td>';
                                            }
                                            
                                            $total = $data[$parent['id']]['total'] ?? 0;
                                            echo '<td><strong>' . formatAccounting($total) . '</strong></td>';
                                            echo '</tr>';
                                        }
                                    }
                                }
                                
                                renderCashflowRows($flattenedData, $data);
                            @endphp
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection