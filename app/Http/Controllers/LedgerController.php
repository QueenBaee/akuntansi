<?php

namespace App\Http\Controllers;

use App\Models\Ledger;
use App\Models\TrialBalance;
use Illuminate\Http\Request;

class LedgerController extends Controller
{
    private function getTrialBalances($type = null)
    {
        $query = TrialBalance::query();
        
        if ($type === 'kas') {
            // Cash: A11-00 to A11-19
            $query->where('kode', 'REGEXP', '^A11-0[0-9]$|^A11-1[0-9]$');
        } elseif ($type === 'bank') {
            // Bank: A11-20 to A11-49
            $query->where('kode', 'REGEXP', '^A11-[2-4][0-9]$');
        } else {
            // All cash and bank accounts
            $query->where('kode', 'REGEXP', '^A11-[0-4][0-9]$');
        }
        
        return $query->orderBy('kode')->get();
    }

    public function index(Request $request)
    {
        $type = null;
        
        if ($request->route()->getName() === 'ledgers.cash') {
            $type = 'kas';
        } elseif ($request->route()->getName() === 'ledgers.bank') {
            $type = 'bank';
        }
        
        $query = Ledger::with('trialBalance')->orderBy('trial_balance_id');
        
        if (!auth()->user()->hasRole('admin')) {
            $userLedgerIds = auth()->user()->userLedgers()->where('is_active', true)->pluck('ledger_id');
            $query->whereIn('id', $userLedgerIds);
        }
        
        if ($type) {
            $query->where('tipe_ledger', $type);
        }
        
        $ledgers = $query->get();
        $trialBalances = $this->getTrialBalances($type);

        return view('ledgers.index', compact('ledgers', 'trialBalances', 'type'));
    }

    public function create(Request $request)
    {
        if (!auth()->user()->hasRole('admin')) {
            abort(403, 'You do not have permission to create ledgers.');
        }
        
        $type = $request->get('type');
        $trialBalances = $this->getTrialBalances($type);
            
        return view('ledgers.create', compact('trialBalances', 'type'));
    }

    public function store(Request $request)
    {
        if (!auth()->user()->hasRole('admin')) {
            abort(403, 'You do not have permission to create ledgers.');
        }
        
        $validated = $request->validate([
            'nama_ledger' => 'required|string|max:255',
            'kode_ledger' => 'required|string|unique:ledgers',
            'tipe_ledger' => 'required|in:kas,bank',
            'deskripsi' => 'nullable|string',
            'trial_balance_id' => 'nullable|exists:trial_balances,id'
        ]);

        $ledger = Ledger::create($validated);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Ledger berhasil ditambahkan',
                'data' => $ledger
            ], 201);
        }
        
        $redirectRoute = match($validated['tipe_ledger']) {
            'kas' => 'ledgers.cash',
            'bank' => 'ledgers.bank',
            default => 'ledgers.index'
        };
        
        return redirect()->route($redirectRoute)->with('success', 'Ledger berhasil ditambahkan');
    }

    public function show(Ledger $ledger)
    {
        if (!auth()->user()->hasRole('admin')) {
            $hasAccess = auth()->user()->userLedgers()
                ->where('ledger_id', $ledger->id)
                ->where('is_active', true)
                ->exists();
                
            if (!$hasAccess) {
                abort(403, 'You do not have access to this ledger.');
            }
        }
        
        return request()->expectsJson()
            ? response()->json([
                'success' => true,
                'data' => $ledger
            ])
            : view('ledgers.show', compact('ledger'));
    }

    public function edit(Ledger $ledger, Request $request)
    {
        if (!auth()->user()->hasRole('admin')) {
            abort(403, 'You do not have permission to edit ledgers.');
        }
        
        $type = $request->get('type');
        $trialBalances = $this->getTrialBalances($type);
            
        return view('ledgers.edit', compact('ledger', 'trialBalances', 'type'));
    }

    public function update(Request $request, Ledger $ledger)
    {
        if (!auth()->user()->hasRole('admin')) {
            abort(403, 'You do not have permission to update ledgers.');
        }
        
        $validated = $request->validate([
            'nama_ledger' => 'required|string|max:255',
            'kode_ledger' => 'required|string|unique:ledgers,kode_ledger,' . $ledger->id,
            'tipe_ledger' => 'required|in:kas,bank',
            'deskripsi' => 'nullable|string',
            'is_active' => 'sometimes|boolean',
            'trial_balance_id' => 'nullable|exists:trial_balances,id'
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        $ledger->update($validated);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Ledger berhasil diupdate',
                'data' => $ledger->fresh('trialBalance')
            ]);
        }
        
        $redirectRoute = match($validated['tipe_ledger']) {
            'kas' => 'ledgers.cash',
            'bank' => 'ledgers.bank',
            default => 'ledgers.index'
        };
        
        return redirect()->route($redirectRoute)->with('success', 'Ledger berhasil diupdate');
    }

    public function destroy(Ledger $ledger)
    {
        if (!auth()->user()->hasRole('admin')) {
            abort(403, 'You do not have permission to delete ledgers.');
        }
        
        if ($ledger->journals()->exists()) {
            return request()->expectsJson()
                ? response()->json([
                    'success' => false,
                    'message' => 'Ledger tidak dapat dihapus karena sedang digunakan.'
                ], 409)
                : back()->with('error', 'Ledger tidak dapat dihapus karena sedang digunakan.');
        }

        $ledgerType = $ledger->tipe_ledger;
        $ledger->delete();

        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Ledger berhasil dihapus'
            ]);
        }
        
        $redirectRoute = match($ledgerType) {
            'kas' => 'ledgers.cash',
            'bank' => 'ledgers.bank',
            default => 'ledgers.index'
        };
        
        return redirect()->route($redirectRoute)->with('success', 'Ledger berhasil dihapus');
    }
}