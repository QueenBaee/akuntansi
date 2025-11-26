<?php

namespace App\Http\Controllers;

use App\Models\UserLedger;
use App\Models\User;
use App\Models\Ledger;
use App\Http\Requests\UserLedgerRequest;
use Illuminate\Http\Request;

class UserLedgerController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:admin');
    }

    public function index(Request $request)
    {
        if ($request->expectsJson()) {
            $query = UserLedger::with(['user', 'ledger']);

            if ($request->has('user_id')) {
                $query->where('user_id', $request->user_id);
            }

            if ($request->has('ledger_id')) {
                $query->where('ledger_id', $request->ledger_id);
            }

            if ($request->has('is_active')) {
                $query->where('is_active', $request->boolean('is_active'));
            }

            $userLedgers = $query->paginate(15);
            
            return response()->json([
                'success' => true,
                'data' => $userLedgers
            ]);
        }

        return view('user-ledgers.index');
    }

    public function create()
    {
        $users = User::where('is_active', true)->get(['id', 'name', 'email']);
        $ledgers = Ledger::where('is_active', true)->get(['id', 'kode_ledger', 'nama_ledger', 'tipe_ledger']);
        
        return response()->json([
            'success' => true,
            'data' => [
                'users' => $users,
                'ledgers' => $ledgers
            ]
        ]);
    }

    public function store(UserLedgerRequest $request)
    {
        $userLedger = UserLedger::create($request->validated());
        $userLedger->load(['user', 'ledger']);

        return response()->json([
            'success' => true,
            'message' => 'User ledger created successfully',
            'data' => $userLedger
        ], 201);
    }

    public function show(UserLedger $userLedger)
    {
        $userLedger->load(['user', 'ledger']);
        
        return response()->json([
            'success' => true,
            'data' => $userLedger
        ]);
    }

    public function edit(UserLedger $userLedger)
    {
        $users = User::where('is_active', true)->get(['id', 'name', 'email']);
        $ledgers = Ledger::where('is_active', true)->get(['id', 'kode_ledger', 'nama_ledger', 'tipe_ledger']);
        $userLedger->load(['user', 'ledger']);
        
        return response()->json([
            'success' => true,
            'data' => [
                'userLedger' => $userLedger,
                'users' => $users,
                'ledgers' => $ledgers
            ]
        ]);
    }

    public function update(UserLedgerRequest $request, UserLedger $userLedger)
    {
        $userLedger->update($request->validated());
        $userLedger->load(['user', 'ledger']);

        return response()->json([
            'success' => true,
            'message' => 'User ledger updated successfully',
            'data' => $userLedger
        ]);
    }

    public function destroy(UserLedger $userLedger)
    {
        $userLedger->delete();

        return response()->json([
            'success' => true,
            'message' => 'User ledger deleted successfully'
        ]);
    }
}