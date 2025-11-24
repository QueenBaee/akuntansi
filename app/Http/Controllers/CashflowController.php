<?php

namespace App\Http\Controllers;

use App\Models\Cashflow;
use App\Models\TrialBalance;
use Illuminate\Http\Request;

class CashflowController extends Controller
{
    // ============================
    // INDEX
    // ============================
    public function index()
    {
        $cashflows = Cashflow::with('trialBalance', 'parent')
            ->orderBy('kode')
            ->get();

        return view('cashflow.index', compact('cashflows'));
    }

    // ============================
    // CREATE
    // ============================
    public function create()
    {
        // TB Level 4 saja
        $parentsTB = TrialBalance::where('level', 4)->orderBy('kode')->get();

        // Cashflow level 1 dan 2 untuk parent
        $cashflowParents = Cashflow::where('level', '<', 3)->orderBy('kode')->get();

        return view('cashflow.create', compact('parentsTB', 'cashflowParents'));
    }

    // ============================
    // STORE
    // ============================
    public function store(Request $request)
    {
        $request->validate([
            'kode' => 'required',
            'keterangan' => 'required',
            'level' => 'required|integer',
        ]);

        Cashflow::create([
            'kode' => $request->kode,
            'keterangan' => $request->keterangan,
            'parent_id' => $request->parent_id,
            'trial_balance_id' => $request->trial_balance_id,
            'level' => $request->level
        ]);

        return redirect()->route('cashflow.index')->with('success', 'Cashflow berhasil ditambahkan');
    }

    // ============================
    // EDIT
    // ============================
    public function edit($id)
    {
        $cashflow = Cashflow::findOrFail($id);

        // Data parent cashflow
        $cashflowParents = Cashflow::where('id', '!=', $id)
            ->where('level', '<', 3)
            ->orderBy('kode')
            ->get();

        // Trial balance level 4
        $parentsTB = TrialBalance::where('level', 4)->orderBy('kode')->get();

        return view('cashflow.edit', compact('cashflow', 'cashflowParents', 'parentsTB'));
    }

    // ============================
    // UPDATE
    // ============================
    public function update(Request $request, $id)
    {
        $request->validate([
            'kode' => 'required',
            'keterangan' => 'required',
            'level' => 'required|integer',
        ]);

        $cashflow = Cashflow::findOrFail($id);

        $cashflow->update([
            'kode' => $request->kode,
            'keterangan' => $request->keterangan,
            'parent_id' => $request->parent_id,
            'trial_balance_id' => $request->trial_balance_id,
            'level' => $request->level
        ]);

        return redirect()->route('cashflow.index')->with('success', 'Cashflow berhasil diperbarui');
    }

    // ============================
    // DELETE
    // ============================
    public function destroy($id)
    {
        $cashflow = Cashflow::findOrFail($id);
        $cashflow->delete();

        return redirect()->route('cashflow.index')->with('success', 'Cashflow berhasil dihapus');
    }
}
