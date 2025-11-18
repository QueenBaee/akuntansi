<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cashflow;
use App\Models\TrialBalance;

class CashflowController extends Controller
{
    public function index()
    {
        $data = Cashflow::with('trialBalance')->orderBy('kode')->get();
        return view('cashflow.index', compact('data'));
    }

    public function create()
    {
        $accounts = TrialBalance::orderBy('kode')->get();
        return view('cashflow.create', compact('accounts'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode' => 'required',
            'keterangan' => 'required',
            'trial_balance_id' => 'required',
        ]);

        Cashflow::create($request->all());
        return redirect()->route('cashflow.index');
    }

    public function edit(Cashflow $cashflow)
    {
        $accounts =  TrialBalance::orderBy('kode')->get();
        return view('cashflow.edit', compact('cashflow', 'accounts'));
    }

    public function update(Request $request, Cashflow $cashflow)
    {
        $cashflow->update($request->all());
        return redirect()->route('cashflow.index');
    }

    public function destroy(Cashflow $cashflow)
    {
        $cashflow->delete();
        return redirect()->route('cashflow.index');
    }
}
