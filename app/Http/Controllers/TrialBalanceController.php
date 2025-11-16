<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TrialBalanceController extends Controller
{
    public function index()
    {
        $items = TrialBalance::all();
        return view('trial_balance.index', compact('items'));
    }

    public function store(Request $request)
    {
        TrialBalance::create($request->all());
        return redirect()->route('trial_balance.index');
    }
}
