<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\UserAccount;
use App\Models\User;
use App\Models\Account;
use App\Http\Requests\UserAccountRequest;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserAccountController extends Controller
{
    public function index(Request $request)
    {
        $query = UserAccount::with(['user', 'account']);

        if ($request->has('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->has('account_id')) {
            $query->where('account_id', $request->account_id);
        }

        if ($request->has('role')) {
            $query->where('role', $request->role);
        }

        if ($request->has('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        $userAccounts = $query->paginate(15);
        
        return view('user-accounts.index', compact('userAccounts'));
    }

    public function create()
    {
        $users = User::where('is_active', true)->get(['id', 'name', 'email']);
        $accounts = Account::where('is_active', true)->get(['id', 'code', 'name', 'type']);
        
        return view('user-accounts.create', compact('users', 'accounts'));
    }

    public function store(UserAccountRequest $request)
    {
        $validated = $request->validated();

        UserAccount::create($validated);

        return redirect()->route('user-accounts.index')
            ->with('success', 'User account created successfully');
    }

    public function show(UserAccount $userAccount)
    {
        $userAccount->load(['user', 'account']);
        
        return view('user-accounts.show', compact('userAccount'));
    }

    public function edit(UserAccount $userAccount)
    {
        $users = User::where('is_active', true)->get(['id', 'name', 'email']);
        $accounts = Account::where('is_active', true)->get(['id', 'code', 'name', 'type']);
        $userAccount->load(['user', 'account']);
        
        return view('user-accounts.edit', compact('userAccount', 'users', 'accounts'));
    }

    public function update(UserAccountRequest $request, UserAccount $userAccount)
    {
        $validated = $request->validated();

        $userAccount->update($validated);

        return redirect()->route('user-accounts.index')
            ->with('success', 'User account updated successfully');
    }

    public function destroy(UserAccount $userAccount)
    {
        $userAccount->delete();

        return redirect()->route('user-accounts.index')
            ->with('success', 'User account deleted successfully');
    }
}