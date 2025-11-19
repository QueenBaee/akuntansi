<?php

namespace App\Http\Controllers;

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

        $userAccounts = $query->paginate($request->get('per_page', 15));

        return response()->json([
            'data' => $userAccounts->items(),
            'meta' => [
                'current_page' => $userAccounts->currentPage(),
                'last_page' => $userAccounts->lastPage(),
                'per_page' => $userAccounts->perPage(),
                'total' => $userAccounts->total(),
            ]
        ]);
    }

    public function store(UserAccountRequest $request)
    {
        $validated = $request->validated();

        $userAccount = UserAccount::create($validated);
        $userAccount->load(['user', 'account']);

        return response()->json([
            'data' => $userAccount,
            'message' => 'User account created successfully'
        ], 201);
    }

    public function show(UserAccount $userAccount)
    {
        $userAccount->load(['user', 'account']);
        
        return response()->json([
            'data' => $userAccount
        ]);
    }

    public function update(UserAccountRequest $request, UserAccount $userAccount)
    {
        $validated = $request->validated();

        $userAccount->update($validated);
        $userAccount->load(['user', 'account']);

        return response()->json([
            'data' => $userAccount,
            'message' => 'User account updated successfully'
        ]);
    }

    public function destroy(UserAccount $userAccount)
    {
        $userAccount->delete();

        return response()->json([
            'message' => 'User account deleted successfully'
        ]);
    }

    public function getUsers()
    {
        $users = User::where('is_active', true)->get(['id', 'name', 'email']);
        
        return response()->json([
            'data' => $users
        ]);
    }

    public function getAccounts()
    {
        $accounts = Account::where('is_active', true)->get(['id', 'code', 'name', 'type']);
        
        return response()->json([
            'data' => $accounts
        ]);
    }
}