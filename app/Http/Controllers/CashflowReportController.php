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
        $cashflows = Cashflow::orderBy('id')
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
        
        $flattenedData = $this->flattenTreeWithSummaries($tree);
        
        // Add surplus deficit data to main data array
        $data['surplus_deficit'] = $surplusDeficit;
        
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
                        'depth' => 0,
                        'is_leaf' => false,
                        'is_header' => false,
                        'is_summary' => true,
                        'is_surplus_deficit' => true
                    ];
                }
            }
        }
        return $result;
    }
}
