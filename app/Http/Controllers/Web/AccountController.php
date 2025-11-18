<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Account;
use Illuminate\Http\Request;

class AccountController extends Controller
{
    public function index(Request $request)
    {
        $accounts = Account::orderBy('code')->paginate(20);
        
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'data' => $accounts->items()
            ]);
        }
        
        return view('accounts.index', compact('accounts'));
    }

    public function create()
    {
        $types = [
            'kas' => 'Kas',
            'bank' => 'Bank'
        ];
        return view('accounts.create', compact('types'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|unique:accounts,code',
            'name' => 'required|string|max:255',
            'type' => 'required|in:kas,bank',
            'opening_balance' => 'nullable|numeric',
            'is_active' => 'boolean'
        ]);

        $data = $request->all();
        $data['is_active'] = $request->has('is_active');
        
        $account = Account::create($data);
        
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Akun berhasil ditambahkan',
                'data' => $account
            ]);
        }

        return redirect()->route('accounts.index')->with('success', 'Akun berhasil ditambahkan');
    }

    public function edit(Account $account)
    {
        $types = [
            'kas' => 'Kas',
            'bank' => 'Bank'
        ];
        return view('accounts.edit', compact('account', 'types'));
    }

    public function show(Account $account)
    {
        return response()->json([
            'success' => true,
            'data' => $account
        ]);
    }

    public function update(Request $request, Account $account)
    {
        $request->validate([
            'code' => 'required|unique:accounts,code,' . $account->id,
            'name' => 'required|string|max:255',
            'type' => 'required|in:kas,bank'
        ]);

        $data = $request->only(['code', 'name', 'type']);
        $data['is_active'] = true;
        $data['opening_balance'] = $account->opening_balance;
        
        $account->update($data);
        
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Akun berhasil diperbarui',
                'data' => $account
            ]);
        }

        return redirect()->route('accounts.index')->with('success', 'Akun berhasil diperbarui');
    }

    public function destroy(Account $account)
    {
        try {
            $account->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Akun berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus akun: ' . $e->getMessage()
            ], 422);
        }
    }
}