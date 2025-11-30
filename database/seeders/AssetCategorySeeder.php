<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AssetCategory;

class AssetCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            // Bangunan
            ['kode' => '1', 'nama' => 'Bangunan', 'parent_kode' => null, 'level' => 1],
            ['kode' => '1.1', 'nama' => 'Gedung Kantor', 'parent_kode' => '1', 'level' => 2],
            ['kode' => '1.2', 'nama' => 'Gudang', 'parent_kode' => '1', 'level' => 2],
            ['kode' => '1.3', 'nama' => 'Pabrik', 'parent_kode' => '1', 'level' => 2],
            
            // Kendaraan
            ['kode' => '2', 'nama' => 'Kendaraan', 'parent_kode' => null, 'level' => 1],
            ['kode' => '2.1', 'nama' => 'Mobil', 'parent_kode' => '2', 'level' => 2],
            ['kode' => '2.2', 'nama' => 'Motor', 'parent_kode' => '2', 'level' => 2],
            ['kode' => '2.3', 'nama' => 'Truk', 'parent_kode' => '2', 'level' => 2],
            
            // Peralatan
            ['kode' => '3', 'nama' => 'Peralatan', 'parent_kode' => null, 'level' => 1],
            ['kode' => '3.1', 'nama' => 'Komputer', 'parent_kode' => '3', 'level' => 2],
            ['kode' => '3.2', 'nama' => 'Mesin', 'parent_kode' => '3', 'level' => 2],
            ['kode' => '3.3', 'nama' => 'Furniture', 'parent_kode' => '3', 'level' => 2],
            
            // Tanah
            ['kode' => '4', 'nama' => 'Tanah', 'parent_kode' => null, 'level' => 1],
            ['kode' => '4.1', 'nama' => 'Tanah Kantor', 'parent_kode' => '4', 'level' => 2],
            ['kode' => '4.2', 'nama' => 'Tanah Pabrik', 'parent_kode' => '4', 'level' => 2],
        ];

        foreach ($categories as $category) {
            AssetCategory::create($category);
        }
    }
}