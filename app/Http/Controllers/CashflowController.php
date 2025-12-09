<?php

namespace App\Http\Controllers;

use App\Models\Cashflow;
use App\Models\TrialBalance;
use Illuminate\Http\Request;

class CashflowController extends Controller
{
    public function index(Request $request)
    {
        if ($request->search) {
            $query = Cashflow::with(['trialBalance', 'parent', 'children'])
                ->where(function ($q) use ($request) {
                    $q->where('kode', 'like', "%{$request->search}%")
                        ->orWhere('keterangan', 'like', "%{$request->search}%");
                })
                ->orderBy('kode');
            $cashflows = $query->get();
        } else {
            // Get transaction counts for filtering (transactions only)
            $transactionCounts = \DB::table('cash_transactions')
                ->join('trial_balances', function ($join) {
                    $join->on('cash_transactions.cash_account_id', '=', 'trial_balances.id')
                        ->orOn('cash_transactions.contra_account_id', '=', 'trial_balances.id');
                })
                ->select('trial_balances.id', \DB::raw('COUNT(*) as transaction_count'))
                ->groupBy('trial_balances.id')
                ->pluck('transaction_count', 'id');

            $allCashflows = Cashflow::with(['trialBalance', 'parent', 'children'])->get();

            // Add transaction counts (default to 0 for accounts without transactions)
            $allCashflows->each(function ($cashflow) use ($transactionCounts) {
                $cashflow->transaction_count = $cashflow->trial_balance_id
                    ? ($transactionCounts[$cashflow->trial_balance_id] ?? 0)
                    : 0;
            });

            $cashflows = $this->buildFilteredHierarchy($allCashflows);
        }

        return view('cashflow.index', compact('cashflows'));
    }

    private function buildHierarchy($cashflows)
    {
        $result = collect();
        $grouped = $cashflows->groupBy('parent_id');

        // Mulai dari root (parent_id = null)
        $this->addChildren($result, $grouped, null);

        return $result;
    }

    private function addChildren($result, $grouped, $parentId)
    {
        if (! isset($grouped[$parentId])) {
            return;
        }

        $children = $grouped[$parentId]->sortBy(function ($item) {
            // Custom sorting untuk urutan yang benar
            $kode = $this->normalizeKode($item->kode);

            return $kode;
        });

        foreach ($children as $child) {
            $result->push($child);
            $this->addChildren($result, $grouped, $child->id);
        }
    }

    private function normalizeKode($kode)
    {
        // Urutan prioritas: R, E, F, PB
        $priority = ['R' => '1', 'E' => '2', 'F' => '3', 'PB' => '4'];

        if (preg_match('/^([A-Z]+)(\d*)(-\d+)?$/', $kode, $matches)) {
            $letter = $matches[1];
            $number = isset($matches[2]) && $matches[2] !== '' ? (int) $matches[2] : 0;
            $subNumber = isset($matches[3]) ? (int) substr($matches[3], 1) : 0;

            $letterPriority = $priority[$letter] ?? '9';

            return sprintf('%s%s%03d%03d', $letterPriority, $letter, $number, $subNumber);
        }

        return $kode;
    }

    private function buildFilteredHierarchy($cashflows)
    {
        $result = collect();
        $grouped = $cashflows->groupBy('parent_id');

        // Start from root (parent_id = null)
        $this->addFilteredChildren($result, $grouped, null);

        return $result;
    }

    private function addFilteredChildren($result, $grouped, $parentId)
    {
        if (! isset($grouped[$parentId])) {
            return;
        }

        $children = $grouped[$parentId]->sortBy(function ($item) {
            $kode = $this->normalizeKode($item->kode);

            return $kode;
        });

        foreach ($children as $child) {
            // Check if this node should be included
            if ($this->shouldIncludeNode($child, $grouped)) {
                $result->push($child);
                $this->addFilteredChildren($result, $grouped, $child->id);
            }
        }
    }

    private function shouldIncludeNode($node, $grouped)
    {
        // Level 3: Include ONLY if has transactions (> 0)
        if ($node->level == 3) {
            return $node->transaction_count > 0;
        }

        // Level 1 & 2: Include if has children with transactions
        return $this->hasChildrenWithTransactions($node, $grouped);
    }

    private function hasChildrenWithTransactions($node, $grouped)
    {
        if (! isset($grouped[$node->id])) {
            return false;
        }

        foreach ($grouped[$node->id] as $child) {
            if ($child->level == 3 && $child->transaction_count > 0) {
                return true;
            }

            if ($child->level < 3 && $this->hasChildrenWithTransactions($child, $grouped)) {
                return true;
            }
        }

        return false;
    }

    public function create()
    {
        $parentsTB = TrialBalance::where('level', 4)->orderBy('kode')->get();
        $cashflowParents = Cashflow::where('level', '<', 3)->get();

        return view('cashflow.create', compact('parentsTB', 'cashflowParents'));
    }

    public function store(Request $request)
    {
        Cashflow::create([
            'kode' => $request->kode,
            'keterangan' => $request->keterangan,
            'parent_id' => $request->parent_id,
            'trial_balance_id' => $request->trial_balance_id,
            'level' => $request->level,
        ]);

        return redirect()->route('cashflow.index');
    }

    public function edit($id)
    {
        $cashflow = Cashflow::findOrFail($id);

        $cashflowParents = Cashflow::where('id', '<>', $id)
            ->where('level', '<', 3)
            ->get();

        $parentsTB = ($cashflow->level == 3)
            ? TrialBalance::where('level', 4)->orderBy('kode')->get()
            : collect();

        return view('cashflow.edit', compact('cashflow', 'cashflowParents', 'parentsTB'));
    }

    public function update(Request $request, $id)
    {
        $cashflow = Cashflow::findOrFail($id);

        $cashflow->update([
            'kode' => $request->kode,
            'keterangan' => $request->keterangan,
            'parent_id' => $request->parent_id,
            'trial_balance_id' => $cashflow->level == 3 ? $request->trial_balance_id : null,
        ]);

        return redirect()->route('cashflow.index');
    }

    public function destroy($id)
    {
        Cashflow::findOrFail($id)->delete();

        return redirect()->route('cashflow.index');
    }

    /**
     * AJAX endpoint for cashflow data
     */
    public function getData(Request $request)
    {
        try {
            // Get transaction counts for each trial balance account
            $transactionCounts = \DB::table('cash_transactions')
                ->join('trial_balances', function ($join) {
                    $join->on('cash_transactions.cash_account_id', '=', 'trial_balances.id')
                        ->orOn('cash_transactions.contra_account_id', '=', 'trial_balances.id');
                })
                ->select('trial_balances.id', \DB::raw('COUNT(*) as transaction_count'))
                ->groupBy('trial_balances.id')
                ->pluck('transaction_count', 'id');

            // Get all cashflows with relationships
            $allCashflows = Cashflow::with(['trialBalance', 'parent', 'children'])->get();

            // Add transaction counts to cashflows (default to 0)
            $allCashflows->each(function ($cashflow) use ($transactionCounts) {
                $cashflow->transaction_count = $cashflow->trial_balance_id
                    ? ($transactionCounts[$cashflow->trial_balance_id] ?? 0)
                    : 0;
            });

            // Filter and build hierarchy
            $items = $this->buildFilteredHierarchy($allCashflows);

            return response()->json([
                'status' => 'success',
                'data' => [
                    'items' => $items,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to load cashflow data: '.$e->getMessage(),
            ], 500);
        }
    }
}
