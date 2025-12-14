<?php

namespace App\Services;

use App\Models\Journal;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class JournalNumberService
{
    /**
     * Generate a unique journal number with database locking to prevent duplicates
     */
    public static function generate(string $date = null): string
    {
        return DB::transaction(function () use ($date) {
            $date = $date ? Carbon::parse($date) : now();
            $prefix = 'JRN-' . $date->format('Ym') . '-';
            
            // Find the highest existing number (including soft-deleted)
            $lastJournal = Journal::withTrashed()
                ->where('number', 'like', $prefix . '%')
                ->orderByRaw('CAST(SUBSTRING(number, -4) AS UNSIGNED) DESC')
                ->lockForUpdate()
                ->first();
            
            if ($lastJournal) {
                $lastNumber = intval(substr($lastJournal->number, -4));
                $newNumber = $lastNumber + 1;
            } else {
                $newNumber = 1;
            }
            
            $journalNumber = $prefix . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
            
            // Double check if number exists (including soft-deleted)
            while (Journal::withTrashed()->where('number', $journalNumber)->exists()) {
                $newNumber++;
                $journalNumber = $prefix . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
            }
            
            return $journalNumber;
        });
    }
}