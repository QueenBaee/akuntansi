<?php

namespace App\Services;

use App\Models\Journal;
use App\Models\JournalDetail;
use App\Models\Account;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TransactionService
{
    public function createJournal(array $data): Journal
    {
        return DB::transaction(function () use ($data) {
            // Generate journal number
            $journalNumber = $this->generateJournalNumber($data['date'], $data['source_module']);
            
            // Calculate totals
            $totalDebit = collect($data['details'])->sum('debit');
            $totalCredit = collect($data['details'])->sum('credit');
            
            // Validate double entry
            if ($totalDebit != $totalCredit) {
                throw new \Exception('Total debit must equal total credit');
            }
            
            // Create journal header
            $journal = Journal::create([
                'date' => $data['date'],
                'number' => $journalNumber,
                'reference' => $data['reference'] ?? null,
                'description' => $data['description'],
                'source_module' => $data['source_module'],
                'source_id' => $data['source_id'] ?? null,
                'total_debit' => $totalDebit,
                'total_credit' => $totalCredit,
                'is_posted' => true,
                'created_by' => auth()->id(),
            ]);
            
            // Create journal details
            foreach ($data['details'] as $detail) {
                JournalDetail::create([
                    'journal_id' => $journal->id,
                    'account_id' => $detail['account_id'],
                    'description' => $detail['description'],
                    'debit' => $detail['debit'] ?? 0,
                    'credit' => $detail['credit'] ?? 0,
                ]);
            }
            
            return $journal->load('details.account');
        });
    }
    
    public function createCashJournal(array $transactionData): Journal
    {
        $details = [];
        
        if ($transactionData['type'] === 'in') {
            // Cash In: Debit Cash, Credit Contra Account
            $details[] = [
                'account_id' => $transactionData['cash_account_id'],
                'description' => $transactionData['description'],
                'debit' => $transactionData['amount'],
                'credit' => 0,
            ];
            $details[] = [
                'account_id' => $transactionData['contra_account_id'],
                'description' => $transactionData['description'],
                'debit' => 0,
                'credit' => $transactionData['amount'],
            ];
        } else {
            // Cash Out: Debit Contra Account, Credit Cash
            $details[] = [
                'account_id' => $transactionData['contra_account_id'],
                'description' => $transactionData['description'],
                'debit' => $transactionData['amount'],
                'credit' => 0,
            ];
            $details[] = [
                'account_id' => $transactionData['cash_account_id'],
                'description' => $transactionData['description'],
                'debit' => 0,
                'credit' => $transactionData['amount'],
            ];
        }
        
        return $this->createJournal([
            'date' => $transactionData['date'],
            'description' => $transactionData['description'],
            'reference' => $transactionData['reference'],
            'source_module' => 'cash',
            'source_id' => $transactionData['id'] ?? null,
            'details' => $details,
        ]);
    }
    
    public function createBankJournal(array $transactionData): Journal
    {
        $details = [];
        
        if ($transactionData['type'] === 'in') {
            // Bank In: Debit Bank, Credit Contra Account
            $details[] = [
                'account_id' => $transactionData['bank_account_id'],
                'description' => $transactionData['description'],
                'debit' => $transactionData['amount'],
                'credit' => 0,
            ];
            $details[] = [
                'account_id' => $transactionData['contra_account_id'],
                'description' => $transactionData['description'],
                'debit' => 0,
                'credit' => $transactionData['amount'],
            ];
        } else {
            // Bank Out: Debit Contra Account, Credit Bank
            $details[] = [
                'account_id' => $transactionData['contra_account_id'],
                'description' => $transactionData['description'],
                'debit' => $transactionData['amount'],
                'credit' => 0,
            ];
            $details[] = [
                'account_id' => $transactionData['bank_account_id'],
                'description' => $transactionData['description'],
                'debit' => 0,
                'credit' => $transactionData['amount'],
            ];
        }
        
        return $this->createJournal([
            'date' => $transactionData['date'],
            'description' => $transactionData['description'],
            'reference' => $transactionData['reference'],
            'source_module' => 'bank',
            'source_id' => $transactionData['id'] ?? null,
            'details' => $details,
        ]);
    }
    
    public function createDepreciationJournal(array $depreciationData): Journal
    {
        $details = [
            [
                'account_id' => $depreciationData['expense_account_id'],
                'description' => "Depreciation - {$depreciationData['asset_name']}",
                'debit' => $depreciationData['depreciation_amount'],
                'credit' => 0,
            ],
            [
                'account_id' => $depreciationData['depreciation_account_id'],
                'description' => "Accumulated Depreciation - {$depreciationData['asset_name']}",
                'debit' => 0,
                'credit' => $depreciationData['depreciation_amount'],
            ],
        ];
        
        return $this->createJournal([
            'date' => $depreciationData['period_date'],
            'description' => "Monthly depreciation for {$depreciationData['asset_name']}",
            'source_module' => 'depreciation',
            'source_id' => $depreciationData['depreciation_id'],
            'details' => $details,
        ]);
    }
    
    private function generateJournalNumber(string $date, string $module): string
    {
        $date = Carbon::parse($date);
        $prefix = match($module) {
            'manual' => 'JU',
            'cash' => 'KM',
            'bank' => 'BK',
            'asset' => 'AS',
            'depreciation' => 'DP',
            'maklon' => 'MK',
            'rent_income' => 'RI',
            'rent_expense' => 'RE',
            default => 'JU',
        };
        
        $yearMonth = $date->format('Ym');
        
        // Get last number for this month and module
        $lastJournal = Journal::where('number', 'like', "{$prefix}{$yearMonth}%")
            ->orderBy('number', 'desc')
            ->first();
            
        $sequence = 1;
        if ($lastJournal) {
            $lastSequence = (int) substr($lastJournal->number, -4);
            $sequence = $lastSequence + 1;
        }
        
        return $prefix . $yearMonth . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }
}