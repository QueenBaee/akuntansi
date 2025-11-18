<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Account;
use Illuminate\Http\Request;

class AccountController extends Controller
{
    public function index()
    {
        $accounts = Account::with('parent')->orderBy('code')->paginate(20);
        return view('accounts.index', compact('accounts'));
    }

    public function create()
    {
        $parentAccounts = Account::whereNull('parent_id')->get();
        $types = [
            // 'asset' => 'Aset',
            // 'liability' => 'Kewajiban', 
            // 'equity' => 'Ekuitas',
            // 'revenue' => 'Pendapatan',
            // 'expense' => 'Beban'
            'Kas' => 'Kas',
            'Bank' => 'Bank'
        ];
        return view('accounts.create', compact('parentAccounts', 'types'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|unique:accounts,code',
            'name' => 'required|string|max:255',
            'type' => 'required|in:asset,liability,equity,revenue,expense'
        ]);

        Account::create($request->all());
        return redirect()->route('accounts.index')->with('success', 'Akun berhasil ditambahkan');
    }

    public function edit(Account $account)
    {
        $parentAccounts = Account::whereNull('parent_id')->where('id', '!=', $account->id)->get();
        $types = [
            'asset' => 'Aset',
            'liability' => 'Kewajiban',
            'equity' => 'Ekuitas', 
            'revenue' => 'Pendapatan',
            'expense' => 'Beban'
        ];
        return view('accounts.edit', compact('account', 'parentAccounts', 'types'));
    }

    public function update(Request $request, Account $account)
    {
        $request->validate([
            'code' => 'required|unique:accounts,code,' . $account->id,
            'name' => 'required|string|max:255',
            'type' => 'required|in:asset,liability,equity,revenue,expense'
        ]);

        $account->update($request->all());
        return redirect()->route('accounts.index')->with('success', 'Akun berhasil diperbarui');
    }

    public function destroy(Account $account)
    {
        $account->delete();
        return redirect()->route('accounts.index')->with('success', 'Akun berhasil dihapus');
    }
}