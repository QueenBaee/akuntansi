<?php

namespace App\Services;

use App\Models\Ledger;
use Illuminate\Database\Eloquent\Collection;

class LedgerService
{
    public function createLedgerEntry(array $data): Ledger
    {
        return Ledger::create($data);
    }

    public function updateLedgerEntry(Ledger $ledger, array $data): Ledger
    {
        $ledger->update($data);
        return $ledger->fresh();
    }

    public function deleteLedgerEntry(Ledger $ledger): bool
    {
        return $ledger->delete();
    }

    public function getLedgerByAccount(int $accountId, ?string $start = null, ?string $end = null): Collection
    {
        $query = Ledger::with(['account', 'journalEntry'])
            ->where('account_id', $accountId);
            
        if ($start && $end) {
            $query->whereBetween('date', [$start, $end]);
        }
        
        return $query->orderBy('date')->orderBy('id')->get();
    }

    public function getRunningBalance(int $accountId, string $upToDate): float
    {
        $ledgers = Ledger::where('account_id', $accountId)
            ->where('date', '<=', $upToDate)
            ->orderBy('date')
            ->orderBy('id')
            ->get();

        $balance = 0;
        foreach ($ledgers as $ledger) {
            $balance += ($ledger->debit - $ledger->credit);
        }

        return $balance;
    }
}