<?php

namespace App\Services;

use App\Models\Account;
use App\Models\Journal;
use App\Models\JournalDetail;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReportService
{
    public function getTrialBalance(Carbon $asOfDate): array
    {
        $accounts = Account::active()
            ->with(['journalDetails' => function ($query) use ($asOfDate) {
                $query->whereHas('journal', function ($q) use ($asOfDate) {
                    $q->where('date', '<=', $asOfDate)
                      ->where('is_posted', true);
                });
            }])
            ->get();
            
        $trialBalance = [];
        $totalDebit = 0;
        $totalCredit = 0;
        
        foreach ($accounts as $account) {
            $debitSum = $account->journalDetails->sum('debit');
            $creditSum = $account->journalDetails->sum('credit');
            
            $balance = match($account->type) {
                'asset', 'expense' => $account->opening_balance + $debitSum - $creditSum,
                'liability', 'equity', 'revenue' => $account->opening_balance + $creditSum - $debitSum,
            };
            
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
            
        $cashTransactions = JournalDetail::whereIn('account_id', $cashAccounts)
            ->whereHas('journal', function ($query) use ($startDate, $endDate) {
                $query->whereBetween('date', [$startDate, $endDate])
                      ->where('is_posted', true);
            })
            ->with(['journal', 'account'])
            ->get();
            
        $operating = [];
        $investing = [];
        $financing = [];
        
        foreach ($cashTransactions as $transaction) {
            $amount = $transaction->debit - $transaction->credit;
            
            // Categorize based on contra account or transaction type
            $category = $this->categorizeCashFlow($transaction);
            
            $item = [
                'date' => $transaction->journal->date,
                'description' => $transaction->description,
                'amount' => $amount,
                'journal_number' => $transaction->journal->number,
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
        
        $transactions = JournalDetail::where('account_id', $accountId)
            ->whereHas('journal', function ($query) use ($startDate, $endDate) {
                $query->whereBetween('date', [$startDate, $endDate])
                      ->where('is_posted', true);
            })
            ->with(['journal'])
            ->orderBy('created_at')
            ->get();
            
        $runningBalance = $account->opening_balance;
        $ledgerEntries = [];
        
        foreach ($transactions as $transaction) {
            $debit = $transaction->debit;
            $credit = $transaction->credit;
            
            // Calculate running balance based on account type
            if (in_array($account->type, ['asset', 'expense'])) {
                $runningBalance += $debit - $credit;
            } else {
                $runningBalance += $credit - $debit;
            }
            
            $ledgerEntries[] = [
                'date' => $transaction->journal->date,
                'journal_number' => $transaction->journal->number,
                'description' => $transaction->description,
                'reference' => $transaction->journal->reference,
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
    
    private function getAccountBalancesByType(string $type, ?Carbon $startDate, Carbon $endDate): array
    {
        $accounts = Account::where('type', $type)
            ->where('is_active', true)
            ->with(['journalDetails' => function ($query) use ($startDate, $endDate) {
                $query->whereHas('journal', function ($q) use ($startDate, $endDate) {
                    if ($startDate) {
                        $q->whereBetween('date', [$startDate, $endDate]);
                    } else {
                        $q->where('date', '<=', $endDate);
                    }
                    $q->where('is_posted', true);
                });
            }])
            ->get();
            
        $balances = [];
        
        foreach ($accounts as $account) {
            $debitSum = $account->journalDetails->sum('debit');
            $creditSum = $account->journalDetails->sum('credit');
            
            $balance = match($type) {
                'asset', 'expense' => ($startDate ? 0 : $account->opening_balance) + $debitSum - $creditSum,
                'liability', 'equity', 'revenue' => ($startDate ? 0 : $account->opening_balance) + $creditSum - $debitSum,
            };
            
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
    
    private function categorizeCashFlow(JournalDetail $transaction): string
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