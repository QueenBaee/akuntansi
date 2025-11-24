<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
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
        $query = UserLedger::with(['user', 'ledger']);

        if ($request->has('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->has('ledger_id')) {
            $query->where('ledger_id', $request->ledger_id);
        }

        if ($request->has('role')) {
            $query->where('role', $request->role);
        }

        if ($request->has('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        $userLedgers = $query->paginate(15);
        
        return view('user-ledgers.index', compact('userLedgers'));
    }

    public function create()
    {
        $users = User::where('is_active', true)->get(['id', 'name', 'email']);
        $ledgers = Ledger::where('is_active', true)->get(['id', 'kode_ledger', 'nama_ledger', 'tipe_ledger']);
        
        return view('user-ledgers.create', compact('users', 'ledgers'));
    }

    public function store(UserLedgerRequest $request)
    {
        $validated = $request->validated();

        UserLedger::create($validated);

        return redirect()->route('user-ledgers.index')
            ->with('success', 'User ledger created successfully');
    }

    public function show(UserLedger $userLedger)
    {
        $userLedger->load(['user', 'ledger']);
        
        return view('user-ledgers.show', compact('userLedger'));
    }

    public function edit(UserLedger $userLedger)
    {
        $users = User::where('is_active', true)->get(['id', 'name', 'email']);
        $ledgers = Ledger::where('is_active', true)->get(['id', 'kode_ledger', 'nama_ledger', 'tipe_ledger']);
        $userLedger->load(['user', 'ledger']);
        
        return view('user-ledgers.edit', compact('userLedger', 'users', 'ledgers'));
    }

    public function update(UserLedgerRequest $request, UserLedger $userLedger)
    {
        $validated = $request->validated();

        $userLedger->update($validated);

        return redirect()->route('user-ledgers.index')
            ->with('success', 'User ledger updated successfully');
    }

    public function destroy(UserLedger $userLedger)
    {
        $userLedger->delete();

        return redirect()->route('user-ledgers.index')
            ->with('success', 'User ledger deleted successfully');
    }
}