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
                        </tbody>



                    </table>
                </div>
            </div>

        </div>
    </div>
@endsection
