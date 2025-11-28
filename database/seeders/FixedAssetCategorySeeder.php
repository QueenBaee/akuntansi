<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\FixedAssetCategory;

class FixedAssetCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Bangunan',
                'code' => 'BNG',
                'children' => [
                    ['name' => 'Gedung Kantor', 'code' => 'BNG-01'],
                    ['name' => 'Gudang', 'code' => 'BNG-02'],
                    ['name' => 'Pabrik', 'code' => 'BNG-03'],
                ]
            ],
            [
                'name' => 'Kendaraan',
                'code' => 'KND',
                'children' => [
                    ['name' => 'Mobil', 'code' => 'KND-01'],
                    ['name' => 'Motor', 'code' => 'KND-02'],
                    ['name' => 'Truk', 'code' => 'KND-03'],
                ]
            ],
            [
                'name' => 'Peralatan',
                'code' => 'PRL',
                'children' => [
                    ['name' => 'Komputer', 'code' => 'PRL-01'],
                    ['name' => 'Mesin', 'code' => 'PRL-02'],
                    ['name' => 'Furniture', 'code' => 'PRL-03'],
                ]
            ],
            [
                'name' => 'Tanah',
                'code' => 'TNH',
                'children' => [
                    ['name' => 'Tanah Kantor', 'code' => 'TNH-01'],
                    ['name' => 'Tanah Pabrik', 'code' => 'TNH-02'],
                ]
            ],
        ];

        foreach ($categories as $categoryData) {
            $category = FixedAssetCategory::create([
                'name' => $categoryData['name'],
                'code' => $categoryData['code'],
                'parent_id' => null,
                'is_active' => true,
            ]);

            foreach ($categoryData['children'] as $childData) {
                FixedAssetCategory::create([
                    'name' => $childData['name'],
                    'code' => $childData['code'],
                    'parent_id' => $category->id,
                    'is_active' => true,
                ]);
            }
        }
    }
}