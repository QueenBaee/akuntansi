<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TrialBalance;
use Illuminate\Support\Facades\DB;

class ComprehensiveIncomeController extends Controller
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
        '13. PENDAPATAN' => ['R11-01', 'R11-02', 'R11-03'],
        '14. BEBAN PRODUKSI' => ['E11-01', 'E11-02', 'E11-03', 'E11-04', 'E11-05', 'E11-06', 'E11-07'],
        'PEMASARAN' => ['E21-01', 'E21-02'],
        '15. ADMINISTRASI & UMUM' => ['E22-01', 'E22-02', 'E22-03', 'E22-04', 'E22-05', 'E22-06', 'E22-07', 'E22-08', 'E22-09', 'E22-10', 'E22-11', 'E22-89', 'E22-96', 'E22-97', 'E22-98', 'E22-99'],
        '16. PENDAPATAN LAIN-LAIN' => ['R21-01', 'R21-02', 'R21-99'],
        '17. BEBAN LAIN-LAIN' => ['E31-01', 'E31-02', 'E31-03'],
        'BEBAN PAJAK PENGHASILAN' => ['E91-01'],
        'SALDO LABA AWAL' => ['C21-01'],
    ];

    public function index(Request $request)
    {
        $year = $request->year ?? date('Y');
        $previousYear = $year - 1;

        // Use exact same logic as NotesToFinancialStatementsController
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
                    if (substr($item->kode, 0, 1) === 'E') {
                        $row["month_$m"] = $debit - $credit;
                    } else {
                        $row["month_$m"] = $credit - $debit;
                    }
                }
                $row['opening'] = 0;
                $row['total'] = array_sum(array_filter($row, function($key) {
                    return strpos($key, 'month_') === 0;
                }, ARRAY_FILTER_USE_KEY));
            }
            
            $data[$item->id] = $row;
        }

        // Apply custom calculation rules (same as NotesToFinancialStatementsController)
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

        return view('comprehensive_income.index', compact('items', 'data', 'year'));
    }
}