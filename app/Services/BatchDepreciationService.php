<?php

namespace App\Services;

use App\Models\FixedAsset;
use App\Models\AssetDepreciation;
use App\Models\TrialBalance;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

class BatchDepreciationService
{
    private FixedAssetService $assetService;
    
    public function __construct(FixedAssetService $assetService)
    {
        $this->assetService = $assetService;
    }

    /**
     * Get eligible assets for depreciation in a specific month
     */
    public function getEligibleAssets(string $periodMonth): Collection
    {
        $periodDate = Carbon::createFromFormat('Y-m', $periodMonth)->startOfMonth();
        
        return FixedAsset::query()
            ->select(['id', 'name', 'code', 'group', 'acquisition_date', 'depreciation_start_date', 
                     'acquisition_price', 'accumulated_depreciation', 'residual_value', 'useful_life_months',
                     'expense_account_id', 'accumulated_account_id'])
            ->where('is_active', true)
            ->whereNotIn('group', ['Aset Dalam Penyelesaian', 'Tanah']) // Exclude non-depreciable
            ->whereNotNull('useful_life_months')
            ->where('useful_life_months', '>', 0)
            ->where(function ($query) use ($periodDate) {
                // Asset must be acquired before or in the selected month
                $query->where('acquisition_date', '<=', $periodDate->endOfMonth());
            })
            ->whereDoesntHave('depreciations', function ($query) use ($periodDate) {
                // Not already depreciated for this period
                $query->where('period_date', $periodDate->format('Y-m-01'));
            })
            ->with(['expenseAccount:id,kode,keterangan', 'accumulatedAccount:id,kode,keterangan'])
            ->get()
            ->filter(function ($asset) use ($periodDate) {
                // Additional business logic checks
                return $this->isAssetDepreciableForPeriod($asset, $periodDate);
            });
    }

    /**
     * Check if asset is depreciable for the given period
     */
    private function isAssetDepreciableForPeriod(FixedAsset $asset, Carbon $periodDate): bool
    {
        // Start depreciation from next month after acquisition
        $depreciationStartDate = $asset->depreciation_start_date 
            ? Carbon::parse($asset->depreciation_start_date)
            : Carbon::parse($asset->acquisition_date)->addMonth()->startOfMonth();
            
        // Check if period is within depreciation timeline
        if ($periodDate->lt($depreciationStartDate)) {
            return false;
        }
        
        // Check if asset is fully depreciated
        $depreciableAmount = $asset->acquisition_price - $asset->residual_value;
        if ($asset->accumulated_depreciation >= $depreciableAmount) {
            return false;
        }
        
        // Check if period exceeds useful life
        $endDate = $depreciationStartDate->copy()->addMonths($asset->useful_life_months - 1);
        if ($periodDate->gt($endDate)) {
            return false;
        }
        
        return true;
    }

    /**
     * Process batch depreciation for eligible assets
     */
    public function processBatchDepreciation(string $periodMonth, int $userId): array
    {
        $periodDate = Carbon::createFromFormat('Y-m', $periodMonth)->startOfMonth();
        $eligibleAssets = $this->getEligibleAssets($periodMonth);
        
        if ($eligibleAssets->isEmpty()) {
            return [
                'success' => true,
                'message' => 'No eligible assets found for depreciation',
                'processed_count' => 0,
                'results' => []
            ];
        }

        $results = [];
        $processedCount = 0;

        DB::transaction(function () use ($eligibleAssets, $periodDate, $userId, &$results, &$processedCount) {
            foreach ($eligibleAssets as $asset) {
                try {
                    $depreciation = $this->assetService->postDepreciationToMemorial($asset, $periodDate, $userId);
                    
                    $results[] = [
                        'asset_id' => $asset->id,
                        'asset_name' => $asset->name,
                        'asset_code' => $asset->code,
                        'status' => 'success',
                        'depreciation_amount' => $depreciation->depreciation_amount,
                        'journal_number' => $depreciation->journal->number ?? null
                    ];
                    
                    $processedCount++;
                } catch (\Exception $e) {
                    $results[] = [
                        'asset_id' => $asset->id,
                        'asset_name' => $asset->name,
                        'asset_code' => $asset->code,
                        'status' => 'error',
                        'error_message' => $e->getMessage()
                    ];
                }
            }
        });

        return [
            'success' => true,
            'message' => "Processed {$processedCount} assets successfully",
            'processed_count' => $processedCount,
            'total_eligible' => $eligibleAssets->count(),
            'results' => $results
        ];
    }

    /**
     * Process batch depreciation with chunking for large datasets
     */
    public function processBatchDepreciationChunked(string $periodMonth, int $userId, int $chunkSize = 50): array
    {
        $periodDate = Carbon::createFromFormat('Y-m', $periodMonth)->startOfMonth();
        $eligibleAssets = $this->getEligibleAssets($periodMonth);
        
        if ($eligibleAssets->isEmpty()) {
            return [
                'success' => true,
                'message' => 'No eligible assets found for depreciation',
                'processed_count' => 0,
                'results' => []
            ];
        }

        $results = [];
        $processedCount = 0;
        $chunks = $eligibleAssets->chunk($chunkSize);

        foreach ($chunks as $chunk) {
            DB::transaction(function () use ($chunk, $periodDate, $userId, &$results, &$processedCount) {
                foreach ($chunk as $asset) {
                    try {
                        $depreciation = $this->assetService->postDepreciationToMemorial($asset, $periodDate, $userId);
                        
                        $results[] = [
                            'asset_id' => $asset->id,
                            'asset_name' => $asset->name,
                            'asset_code' => $asset->code,
                            'status' => 'success',
                            'depreciation_amount' => $depreciation->depreciation_amount,
                            'journal_number' => $depreciation->journal->number ?? null
                        ];
                        
                        $processedCount++;
                    } catch (\Exception $e) {
                        $results[] = [
                            'asset_id' => $asset->id,
                            'asset_name' => $asset->name,
                            'asset_code' => $asset->code,
                            'status' => 'error',
                            'error_message' => $e->getMessage()
                        ];
                    }
                }
            });
        }

        return [
            'success' => true,
            'message' => "Processed {$processedCount} assets successfully",
            'processed_count' => $processedCount,
            'total_eligible' => $eligibleAssets->count(),
            'results' => $results
        ];
    }
    public function previewEligibleAssets(string $periodMonth): array
    {
        $eligibleAssets = $this->getEligibleAssets($periodMonth);
        $periodDate = Carbon::createFromFormat('Y-m', $periodMonth)->startOfMonth();
        
        $preview = $eligibleAssets->map(function ($asset) use ($periodDate) {
            $monthlyDepreciation = $this->assetService->calculateMonthlyDepreciationAmount($asset);
            
            return [
                'id' => $asset->id,
                'code' => $asset->code,
                'name' => $asset->name,
                'group' => $asset->group,
                'acquisition_price' => $asset->acquisition_price,
                'accumulated_depreciation' => $asset->accumulated_depreciation,
                'monthly_depreciation' => $monthlyDepreciation,
                'book_value_after' => $asset->acquisition_price - ($asset->accumulated_depreciation + $monthlyDepreciation),
                'expense_account' => $asset->expenseAccount?->keterangan,
                'accumulated_account' => $asset->accumulatedAccount?->keterangan
            ];
        });

        return [
            'period' => $periodDate->format('M Y'),
            'eligible_count' => $eligibleAssets->count(),
            'total_depreciation' => $preview->sum('monthly_depreciation'),
            'assets' => $preview->toArray()
        ];
    }
}