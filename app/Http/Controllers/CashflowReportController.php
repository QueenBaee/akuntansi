<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cashflow;
use Illuminate\Support\Facades\DB;

class CashflowReportController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'year' => 'nullable|integer|min:2000|max:' . (date('Y') + 10)
        ]);

        $year = (int) ($request->year ?? date('Y'));

        // Load all cashflow items in stable order by kode so grouping follows kode order
        $items = Cashflow::orderBy('id')->get();

        // Build maps for quick access
        $itemsByKode = [];
        foreach ($items as $it) {
            $itemsByKode[$it->kode] = $it;
        }

        // Determine parent kode for each item:
        // - If kode contains '-', parent is everything before last '-'
        // - Else if kode length > 1 and starts with letter+digit (e.g., R1), parent is first letter (R)
        // - Else no parent (root)
        $parentOf = [];
        foreach ($items as $it) {
            $kode = $it->kode;
            $parent = null;
            if (strpos($kode, '-') !== false) {
                $parent = substr($kode, 0, strrpos($kode, '-'));
            } else {
                // Handle codes like R1 -> parent R
                if (preg_match('/^[A-Z]\d+$/i', $kode)) {
                    $parent = substr($kode, 0, 1);
                } else {
                    // single letter like R has no parent
                    $parent = null;
                }
            }
            if ($parent && isset($itemsByKode[$parent])) {
                $parentOf[$kode] = $parent;
            } else {
                $parentOf[$kode] = null;
            }
        }

        // Build children lists preserving original kode order
        $children = [];
        foreach ($items as $it) {
            $children[$it->kode] = [];
        }
        foreach ($items as $it) {
            $p = $parentOf[$it->kode];
            if ($p !== null) {
                $children[$p][] = $it->kode;
            }
        }

        // Find roots (items that have no parent)
        $roots = [];
        foreach ($items as $it) {
            if ($parentOf[$it->kode] === null) {
                $roots[] = $it->kode;
            }
        }

        // Load opening balances: sum(debit) - sum(credit) for journals before $year
        $openingQuery = DB::table('journals as j')
            ->join('cashflows as c', 'j.cashflow_id', '=', 'c.id')
            ->select(
                'j.cashflow_id',
                DB::raw('SUM(CASE WHEN j.debit_account_id = c.trial_balance_id THEN j.total_debit ELSE 0 END) as total_debit'),
                DB::raw('SUM(CASE WHEN j.credit_account_id = c.trial_balance_id THEN j.total_credit ELSE 0 END) as total_credit')
            )
            ->whereYear('j.date', '<', $year)
            ->groupBy('j.cashflow_id')
            ->get()
            ->keyBy('cashflow_id');

        $openingBalance = [];
        foreach ($items as $it) {
            $row = $openingQuery[$it->id] ?? null;
            $openingBalance[$it->id] = $row ? ($row->total_debit - $row->total_credit) : 0;
        }

        // Load monthly journal movements for year
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

        // Compute running balances per item (opening + month deltas)
        $data = [];
        foreach ($items as $it) {
            $saldo = $openingBalance[$it->id] ?? 0;
            $row = [];
            $trx = $journalMonthly[$it->id] ?? collect();

            for ($m = 1; $m <= 12; $m++) {
                $monthData = $trx->where('month', $m)->first();
                if ($monthData) {
                    $saldo += ($monthData->total_debit - $monthData->total_credit);
                }
                $row["month_$m"] = $saldo;
            }

            $row['total'] = $saldo;
            $row['opening'] = $openingBalance[$it->id] ?? 0;
            $data[$it->kode] = $row; // key by kode for easier aggregation
            $data[$it->id] = $row;   // also keep id key for existing view access
        }

        // Helper to sum two aggregate rows
        $sumRows = function ($a, $b) {
            $res = [];
            for ($m = 1; $m <= 12; $m++) {
                $res["month_$m"] = ($a["month_$m"] ?? 0) + ($b["month_$m"] ?? 0);
            }
            $res['total'] = ($a['total'] ?? 0) + ($b['total'] ?? 0);
            $res['opening'] = ($a['opening'] ?? 0) + ($b['opening'] ?? 0);
            return $res;
        };

        // Recursive traversal that builds enhancedItems and returns aggregated sums for parent
        $enhancedItems = [];
        $visited = [];

        $buildRec = function ($kode) use (&$buildRec, &$enhancedItems, &$children, $itemsByKode, $data, $sumRows, &$visited) {
            // Prevent infinite loop
            if (isset($visited[$kode])) {
                return ['month_1'=>0,'month_2'=>0,'month_3'=>0,'month_4'=>0,'month_5'=>0,'month_6'=>0,'month_7'=>0,'month_8'=>0,'month_9'=>0,'month_10'=>0,'month_11'=>0,'month_12'=>0,'total'=>0,'opening'=>0];
            }
            $visited[$kode] = true;

            // push current node (if exists in itemsByKode)
            if (isset($itemsByKode[$kode])) {
                $enhancedItems[] = $itemsByKode[$kode];
            }

            // aggregate starts with current node's own row (so parent totals include its own value)
            $agg = $data[$kode] ?? ['month_1'=>0,'month_2'=>0,'month_3'=>0,'month_4'=>0,'month_5'=>0,'month_6'=>0,'month_7'=>0,'month_8'=>0,'month_9'=>0,'month_10'=>0,'month_11'=>0,'month_12'=>0,'total'=>0,'opening'=>0];

            // process children in original children order
            if (!empty($children[$kode])) {
                foreach ($children[$kode] as $childKode) {
                    $childAgg = $buildRec($childKode);
                    // accumulate into parent's agg
                    $agg = $sumRows($agg, $childAgg);
                }

                // After processing children, insert a subtotal row for this group that sums children + self.
                // Make sure subtotal id unique and not colliding with real kode
                $subtotalId = 'subtotal_' . $kode;
                $subtotalItem = (object)[
                    'id' => $subtotalId,
                    'kode' => '',
                    'keterangan' => 'Subtotal ' . $kode,
                    // level: if parent exists and has level, use parent level; else fallback to 0
                    'level' => (isset($itemsByKode[$kode]) ? ($itemsByKode[$kode]->level ?? 0) : 0),
                    'is_subtotal' => true
                ];
                $enhancedItems[] = $subtotalItem;
                // store aggregated data under that id so view can read it
                $data[$subtotalId] = $agg;
                // return agg to upper caller
                return $agg;
            }

            // no children: return own agg
            return $agg;
        };

        // Loop through roots in items order
        foreach ($roots as $rootKode) {
            $buildRec($rootKode);
        }

        // After building enhancedItems we might want top-level totals per main code (R, E, F)
        // Compute totals by scanning enhancedItems aggregations for root groups starting with letter R/E/F
        $mainTotals = [];
        foreach ($enhancedItems as $ei) {
            $key = $ei->kode ?? $ei->id;
            // only consider root real items (exist in itemsByKode and single-letter kode)
            if (isset($itemsByKode[$key]) && strlen($key) === 1) {
                // find aggregated row stored at subtotal key (subtotal_rootKode) if exists; else use data[$key]
                $agg = $data['subtotal_' . $key] ?? ($data[$key] ?? null);
                if ($agg) {
                    $mainTotals[$key] = $agg;
                } else {
                    // fallback zero
                    $mainTotals[$key] = ['month_1'=>0,'month_2'=>0,'month_3'=>0,'month_4'=>0,'month_5'=>0,'month_6'=>0,'month_7'=>0,'month_8'=>0,'month_9'=>0,'month_10'=>0,'month_11'=>0,'month_12'=>0,'total'=>0,'opening'=>0];
                }
            }
        }

        // Append grand total rows for R, E, F in the order they appear among roots (if present)
        foreach ($roots as $rKode) {
            if (in_array($rKode, ['R','E','F']) && isset($mainTotals[$rKode])) {
                $totalItem = (object)[
                    'id' => 'total_' . $rKode,
                    'kode' => '',
                    'keterangan' => ($rKode === 'R' ? 'TOTAL PEMASUKAN' : ($rKode === 'E' ? 'TOTAL PENGELUARAN' : ($rKode === 'F' ? 'TOTAL INVESTASI & PENDANAAN' : 'TOTAL ' . $rKode))),
                    'level' => 0,
                    'is_total' => true
                ];
                $enhancedItems[] = $totalItem;
                $data[$totalItem->id] = $mainTotals[$rKode];
            }
        }

        // SURPLUS/DEFICIT operational (R - E)
        if (isset($mainTotals['R']) && isset($mainTotals['E'])) {
            $surplusData = [];
            for ($m = 1; $m <= 12; $m++) {
                $surplusData["month_$m"] = ($mainTotals['R']["month_$m"] ?? 0) - ($mainTotals['E']["month_$m"] ?? 0);
            }
            $surplusData['total'] = ($mainTotals['R']['total'] ?? 0) - ($mainTotals['E']['total'] ?? 0);
            $surplusData['opening'] = ($mainTotals['R']['opening'] ?? 0) - ($mainTotals['E']['opening'] ?? 0);

            $surplusItem = (object)[
                'id' => 'surplus_operational',
                'kode' => '',
                'keterangan' => 'SURPLUS / DEFISIT USAHA',
                'level' => 0,
                'is_surplus' => true
            ];
            $enhancedItems[] = $surplusItem;
            $data[$surplusItem->id] = $surplusData;

            // If F exists, final surplus = operational + F
            if (isset($mainTotals['F'])) {
                $finalSurplus = [];
                for ($m = 1; $m <= 12; $m++) {
                    $finalSurplus["month_$m"] = $surplusData["month_$m"] + ($mainTotals['F']["month_$m"] ?? 0);
                }
                $finalSurplus['total'] = $surplusData['total'] + ($mainTotals['F']['total'] ?? 0);
                $finalSurplus['opening'] = $surplusData['opening'] + ($mainTotals['F']['opening'] ?? 0);

                $finalItem = (object)[
                    'id' => 'surplus_final',
                    'kode' => '',
                    'keterangan' => 'SURPLUS / DEFISIT BERSIH',
                    'level' => 0,
                    'is_surplus' => true
                ];
                $enhancedItems[] = $finalItem;
                $data[$finalItem->id] = $finalSurplus;
            }
        }

        // Note: $enhancedItems elements may be Eloquent models (original rows) or stdClass objects for subtotal/total.
        // The Blade expects $data indexed by item->id or custom id strings; we filled both forms.
        return view('cashflow_report.index', compact('enhancedItems', 'data', 'year'));
    }
}
