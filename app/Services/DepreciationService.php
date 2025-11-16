<?php

namespace App\Services;

use App\Models\Asset;
use App\Models\Depreciation;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DepreciationService
{
    private TransactionService $transactionService;
    
    public function __construct(TransactionService $transactionService)
    {
        $this->transactionService = $transactionService;
    }
    
    public function calculateMonthlyDepreciation(Asset $asset, Carbon $periodDate): array
    {
        $depreciableAmount = $asset->purchase_price - $asset->residual_value;
        $monthlyDepreciation = $depreciableAmount / $asset->useful_life_months;
        
        // Check if asset is still depreciable
        if ($asset->accumulated_depreciation >= $depreciableAmount) {
            return [
                'depreciation_amount' => 0,
                'accumulated_depreciation' => $asset->accumulated_depreciation,
                'book_value' => $asset->residual_value,
                'is_fully_depreciated' => true,
            ];
        }
        
        // Ensure we don't exceed depreciable amount
        $remainingDepreciable = $depreciableAmount - $asset->accumulated_depreciation;
        $currentDepreciation = min($monthlyDepreciation, $remainingDepreciable);
        
        $newAccumulatedDepreciation = $asset->accumulated_depreciation + $currentDepreciation;
        $bookValue = $asset->purchase_price - $newAccumulatedDepreciation;
        
        return [
            'depreciation_amount' => $currentDepreciation,
            'accumulated_depreciation' => $newAccumulatedDepreciation,
            'book_value' => $bookValue,
            'is_fully_depreciated' => $newAccumulatedDepreciation >= $depreciableAmount,
        ];
    }
    
    public function processMonthlyDepreciation(Carbon $periodDate): array
    {
        $results = [];
        
        $assets = Asset::where('is_active', true)
            ->where('purchase_date', '<=', $periodDate->endOfMonth())
            ->get();
            
        DB::transaction(function () use ($assets, $periodDate, &$results) {
            foreach ($assets as $asset) {
                // Check if depreciation already exists for this period
                $existingDepreciation = Depreciation::where('asset_id', $asset->id)
                    ->where('period_date', $periodDate->format('Y-m-d'))
                    ->first();
                    
                if ($existingDepreciation) {
                    $results[] = [
                        'asset_id' => $asset->id,
                        'asset_name' => $asset->name,
                        'status' => 'already_processed',
                        'depreciation_amount' => $existingDepreciation->depreciation_amount,
                    ];
                    continue;
                }
                
                $calculation = $this->calculateMonthlyDepreciation($asset, $periodDate);
                
                if ($calculation['depreciation_amount'] > 0) {
                    // Create depreciation record
                    $depreciation = Depreciation::create([
                        'asset_id' => $asset->id,
                        'period_date' => $periodDate->format('Y-m-d'),
                        'depreciation_amount' => $calculation['depreciation_amount'],
                        'accumulated_depreciation' => $calculation['accumulated_depreciation'],
                        'book_value' => $calculation['book_value'],
                        'is_posted' => false,
                    ]);
                    
                    // Create journal entry
                    $journal = $this->transactionService->createDepreciationJournal([
                        'depreciation_id' => $depreciation->id,
                        'period_date' => $periodDate->format('Y-m-d'),
                        'asset_name' => $asset->name,
                        'depreciation_amount' => $calculation['depreciation_amount'],
                        'expense_account_id' => $asset->expense_account_id,
                        'depreciation_account_id' => $asset->depreciation_account_id,
                    ]);
                    
                    // Update depreciation with journal reference
                    $depreciation->update([
                        'journal_id' => $journal->id,
                        'is_posted' => true,
                    ]);
                    
                    // Update asset accumulated depreciation
                    $asset->update([
                        'accumulated_depreciation' => $calculation['accumulated_depreciation'],
                    ]);
                    
                    $results[] = [
                        'asset_id' => $asset->id,
                        'asset_name' => $asset->name,
                        'status' => 'processed',
                        'depreciation_amount' => $calculation['depreciation_amount'],
                        'journal_number' => $journal->number,
                    ];
                } else {
                    $results[] = [
                        'asset_id' => $asset->id,
                        'asset_name' => $asset->name,
                        'status' => 'fully_depreciated',
                        'depreciation_amount' => 0,
                    ];
                }
            }
        });
        
        return $results;
    }
    
    public function generateDepreciationSchedule(Asset $asset): array
    {
        $schedule = [];
        $startDate = Carbon::parse($asset->purchase_date)->startOfMonth();
        $currentAccumulated = 0;
        
        for ($i = 0; $i < $asset->useful_life_months; $i++) {
            $periodDate = $startDate->copy()->addMonths($i);
            $calculation = $this->calculateMonthlyDepreciation($asset, $periodDate);
            
            if ($calculation['depreciation_amount'] > 0) {
                $currentAccumulated += $calculation['depreciation_amount'];
                
                $schedule[] = [
                    'period' => $periodDate->format('Y-m'),
                    'depreciation_amount' => $calculation['depreciation_amount'],
                    'accumulated_depreciation' => $currentAccumulated,
                    'book_value' => $asset->purchase_price - $currentAccumulated,
                ];
            }
        }
        
        return $schedule;
    }
}