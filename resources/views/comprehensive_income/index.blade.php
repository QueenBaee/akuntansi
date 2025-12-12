@extends('layouts.app')

@section('title', 'Laporan Penghasilan Komprehensif dan Laporan Laba Rugi')

@section('page-header')
    <div class="page-pretitle">Laporan</div>
    <h2 class="page-title">Laporan Penghasilan Komprehensif dan Laporan Laba Rugi Tahun {{ $year }}</h2>
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
                            min-width: 200px !important;
                            width: auto !important;
                            text-align: left !important;
                            font-weight: 600 !important;
                        }

                        .no-equal-width td:nth-child(2),
                        .no-equal-width th:nth-child(2) {
                            min-width: 80px !important;
                            width: 80px !important;
                            text-align: center !important;
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
                                <th>Keterangan</th>
                                <th>Note</th>
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
                                            for ($m = 1; $m <= 12; $m++) {
                                                $groupTotals["month_$m"] += abs($data[$item->id]["month_$m"] ?? 0);
                                            }
                                            $groupTotals['total'] += abs($data[$item->id]['total'] ?? 0);
                                            $groupTotals['opening'] += abs($data[$item->id]['opening'] ?? 0);
                                        }
                                    }
                                    return $groupTotals;
                                }
                                
                                $accountGroups = [
                                    '13. PENDAPATAN' => ['R11-01', 'R11-02', 'R11-03'],
                                    '14. BEBAN PRODUKSI' => ['E11-01', 'E11-02', 'E11-03', 'E11-04', 'E11-05', 'E11-06', 'E11-07'],
                                    'PEMASARAN' => ['E21-01', 'E21-02'],
                                    '15. ADMINISTRASI & UMUM' => ['E22-01', 'E22-02', 'E22-03', 'E22-04', 'E22-05', 'E22-06', 'E22-07', 'E22-08', 'E22-09', 'E22-10', 'E22-11', 'E22-89', 'E22-96', 'E22-97', 'E22-98', 'E22-99'],
                                    '16. PENDAPATAN LAIN-LAIN' => ['R21-01', 'R21-02', 'R21-99'],
                                    '17. BEBAN LAIN-LAIN' => ['E31-01', 'E31-02', 'E31-03'],
                                    'BEBAN PAJAK PENGHASILAN' => ['E91-01'],
                                    'SALDO LABA AWAL' => ['C21-01'],
                                ];
                                
                                // Calculate totals
                                $pendapatan = calculateGroupTotal($accountGroups['13. PENDAPATAN'], $items, $data);
                                $bebanProduksi = calculateGroupTotal($accountGroups['14. BEBAN PRODUKSI'], $items, $data);
                                $pemasaran = calculateGroupTotal($accountGroups['PEMASARAN'], $items, $data);
                                $administrasi = calculateGroupTotal($accountGroups['15. ADMINISTRASI & UMUM'], $items, $data);
                                $pendapatanLain = calculateGroupTotal($accountGroups['16. PENDAPATAN LAIN-LAIN'], $items, $data);
                                $bebanLain = calculateGroupTotal($accountGroups['17. BEBAN LAIN-LAIN'], $items, $data);
                                $bebanPajak = calculateGroupTotal($accountGroups['BEBAN PAJAK PENGHASILAN'], $items, $data);
                                $saldoLabaAwal = calculateGroupTotal($accountGroups['SALDO LABA AWAL'], $items, $data);
                            @endphp
                            
                            <!-- PENDAPATAN -->
                            <tr>
                                <td>PENDAPATAN</td>
                                <td>13</td>
                                @for ($m = 1; $m <= 12; $m++)
                                    <td>{{ formatAccounting($pendapatan["month_$m"]) }}</td>
                                @endfor
                                <td>{{ formatAccounting($pendapatan['total']) }}</td>
                                <td>{{ formatAccounting($pendapatan['opening']) }}</td>
                            </tr>
                            
                            <!-- BEBAN PRODUKSI -->
                            <tr>
                                <td>BEBAN PRODUKSI</td>
                                <td>14</td>
                                @for ($m = 1; $m <= 12; $m++)
                                    <td>({{ formatAccounting($bebanProduksi["month_$m"]) }})</td>
                                @endfor
                                <td>({{ formatAccounting($bebanProduksi['total']) }})</td>
                                <td>({{ formatAccounting($bebanProduksi['opening']) }})</td>
                            </tr>
                            
                            <!-- LABA/(RUGI) KOTOR -->
                            <tr class="financial-report-total">
                                <td>LABA/(RUGI) KOTOR</td>
                                <td></td>
                                @for ($m = 1; $m <= 12; $m++)
                                    @php $labaKotor = $pendapatan["month_$m"] - $bebanProduksi["month_$m"]; @endphp
                                    <td>{{ formatAccounting($labaKotor) }}</td>
                                @endfor
                                @php $labaKotorTotal = $pendapatan['total'] - $bebanProduksi['total']; @endphp
                                @php $labaKotorOpening = $pendapatan['opening'] - $bebanProduksi['opening']; @endphp
                                <td>{{ formatAccounting($labaKotorTotal) }}</td>
                                <td>{{ formatAccounting($labaKotorOpening) }}</td>
                            </tr>
                            
                            <!-- BEBAN USAHA -->
                            <tr class="financial-report-header">
                                <td>BEBAN USAHA</td>
                                <td></td>
                                <td colspan="14"></td>
                            </tr>
                            
                            <!-- Pemasaran -->
                            <tr>
                                <td style="padding-left: 20px;">Pemasaran</td>
                                <td></td>
                                @for ($m = 1; $m <= 12; $m++)
                                    <td>({{ formatAccounting($pemasaran["month_$m"]) }})</td>
                                @endfor
                                <td>({{ formatAccounting($pemasaran['total']) }})</td>
                                <td>({{ formatAccounting($pemasaran['opening']) }})</td>
                            </tr>
                            
                            <!-- Administrasi & Umum -->
                            <tr>
                                <td style="padding-left: 20px;">Administrasi & Umum</td>
                                <td>15</td>
                                @for ($m = 1; $m <= 12; $m++)
                                    <td>({{ formatAccounting($administrasi["month_$m"]) }})</td>
                                @endfor
                                <td>({{ formatAccounting($administrasi['total']) }})</td>
                                <td>({{ formatAccounting($administrasi['opening']) }})</td>
                            </tr>
                            
                            <!-- LABA/(RUGI) USAHA -->
                            <tr style="background-color: #f8f9fa; font-weight: bold;">
                                <td>LABA/(RUGI) USAHA</td>
                                <td></td>
                                @for ($m = 1; $m <= 12; $m++)
                                    @php 
                                        $labaKotor = $pendapatan["month_$m"] - $bebanProduksi["month_$m"];
                                        $labaUsaha = $labaKotor - $pemasaran["month_$m"] - $administrasi["month_$m"];
                                    @endphp
                                    <td>{{ formatAccounting($labaUsaha) }}</td>
                                @endfor
                                @php 
                                    $labaKotorTotal = $pendapatan['total'] - $bebanProduksi['total'];
                                    $labaUsahaTotal = $labaKotorTotal - $pemasaran['total'] - $administrasi['total'];
                                    $labaKotorOpening = $pendapatan['opening'] - $bebanProduksi['opening'];
                                    $labaUsahaOpening = $labaKotorOpening - $pemasaran['opening'] - $administrasi['opening'];
                                @endphp
                                <td>{{ formatAccounting($labaUsahaTotal) }}</td>
                                <td>{{ formatAccounting($labaUsahaOpening) }}</td>
                            </tr>
                            
                            <!-- PENDAPATAN/(BEBAN) LAIN-LAIN -->
                            <tr style="background-color: #e9ecef; font-weight: bold;">
                                <td>PENDAPATAN/(BEBAN) LAIN-LAIN</td>
                                <td></td>
                                <td colspan="14"></td>
                            </tr>
                            
                            <!-- Pendapatan Lain-Lain -->
                            <tr>
                                <td style="padding-left: 20px;">Pendapatan Lain-Lain</td>
                                <td>16</td>
                                @for ($m = 1; $m <= 12; $m++)
                                    <td>{{ formatAccounting($pendapatanLain["month_$m"]) }}</td>
                                @endfor
                                <td>{{ formatAccounting($pendapatanLain['total']) }}</td>
                                <td>{{ formatAccounting($pendapatanLain['opening']) }}</td>
                            </tr>
                            
                            <!-- Beban Lain-lain -->
                            <tr>
                                <td style="padding-left: 20px;">Beban Lain-lain</td>
                                <td>17</td>
                                @for ($m = 1; $m <= 12; $m++)
                                    <td>({{ formatAccounting($bebanLain["month_$m"]) }})</td>
                                @endfor
                                <td>({{ formatAccounting($bebanLain['total']) }})</td>
                                <td>({{ formatAccounting($bebanLain['opening']) }})</td>
                            </tr>
                            
                            <!-- LABA/(RUGI) SEBELUM PAJAK -->
                            <tr style="background-color: #f8f9fa; font-weight: bold;">
                                <td>LABA/(RUGI) SEBELUM PAJAK</td>
                                <td></td>
                                @for ($m = 1; $m <= 12; $m++)
                                    @php 
                                        $labaKotor = $pendapatan["month_$m"] - $bebanProduksi["month_$m"];
                                        $labaUsaha = $labaKotor - $pemasaran["month_$m"] - $administrasi["month_$m"];
                                        $labaSebelumPajak = $labaUsaha + $pendapatanLain["month_$m"] - $bebanLain["month_$m"];
                                    @endphp
                                    <td>{{ formatAccounting($labaSebelumPajak) }}</td>
                                @endfor
                                @php 
                                    $labaSebelumPajakTotal = $labaUsahaTotal + $pendapatanLain['total'] - $bebanLain['total'];
                                    $labaSebelumPajakOpening = $labaUsahaOpening + $pendapatanLain['opening'] - $bebanLain['opening'];
                                @endphp
                                <td>{{ formatAccounting($labaSebelumPajakTotal) }}</td>
                                <td>{{ formatAccounting($labaSebelumPajakOpening) }}</td>
                            </tr>
                            
                            <!-- BEBAN PAJAK PENGHASILAN -->
                            <tr>
                                <td>BEBAN PAJAK PENGHASILAN</td>
                                <td>E91-01</td>
                                @for ($m = 1; $m <= 12; $m++)
                                    <td>({{ formatAccounting($bebanPajak["month_$m"]) }})</td>
                                @endfor
                                <td>({{ formatAccounting($bebanPajak['total']) }})</td>
                                <td>({{ formatAccounting($bebanPajak['opening']) }})</td>
                            </tr>
                            
                            <!-- LABA/(RUGI) BERSIH -->
                            <tr style="background-color: #dee2e6; font-weight: bold; font-size: 16px;">
                                <td>LABA/(RUGI) BERSIH</td>
                                <td></td>
                                @for ($m = 1; $m <= 12; $m++)
                                    @php 
                                        $labaKotor = $pendapatan["month_$m"] - $bebanProduksi["month_$m"];
                                        $labaUsaha = $labaKotor - $pemasaran["month_$m"] - $administrasi["month_$m"];
                                        $labaSebelumPajak = $labaUsaha + $pendapatanLain["month_$m"] - $bebanLain["month_$m"];
                                        $labaBersih = $labaSebelumPajak - $bebanPajak["month_$m"];
                                    @endphp
                                    <td>{{ formatAccounting($labaBersih) }}</td>
                                @endfor
                                @php 
                                    $labaBersihTotal = $labaSebelumPajakTotal - $bebanPajak['total'];
                                    $labaBersihOpening = $labaSebelumPajakOpening - $bebanPajak['opening'];
                                @endphp
                                <td>{{ formatAccounting($labaBersihTotal) }}</td>
                                <td>{{ formatAccounting($labaBersihOpening) }}</td>
                            </tr>
                            
                            <!-- SALDO LABA/(RUGI) AWAL -->
                            <tr>
                                <td>SALDO LABA/(RUGI) AWAL</td>
                                <td></td>
                                @for ($m = 1; $m <= 12; $m++)
                                    <td>{{ formatAccounting($saldoLabaAwal["month_$m"]) }}</td>
                                @endfor
                                <td>{{ formatAccounting($saldoLabaAwal['total']) }}</td>
                                <td>{{ formatAccounting($saldoLabaAwal['opening']) }}</td>
                            </tr>
                            
                            <!-- SALDO LABA/(RUGI) AKHIR -->
                            <tr style="background-color: #ced4da; font-weight: bold; font-size: 17px;">
                                <td>SALDO LABA/(RUGI) AKHIR</td>
                                <td></td>
                                @for ($m = 1; $m <= 12; $m++)
                                    @php 
                                        $labaKotor = $pendapatan["month_$m"] - $bebanProduksi["month_$m"];
                                        $labaUsaha = $labaKotor - $pemasaran["month_$m"] - $administrasi["month_$m"];
                                        $labaSebelumPajak = $labaUsaha + $pendapatanLain["month_$m"] - $bebanLain["month_$m"];
                                        $labaBersih = $labaSebelumPajak - $bebanPajak["month_$m"];
                                        $saldoLabaAkhir = $saldoLabaAwal["month_$m"] + $labaBersih;
                                    @endphp
                                    <td>{{ formatAccounting($saldoLabaAkhir) }}</td>
                                @endfor
                                @php 
                                    $saldoLabaAkhirTotal = $saldoLabaAwal['total'] + $labaBersihTotal;
                                    $saldoLabaAkhirOpening = $saldoLabaAwal['opening'] + $labaBersihOpening;
                                @endphp
                                <td>{{ formatAccounting($saldoLabaAkhirTotal) }}</td>
                                <td>{{ formatAccounting($saldoLabaAkhirOpening) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection