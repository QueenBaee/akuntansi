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

        // Ambil semua akun cashflow
        $cashflows = Cashflow::orderBy('id')->get();

        // Ambil jurnal bulanan
        $journalMonthly = $this->getMonthlyJournalData($year);

        // Build data awal
        $data = [];
        foreach ($cashflows as $cashflow) {
            $data[$cashflow->id] = ['total' => 0];
            for ($m = 1; $m <= 12; $m++) {
                $monthVal = 0;
                if (isset($journalMonthly[$cashflow->id])) {
                    $row = $journalMonthly[$cashflow->id]->firstWhere('month', $m);
                    if ($row) {
                        // For expenses (E codes), show as negative (cash_out - cash_in)
                        // For income (R codes), show as positive (cash_in - cash_out)
                        if (str_starts_with($cashflow->kode, 'E')) {
                            $monthVal = $row->total_out - $row->total_in;
                        } else {
                            $monthVal = $row->total_in - $row->total_out;
                        }
                    }
                }
                $data[$cashflow->id]["month_$m"] = $monthVal;
                $data[$cashflow->id]['total'] += $monthVal;
            }
        }

        // Build struktur parent-child
        $tree = $this->buildTreeWithMonthlyData($cashflows, $data);

        // Surplus deficit container
        $surplusDeficit = ['total' => 0];

        // Hitung parent & surplus/deficit sekaligus
        $this->calculateMonthlyTotals($tree, $data, $surplusDeficit);

        // Flatten untuk view
        $flattenedData = [];
        $this->flattenTree($tree, $flattenedData);

        return view('cashflow_report.index', compact('flattenedData', 'data', 'year', 'surplusDeficit'));
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

    private function calculateMonthlyTotals(&$nodes, &$data, &$surplusDeficit)
    {
        foreach ($nodes as &$node) {
            if (!$node['is_leaf']) {
                // Reset parent bulan
                $data[$node['id']] = ['total' => 0];
                for ($m = 1; $m <= 12; $m++) {
                    $data[$node['id']]["month_$m"] = 0;
                }

                // Hitung anak secara rekursif
                $this->calculateMonthlyTotals($node['children'], $data, $surplusDeficit);

                // Jumlahkan nilai anak ke parent
                foreach ($node['children'] as $child) {
                    for ($m = 1; $m <= 12; $m++) {
                        $data[$node['id']]["month_$m"] += $data[$child['id']]["month_$m"];
                    }
                    $data[$node['id']]['total'] += $data[$child['id']]['total'];
                }
            }
        }

        // Calculate surplus/deficit after all parent totals are computed
        $this->calculateSurplusDeficit($nodes, $data, $surplusDeficit);
    }

    private function calculateSurplusDeficit($nodes, $data, &$surplusDeficit)
    {
        // Initialize
        for ($m = 1; $m <= 12; $m++) {
            $surplusDeficit["pemasukan_$m"] = 0;
            $surplusDeficit["pengeluaran_$m"] = 0;
        }

        // Sum all root level R and E totals
        foreach ($nodes as $node) {
            if ($node['parent_id'] === null && ($node['code'] === 'R' || $node['code'] === 'E')) {
                for ($m = 1; $m <= 12; $m++) {
                    $value = $data[$node['id']]["month_$m"] ?? 0;
                    
                    if ($node['code'] === 'R') {
                        $surplusDeficit["pemasukan_$m"] += $value;
                    } elseif ($node['code'] === 'E') {
                        $surplusDeficit["pengeluaran_$m"] += $value;
                    }
                }
            }
        }

        // Calculate final surplus/deficit
        $surplusDeficit['total'] = 0;
        for ($m = 1; $m <= 12; $m++) {
            $surplusDeficit["month_$m"] = $surplusDeficit["pemasukan_$m"] - $surplusDeficit["pengeluaran_$m"];
            $surplusDeficit['total'] += $surplusDeficit["month_$m"];
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
