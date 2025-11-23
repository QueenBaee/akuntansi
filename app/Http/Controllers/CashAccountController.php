<?php

namespace App\Http\Controllers;

use App\Models\CashAccount;
use Illuminate\Http\Request;

class CashAccountController extends Controller
{
    public function index()
    {
        $cashAccounts = CashAccount::orderBy('name')->get();
        return view('cash-accounts.index', compact('cashAccounts'));
    }

    public function create()
    {
        return view('cash-accounts.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'account_number' => 'required|string|unique:cash_accounts',
            'description' => 'nullable|string'
        ]);

        CashAccount::create($request->all());
        return redirect()->route('cash-accounts.index')->with('success', 'Akun kas berhasil ditambahkan');
    }

    public function edit(CashAccount $cashAccount)
    {
        return view('cash-accounts.edit', compact('cashAccount'));
    }

    public function update(Request $request, CashAccount $cashAccount)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'account_number' => 'required|string|unique:cash_accounts,account_number,' . $cashAccount->id,
            'description' => 'nullable|string'
        ]);

        $cashAccount->update($request->all());
        return redirect()->route('cash-accounts.index')->with('success', 'Akun kas berhasil diupdate');
    }

    public function destroy(CashAccount $cashAccount)
    {
        $cashAccount->delete();
        return redirect()->route('cash-accounts.index')->with('success', 'Akun kas berhasil dihapus');
    }
}