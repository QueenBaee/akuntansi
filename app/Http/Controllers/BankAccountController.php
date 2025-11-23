<?php

namespace App\Http\Controllers;

use App\Models\BankAccount;
use Illuminate\Http\Request;

class BankAccountController extends Controller
{
    public function index()
    {
        $bankAccounts = BankAccount::orderBy('name')->get();
        return view('bank-accounts.index', compact('bankAccounts'));
    }

    public function create()
    {
        return view('bank-accounts.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'bank_name' => 'required|string|max:255',
            'account_number' => 'required|string|unique:bank_accounts',
            'description' => 'nullable|string'
        ]);

        BankAccount::create($request->all());
        return redirect()->route('bank-accounts.index')->with('success', 'Akun bank berhasil ditambahkan');
    }

    public function edit(BankAccount $bankAccount)
    {
        return view('bank-accounts.edit', compact('bankAccount'));
    }

    public function update(Request $request, BankAccount $bankAccount)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'bank_name' => 'required|string|max:255',
            'account_number' => 'required|string|unique:bank_accounts,account_number,' . $bankAccount->id,
            'description' => 'nullable|string'
        ]);

        $bankAccount->update($request->all());
        return redirect()->route('bank-accounts.index')->with('success', 'Akun bank berhasil diupdate');
    }

    public function destroy(BankAccount $bankAccount)
    {
        $bankAccount->delete();
        return redirect()->route('bank-accounts.index')->with('success', 'Akun bank berhasil dihapus');
    }
}