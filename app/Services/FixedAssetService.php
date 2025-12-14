<?php

namespace App\Services;

use App\Models\FixedAsset;
use App\Models\AssetDepreciation;
use App\Models\Journal;
use App\Services\JournalNumberService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class FixedAssetService
{
    public function generateDepreciationSchedule(FixedAsset $asset): array
    {
        $schedule = [];
        // Start depreciation from the next month after acquisition
        $startDate = Carbon::parse($asset->acquisition_date)->addMonth()->startOfMonth();
        $monthlyDepreciation = $this->calculateMonthlyDepreciationAmount($asset);
        $accumulatedDepreciation = 0;
        $bookValue = $asset->acquisition_price;

        for ($i = 0; $i < $asset->useful_life_months; $i++) {
            $periodDate = $startDate->copy()->addMonths($i);
            
            // Calculate depreciation for this period
            $depreciationAmount = $monthlyDepreciation;
            
            // Adjust last month to prevent negative book value
            if ($i == $asset->useful_life_months - 1) {
                $depreciationAmount = min($depreciationAmount, $bookValue - $asset->residual_value);
            }
            
            $accumulatedDepreciation += $depreciationAmount;
            $bookValue -= $depreciationAmount;
            
            $schedule[] = $this->simulateRow($asset, $periodDate, $depreciationAmount, $accumulatedDepreciation, $bookValue);
        }

        return $schedule;
    }

    public function calculateMonthlyDepreciationAmount(FixedAsset $asset): float
    {
        if ($asset->useful_life_months <= 0) {
            return 0;
        }

        $depreciableAmount = $asset->acquisition_price - $asset->residual_value;
        return $depreciableAmount / $asset->useful_life_months;
    }

    public function isPosted(FixedAsset $asset, Carbon $period): bool
    {
        return AssetDepreciation::where('asset_id', $asset->id)
            ->where('period_date', $period->format('Y-m-01'))
            ->exists();
    }

    public function simulateRow(FixedAsset $asset, Carbon $period, float $depreciationAmount = null, float $accumulatedDepreciation = null, float $bookValue = null): array
    {
        if ($depreciationAmount === null) {
            $depreciationAmount = $this->calculateMonthlyDepreciationAmount($asset);
        }

        $isPosted = $this->isPosted($asset, $period);
        $postedData = null;

        if ($isPosted) {
            $postedData = AssetDepreciation::where('asset_id', $asset->id)
                ->where('period_date', $period->format('Y-m-01'))
                ->with('journal')
                ->first();
        }

        return [
            'period' => $period->format('Y-m-01'),
            'period_formatted' => $period->format('M Y'),
            'depreciation_amount' => $depreciationAmount,
            'accumulated_depreciation' => $accumulatedDepreciation ?? ($asset->accumulated_depreciation + $depreciationAmount),
            'book_value' => $bookValue ?? ($asset->acquisition_price - ($asset->accumulated_depreciation + $depreciationAmount)),
            'is_posted' => $isPosted,
            'posted_data' => $postedData,
        ];
    }

    public function postDepreciationToMemorial(FixedAsset $asset, Carbon $period, int $userId): AssetDepreciation
    {
        return DB::transaction(function () use ($asset, $period, $userId) {
            // Check if already posted
            $existingDepreciation = AssetDepreciation::where('asset_id', $asset->id)
                ->where('period_date', $period->format('Y-m-01'))
                ->first();

            if ($existingDepreciation) {
                throw new \Exception('Depreciation for this period has already been posted.');
            }

            $depreciationAmount = $this->calculateMonthlyDepreciationAmount($asset);
            $newAccumulatedDepreciation = $asset->accumulated_depreciation + $depreciationAmount;
            $newBookValue = $asset->acquisition_price - $newAccumulatedDepreciation;

            // Prevent negative book value
            if ($newBookValue < $asset->residual_value) {
                $depreciationAmount = $asset->acquisition_price - $asset->accumulated_depreciation - $asset->residual_value;
                $newAccumulatedDepreciation = $asset->accumulated_depreciation + $depreciationAmount;
                $newBookValue = $asset->residual_value;
            }

            // Create journal entry
            $journal = $this->createDepreciationJournal($asset, $period, $depreciationAmount, $userId);

            // Create depreciation record
            $depreciation = AssetDepreciation::create([
                'asset_id' => $asset->id,
                'period_date' => $period->format('Y-m-01'),
                'depreciation_amount' => $depreciationAmount,
                'accumulated_depreciation' => $newAccumulatedDepreciation,
                'book_value' => $newBookValue,
                'journal_id' => $journal->id,
                'created_by' => $userId,
            ]);

            // Update asset accumulated depreciation
            $asset->update([
                'accumulated_depreciation' => $newAccumulatedDepreciation
            ]);

            return $depreciation;
        });
    }

    private function createDepreciationJournal(FixedAsset $asset, Carbon $period, float $amount, int $userId): Journal
    {
        $journalNumber = JournalNumberService::generate($period->format('Y-m-01'));

        $journal = Journal::create([
            'date' => $period->format('Y-m-01'),
            'number' => $journalNumber,
            'description' => "Penyusutan {$asset->name} - {$period->format('M Y')}",
            'pic' => 'System',
            'cash_in' => $amount,
            'cash_out' => $amount,
            'total_debit' => $amount,
            'total_credit' => $amount,
            'debit_account_id' => $asset->expense_account_id,
            'credit_account_id' => $asset->accumulated_account_id,
            'source_module' => 'asset_depreciation',
            'source_id' => $asset->id,
            'is_posted' => true,
            'created_by' => $userId,
        ]);

        return $journal;
    }


}