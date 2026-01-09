<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TrialBalance;
use Illuminate\Support\Facades\DB;

class FinancialPositionController extends Controller
{
    private function isBalanceSheetAccount($code) {
        if ($code === 'C21-02') return false;
        return in_array(substr($code, 0, 1), ['A', 'L', 'C']);
    }

    private function isIncomeStatementAccount($code) {
        return in_array(substr($code, 0, 1), ['R', 'E']);
    }

    private function isDividendAccount($code) {
        return $code === 'C21-02';
    }

    private $accountGroups = [
        // ASET LANCAR
        'Kas & Setara Kas' => ['A11-01', 'A11-21', 'A11-22', 'A11-23'], // Note 1
        'Piutang Usaha' => ['A12-01', 'A12-02', 'A12-03'], // Note 2
        'Piutang Lain-lain' => ['A13-01', 'A13-02', 'A13-03', 'A13-98', 'A13-99'], // Note 3
        'Investasi Jangka Pendek' => ['A14-01', 'A14-02', 'A14-99'],
        'Persediaan' => ['A15-01', 'A15-02', 'A15-99'],
        'Biaya Dibayar Di muka' => ['A16-01', 'A16-02'], // Note 4
        'Uang Muka Pajak' => ['A17-01', 'A17-02', 'A17-03', 'A17-04', 'A17-11'], // Note 5
        'Aset Lancar Lainnya' => ['A18-01', 'A18-02'],
        
        // ASET TIDAK LANCAR
        'Piutang Lain-lain - Jangka Panjang' => ['A21-01', 'A21-02'],
        'Investasi Jangka Panjang' => ['A22-01', 'A22-02'],
        'Aset Tetap Bersih' => ['A23-01', 'A23-02', 'A23-03', 'A23-04', 'A23-05', 'A24-01', 'A24-02', 'A24-03', 'A24-04'], // Note 6
        'Aset dalam Penyelesaian' => ['A25-01', 'A25-02', 'A25-03', 'A25-04', 'A25-05'], // Note 6
        'Aset Tidak Berwujud' => ['A26-01', 'A26-02'],
        'Aset Tidak Lancar Lainnya' => ['A27-01', 'A27-02'], // Note 8
        
        // KEWAJIBAN JANGKA PENDEK
        'Utang Usaha' => ['L11-01', 'L11-99'], // Note 9
        'Utang Lain-lain' => ['L12-01', 'L12-02'], // Note 10
        'Biaya yang Harus Dibayar' => ['L13-01', 'L13-02', 'L13-03', 'L13-04', 'L13-05', 'L13-06', 'L13-99'], // Note 11
        'Utang Pajak' => ['L14-01', 'L14-02', 'L14-03', 'L14-04', 'L14-05', 'L14-11', 'L14-12'], // Note 12
        'Uang Muka Pendapatan' => ['L15-01', 'L15-02', 'L15-99'],
        'Pinjaman Jangka Pendek' => ['L16-01', 'L16-02'],
        'Kewajiban Imbalan Pasca Kerja' => ['L17-01', 'L17-02'], // Note 13
        
        // KEWAJIBAN JANGKA PANJANG
        'Utang Usaha - Jk. Panjang' => ['L21-01', 'L21-02'],
        'Utang Lain-lain - Jk. Panjang' => ['L22-01', 'L22-02'],
        'Pinjaman Jangka Panjang' => ['L23-01', 'L23-02'],
        'Kewajiban Imbalan Pasca Kerja - Jk. Panjang' => ['L24-01', 'L24-02'],
        'Kewajiban Jangka Panjang Lainnya' => ['L25-01', 'L25-02'],
        
        // EKUITAS
        'Modal Disetor' => ['C11-01', 'C11-02', 'C11-03'], // Note 14
        'Saldo (Laba)/Rugi' => ['C21-01'],
    ];

