<?php

namespace App\Jobs;

use App\Models\RentIncome;
use App\Models\RentIncomeSchedule;
use App\Services\TransactionService;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProcessMonthlyRentIncome implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private Carbon $periodDate;

    public function __construct(Carbon $periodDate)
    {
        $this->periodDate = $periodDate;
    }

    public function handle(TransactionService $transactionService): void
    {
        try {
            Log::info("Starting monthly rent income process for {$this->periodDate->format('Y-m')}");
            
            $activeContracts = RentIncome::where('is_active', true)
                ->where('start_date', '<=', $this->periodDate->endOfMonth())
                ->where('end_date', '>=', $this->periodDate->startOfMonth())
                ->get();
                
            $processed = 0;
            $alreadyProcessed = 0;
            
            DB::transaction(function () use ($activeContracts, $transactionService, &$processed, &$alreadyProcessed) {
                foreach ($activeContracts as $contract) {
                    // Check if schedule already exists
                    $existingSchedule = RentIncomeSchedule::where('rent_income_id', $contract->id)
                        ->where('period_date', $this->periodDate->format('Y-m-d'))
                        ->first();
                        
                    if ($existingSchedule) {
                        $alreadyProcessed++;
                        continue;
                    }
                    
                    // Create schedule
                    $schedule = RentIncomeSchedule::create([
                        'rent_income_id' => $contract->id,
                        'period_date' => $this->periodDate->format('Y-m-d'),
                        'amount' => $contract->monthly_amount,
                        'is_posted' => false,
                    ]);
                    
                    // Create journal entry
                    $journal = $transactionService->createJournal([
                        'date' => $this->periodDate->format('Y-m-d'),
                        'description' => "Monthly rent income - {$contract->tenant_name}",
                        'source_module' => 'rent_income',
                        'source_id' => $schedule->id,
                        'details' => [
                            [
                                'account_id' => $contract->receivable_account_id,
                                'description' => "Rent receivable - {$contract->tenant_name}",
                                'debit' => $contract->monthly_amount,
                                'credit' => 0,
                            ],
                            [
                                'account_id' => $contract->revenue_account_id,
                                'description' => "Rent income - {$contract->tenant_name}",
                                'debit' => 0,
                                'credit' => $contract->monthly_amount,
                            ],
                        ],
                    ]);
                    
                    // Update schedule with journal reference
                    $schedule->update([
                        'journal_id' => $journal->id,
                        'is_posted' => true,
                    ]);
                    
                    $processed++;
                }
            });
            
            Log::info("Monthly rent income completed", [
                'period' => $this->periodDate->format('Y-m'),
                'processed' => $processed,
                'already_processed' => $alreadyProcessed,
                'total_contracts' => $activeContracts->count(),
            ]);
            
        } catch (\Exception $e) {
            Log::error("Monthly rent income failed", [
                'period' => $this->periodDate->format('Y-m'),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            
            throw $e;
        }
    }
}