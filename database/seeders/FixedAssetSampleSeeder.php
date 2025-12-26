<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\FixedAsset;
use App\Models\TrialBalance;

class FixedAssetSampleSeeder extends Seeder
{
    public function run(): void
    {
        // Get some trial balance accounts for mapping
        $assetAccount = TrialBalance::where('kode', 'like', '1%')->first();
        $accumulatedAccount = TrialBalance::where('kode', 'like', '1%')->skip(1)->first();
        $expenseAccount = TrialBalance::where('kode', 'like', '5%')->first();

        $sampleAssets = [
            // Bangunan
            [
                'code' => 'BNG-001',
                'name' => 'Gedung Kantor Pusat',
                'category_kode' => '1.1',
                'acquisition_date' => '2020-01-15',
                'acquisition_price' => 2500000000,
                'residual_value' => 0.10,
                'useful_life_months' => 240,
                'accumulated_depreciation' => 520833333,
                'is_active' => true,
            ],
            [
                'code' => 'BNG-002',
                'name' => 'Gudang Penyimpanan',
                'category_kode' => '1.2',
                'acquisition_date' => '2021-03-10',
                'acquisition_price' => 800000000,
                'residual_value' => 0.05,
                'useful_life_months' => 180,
                'accumulated_depreciation' => 140000000,
                'is_active' => true,
            ],
            
            // Kendaraan
            [
                'code' => 'KND-001',
                'name' => 'Toyota Avanza 2022',
                'category_kode' => '2.1',
                'acquisition_date' => '2022-06-15',
                'acquisition_price' => 250000000,
                'residual_value' => 0.20,
                'useful_life_months' => 60,
                'accumulated_depreciation' => 50000000,
                'is_active' => true,
            ],
            [
                'code' => 'KND-002',
                'name' => 'Honda Vario 150',
                'category_kode' => '2.2',
                'acquisition_date' => '2023-01-20',
                'acquisition_price' => 25000000,
                'residual_value' => 0.15,
                'useful_life_months' => 36,
                'accumulated_depreciation' => 5000000,
                'is_active' => true,
            ],
            [
                'code' => 'KND-003',
                'name' => 'Isuzu Elf Truck',
                'category_kode' => '2.3',
                'acquisition_date' => '2021-09-05',
                'acquisition_price' => 450000000,
                'residual_value' => 0.10,
                'useful_life_months' => 84,
                'accumulated_depreciation' => 120000000,
                'is_active' => true,
            ],
            
            // Peralatan
            [
                'code' => 'PRL-001',
                'name' => 'Laptop Dell Latitude',
                'category_kode' => '3.1',
                'acquisition_date' => '2023-08-10',
                'acquisition_price' => 15000000,
                'residual_value' => 0.10,
                'useful_life_months' => 36,
                'accumulated_depreciation' => 2000000,
                'is_active' => true,
            ],
            [
                'code' => 'PRL-002',
                'name' => 'Mesin Produksi A1',
                'category_kode' => '3.2',
                'acquisition_date' => '2020-12-01',
                'acquisition_price' => 500000000,
                'residual_value' => 0.05,
                'useful_life_months' => 120,
                'accumulated_depreciation' => 190000000,
                'is_active' => true,
            ],
            [
                'code' => 'PRL-003',
                'name' => 'Meja Kerja Executive',
                'category_kode' => '3.3',
                'acquisition_date' => '2022-04-15',
                'acquisition_price' => 8000000,
                'residual_value' => 0.20,
                'useful_life_months' => 60,
                'accumulated_depreciation' => 1600000,
                'is_active' => true,
            ],
        ];

        foreach ($sampleAssets as $assetData) {
            FixedAsset::create([
                'code' => $assetData['code'],
                'name' => $assetData['name'],
                'acquisition_date' => $assetData['acquisition_date'],
                'acquisition_price' => $assetData['price'],
                'residual_value' => 0.10,
                'useful_life_months' => 60,
                'accumulated_depreciation' => $assetData['price'] * 0.2,
                'asset_account_id' => $assetAccount?->id,
                'accumulated_account_id' => $accumulatedAccount?->id,
                'expense_account_id' => $expenseAccount?->id,
                'is_active' => true,
                'created_by' => 1,
            ]);
        }
    }
}