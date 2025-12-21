<?php

namespace App\Jobs;

use App\Services\BatchDepreciationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessBatchDepreciationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 300; // 5 minutes
    public $tries = 3;

    private string $periodMonth;
    private int $userId;

    public function __construct(string $periodMonth, int $userId)
    {
        $this->periodMonth = $periodMonth;
        $this->userId = $userId;
    }

    public function handle(BatchDepreciationService $batchService)
    {
        try {
            Log::info("Starting batch depreciation for period: {$this->periodMonth}");
            
            $result = $batchService->processBatchDepreciation($this->periodMonth, $this->userId);
            
            Log::info("Batch depreciation completed", [
                'period' => $this->periodMonth,
                'processed_count' => $result['processed_count'],
                'total_eligible' => $result['total_eligible']
            ]);
            
            // You can add notification logic here to inform the user
            // For example, send email or create a notification record
            
        } catch (\Exception $e) {
            Log::error("Batch depreciation failed", [
                'period' => $this->periodMonth,
                'user_id' => $this->userId,
                'error' => $e->getMessage()
            ]);
            
            throw $e; // Re-throw to trigger retry mechanism
        }
    }

    public function failed(\Throwable $exception)
    {
        Log::error("Batch depreciation job failed permanently", [
            'period' => $this->periodMonth,
            'user_id' => $this->userId,
            'error' => $exception->getMessage()
        ]);
        
        // Handle permanent failure - notify user, etc.
    }
}