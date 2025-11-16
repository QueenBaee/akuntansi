<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\CashTransaction;
use App\Models\Asset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function stats()
    {
        $totalCash = Account::where('type', 'asset')
            ->where('name', 'like', '%kas%')
            ->sum('balance');

        $totalAccounts = Account::count();

        $monthlyTransactions = CashTransaction::whereMonth('date', now()->month)
            ->whereYear('date', now()->year)
            ->count();

        $totalAssets = Asset::count();

        return response()->json([
            'data' => [
                'totalCash' => $totalCash,
                'totalAccounts' => $totalAccounts,
                'monthlyTransactions' => $monthlyTransactions,
                'totalAssets' => $totalAssets
            ]
        ]);
    }

    public function recentTransactions()
    {
        $transactions = CashTransaction::with('cashAccount')
            ->orderBy('date', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($transaction) {
                return [
                    'id' => $transaction->id,
                    'date' => $transaction->date,
                    'description' => $transaction->description,
                    'account_name' => $transaction->cashAccount->name,
                    'amount' => $transaction->amount
                ];
            });

        return response()->json(['data' => $transactions]);
    }
}