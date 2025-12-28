<?php

namespace App\Http\Controllers;

use App\Models\FixedAsset;
use App\Models\Journal;
use App\Models\Maklon;
use App\Models\TrialBalance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        return view('dashboard.index');
    }

    public function stats()
    {
        // Get opening balance from child accounts whose parent is kas/bank in one query
        $openingBalance = TrialBalance::join('trial_balances as parent', 'trial_balances.parent_id', '=', 'parent.id')
            ->where('parent.is_kas_bank', true)
            ->sum('trial_balances.tahun_2024');
        
        // Get total cash movements from journals
        $cashMovements = Journal::where(function($q) {
                $q->where('cash_in', '>', 0)->orWhere('cash_out', '>', 0);
            })
            ->sum('cash_in') - Journal::where(function($q) {
                $q->where('cash_in', '>', 0)->orWhere('cash_out', '>', 0);
            })
            ->sum('cash_out');
        
        $totalCash = $openingBalance + $cashMovements;
        
        $totalAccounts = TrialBalance::count();
        $monthlyTransactions = Journal::whereMonth('date', now()->month)
            ->whereYear('date', now()->year)
            ->count();
        $totalAssets = FixedAsset::where('is_active', true)->count();

        return response()->json([
            'data' => [
                'totalCash' => $totalCash,
                'totalAccounts' => $totalAccounts,
                'monthlyTransactions' => $monthlyTransactions,
                'totalAssets' => $totalAssets
            ]
        ]);
    }

    public function cashFlowChart()
    {
        $months = [];
        $cashIn = [];
        $cashOut = [];
        
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $months[] = $date->format('M Y');
            
            $inAmount = Journal::whereMonth('date', $date->month)
                ->whereYear('date', $date->year)
                ->sum('cash_in');
            $cashIn[] = $inAmount;
            
            $outAmount = Journal::whereMonth('date', $date->month)
                ->whereYear('date', $date->year)
                ->sum('cash_out');
            $cashOut[] = $outAmount;
        }

        return response()->json([
            'labels' => $months,
            'datasets' => [
                [
                    'label' => 'Cash In',
                    'data' => $cashIn,
                    'backgroundColor' => 'rgba(34, 197, 94, 0.8)',
                    'borderColor' => 'rgb(34, 197, 94)',
                    'borderWidth' => 2
                ],
                [
                    'label' => 'Cash Out',
                    'data' => $cashOut,
                    'backgroundColor' => 'rgba(239, 68, 68, 0.8)',
                    'borderColor' => 'rgb(239, 68, 68)',
                    'borderWidth' => 2
                ]
            ]
        ]);
    }

    public function assetsByCategory()
    {
        $assets = FixedAsset::select('group', DB::raw('count(*) as count'), DB::raw('sum(acquisition_price) as total_value'))
            ->where('is_active', true)
            ->groupBy('group')
            ->get();

        return response()->json([
            'labels' => $assets->pluck('group'),
            'datasets' => [[
                'data' => $assets->pluck('total_value'),
                'backgroundColor' => [
                    'rgba(59, 130, 246, 0.8)',
                    'rgba(16, 185, 129, 0.8)',
                    'rgba(245, 158, 11, 0.8)',
                    'rgba(139, 92, 246, 0.8)',
                    'rgba(236, 72, 153, 0.8)'
                ]
            ]]
        ]);
    }

    public function monthlyJournals()
    {
        $months = [];
        $journalCounts = [];
        
        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $months[] = $date->format('M Y');
            
            $count = Journal::whereMonth('date', $date->month)
                ->whereYear('date', $date->year)
                ->count();
            $journalCounts[] = $count;
        }

        return response()->json([
            'labels' => $months,
            'datasets' => [[
                'label' => 'Journal Entries',
                'data' => $journalCounts,
                'backgroundColor' => 'rgba(99, 102, 241, 0.8)',
                'borderColor' => 'rgb(99, 102, 241)',
                'borderWidth' => 2,
                'fill' => true
            ]]
        ]);
    }

    public function maklonRevenue()
    {
        $months = [];
        $revenues = [];
        
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $months[] = $date->format('M Y');
            
            $revenue = Maklon::whereMonth('date', $date->month)
                ->whereYear('date', $date->year)
                ->sum('dpp');
            $revenues[] = $revenue;
        }

        return response()->json([
            'labels' => $months,
            'datasets' => [[
                'label' => 'Maklon Revenue',
                'data' => $revenues,
                'backgroundColor' => 'rgba(168, 85, 247, 0.8)',
                'borderColor' => 'rgb(168, 85, 247)',
                'borderWidth' => 2
            ]]
        ]);
    }

    public function recentTransactions()
    {
        $transactions = Journal::where(function($q) {
                $q->where('cash_in', '>', 0)->orWhere('cash_out', '>', 0);
            })
            ->orderBy('date', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($journal) {
                return [
                    'id' => $journal->id,
                    'date' => $journal->date->format('d/m/Y'),
                    'description' => $journal->description,
                    'account_name' => $journal->reference ?? 'N/A',
                    'amount' => $journal->cash_in ?: $journal->cash_out,
                    'type' => $journal->cash_in > 0 ? 'in' : 'out'
                ];
            });

        return response()->json(['data' => $transactions]);
    }
}