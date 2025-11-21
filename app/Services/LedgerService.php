<?php

namespace App\Services;

use App\Models\Ledger;
use Illuminate\Database\Eloquent\Collection;

class LedgerService
{
    public function getAllLedgers(): Collection
    {
        return Ledger::orderBy('kode')->get();
    }

    public function createLedger(array $data): Ledger
    {
        return Ledger::create($data);
    }

    public function updateLedger(Ledger $ledger, array $data): Ledger
    {
        $ledger->update($data);
        return $ledger->fresh();
    }

    public function deleteLedger(Ledger $ledger): bool
    {
        return $ledger->delete();
    }

    public function findLedger(int $id): ?Ledger
    {
        return Ledger::find($id);
    }
}