<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\FixedAsset;
use App\Models\TrialBalance;
use App\Models\User;

class EnhancedFixedAssetSeeder extends Seeder
{
    public function run(): void
    {
        // Get admin user
        $adminUser = User::where('email', 'admin@example.com')->first();
        if (!$adminUser) {
            $adminUser = User::first();
        }

        // Get trial balance accounts for asset accounts
        $assetAccounts = TrialBalance::where('kode', 'like', 'A23-%')->get();
        $accumulatedAccounts = TrialBalance::where('kode', 'like', 'A24-%')->get();
        $expenseAccounts = TrialBalance::where('kode', 'like', 'E22-%')->get();

        // Sample fixed assets with new fields
        $assets = [
            [
                'asset_number' => 'AST-0001',
                'asset_name' => 'Gedung Kantor Pusat',
                'quantity' => 1,
                'location' => 'Jakarta Pusat',
                'group' => 'Permanent',
                'condition' => 'Good',
                'status' => 'Active',
                'acquisition_date' => '2023-01-15',
                'acquisition_price' => 2500000000,
                'residual_value' => 250000000,
                'depreciation_method' => 'Straight Line',
                'useful_life_years' => 20,
                'useful_life_months' => 240,
                'depreciation_start_date' => '2023-02-01',
                'account_acquisition' => 'A23-01',
                'account_accumulated' => 'A24-01',
                'account_expense' => 'E22-96',
            ],
            [
                'asset_number' => 'AST-0002',
                'asset_name' => 'Gudang Penyimpanan',
                'quantity' => 1,
                'location' => 'Bekasi',
                'group' => 'Permanent',
                'condition' => 'Good',
                'status' => 'Active',
                'acquisition_date' => '2023-03-10',
                'acquisition_price' => 800000000,
                'residual_value' => 80000000,
                'depreciation_method' => 'Straight Line',
                'useful_life_years' => 15,
                'useful_life_months' => 180,
                'depreciation_start_date' => '2023-04-01',
                'account_acquisition' => 'A23-01',
                'account_accumulated' => 'A24-01',
                'account_expense' => 'E22-96',
            ],
            [
                'asset_number' => 'AST-0003',
                'asset_name' => 'Mobil Toyota Avanza',
                'quantity' => 2,
                'location' => 'Pool Kendaraan',
                'group' => 'Non-permanent',
                'condition' => 'Good',
                'status' => 'Active',
                'acquisition_date' => '2023-05-20',
                'acquisition_price' => 250000000,
                'residual_value' => 50000000,
                'depreciation_method' => 'Declining Balance',
                'useful_life_years' => 8,
                'useful_life_months' => 96,
                'depreciation_start_date' => '2023-06-01',
                'account_acquisition' => 'A23-02',
                'account_accumulated' => 'A24-02',
                'account_expense' => 'E22-97',
            ],
            [
                'asset_number' => 'AST-0004',
                'asset_name' => 'Motor Honda Vario',
                'quantity' => 3,
                'location' => 'Pool Kendaraan',
                'group' => 'Group 1',
                'condition' => 'Good',
                'status' => 'Active',
                'acquisition_date' => '2023-07-15',
                'acquisition_price' => 75000000,
                'residual_value' => 15000000,
                'depreciation_method' => 'Straight Line',
                'useful_life_years' => 5,
                'useful_life_months' => 60,
                'depreciation_start_date' => '2023-08-01',
                'account_acquisition' => 'A23-03',
                'account_accumulated' => 'A24-03',
                'account_expense' => 'E22-98',
            ],
            [
                'asset_number' => 'AST-0005',
                'asset_name' => 'Laptop Dell Latitude',
                'quantity' => 10,
                'location' => 'Kantor IT',
                'group' => 'Group 2',
                'condition' => 'Good',
                'status' => 'Active',
                'acquisition_date' => '2023-09-01',
                'acquisition_price' => 150000000,
                'residual_value' => 15000000,
                'depreciation_method' => 'Declining Balance',
                'useful_life_years' => 4,
                'useful_life_months' => 48,
                'depreciation_start_date' => '2023-09-01',
                'account_acquisition' => 'A23-04',
                'account_accumulated' => 'A24-04',
                'account_expense' => 'E22-99',
            ],
            [
                'asset_number' => 'AST-0006',
                'asset_name' => 'Mesin Produksi A1',
                'quantity' => 1,
                'location' => 'Pabrik Lantai 1',
                'group' => 'Permanent',
                'condition' => 'Good',
                'status' => 'Active',
                'acquisition_date' => '2023-10-15',
                'acquisition_price' => 500000000,
                'residual_value' => 50000000,
                'depreciation_method' => 'Straight Line',
                'useful_life_years' => 10,
                'useful_life_months' => 120,
                'depreciation_start_date' => '2023-11-01',
                'account_acquisition' => 'A23-01',
                'account_accumulated' => 'A24-01',
                'account_expense' => 'E22-96',
            ],
        ];

        foreach ($assets as $assetData) {
            // Calculate depreciation rate
            if ($assetData['depreciation_method'] === 'Straight Line') {
                $depreciationRate = round(100 / $assetData['useful_life_years'], 2);
            } else {
                $depreciationRate = round((2 / $assetData['useful_life_years']) * 100, 2);
            }

            // Find corresponding trial balance accounts
            $assetAccount = $assetAccounts->where('kode', $assetData['account_acquisition'])->first();
            $accumulatedAccount = $accumulatedAccounts->where('kode', $assetData['account_accumulated'])->first();
            $expenseAccount = $expenseAccounts->where('kode', $assetData['account_expense'])->first();

            FixedAsset::create([
                'asset_number' => $assetData['asset_number'],
                'asset_name' => $assetData['asset_name'],
                'code' => $assetData['asset_number'], // Use asset_number as code for backward compatibility
                'name' => $assetData['asset_name'], // Use asset_name as name for backward compatibility
                'quantity' => $assetData['quantity'],
                'location' => $assetData['location'],
                'group' => $assetData['group'],
                'condition' => $assetData['condition'],
                'status' => $assetData['status'],
                'acquisition_date' => $assetData['acquisition_date'],
                'acquisition_price' => $assetData['acquisition_price'],
                'residual_value' => $assetData['residual_value'],
                'depreciation_method' => $assetData['depreciation_method'],
                'useful_life_years' => $assetData['useful_life_years'],
                'useful_life_months' => $assetData['useful_life_months'],
                'depreciation_rate' => $depreciationRate,
                'depreciation_start_date' => $assetData['depreciation_start_date'],
                'account_acquisition' => $assetData['account_acquisition'],
                'account_accumulated' => $assetData['account_accumulated'],
                'account_expense' => $assetData['account_expense'],
                'asset_account_id' => $assetAccount?->id,
                'accumulated_account_id' => $accumulatedAccount?->id,
                'expense_account_id' => $expenseAccount?->id,
                'accumulated_depreciation' => 0,
                'is_active' => $assetData['status'] === 'Active',
                'created_by' => $adminUser->id,
            ]);
        }

        $this->command->info('Enhanced fixed assets seeded successfully!');
    }
}