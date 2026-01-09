<?php

namespace App\Http\Controllers;

use App\Models\Journal;
use App\Models\TrialBalance;
use Illuminate\Http\Request;

class BukuBesarController extends Controller
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
        $bukuBesarData = [];
        $totalDebit = 0;
        $totalCredit = 0;
        $endingBalance = 0;

        // Process if account is selected
        if ($accountId) {
            $selectedAccount = TrialBalance::find($accountId);

            if ($selectedAccount) {
                // Get opening balance from trial_balances (authoritative source)
                // Rule: Use current year if exists, else previous year, else 0
                $openingBalance = 0;
                
                if (isset($selectedAccount->{"tahun_$year"})) {
                    // Use current year balance if exists
                    $openingBalance = $selectedAccount->{"tahun_$year"} ?? 0;
                } else {
                    // Fallback to previous year balance
                    $previousYear = $year - 1;
                    $openingBalance = $selectedAccount->{"tahun_$previousYear"} ?? 0;
                }

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

                    // Add to buku besar data
                    $bukuBesarData[] = [
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
        // Trial Balance = opening balance from trial_balances table (authoritative)
        $trialBalanceTotal = 0;
        if ($selectedAccount) {
            if (isset($selectedAccount->{"tahun_$year"})) {
                $trialBalanceTotal = $selectedAccount->{"tahun_$year"} ?? 0;
            } else {
                $previousYear = $year - 1;
                $trialBalanceTotal = $selectedAccount->{"tahun_$previousYear"} ?? 0;
            }
        }
        
        // Buku Besar = ending balance after all transactions
        $bukuBesarTotal = $endingBalance;

        return view('buku_besar.index', compact(
            'accounts',
            'selectedAccount',
            'year',
            'startDate',
            'endDate',
            'openingBalance',
            'bukuBesarData',
            'totalDebit',
            'totalCredit',
            'endingBalance',
            'trialBalanceTotal',
            'bukuBesarTotal'
        ));
    }

    public function exportPdf(Request $request)
    {
        $accountId = $request->get('account_id');
        $year = $request->get('year', date('Y'));
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        $selectedAccount = TrialBalance::find($accountId);
        if (!$selectedAccount) {
            abort(404, 'Akun tidak ditemukan');
        }

        $openingBalance = 0;
        if (isset($selectedAccount->{"tahun_$year"})) {
            $openingBalance = $selectedAccount->{"tahun_$year"} ?? 0;
        } else {
            $previousYear = $year - 1;
            $openingBalance = $selectedAccount->{"tahun_$previousYear"} ?? 0;
        }

        $query = Journal::where('is_posted', true)
            ->where(function ($q) use ($accountId) {
                $q->where('debit_account_id', $accountId)
                  ->orWhere('credit_account_id', $accountId);
            });

        if ($startDate) {
            $query->where('date', '>=', $startDate);
        }
        if ($endDate) {
            $query->where('date', '<=', $endDate);
        } else {
            $query->whereYear('date', $year);
        }

        $journals = $query->orderBy('date')->orderBy('id')->get();

        $bukuBesarData = [];
        $totalDebit = 0;
        $totalCredit = 0;
        $runningBalance = $openingBalance;

        foreach ($journals as $journal) {
            $debit = $journal->debit_account_id == $accountId ? $journal->total_debit : 0;
            $credit = $journal->credit_account_id == $accountId ? $journal->total_credit : 0;
            $runningBalance += $debit - $credit;

            $bukuBesarData[] = [
                'date' => $journal->date,
                'description' => $journal->description,
                'pic' => $journal->pic,
                'proof_number' => $journal->proof_number,
                'debit' => $debit,
                'credit' => $credit,
                'balance' => $runningBalance,
            ];

            $totalDebit += $debit;
            $totalCredit += $credit;
        }

        $endingBalance = $runningBalance;

        return view('buku_besar.pdf', compact(
            'selectedAccount',
            'year',
            'openingBalance',
            'bukuBesarData',
            'totalDebit',
            'totalCredit',
            'endingBalance'
        ));
    }
}
