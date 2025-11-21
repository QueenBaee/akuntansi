<?php

namespace App\Http\Controllers;

use App\Models\Cashflow;
use App\Models\TrialBalance;
use Illuminate\Http\Request;

class CashflowController extends Controller
{
    public function index()
    {
        $cashflows = Cashflow::with('trialBalance', 'parent')
            ->orderBy('kode')
            ->get();

        return view('cashflow.index', compact('cashflows'));
    }

    public function create()
    {
        // Ambil TB level 4 saja
        $parentsTB = TrialBalance::where('level', 4)->orderBy('kode')->get();

        // Ambil parent cashflow level 1 & 2
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
            'level' => $request->level
        ]);

        return redirect()->route('cashflow.index');
    }

    public function edit($id)
    {
        $cashflow = Cashflow::findOrFail($id);

        // Ambil parent cashflow untuk level < 3
        $cashflowParents = Cashflow::where('id', '<>', $id)
            ->where('level', '<', 3)
            ->get();

        // TB hanya untuk level 3
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
}
