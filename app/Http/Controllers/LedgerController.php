<?php

namespace App\Http\Controllers;

use App\Models\Journal;
use App\Models\TrialBalance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LedgerController extends Controller
{
    public function index(Request $request)
    {
        // Get filter parameters
        $accountId = $request->get('account_id');
        $year = $request->get('year', date('Y'));
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        // Get all accounts for dropdown
        $accounts = TrialBalance::where('level', 4)
            ->orderBy('kode')
            ->get();

        // Initialize variables
        $selectedAccount = null;
        $openingBalance = 0;
        $ledgerData = [];
        $totalDebit = 0;
        $totalCredit = 0;
        $endingBalance = 0;

        // Process if account is selected
        if ($accountId) {
            $selectedAccount = TrialBalance::find($accountId);

            if ($selectedAccount) {
                // Get opening balance from trial_balances
                $openingBalance = $selectedAccount->{"tahun_$year"} ?? 0;

                // Build query for journals
                $query = Journal::where('is_posted', true)
                    ->where(function ($q) use ($accountId) {
                        $q->where('debit_account_id', $accountId)
                          ->orWhere('credit_account_id', $accountId);
                    });

                // Apply date filters
                if ($startDate) {
                    $query->where('date', '>=', $startDate);
                }
                if ($endDate) {
                    $query->where('date', '<=', $endDate);
                } else {
                    // Default to year filter if no end date
                    $query->whereYear('date', $year);
                }

                // Get journals ordered by date
                $journals = $query->orderBy('date')
                    ->orderBy('id')
                    ->with(['debitAccount', 'creditAccount'])
                    ->get();

                // Calculate running balance
                $runningBalance = $openingBalance;

                foreach ($journals as $journal) {
                    $debit = 0;
                    $credit = 0;

                    // Determine debit/credit for this account
                    if ($journal->debit_account_id == $accountId) {
                        $debit = $journal->total_debit;
                    }
                    if ($journal->credit_account_id == $accountId) {
                        $credit = $journal->total_credit;
                    }

                    // Calculate running balance
                    $runningBalance += $debit - $credit;

                    // Add to ledger data
                    $ledgerData[] = [
                        'date' => $journal->date,
                        'description' => $journal->description,
                        'pic' => $journal->pic,
                        'proof_number' => $journal->proof_number,
                        'debit' => $debit,
                        'credit' => $credit,
                        'balance' => $runningBalance,
                    ];

                    // Accumulate totals
                    $totalDebit += $debit;
                    $totalCredit += $credit;
                }

                $endingBalance = $runningBalance;
            }
        }

        // Summary variables for display
        // Trial Balance = opening balance from trial_balances table
        $trialBalanceTotal = $selectedAccount ? ($selectedAccount->{"tahun_$year"} ?? 0) : 0;
        
        // Buku Besar = ending balance after all transactions
        $ledgerTotal = $endingBalance;

        return view('ledger.index', compact(
            'accounts',
            'selectedAccount',
            'year',
            'startDate',
            'endDate',
            'openingBalance',
            'ledgerData',
            'totalDebit',
            'totalCredit',
            'endingBalance',
            'trialBalanceTotal',
            'ledgerTotal'
        ));
    }
}
