<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CashflowCategory;

class CashflowCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Penjualan', 'type' => 'operating', 'is_active' => true],
            ['name' => 'Pembelian', 'type' => 'operating', 'is_active' => true],
            ['name' => 'Beban Operasional', 'type' => 'operating', 'is_active' => true],
            ['name' => 'Investasi Aset', 'type' => 'investing', 'is_active' => true],
            ['name' => 'Penjualan Aset', 'type' => 'investing', 'is_active' => true],
            ['name' => 'Pinjaman Bank', 'type' => 'financing', 'is_active' => true],
            ['name' => 'Pembayaran Pinjaman', 'type' => 'financing', 'is_active' => true],
            ['name' => 'Modal', 'type' => 'financing', 'is_active' => true],
        ];

        foreach ($categories as $category) {
            CashflowCategory::create($category);
        }
    }
}