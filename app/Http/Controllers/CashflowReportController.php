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

        // Get all cashflow accounts
        $cashflows = Cashflow::orderBy('id')->get();
        
        // Get monthly journal data
        $journalMonthly = $this->getMonthlyJournalData($year);
        
        // Build data array with monthly values
        $data = [];
        foreach ($cashflows as $cashflow) {
            $data[$cashflow->id] = ['total' => 0];
            
            for ($m = 1; $m <= 12; $m++) {
                $monthVal = 0;
                if (isset($journalMonthly[$cashflow->id])) {
                    $row = $journalMonthly[$cashflow->id]->firstWhere('month', $m);
                    if ($row) {
                        $monthVal = $row->total_in - $row->total_out;
                    }
                }
                $data[$cashflow->id]["month_$m"] = $monthVal;
                $data[$cashflow->id]['total'] += $monthVal;
            }
        }
        
        // Build hierarchical tree and calculate parent totals
        $tree = $this->buildTreeWithMonthlyData($cashflows, $data);
        $this->calculateMonthlyTotals($tree, $data);
        
        // Flatten tree for display
        $flattenedData = [];
        $this->flattenTree($tree, $flattenedData);
        
        return view('cashflow_report.index', compact('flattenedData', 'data', 'year'));
    }

    private function getMonthlyJournalData($year)
    {
        return DB::table('journals')
            ->select(
                'cashflow_id',
                DB::raw('MONTH(date) as month'),
                DB::raw('SUM(cash_in) as total_in'),
                DB::raw('SUM(cash_out) as total_out')
            )
            ->whereNotNull('cashflow_id')
            ->whereYear('date', $year)
            ->groupBy('cashflow_id', 'month')
            ->get()
            ->groupBy('cashflow_id');
    }

    private function buildTreeWithMonthlyData($cashflows, $data)
    {
        $indexed = [];
        $tree = [];
        
        foreach ($cashflows as $cashflow) {
            $node = [
                'id' => $cashflow->id,
                'code' => $cashflow->kode,
                'name' => $cashflow->keterangan,
                'parent_id' => $cashflow->parent_id ?? null,
                'children' => [],
                'is_leaf' => true
            ];
            
            $indexed[$cashflow->id] = $node;
        }
        
        // Build parent-child relationships
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

    private function calculateMonthlyTotals(&$nodes, &$data)
    {
        foreach ($nodes as &$node) {
            if (!$node['is_leaf']) {
                // Reset monthly values for parent nodes
                $data[$node['id']] = ['total' => 0];
                for ($m = 1; $m <= 12; $m++) {
                    $data[$node['id']]["month_$m"] = 0;
                }
                
                // Recursively calculate children totals
                $this->calculateMonthlyTotals($node['children'], $data);
                
                // Sum children amounts
                foreach ($node['children'] as $child) {
                    for ($m = 1; $m <= 12; $m++) {
                        $data[$node['id']]["month_$m"] += $data[$child['id']]["month_$m"];
                    }
                    $data[$node['id']]['total'] += $data[$child['id']]['total'];
                }
            }
        }
    }

    private function flattenTree($nodes, &$result, $depth = 0)
    {
        foreach ($nodes as $node) {
            $result[] = [
                'id' => $node['id'],
                'code' => $node['code'],
                'name' => $node['name'],
                'depth' => $depth,
                'is_leaf' => $node['is_leaf']
            ];
            
            if (!empty($node['children'])) {
                $this->flattenTree($node['children'], $result, $depth + 1);
            }
        }
    }
}
