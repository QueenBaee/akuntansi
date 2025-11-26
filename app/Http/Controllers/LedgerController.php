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
        
        // Apply access control based on user role
        if (!auth()->user()->hasRole('admin')) {
            // Non-admin users only see ledgers they have access to
            $userLedgerIds = auth()->user()->userLedgers()->where('is_active', true)->pluck('ledger_id');
            $query->whereIn('id', $userLedgerIds);
        }
        
        if ($type) {
            $query->where('tipe_ledger', $type);
        }
        
        $ledgers = $query->get();
        $trialBalances = TrialBalance::orderBy('kode')->get();

        return view('ledgers.index', compact('ledgers', 'trialBalances', 'type'));
    }

    public function create(Request $request)
    {
        // Only admin can create ledgers
        if (!auth()->user()->hasRole('admin')) {
            abort(403, 'You do not have permission to create ledgers.');
        }
        
        $type = $request->get('type');
        $trialBalances = TrialBalance::orderBy('kode')->get();
        return view('ledgers.create', compact('trialBalances', 'type'));
    }

    public function store(Request $request)
    {
        // Only admin can store ledgers
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
        // Check access for non-admin users
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
        // Only admin can edit ledgers
        if (!auth()->user()->hasRole('admin')) {
            abort(403, 'You do not have permission to edit ledgers.');
        }
        
        $type = $request->get('type');
        $trialBalances = TrialBalance::orderBy('kode')->get();
        return view('ledgers.edit', compact('ledger', 'trialBalances', 'type'));
    }

    public function update(Request $request, Ledger $ledger)
    {
        // Only admin can update ledgers
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
        // Only admin can delete ledgers
        if (!auth()->user()->hasRole('admin')) {
            abort(403, 'You do not have permission to delete ledgers.');
        }
        
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
