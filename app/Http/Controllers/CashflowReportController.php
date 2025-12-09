<?php

namespace App\Http\Controllers;

use App\Models\Cashflow;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CashflowReportController extends Controller
{
    public function index(Request $request)
    {
        $year = $request->input('year', date('Y'));
        
        // Filter unique cashflows to avoid duplicates
        $cashflows = Cashflow::with('trialBalance')
            ->orderBy('id')
            ->get()
            ->unique(function($item) {
                return $item->kode . '-' . ($item->parent_id ?? 'root');
            })
            ->values();
        
        $journalData = $this->getJournalData($year);
        
        $data = $this->buildMonthlyData($cashflows, $journalData);
        $tree = $this->buildTree($cashflows);
        
        $this->calculateTotals($tree, $data);
        $surplusDeficit = $this->calculateSurplusDeficit($tree, $data);
        $netSurplusDeficit = $this->calculateNetSurplusDeficit($tree, $data, $surplusDeficit);
        $cashBankBalances = $this->calculateCashBankBalance($year, $netSurplusDeficit);
        $cashBankDetails = $this->calculateCashBankDetails($year);
        
        $flattenedData = $this->flattenTreeWithSummaries($tree);
        
        // Add surplus deficit data to main data array
        $data['surplus_deficit'] = $surplusDeficit;
        $data['net_surplus_deficit'] = $netSurplusDeficit;
        $data['cash_bank_opening'] = $cashBankBalances['opening'];
        $data['cash_bank_closing'] = $cashBankBalances['closing'];
        
        // Add detailed cash bank data with prefixed keys to avoid conflicts
        foreach ($cashBankDetails as $id => $detail) {
            $data['tb_' . $id] = $detail;
        }
        
        // Calculate total for cash bank details
        $data['cash_bank_detail_total'] = $this->calculateCashBankDetailTotal($cashBankDetails);
        
        return view('cashflow_report.index', compact('flattenedData', 'data', 'year', 'surplusDeficit'));
    }

    private function getJournalData($year)
    {
        return DB::table('journals')
            ->select('cashflow_id', DB::raw('MONTH(date) as month, SUM(cash_in) as total_in, SUM(cash_out) as total_out'))
            ->whereNotNull('cashflow_id')
            ->whereYear('date', $year)
            ->groupBy('cashflow_id', 'month')
            ->get()
            ->groupBy('cashflow_id')
            ->map(fn($rows) => $rows->keyBy('month'));
    }

    private function buildMonthlyData($cashflows, $journalData)
    {
        $data = [];
        
        foreach ($cashflows as $cashflow) {
            $monthlyData = ['total' => 0];
            $isExpense = str_starts_with($cashflow->kode, 'E');
            
            for ($m = 1; $m <= 12; $m++) {
                $monthVal = 0;
                if (isset($journalData[$cashflow->id][$m])) {
                    $row = $journalData[$cashflow->id][$m];
                    $monthVal = $isExpense ? 
                        ($row->total_out - $row->total_in) : 
                        ($row->total_in - $row->total_out);
                }
                $monthlyData["month_$m"] = $monthVal;
                $monthlyData['total'] += $monthVal;
            }
            
            $data[$cashflow->id] = $monthlyData;
        }
        
        return $data;
    }

    private function buildTree($cashflows)
    {
        $indexed = $cashflows->keyBy('id')->map(fn($cf) => [
            'id' => $cf->id,
            'code' => $cf->kode,
            'name' => $cf->keterangan,
            'parent_id' => $cf->parent_id,
            'trial_balance_code' => $cf->trialBalance?->kode ?? '',
            'trial_balance_name' => $cf->trialBalance?->keterangan ?? '',
            'children' => [],
            'is_leaf' => true
        ])->toArray();

        $tree = [];
        foreach ($indexed as $id => $node) {
            if ($node['parent_id']) {
                $indexed[$node['parent_id']]['children'][] = &$indexed[$id];
                $indexed[$node['parent_id']]['is_leaf'] = false;
            } else {
                $tree[] = &$indexed[$id];
            }
        }
        
        return $tree;
    }

    private function calculateTotals(&$nodes, &$data)
    {
        foreach ($nodes as &$node) {
            if ($node['is_leaf']) continue;
            
            $data[$node['id']] = array_fill_keys(array_merge(['total'], array_map(fn($m) => "month_$m", range(1, 12))), 0);
            
            $this->calculateTotals($node['children'], $data);
            
            foreach ($node['children'] as $child) {
                $data[$node['id']]['total'] += $data[$child['id']]['total'];
                for ($m = 1; $m <= 12; $m++) {
                    $data[$node['id']]["month_$m"] += $data[$child['id']]["month_$m"];
                }
            }
        }
    }

    private function calculateSurplusDeficit($nodes, $data)
    {
        $surplusDeficit = array_fill_keys(array_merge(['total'], array_map(fn($m) => "month_$m", range(1, 12))), 0);
        $pemasukan = $pengeluaran = array_fill_keys(array_map(fn($m) => "month_$m", range(1, 12)), 0);
        
        foreach ($nodes as $node) {
            if ($node['parent_id'] !== null || !in_array($node['code'], ['R', 'E'])) continue;
            
            for ($m = 1; $m <= 12; $m++) {
                $value = $data[$node['id']]["month_$m"] ?? 0;
                if ($node['code'] === 'R') {
                    $pemasukan["month_$m"] += $value;
                } else {
                    $pengeluaran["month_$m"] += $value;
                }
            }
        }
        
        for ($m = 1; $m <= 12; $m++) {
            $surplusDeficit["month_$m"] = $pemasukan["month_$m"] - $pengeluaran["month_$m"];
            $surplusDeficit['total'] += $surplusDeficit["month_$m"];
        }
        
        return $surplusDeficit;
    }

    private function calculateNetSurplusDeficit($nodes, $data, $surplusDeficit)
    {
        $netSurplusDeficit = array_fill_keys(array_merge(['total'], array_map(fn($m) => "month_$m", range(1, 12))), 0);
        
        // Find INVESTASI DAN PENDANAAN (F) total
        $investmentFinancingTotal = array_fill_keys(array_map(fn($m) => "month_$m", range(1, 12)), 0);
        
        foreach ($nodes as $node) {
            if ($node['code'] === 'F' && $node['parent_id'] === null) {
                for ($m = 1; $m <= 12; $m++) {
                    $investmentFinancingTotal["month_$m"] = $data[$node['id']]["month_$m"] ?? 0;
                }
                break;
            }
        }
        
        // Calculate net surplus/deficit = surplus/deficit + investment & financing
        for ($m = 1; $m <= 12; $m++) {
            $netSurplusDeficit["month_$m"] = $surplusDeficit["month_$m"] + $investmentFinancingTotal["month_$m"];
            $netSurplusDeficit['total'] += $netSurplusDeficit["month_$m"];
        }
        
        return $netSurplusDeficit;
    }

    private function flattenTreeWithSummaries($nodes, $depth = 0)
    {
        $result = [];
        foreach ($nodes as $node) {
            // Add header for non-leaf nodes
            if (!$node['is_leaf']) {
                $result[] = [
                    'id' => $node['id'],
                    'code' => $node['code'],
                    'name' => $node['name'],
                    'trial_balance_code' => $node['trial_balance_code'] ?? '',
                    'trial_balance_name' => $node['trial_balance_name'] ?? '',
                    'depth' => $depth,
                    'is_leaf' => false,
                    'is_header' => true
                ];
            }
            
            // Add children
            if (!empty($node['children'])) {
                $result = array_merge($result, $this->flattenTreeWithSummaries($node['children'], $depth + 1));
            }
            
            // Add leaf nodes or summary for non-leaf
            if ($node['is_leaf']) {
                $result[] = [
                    'id' => $node['id'],
                    'code' => $node['code'],
                    'name' => $node['name'],
                    'trial_balance_code' => $node['trial_balance_code'] ?? '',
                    'trial_balance_name' => $node['trial_balance_name'] ?? '',
                    'depth' => $depth,
                    'is_leaf' => true,
                    'is_header' => false
                ];
            } else {
                // Add summary row for non-leaf nodes
                $result[] = [
                    'id' => $node['id'],
                    'code' => $node['code'],
                    'name' => 'TOTAL ' . $node['name'],
                    'trial_balance_code' => $node['trial_balance_code'] ?? '',
                    'trial_balance_name' => $node['trial_balance_name'] ?? '',
                    'depth' => $depth,
                    'is_leaf' => false,
                    'is_header' => false,
                    'is_summary' => true
                ];
                
                // Add surplus/deficit after total expenses
                if ($node['code'] === 'E' && $node['parent_id'] === null) {
                    $result[] = [
                        'id' => 'surplus_deficit',
                        'code' => 'S/D',
                        'name' => 'SURPLUS/(DEFISIT) USAHA',
                        'trial_balance_code' => '',
                        'trial_balance_name' => '',
                        'depth' => 0,
                        'is_leaf' => false,
                        'is_header' => false,
                        'is_summary' => true,
                        'is_surplus_deficit' => true
                    ];
                }
                
                // Add net surplus/deficit after total investment & financing
                if ($node['code'] === 'F' && $node['parent_id'] === null) {
                    $result[] = [
                        'id' => 'net_surplus_deficit',
                        'code' => 'S/D NET',
                        'name' => 'SURPLUS/(DEFISIT) BERSIH',
                        'trial_balance_code' => '',
                        'trial_balance_name' => '',
                        'depth' => 0,
                        'is_leaf' => false,
                        'is_header' => false,
                        'is_summary' => true,
                        'is_net_surplus_deficit' => true
                    ];
                    
                    // Add cash & bank opening balance row
                    $result[] = [
                        'id' => 'cash_bank_opening',
                        'code' => 'AWAL',
                        'name' => 'SALDO AWAL KAS & BANK',
                        'trial_balance_code' => '',
                        'trial_balance_name' => '',
                        'depth' => 0,
                        'is_leaf' => false,
                        'is_header' => false,
                        'is_summary' => true,
                        'is_cash_bank_opening' => true
                    ];
                    
                    // Add cash & bank closing balance row
                    $result[] = [
                        'id' => 'cash_bank_closing',
                        'code' => 'AKHIR',
                        'name' => 'SALDO AKHIR KAS & BANK',
                        'trial_balance_code' => '',
                        'trial_balance_name' => '',
                        'depth' => 0,
                        'is_leaf' => false,
                        'is_header' => false,
                        'is_summary' => true,
                        'is_cash_bank_closing' => true
                    ];
                    
                    // Add detailed cash & bank breakdown
                    $result = array_merge($result, $this->getCashBankDetailRows());
                }
            }
        }
        return $result;
    }

    private function calculateCashBankBalance($year, $netSurplusDeficit)
    {
        $previousYear = $year - 1;
        $startBaseYear = 2025;
        
        // Get cash & bank trial balance accounts for opening balance (only with values)
        $cashBankAccounts = DB::table('trial_balances as tb')
            ->leftJoin('trial_balances as parent', 'tb.parent_id', '=', 'parent.id')
            ->where(function($query) {
                $query->where('tb.is_kas_bank', true)
                      ->orWhere('parent.is_kas_bank', true);
            })
            ->where('tb.tahun_2024', '>', 0) // Filter only accounts with value
            ->select('tb.id', 'tb.tahun_2024')
            ->get();

        // Calculate opening balance (Trial Balance logic for opening only)
        $totalOpening = 0;
        if ($previousYear >= $startBaseYear) {
            $debitPrev = DB::table('journals')
                ->select(
                    DB::raw("debit_account_id AS account_id"),
                    DB::raw("SUM(total_debit) AS debit_amount"),
                    DB::raw("0 AS credit_amount")
                )
                ->whereYear('date', '>=', $startBaseYear)
                ->whereYear('date', '<=', $previousYear)
                ->whereIn('debit_account_id', $cashBankAccounts->pluck('id'))
                ->groupBy('account_id');

            $creditPrev = DB::table('journals')
                ->select(
                    DB::raw("credit_account_id AS account_id"),
                    DB::raw("0 AS debit_amount"),
                    DB::raw("SUM(total_credit) AS credit_amount")
                )
                ->whereYear('date', '>=', $startBaseYear)
                ->whereYear('date', '<=', $previousYear)
                ->whereIn('credit_account_id', $cashBankAccounts->pluck('id'))
                ->groupBy('account_id');

            $prevQuery = $debitPrev->unionAll($creditPrev)->get()->groupBy('account_id');
            
            foreach ($cashBankAccounts as $account) {
                $rows = $prevQuery[$account->id] ?? collect();
                $debit = $rows->sum('debit_amount');
                $credit = $rows->sum('credit_amount');
                $totalOpening += ($account->tahun_2024 ?? 0) + ($debit - $credit);
            }
        } else {
            $totalOpening = $cashBankAccounts->sum('tahun_2024');
        }
        
        // Use cashflow-based calculation for running balances
        $openingData = ['opening' => $totalOpening];
        $closingData = ['opening' => $totalOpening];
        $runningBalance = $totalOpening;
        
        for ($m = 1; $m <= 12; $m++) {
            // Opening balance for this month is previous month's closing
            $openingData["month_$m"] = $runningBalance;
            
            // Add this month's net surplus/deficit (cashflow-based)
            $runningBalance += $netSurplusDeficit["month_$m"] ?? 0;
            
            // Closing balance for this month
            $closingData["month_$m"] = $runningBalance;
        }
        
        $openingData['total'] = $totalOpening;
        $closingData['total'] = $runningBalance;
        
        return [
            'opening' => $openingData,
            'closing' => $closingData
        ];
    }

    private function calculateCashBankDetails($year)
    {
        $previousYear = $year - 1;
        $startBaseYear = 2025;
        
        // Get cash & bank trial balance accounts (only with values)
        $cashBankAccounts = DB::table('trial_balances as tb')
            ->leftJoin('trial_balances as parent', 'tb.parent_id', '=', 'parent.id')
            ->where(function($query) {
                $query->where('tb.is_kas_bank', true)
                      ->orWhere('parent.is_kas_bank', true);
            })
            ->where('tb.tahun_2024', '>', 0) // Filter only accounts with value
            ->select('tb.id', 'tb.kode', 'tb.keterangan', 'tb.tahun_2024')
            ->get();

        // Calculate opening balance (same logic as Trial Balance Report)
        $openingBalance = [];
        if ($previousYear >= $startBaseYear) {
            // Get previous year mutations
            $debitPrev = DB::table('journals')
                ->select(
                    DB::raw("debit_account_id AS account_id"),
                    DB::raw("SUM(total_debit) AS debit_amount"),
                    DB::raw("0 AS credit_amount")
                )
                ->whereYear('date', '>=', $startBaseYear)
                ->whereYear('date', '<=', $previousYear)
                ->whereIn('debit_account_id', $cashBankAccounts->pluck('id'))
                ->groupBy('account_id');

            $creditPrev = DB::table('journals')
                ->select(
                    DB::raw("credit_account_id AS account_id"),
                    DB::raw("0 AS debit_amount"),
                    DB::raw("SUM(total_credit) AS credit_amount")
                )
                ->whereYear('date', '>=', $startBaseYear)
                ->whereYear('date', '<=', $previousYear)
                ->whereIn('credit_account_id', $cashBankAccounts->pluck('id'))
                ->groupBy('account_id');

            $prevQuery = $debitPrev->unionAll($creditPrev)->get()->groupBy('account_id');
            
            foreach ($cashBankAccounts as $account) {
                $rows = $prevQuery[$account->id] ?? collect();
                $debit = $rows->sum('debit_amount');
                $credit = $rows->sum('credit_amount');
                $openingBalance[$account->id] = ($account->tahun_2024 ?? 0) + ($debit - $credit);
            }
        } else {
            foreach ($cashBankAccounts as $account) {
                $openingBalance[$account->id] = $account->tahun_2024 ?? 0;
            }
        }

        // Get current year monthly mutations (same logic as Trial Balance Report)
        $debits = DB::table('journals')
            ->select(
                DB::raw("debit_account_id AS account_id"),
                DB::raw("MONTH(date) AS month"),
                DB::raw("SUM(total_debit) AS debit_amount"),
                DB::raw("0 AS credit_amount")
            )
            ->whereYear('date', $year)
            ->whereIn('debit_account_id', $cashBankAccounts->pluck('id'))
            ->groupBy('account_id', 'month');

        $credits = DB::table('journals')
            ->select(
                DB::raw("credit_account_id AS account_id"),
                DB::raw("MONTH(date) AS month"),
                DB::raw("0 AS debit_amount"),
                DB::raw("SUM(total_credit) AS credit_amount")
            )
            ->whereYear('date', $year)
            ->whereIn('credit_account_id', $cashBankAccounts->pluck('id'))
            ->groupBy('account_id', 'month');

        $journalMonthly = $debits->unionAll($credits)->get()->groupBy('account_id');

        // Calculate running balances (same logic as Trial Balance Report)
        $details = [];
        foreach ($cashBankAccounts as $account) {
            $saldo = $openingBalance[$account->id] ?? 0;
            $monthlyData = ['opening' => $saldo];
            
            for ($m = 1; $m <= 12; $m++) {
                $trx = $journalMonthly[$account->id] ?? collect();
                $debit = $trx->where('month', $m)->sum('debit_amount');
                $credit = $trx->where('month', $m)->sum('credit_amount');
                
                $saldo = $saldo + $debit - $credit;
                $monthlyData["month_$m"] = $saldo;
            }
            
            $monthlyData['total'] = $saldo;
            $details[$account->id] = $monthlyData;
        }
        
        return $details;
    }

    private function getCashBankDetailRows()
    {
        $cashBankAccounts = DB::table('trial_balances as tb')
            ->leftJoin('trial_balances as parent', 'tb.parent_id', '=', 'parent.id')
            ->where('tb.level', 4)
            ->where(function($query) {
                $query->where('tb.is_kas_bank', true)
                      ->orWhere('parent.is_kas_bank', true);
            })
            ->where('tb.tahun_2024', '>', 0) // Filter only accounts with value
            ->select('tb.id', 'tb.kode', 'tb.keterangan')
            ->orderBy('tb.sort_order')
            ->get();

        $result = [];
        
        foreach ($cashBankAccounts as $account) {
            $result[] = [
                'id' => 'tb_' . $account->id,
                'code' => $account->kode,
                'name' => $account->keterangan,
                'trial_balance_code' => $account->kode,
                'trial_balance_name' => $account->keterangan,
                'depth' => 1,
                'is_leaf' => true,
                'is_header' => false,
                'is_cash_bank_detail' => true
            ];
        }
        
        // Add total detail row
        $result[] = [
            'id' => 'cash_bank_detail_total',
            'code' => 'TOTAL',
            'name' => 'TOTAL RINCIAN KAS & BANK',
            'trial_balance_code' => '',
            'trial_balance_name' => '',
            'depth' => 0,
            'is_leaf' => false,
            'is_header' => false,
            'is_summary' => true,
            'is_cash_bank_detail_total' => true
        ];
        
        return $result;
    }

    private function calculateCashBankDetailTotal($cashBankDetails)
    {
        $total = array_fill_keys(array_merge(['opening', 'total'], array_map(fn($m) => "month_$m", range(1, 12))), 0);
        
        foreach ($cashBankDetails as $detail) {
            $total['opening'] += $detail['opening'];
            $total['total'] += $detail['total'];
            
            for ($m = 1; $m <= 12; $m++) {
                $total["month_$m"] += $detail["month_$m"];
            }
        }
        
        return $total;
    }
}
