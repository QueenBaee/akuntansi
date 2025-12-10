<?php

namespace App\Services;

use App\Models\Account;
use App\Models\Journal;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReportService
{
    public function getTrialBalance(Carbon $asOfDate): array
    {
        $accounts = Account::active()
            ->get();
            
        $trialBalance = [];
        $totalDebit = 0;
        $totalCredit = 0;
        
        foreach ($accounts as $account) {
            $balance = $this->getAccountBalance($account->id, $asOfDate);
            
            if ($balance != 0) {
                $debitBalance = $balance > 0 ? $balance : 0;
                $creditBalance = $balance < 0 ? abs($balance) : 0;
                
                $trialBalance[] = [
                    'account_code' => $account->code,
                    'account_name' => $account->name,
                    'account_type' => $account->type,
                    'debit_balance' => $debitBalance,
                    'credit_balance' => $creditBalance,
                ];
                
                $totalDebit += $debitBalance;
                $totalCredit += $creditBalance;
            }
        }
        
        return [
            'as_of_date' => $asOfDate->format('Y-m-d'),
            'accounts' => $trialBalance,
            'total_debit' => $totalDebit,
            'total_credit' => $totalCredit,
            'is_balanced' => $totalDebit == $totalCredit,
        ];
    }
    
    public function getIncomeStatement(Carbon $startDate, Carbon $endDate): array
    {
        $revenues = $this->getAccountBalancesByType('revenue', $startDate, $endDate);
        $expenses = $this->getAccountBalancesByType('expense', $startDate, $endDate);
        
        $totalRevenue = collect($revenues)->sum('balance');
        $totalExpense = collect($expenses)->sum('balance');
        $netIncome = $totalRevenue - $totalExpense;
        
        return [
            'period' => [
                'start_date' => $startDate->format('Y-m-d'),
                'end_date' => $endDate->format('Y-m-d'),
            ],
            'revenues' => $revenues,
            'expenses' => $expenses,
            'total_revenue' => $totalRevenue,
            'total_expense' => $totalExpense,
            'net_income' => $netIncome,
        ];
    }
    
    public function getBalanceSheet(Carbon $asOfDate): array
    {
        $assets = $this->getAccountBalancesByType('asset', null, $asOfDate);
        $liabilities = $this->getAccountBalancesByType('liability', null, $asOfDate);
        $equity = $this->getAccountBalancesByType('equity', null, $asOfDate);
        
        // Calculate retained earnings (net income from beginning of year)
        $yearStart = $asOfDate->copy()->startOfYear();
        $incomeStatement = $this->getIncomeStatement($yearStart, $asOfDate);
        $retainedEarnings = $incomeStatement['net_income'];
        
        $totalAssets = collect($assets)->sum('balance');
        $totalLiabilities = collect($liabilities)->sum('balance');
        $totalEquity = collect($equity)->sum('balance') + $retainedEarnings;
        
        return [
            'as_of_date' => $asOfDate->format('Y-m-d'),
            'assets' => $assets,
            'liabilities' => $liabilities,
            'equity' => $equity,
            'retained_earnings' => $retainedEarnings,
            'total_assets' => $totalAssets,
            'total_liabilities' => $totalLiabilities,
            'total_equity' => $totalEquity,
            'is_balanced' => abs($totalAssets - ($totalLiabilities + $totalEquity)) < 0.01,
        ];
    }
    
    public function getCashFlow(Carbon $startDate, Carbon $endDate): array
    {
        $cashAccounts = Account::where('type', 'asset')
            ->where('category', 'current_asset')
            ->where('name', 'like', '%kas%')
            ->orWhere('name', 'like', '%bank%')
            ->pluck('id');
            
        $cashTransactions = Journal::where(function($query) use ($cashAccounts) {
                $query->whereIn('debit_account_id', $cashAccounts)
                      ->orWhereIn('credit_account_id', $cashAccounts);
            })
            ->whereBetween('date', [$startDate, $endDate])
            ->where('is_posted', true)
            ->whereNull('deleted_at')
            ->get();
            
        $operating = [];
        $investing = [];
        $financing = [];
        
        foreach ($cashTransactions as $transaction) {
            $amount = 0;
            if (in_array($transaction->debit_account_id, $cashAccounts->toArray())) {
                $amount = $transaction->total_amount;
            } elseif (in_array($transaction->credit_account_id, $cashAccounts->toArray())) {
                $amount = -$transaction->total_amount;
            }
            
            // Categorize based on transaction type
            $category = $this->categorizeCashFlow($transaction);
            
            $item = [
                'date' => $transaction->date,
                'description' => $transaction->description,
                'amount' => $amount,
                'journal_number' => $transaction->number,
            ];
            
            match($category) {
                'operating' => $operating[] = $item,
                'investing' => $investing[] = $item,
                'financing' => $financing[] = $item,
            };
        }
        
        return [
            'period' => [
                'start_date' => $startDate->format('Y-m-d'),
                'end_date' => $endDate->format('Y-m-d'),
            ],
            'operating_activities' => $operating,
            'investing_activities' => $investing,
            'financing_activities' => $financing,
            'net_operating_cash' => collect($operating)->sum('amount'),
            'net_investing_cash' => collect($investing)->sum('amount'),
            'net_financing_cash' => collect($financing)->sum('amount'),
            'net_cash_flow' => collect($operating)->sum('amount') + 
                              collect($investing)->sum('amount') + 
                              collect($financing)->sum('amount'),
        ];
    }
    
    public function getGeneralLedger(int $accountId, Carbon $startDate, Carbon $endDate): array
    {
        $account = Account::findOrFail($accountId);
        
        $transactions = Journal::where(function($query) use ($accountId) {
                $query->where('debit_account_id', $accountId)
                      ->orWhere('credit_account_id', $accountId);
            })
            ->whereBetween('date', [$startDate, $endDate])
            ->where('is_posted', true)
            ->whereNull('deleted_at')
            ->orderBy('date')
            ->get();
            
        $runningBalance = $account->opening_balance;
        $ledgerEntries = [];
        
        foreach ($transactions as $transaction) {
            $debit = $transaction->debit_account_id == $accountId ? $transaction->total_amount : 0;
            $credit = $transaction->credit_account_id == $accountId ? $transaction->total_amount : 0;
            
            // Calculate running balance based on account type
            if (in_array($account->type, ['asset', 'expense'])) {
                $runningBalance += $debit - $credit;
            } else {
                $runningBalance += $credit - $debit;
            }
            
            $ledgerEntries[] = [
                'date' => $transaction->date,
                'journal_number' => $transaction->number,
                'description' => $transaction->description,
                'reference' => $transaction->reference,
                'debit' => $debit,
                'credit' => $credit,
                'balance' => $runningBalance,
            ];
        }
        
        return [
            'account' => [
                'code' => $account->code,
                'name' => $account->name,
                'type' => $account->type,
            ],
            'period' => [
                'start_date' => $startDate->format('Y-m-d'),
                'end_date' => $endDate->format('Y-m-d'),
            ],
            'opening_balance' => $account->opening_balance,
            'transactions' => $ledgerEntries,
            'ending_balance' => $runningBalance,
        ];
    }
    
    private function getAccountBalance(int $accountId, Carbon $endDate): float
    {
        $account = Account::find($accountId);
        if (!$account) return 0;
        
        $journals = Journal::where(function($query) use ($accountId) {
                $query->where('debit_account_id', $accountId)
                      ->orWhere('credit_account_id', $accountId);
            })
            ->where('date', '<=', $endDate)
            ->where('is_posted', true)
            ->whereNull('deleted_at')
            ->get();
            
        $balance = $account->opening_balance;
        
        foreach ($journals as $journal) {
            if ($journal->debit_account_id == $accountId) {
                $balance += $journal->total_amount;
            }
            if ($journal->credit_account_id == $accountId) {
                $balance -= $journal->total_amount;
            }
        }
        
        return $balance;
    }
    
    private function getAccountBalancesByType(string $type, ?Carbon $startDate, Carbon $endDate): array
    {
        $accounts = Account::where('type', $type)
            ->where('is_active', true)
            ->get();
            
        $balances = [];
        
        foreach ($accounts as $account) {
            $balance = $this->getAccountBalance($account->id, $endDate);
            
            if ($balance != 0) {
                $balances[] = [
                    'account_code' => $account->code,
                    'account_name' => $account->name,
                    'account_category' => $account->category,
                    'balance' => abs($balance),
                ];
            }
        }
        
        return $balances;
    }
    
    private function categorizeCashFlow(Journal $transaction): string
    {
        // Simple categorization logic - can be enhanced with more sophisticated rules
        $description = strtolower($transaction->description);
        
        if (str_contains($description, 'penjualan') || str_contains($description, 'pendapatan')) {
            return 'operating';
        }
        
        if (str_contains($description, 'aset') || str_contains($description, 'investasi')) {
            return 'investing';
        }
        
        if (str_contains($description, 'pinjaman') || str_contains($description, 'modal')) {
            return 'financing';
        }
        
        return 'operating'; // Default to operating
    }
}