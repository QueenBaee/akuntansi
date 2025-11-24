<?php

namespace App\Http\Controllers;

use App\Models\Ledger;
use App\Models\TrialBalance;
use Illuminate\Http\Request;

class LedgerController extends Controller
{
    public function index(Request $request)
    {
        $type = null;
        
        // Check if this is a type-specific route
        if ($request->route()->getName() === 'ledgers.cash') {
            $type = 'kas';
        } elseif ($request->route()->getName() === 'ledgers.bank') {
            $type = 'bank';
        }
        
        $query = Ledger::with('trialBalance')->orderBy('nama_ledger');
        
        if ($type) {
            $query->where('tipe_ledger', $type);
        }
        
        $ledgers = $query->get();
        $trialBalances = TrialBalance::orderBy('kode')->get();

        return view('ledgers.index', compact('ledgers', 'trialBalances', 'type'));
    }

    public function create(Request $request)
    {
        $type = $request->get('type');
        $trialBalances = TrialBalance::orderBy('kode')->get();
        return view('ledgers.create', compact('trialBalances', 'type'));
    }

    public function store(Request $request)
    {
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
        
        // Redirect based on ledger type
        $redirectRoute = match($validated['tipe_ledger']) {
            'kas' => 'ledgers.cash',
            'bank' => 'ledgers.bank',
            default => 'ledgers.index'
        };
        
        return redirect()->route($redirectRoute)->with('success', 'Ledger berhasil ditambahkan');
    }

    public function show(Ledger $ledger)
    {
        return request()->expectsJson()
            ? response()->json([
                'success' => true,
                'data' => $ledger
            ])
            : view('ledgers.show', compact('ledger'));
    }

    public function edit(Ledger $ledger, Request $request)
    {
        $type = $request->get('type');
        $trialBalances = TrialBalance::orderBy('kode')->get();
        return view('ledgers.edit', compact('ledger', 'trialBalances', 'type'));
    }

    public function update(Request $request, Ledger $ledger)
    {
        $validated = $request->validate([
            'nama_ledger' => 'required|string|max:255',
            'kode_ledger' => 'required|string|unique:ledgers,kode_ledger,' . $ledger->id,
            'tipe_ledger' => 'required|in:kas,bank',
            'deskripsi' => 'nullable|string',
            'is_active' => 'sometimes|boolean',
            'trial_balance_id' => 'nullable|exists:trial_balances,id'
        ]);

        // pastikan unchecked checkbox = false
        $validated['is_active'] = $request->boolean('is_active');

        $ledger->update($validated);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Ledger berhasil diupdate',
                'data' => $ledger->fresh('trialBalance')
            ]);
        }
        
        // Redirect based on ledger type
        $redirectRoute = match($validated['tipe_ledger']) {
            'kas' => 'ledgers.cash',
            'bank' => 'ledgers.bank',
            default => 'ledgers.index'
        };
        
        return redirect()->route($redirectRoute)->with('success', 'Ledger berhasil diupdate');
    }

    public function destroy(Ledger $ledger)
    {
        // optional safety: cegah delete ledger yg terhubung jurnal
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
        
        // Redirect based on ledger type
        $redirectRoute = match($ledgerType) {
            'kas' => 'ledgers.cash',
            'bank' => 'ledgers.bank',
            default => 'ledgers.index'
        };
        
        return redirect()->route($redirectRoute)->with('success', 'Ledger berhasil dihapus');
    }
}
