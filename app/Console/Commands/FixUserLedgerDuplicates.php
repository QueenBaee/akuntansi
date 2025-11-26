<?php

namespace App\Console\Commands;

use App\Models\UserLedger;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class FixUserLedgerDuplicates extends Command
{
    protected $signature = 'fix:user-ledger-duplicates';
    protected $description = 'Fix duplicate user-ledger combinations';

    public function handle()
    {
        $this->info('Checking for duplicate user-ledger combinations...');

        // Find duplicates
        $duplicates = DB::table('user_ledgers')
            ->select('user_id', 'ledger_id', DB::raw('COUNT(*) as count'))
            ->groupBy('user_id', 'ledger_id')
            ->having('count', '>', 1)
            ->get();

        if ($duplicates->isEmpty()) {
            $this->info('No duplicates found.');
            return 0;
        }

        $this->warn("Found {$duplicates->count()} duplicate combinations.");

        foreach ($duplicates as $duplicate) {
            $this->info("Processing user_id: {$duplicate->user_id}, ledger_id: {$duplicate->ledger_id}");
            
            // Keep the most recent record, delete others
            $records = UserLedger::where('user_id', $duplicate->user_id)
                ->where('ledger_id', $duplicate->ledger_id)
                ->orderBy('created_at', 'desc')
                ->get();

            $keepRecord = $records->first();
            $deleteRecords = $records->skip(1);

            foreach ($deleteRecords as $record) {
                $this->line("  Deleting duplicate record ID: {$record->id}");
                $record->delete();
            }
        }

        $this->info('Duplicate cleanup completed.');
        return 0;
    }
}