    public function index(Request $request)
    {
        $year = $request->year ?? date('Y');
        $previousYear = $year - 1;

        $items = TrialBalance::orderBy('id')->get();

        $baseSaldo = [];
        foreach ($items as $it) {
            $baseSaldo[$it->id] = $it->tahun_2024 ?? 0;
        }

        $startBaseYear = 2025;
        $openingBalance = [];

        if ($previousYear >= $startBaseYear) {
            $debitPrev = DB::table('journals')
                ->select(
                    DB::raw("debit_account_id AS account_id"),
                    DB::raw("SUM(total_debit) AS debit_amount"),
                    DB::raw("0 AS credit_amount")
                )
                ->whereYear('date', '>=', $startBaseYear)
                ->whereYear('date', '<=', $previousYear)
                ->whereNull('deleted_at')
                ->groupBy('account_id');

            $creditPrev = DB::table('journals')
                ->select(
                    DB::raw("credit_account_id AS account_id"),
                    DB::raw("0 AS debit_amount"),
                    DB::raw("SUM(total_credit) AS credit_amount")
                )
                ->whereYear('date', '>=', $startBaseYear)
                ->whereYear('date', '<=', $previousYear)
                ->whereNull('deleted_at')
                ->groupBy('account_id');

            $prevQuery = $debitPrev->unionAll($creditPrev)->get()->groupBy('account_id');

            foreach ($items as $it) {
                $rows = $prevQuery[$it->id] ?? collect();
                $debit = $rows->sum('debit_amount');
                $credit = $rows->sum('credit_amount');
                $openingBalance[$it->id] = ($baseSaldo[$it->id] ?? 0) + ($debit - $credit);
            }
        } else {
            foreach ($items as $it) {
                $openingBalance[$it->id] = $baseSaldo[$it->id] ?? 0;
            }
        }

        $debits = DB::table('journals')
            ->select(
                DB::raw("debit_account_id AS account_id"),
                DB::raw("MONTH(date) AS month"),
                DB::raw("SUM(total_debit) AS debit_amount"),
                DB::raw("0 AS credit_amount")
            )
            ->whereYear('date', $year)
            ->whereNull('deleted_at')
            ->groupBy('account_id', 'month');

        $credits = DB::table('journals')
            ->select(
                DB::raw("credit_account_id AS account_id"),
                DB::raw("MONTH(date) AS month"),
                DB::raw("0 AS debit_amount"),
                DB::raw("SUM(total_credit) AS credit_amount")
            )
            ->whereYear('date', $year)
            ->whereNull('deleted_at')
            ->groupBy('account_id', 'month');

        $journalMonthly = $debits->unionAll($credits)->get()->groupBy('account_id');

        $data = [];
        foreach ($items as $item) {
            $row = [];
            $isBalanceSheet = $this->isBalanceSheetAccount($item->kode);
            
            if ($isBalanceSheet) {
                $saldo = $openingBalance[$item->id] ?? 0;
                for ($m = 1; $m <= 12; $m++) {
                    $trx = $journalMonthly[$item->id] ?? collect();
                    $debit = $trx->where('month', $m)->sum('debit_amount');
                    $credit = $trx->where('month', $m)->sum('credit_amount');
                    $saldo = $saldo + $debit - $credit;
                    $row["month_$m"] = $saldo;
                }
                $row['opening'] = $openingBalance[$item->id] ?? 0;
                $row['total'] = $row['month_12'];
            } else {
                for ($m = 1; $m <= 12; $m++) {
                    $trx = $journalMonthly[$item->id] ?? collect();
                    $debit = $trx->where('month', $m)->sum('debit_amount');
                    $credit = $trx->where('month', $m)->sum('credit_amount');
                    $row["month_$m"] = $debit - $credit;
                }
                $row['opening'] = 0;
                $row['total'] = array_sum(array_filter($row, function($key) {
                    return strpos($key, 'month_') === 0;
                }, ARRAY_FILTER_USE_KEY));
            }
            
            $data[$item->id] = $row;
        }

        // Apply custom calculation rules
        $c2101 = $items->where('kode', 'C21-01')->first();
        $c2102 = $items->where('kode', 'C21-02')->first();
        $c2199 = $items->where('kode', 'C21-99')->first();

        if ($c2101 && $c2102 && $c2199) {
            // Calculate C2199 opening
            $c2199Opening = 0;
            if ($year > 2024) {
                $revenueExpenseIds = $items->filter(function($item) {
                    return $this->isIncomeStatementAccount($item->kode);
                })->pluck('id');
                
                if ($year == 2025) {
                    $c2199Opening = $c2199->tahun_2024 ?? 0;
                } else if (!$revenueExpenseIds->isEmpty()) {
                    $debit = DB::table('journals')
                        ->whereIn('debit_account_id', $revenueExpenseIds)
                        ->whereYear('date', '>=', 2025)
                        ->whereYear('date', '<=', $previousYear)
                        ->whereNull('deleted_at')
                        ->sum('total_debit');
                    
                    $credit = DB::table('journals')
                        ->whereIn('credit_account_id', $revenueExpenseIds)
                        ->whereYear('date', '>=', 2025)
                        ->whereYear('date', '<=', $previousYear)
                        ->whereNull('deleted_at')
                        ->sum('total_credit');
                    
                    $c2199Opening = $debit - $credit;
                }
            }
            
            // Calculate C2101 opening
            $c2101Opening = 0;
            if ($year > 2024) {
                $base2024C2101 = $c2101->tahun_2024 ?? 0;
                $base2024C2199 = $c2199->tahun_2024 ?? 0;
                
                if ($year == 2025) {
                    $c2101Opening = $base2024C2101;
                } else {
                    $revenueExpenseIds = $items->filter(function($item) {
                        return $this->isIncomeStatementAccount($item->kode);
                    })->pluck('id');
                    
                    if (!$revenueExpenseIds->isEmpty()) {
                        $debit = DB::table('journals')
                            ->whereIn('debit_account_id', $revenueExpenseIds)
                            ->whereYear('date', '>=', 2025)
                            ->whereYear('date', '<=', $previousYear)
                            ->whereNull('deleted_at')
                            ->sum('total_debit');
                        
                        $credit = DB::table('journals')
                            ->whereIn('credit_account_id', $revenueExpenseIds)
                            ->whereYear('date', '>=', 2025)
                            ->whereYear('date', '<=', $previousYear)
                            ->whereNull('deleted_at')
                            ->sum('total_credit');
                        
                        $netMutation = $debit - $credit;
                        $c2101Opening = $base2024C2101 + $base2024C2199 + $netMutation - $c2199Opening;
                    } else {
                        $c2101Opening = $base2024C2101;
                    }
                }
            }
            
            // Calculate C2199 monthly mutations
            $c2199Monthly = array_fill(1, 12, 0);
            $revenueExpenseItems = $items->filter(function($item) {
                return $this->isIncomeStatementAccount($item->kode);
            });
            
            foreach ($revenueExpenseItems as $item) {
                $trx = $journalMonthly[$item->id] ?? collect();
                for ($m = 1; $m <= 12; $m++) {
                    $debit = $trx->where('month', $m)->sum('debit_amount');
                    $credit = $trx->where('month', $m)->sum('credit_amount');
                    $c2199Monthly[$m] += ($debit - $credit);
                }
            }
            
            // Apply C2101 rules
            $data[$c2101->id]['opening'] = $c2101Opening;
            $runningBalance = $c2101Opening + $c2199Opening;
            for ($m = 1; $m <= 12; $m++) {
                $runningBalance += $c2199Monthly[$m];
                $data[$c2101->id]["month_$m"] = $runningBalance;
            }
            $data[$c2101->id]['total'] = $data[$c2101->id]['month_12'];
            
            // Apply C2102 rules
            $data[$c2102->id]['opening'] = 0;
            $c2102Monthly = [];
            for ($m = 1; $m <= 12; $m++) {
                $trx = $journalMonthly[$c2102->id] ?? collect();
                $debit = $trx->where('month', $m)->sum('debit_amount');
                $credit = $trx->where('month', $m)->sum('credit_amount');
                $c2102Monthly[$m] = $debit - $credit;
                $data[$c2102->id]["month_$m"] = $c2102Monthly[$m];
            }
            $data[$c2102->id]['total'] = array_sum($c2102Monthly);
            
            // Apply C2199 rules
            $data[$c2199->id]['opening'] = $c2199Opening;
            for ($m = 1; $m <= 12; $m++) {
                $data[$c2199->id]["month_$m"] = $c2199Monthly[$m];
            }
            $data[$c2199->id]['total'] = array_sum($c2199Monthly);
        }

        $accountGroups = $this->accountGroups;
        return view('financial_position.index', compact('items', 'data', 'year', 'accountGroups'));
    }
}