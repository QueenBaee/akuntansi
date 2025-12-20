<?php

namespace App\Services;

use App\Models\Journal;
use App\Models\Account;
use App\Services\JournalNumberService;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class JournalService
{
    public function createJournal(array $data): Journal
    {
        return DB::transaction(function () use ($data) {
            // Validate double entry
            $totalDebit = collect($data['details'])->sum('debit');
            $totalCredit = collect($data['details'])->sum('credit');
            
            if ($totalDebit != $totalCredit) {
                throw new \Exception('Total debit harus sama dengan total kredit');
            }
            
            // Generate journal number
            $journalNumber = JournalNumberService::generate($data['date']);
            
            // Create journal header
            $journal = Journal::create([
                'date' => $data['date'],
                'number' => $journalNumber,
                'reference' => $data['reference'] ?? null,
                'source_module' => $data['source_module'] ?? 'manual',
                'description' => $data['description'],
                'total_debit' => $totalDebit,
                'total_credit' => $totalCredit,
                'is_posted' => true,
                'created_by' => auth()->id(),
            ]);
            
            return $journal;
        });
    }
    
    public function updateJournal(Journal $journal, array $data): Journal
    {
        return DB::transaction(function () use ($journal, $data) {
            // Validate double entry
            $totalDebit = collect($data['details'])->sum('debit');
            $totalCredit = collect($data['details'])->sum('credit');

            if ($totalDebit != $totalCredit) {
                throw new \Exception('Total debit harus sama dengan total kredit');
            }
            
            // Update journal header
            $journal->update([
                'date' => $data['date'],
                'reference' => $data['reference'] ?? null,
                'description' => $data['description'],
                'total_debit' => $totalDebit,
                'total_credit' => $totalCredit,
                'total_amount' => $totalDebit,
            ]);
            
            return $journal;
        });
    }
    

    
    public function getAccountBalance(int $accountId, string $endDate = null): float
    {
        $query = Journal::where(function($q) use ($accountId) {
                $q->where('debit_account_id', $accountId)
                  ->orWhere('credit_account_id', $accountId);
            })
            ->where('is_posted', true);
            
        if ($endDate) {
            $query->whereDate('date', '<=', $endDate);
        }
        
        $journals = $query->get();
        $balance = 0;
        
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
}