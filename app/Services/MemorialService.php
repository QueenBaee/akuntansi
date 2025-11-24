<?php

namespace App\Services;

use App\Models\Journal;
use App\Models\JournalDetail;
use App\Models\TrialBalance;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MemorialService
{
    public function createMemorial(array $data): Journal
    {
        return DB::transaction(function () use ($data) {
            $totalDebit = collect($data['details'])->sum('debit');
            $totalCredit = collect($data['details'])->sum('credit');
            
            if ($totalDebit != $totalCredit) {
                throw new \Exception('Total debit harus sama dengan total kredit');
            }
            
            $memorialNumber = $this->generateMemorialNumber($data['date']);
            
            $journal = Journal::create([
                'date' => $data['date'],
                'number' => $memorialNumber,
                'reference' => $data['reference'] ?? null,
                'source_module' => 'memorial',
                'description' => $data['description'],
                'total_debit' => $totalDebit,
                'total_credit' => $totalCredit,
                'is_posted' => true,
                'created_by' => auth()->id(),
            ]);
            
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
    
    public function updateMemorial(Journal $journal, array $data): Journal
    {
        return DB::transaction(function () use ($journal, $data) {
            $journal->details()->delete();
            
            $totalDebit = collect($data['details'])->sum('debit');
            $totalCredit = collect($data['details'])->sum('credit');
            
            if ($totalDebit != $totalCredit) {
                throw new \Exception('Total debit harus sama dengan total kredit');
            }
            
            $journal->update([
                'date' => $data['date'],
                'reference' => $data['reference'] ?? null,
                'description' => $data['description'],
                'total_debit' => $totalDebit,
                'total_credit' => $totalCredit,
                'total_amount' => $totalDebit,
            ]);
            
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
    
    private function generateMemorialNumber(string $date): string
    {
        $date = Carbon::parse($date);
        $prefix = 'MEMORIAL/' . $date->format('Ymd') . '/';
        
        $lastNumber = Journal::where('number', 'like', $prefix . '%')
            ->where('source_module', 'memorial')
            ->whereDate('date', $date->toDateString())
            ->count();
            
        return $prefix . str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
    }
    
    public function getAccountBalance(int $accountId, string $endDate = null): float
    {
        $query = JournalDetail::where('account_id', $accountId)
            ->whereHas('journal', function ($q) use ($endDate) {
                $q->where('is_posted', true)
                  ->where('source_module', 'memorial');
                if ($endDate) {
                    $q->whereDate('date', '<=', $endDate);
                }
            });
            
        $totalDebit = $query->sum('debit');
        $totalCredit = $query->sum('credit');
        
        $account = TrialBalance::find($accountId);
        
        switch ($account->type ?? 'asset') {
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