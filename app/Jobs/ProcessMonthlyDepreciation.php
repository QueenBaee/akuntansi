<?php

namespace App\Jobs;

use App\Services\DepreciationService;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessMonthlyDepreciation implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private Carbon $periodDate;

    public function __construct(Carbon $periodDate)
    {
        $this->periodDate = $periodDate;
    }

    public function handle(DepreciationService $depreciationService): void
    {
        try {
            Log::info("Starting monthly depreciation process for {$this->periodDate->format('Y-m')}");
            
            $results = $depreciationService->processMonthlyDepreciation($this->periodDate);
            
            $processed = collect($results)->where('status', 'processed')->count();
            $alreadyProcessed = collect($results)->where('status', 'already_processed')->count();
            $fullyDepreciated = collect($results)->where('status', 'fully_depreciated')->count();
            
            Log::info("Monthly depreciation completed", [
                'period' => $this->periodDate->format('Y-m'),
                'processed' => $processed,
                'already_processed' => $alreadyProcessed,
                'fully_depreciated' => $fullyDepreciated,
                'total_assets' => count($results),
            ]);
            
        } catch (\Exception $e) {
            Log::error("Monthly depreciation failed", [
                'period' => $this->periodDate->format('Y-m'),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            
            throw $e;
        }
    }

    public function failed(\Throwable $exception): void
    {
        Log::error("Monthly depreciation job failed", [
            'period' => $this->periodDate->format('Y-m'),
            'error' => $exception->getMessage(),
        ]);
    }
}