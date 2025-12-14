<?php

namespace App\Services;

use App\Models\Journal;
use App\Models\FixedAsset;
use App\Models\TrialBalance;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AssetFromTransactionService
{
    public function canCreateAssetFromTransaction(Journal $journal): bool
    {
        // Check if asset already created from this transaction
        if ($journal->fixed_asset_id) {
            return false;
        }
        
        // Check if debit account is asset account
        if ($journal->debitAccount && $journal->debitAccount->parent && $journal->debitAccount->parent->is_aset) {
            return true;
        }
        
        // Check if credit account is asset account (for asset disposal/transfer)
        if ($journal->creditAccount && $journal->creditAccount->parent && $journal->creditAccount->parent->is_aset) {
            return true;
        }
        
        return false;
    }
    
    public function getAssetAccountFromTransaction(Journal $journal): ?TrialBalance
    {
        if ($journal->debitAccount && $journal->debitAccount->parent && $journal->debitAccount->parent->is_aset) {
            return $journal->debitAccount;
        }
        
        if ($journal->creditAccount && $journal->creditAccount->parent && $journal->creditAccount->parent->is_aset) {
            return $journal->creditAccount;
        }
        
        return null;
    }
    
    public function createAssetFromTransaction(Journal $journal, array $assetData): FixedAsset
    {
        return DB::transaction(function () use ($journal, $assetData) {
            $assetAccount = $this->getAssetAccountFromTransaction($journal);
            
            if (!$assetAccount) {
                throw new \Exception('No asset account found in transaction');
            }
            
            // Determine acquisition value from transaction
            $acquisitionValue = $journal->debitAccount && $journal->debitAccount->parent && $journal->debitAccount->parent->is_aset 
                ? $journal->total_debit 
                : $journal->total_credit;
            
            // Pre-fill asset data from transaction
            $defaultData = [
                'asset_account_id' => $assetAccount->id,
                'acquisition_date' => $journal->date,
                'acquisition_price' => $acquisitionValue,
                'residual_value' => 1,
                'depreciation_start_date' => $journal->date,
                'created_by' => auth()->id(),
                'is_active' => true,
                'status' => 'active'
            ];
            
            // Merge with provided data
            $finalData = array_merge($defaultData, $assetData);
            
            // Auto-suggest related accounts
            $this->autoSuggestAccounts($finalData);
            
            // Create asset
            $asset = FixedAsset::create($finalData);
            
            // Link transaction to asset
            $journal->update(['fixed_asset_id' => $asset->id]);
            
            return $asset;
        });
    }
    
    public function convertAssetInProgressToRegular(array $assetInProgressIds, array $regularAssetData): FixedAsset
    {
        return DB::transaction(function () use ($assetInProgressIds, $regularAssetData) {
            $assetsInProgress = FixedAsset::whereIn('id', $assetInProgressIds)
                ->where('status', 'in_progress')
                ->get();
                
            if ($assetsInProgress->isEmpty()) {
                throw new \Exception('No valid assets in progress found');
            }
            
            // Calculate total acquisition value
            $totalAcquisitionValue = $assetsInProgress->sum('acquisition_price');
            
            // Create regular asset
            $regularAssetData['acquisition_price'] = $totalAcquisitionValue;
            $regularAssetData['status'] = 'active';
            $regularAssetData['created_by'] = auth()->id();
            
            $regularAsset = FixedAsset::create($regularAssetData);
            
            // Update assets in progress status
            foreach ($assetsInProgress as $assetInProgress) {
                $assetInProgress->update([
                    'status' => 'converted',
                    'parent_id' => $regularAsset->id
                ]);
                
                // Transfer journal relationships
                $assetInProgress->journals()->update(['fixed_asset_id' => $regularAsset->id]);
            }
            
            return $regularAsset;
        });
    }
    
    private function autoSuggestAccounts(array &$assetData): void
    {
        if (!isset($assetData['group'])) {
            return;
        }
        
        // For non-depreciable assets, set depreciation accounts to null
        if (in_array($assetData['group'], ['Aset Dalam Penyelesaian', 'Tanah'])) {
            $assetData['accumulated_account_id'] = null;
            $assetData['expense_account_id'] = null;
            $assetData['useful_life_months'] = null;
            return;
        }
        
        $accountMapping = [
            'Permanent' => ['accumulated' => 'A24-01', 'expense' => 'E22-96'],
            'Non-permanent' => ['accumulated' => 'A24-02', 'expense' => 'E22-97'],
            'Group 1' => ['accumulated' => 'A24-03', 'expense' => 'E22-98'],
            'Group 2' => ['accumulated' => 'A24-04', 'expense' => 'E22-99'],
        ];
        
        if (isset($accountMapping[$assetData['group']])) {
            $mapping = $accountMapping[$assetData['group']];
            
            if (!isset($assetData['accumulated_account_id']) && isset($mapping['accumulated'])) {
                $accAccount = TrialBalance::where('kode', $mapping['accumulated'])->first();
                if ($accAccount) {
                    $assetData['accumulated_account_id'] = $accAccount->id;
                }
            }
            
            if (!isset($assetData['expense_account_id']) && isset($mapping['expense'])) {
                $expAccount = TrialBalance::where('kode', $mapping['expense'])->first();
                if ($expAccount) {
                    $assetData['expense_account_id'] = $expAccount->id;
                }
            }
        }
    }
    
    public function getUnconvertedAssetTransactions(): array
    {
        $journals = Journal::with(['debitAccount.parent', 'creditAccount.parent'])
            ->whereNull('fixed_asset_id')
            ->where('is_posted', true)
            ->where(function($query) {
                $query->whereHas('debitAccount.parent', function($subQuery) {
                    $subQuery->where('is_aset', true);
                })
                ->orWhereHas('creditAccount.parent', function($subQuery) {
                    $subQuery->where('is_aset', true);
                });
            })
            ->orderBy('date', 'desc')
            ->get();
            
        return $journals->map(function($journal) {
            return [
                'journal_id' => $journal->id,
                'date' => $journal->date->format('d/m/Y'),
                'description' => $journal->description,
                'amount' => $journal->total_debit,
                'asset_account' => $this->getAssetAccountFromTransaction($journal)?->keterangan,
                'can_create_asset' => $this->canCreateAssetFromTransaction($journal)
            ];
        })->toArray();
    }
}