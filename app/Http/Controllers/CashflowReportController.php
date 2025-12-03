<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cashflow;
use Illuminate\Support\Facades\DB;

class CashflowReportController extends Controller
{
    public function index(Request $request)
    {
        $year = $request->year ?? date('Y');
        $items = Cashflow::orderBy('id')->get();

        // Calculate opening balance from all journal entries before selected year
        $openingBalance = [];
        $openingQuery = DB::table('journals as j')
            ->join('cashflows as c', 'j.cashflow_id', '=', 'c.id')
            ->select(
                'j.cashflow_id',
                DB::raw('SUM(CASE WHEN j.debit_account_id = c.trial_balance_id THEN j.total_debit ELSE 0 END) as total_debit'),
                DB::raw('SUM(CASE WHEN j.credit_account_id = c.trial_balance_id THEN j.total_credit ELSE 0 END) as total_credit')
            )
            ->where(DB::raw('YEAR(j.date)'), '<', $year)
            ->groupBy('j.cashflow_id')
            ->get()
            ->keyBy('cashflow_id');

        foreach ($items as $item) {
            $opening = $openingQuery[$item->id] ?? null;
            $openingBalance[$item->id] = $opening ? ($opening->total_debit - $opening->total_credit) : 0;
        }

        // Calculate monthly movements for selected year
        $journalMonthly = DB::table('journals as j')
            ->join('cashflows as c', 'j.cashflow_id', '=', 'c.id')
            ->select(
                'j.cashflow_id',
                DB::raw('MONTH(j.date) as month'),
                DB::raw('SUM(CASE WHEN j.debit_account_id = c.trial_balance_id THEN j.total_debit ELSE 0 END) as total_debit'),
                DB::raw('SUM(CASE WHEN j.credit_account_id = c.trial_balance_id THEN j.total_credit ELSE 0 END) as total_credit')
            )
            ->whereYear('j.date', $year)
            ->groupBy('j.cashflow_id', 'month')
            ->get()
            ->groupBy('cashflow_id');

        // Calculate running balances
        $data = [];
        foreach ($items as $item) {
            $saldo = $openingBalance[$item->id];
            $row = [];
            $trx = $journalMonthly[$item->id] ?? collect();

            for ($m = 1; $m <= 12; $m++) {
                $monthData = $trx->where('month', $m)->first();
                if ($monthData) {
                    $saldo += ($monthData->total_debit - $monthData->total_credit);
                }
                $row["month_$m"] = $saldo;
            }

            $row['total'] = $saldo;
            $row['opening'] = $openingBalance[$item->id];
            $data[$item->id] = $row;
        }

        // Build enhanced structure with subtotals
        $enhancedItems = [];
        $subtotals = [];
        
        // Group items by main code (R, E, F)
        $grouped = $items->groupBy(function($item) {
            return substr($item->kode, 0, 1);
        });
        
        foreach ($grouped as $mainCode => $mainItems) {
            // Group by level 1 (R1, E1, etc.)
            $level1Groups = $mainItems->groupBy(function($item) {
                if ($item->level <= 1) return $item->kode;
                $parts = explode('-', $item->kode);
                return $parts[0];
            });
            
            $mainTotals = array_fill(1, 12, 0);
            $mainTotalYear = 0;
            $mainTotalOpening = 0;
            
            foreach ($level1Groups as $level1Code => $level1Items) {
                // Add individual items
                foreach ($level1Items as $item) {
                    $enhancedItems[] = $item;
                }
                
                // Calculate level 1 subtotal
                $level1Totals = array_fill(1, 12, 0);
                $level1TotalYear = 0;
                $level1TotalOpening = 0;
                
                foreach ($level1Items as $item) {
                    $itemData = $data[$item->id] ?? [];
                    for ($m = 1; $m <= 12; $m++) {
                        $level1Totals[$m] += $itemData["month_$m"] ?? 0;
                    }
                    $level1TotalYear += $itemData['total'] ?? 0;
                    $level1TotalOpening += $itemData['opening'] ?? 0;
                }
                
                // Add subtotal row if needed
                if (count($level1Items) > 1 || $level1Items->first()->level > 1) {
                    $subtotalItem = (object)[
                        'id' => 'subtotal_' . $level1Code,
                        'kode' => '',
                        'keterangan' => 'Subtotal ' . $level1Code,
                        'level' => 'subtotal_1',
                        'is_subtotal' => true
                    ];
                    $enhancedItems[] = $subtotalItem;
                    
                    $subtotalData = [];
                    for ($m = 1; $m <= 12; $m++) {
                        $subtotalData["month_$m"] = $level1Totals[$m];
                    }
                    $subtotalData['total'] = $level1TotalYear;
                    $subtotalData['opening'] = $level1TotalOpening;
                    $data[$subtotalItem->id] = $subtotalData;
                }
                
                // Add to main totals
                for ($m = 1; $m <= 12; $m++) {
                    $mainTotals[$m] += $level1Totals[$m];
                }
                $mainTotalYear += $level1TotalYear;
                $mainTotalOpening += $level1TotalOpening;
            }
            
            // Add main category total
            $mainCategoryName = [
                'R' => 'TOTAL PEMASUKAN',
                'E' => 'TOTAL PENGELUARAN', 
                'F' => 'TOTAL INVESTASI & PENDANAAN'
            ][$mainCode] ?? 'TOTAL ' . $mainCode;
            
            $totalItem = (object)[
                'id' => 'total_' . $mainCode,
                'kode' => '',
                'keterangan' => $mainCategoryName,
                'level' => 'total_main',
                'is_total' => true
            ];
            $enhancedItems[] = $totalItem;
            
            $totalData = [];
            for ($m = 1; $m <= 12; $m++) {
                $totalData["month_$m"] = $mainTotals[$m];
            }
            $totalData['total'] = $mainTotalYear;
            $totalData['opening'] = $mainTotalOpening;
            $data[$totalItem->id] = $totalData;
            
            $subtotals[$mainCode] = $totalData;
        }
        
        // Add SURPLUS/DEFICIT calculations
        if (isset($subtotals['R']) && isset($subtotals['E'])) {
            $surplusItem = (object)[
                'id' => 'surplus_operational',
                'kode' => '',
                'keterangan' => 'SURPLUS / DEFISIT USAHA',
                'level' => 'surplus',
                'is_surplus' => true
            ];
            $enhancedItems[] = $surplusItem;
            
            $surplusData = [];
            for ($m = 1; $m <= 12; $m++) {
                $surplusData["month_$m"] = ($subtotals['R']["month_$m"] ?? 0) - ($subtotals['E']["month_$m"] ?? 0);
            }
            $surplusData['total'] = ($subtotals['R']['total'] ?? 0) - ($subtotals['E']['total'] ?? 0);
            $surplusData['opening'] = ($subtotals['R']['opening'] ?? 0) - ($subtotals['E']['opening'] ?? 0);
            $data[$surplusItem->id] = $surplusData;
        }
        
        if (isset($subtotals['F'])) {
            $finalSurplusItem = (object)[
                'id' => 'surplus_final',
                'kode' => '',
                'keterangan' => 'SURPLUS / DEFISIT BERSIH',
                'level' => 'surplus_final',
                'is_surplus' => true
            ];
            $enhancedItems[] = $finalSurplusItem;
            
            $finalSurplusData = [];
            $operationalSurplus = $data['surplus_operational'] ?? [];
            for ($m = 1; $m <= 12; $m++) {
                $finalSurplusData["month_$m"] = ($operationalSurplus["month_$m"] ?? 0) + ($subtotals['F']["month_$m"] ?? 0);
            }
            $finalSurplusData['total'] = ($operationalSurplus['total'] ?? 0) + ($subtotals['F']['total'] ?? 0);
            $finalSurplusData['opening'] = ($operationalSurplus['opening'] ?? 0) + ($subtotals['F']['opening'] ?? 0);
            $data[$finalSurplusItem->id] = $finalSurplusData;
        }

        return view('cashflow_report.index', compact('enhancedItems', 'data', 'year'));
    }
}
