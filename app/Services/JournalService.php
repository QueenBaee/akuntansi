<?php

namespace App\Services;

use App\Models\Journal;
use App\Models\JournalDetail;
use App\Models\Account;
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
            $journalNumber = $this->generateJournalNumber($data['date'], $data['source_module'] ?? 'GENERAL');
            
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
            
            // Create journal details
            foreach ($data['details'] as $detail) {
                JournalDetail::create([
                    'journal_id' => $journal->id,
                    'account_id' => $detail['account_id'],
                    'debit' => $detail['debit'] ?? 0,
                    'credit' => $detail['credit'] ?? 0,
                    'description' => $detail['description'] ?? $data['description'],
                ]);
            }
            
            return $journal;
        });
    }
    
    public function updateJournal(Journal $journal, array $data): Journal
    {
        return DB::transaction(function () use ($journal, $data) {
            // Delete existing details
            $journal->details()->delete();
            
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
            
            // Create new journal details
            foreach ($data['details'] as $detail) {
                JournalDetail::create([
                    'journal_id' => $journal->id,
                    'account_id' => $detail['account_id'],
                    'debit' => $detail['debit'] ?? 0,
                    'credit' => $detail['credit'] ?? 0,
                    'description' => $detail['description'] ?? $data['description'],
                ]);
            }
            
            return $journal;
        });
    }
    
    private function generateJournalNumber(string $date, string $module): string
    {
        $date = Carbon::parse($date);
        $prefix = strtoupper($module) . '/' . $date->format('Ymd') . '/';
        
        $lastNumber = Journal::where('number', 'like', $prefix . '%')
            ->whereDate('date', $date->toDateString())
            ->count();
            
        return $prefix . str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
    }
    
    public function getAccountBalance(int $accountId, string $endDate = null): float
    {
        $query = JournalDetail::where('account_id', $accountId)
            ->whereHas('journal', function ($q) use ($endDate) {
                $q->where('is_posted', true);
                if ($endDate) {
                    $q->whereDate('date', '<=', $endDate);
                }
            });
            
        $totalDebit = $query->sum('debit');
        $totalCredit = $query->sum('credit');
        
        $account = Account::find($accountId);
        
        // Normal balance calculation based on account type
        switch ($account->type) {
            case 'asset':
            case 'expense':
                return $totalDebit - $totalCredit;
            case 'liability':
            case 'equity':
            case 'revenue':
                return $totalCredit - $totalDebit;
            default:
                return 0;
        }
    }
}