<?php

namespace App\Jobs;

use App\Services\DepreciationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Carbon\Carbon;

class ProcessMonthlyDepreciation implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    
    protected $periodDate;
    
    public function __construct(string $periodDate = null)
    {
        $this->periodDate = $periodDate ?? Carbon::now()->format('Y-m-d');
    }
    
    public function handle()
    {
        // Process depreciation for all active assets
        $assets = \App\Models\Asset::where('is_active', true)->get();
        
        foreach ($assets as $asset) {
            $this->processAssetDepreciation($asset);
        }
    }
    
    private function processAssetDepreciation($asset)
    {
        $periodDate = Carbon::parse($this->periodDate);
        
        // Check if depreciation already processed for this period
        $existingDepreciation = \App\Models\Depreciation::where('asset_id', $asset->id)
            ->whereYear('period_date', $periodDate->year)
            ->whereMonth('period_date', $periodDate->month)
            ->first();
            
        if ($existingDepreciation) {
            return; // Already processed
        }
        
        // Calculate monthly depreciation
        $monthlyDepreciation = ($asset->purchase_price - $asset->residual_value) / ($asset->useful_life * 12);
        
        // Get accumulated depreciation
        $accumulatedDepreciation = \App\Models\Depreciation::where('asset_id', $asset->id)
            ->sum('depreciation_amount');
            
        $newAccumulated = $accumulatedDepreciation + $monthlyDepreciation;
        $bookValue = $asset->purchase_price - $newAccumulated;
        
        // Create depreciation record
        $depreciation = \App\Models\Depreciation::create([
            'asset_id' => $asset->id,
            'period_date' => $periodDate->format('Y-m-d'),
            'depreciation_amount' => $monthlyDepreciation,
            'accumulated_depreciation' => $newAccumulated,
            'book_value' => $bookValue,
        ]);
        
        // Create journal entry
        $journalService = app(\App\Services\JournalService::class);
        
        $journalData = [
            'date' => $periodDate->format('Y-m-d'),
            'source_module' => 'DEPRECIATION',
            'reference' => $depreciation->id,
            'description' => 'Penyusutan ' . $asset->name . ' - ' . $periodDate->format('M Y'),
            'details' => [
                [
                    'account_id' => $asset->expense_account_id,
                    'debit' => $monthlyDepreciation,
                    'credit' => 0,
                    'description' => 'Beban penyusutan ' . $asset->name
                ],
                [
                    'account_id' => $asset->depreciation_account_id,
                    'debit' => 0,
                    'credit' => $monthlyDepreciation,
                    'description' => 'Akumulasi penyusutan ' . $asset->name
                ]
            ]
        ];
        
        $journal = $journalService->createJournal($journalData);
        
        // Update depreciation with journal reference
        $depreciation->update(['journal_id' => $journal->id]);
    }
}