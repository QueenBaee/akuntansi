<?php

namespace Database\Seeders;

use App\Models\CashflowCategory;
use Illuminate\Database\Seeder;

class CashflowCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            // Operating Activities
            ['name' => 'Penerimaan dari Pelanggan', 'type' => 'operating'],
            ['name' => 'Pembayaran ke Pemasok', 'type' => 'operating'],
            ['name' => 'Pembayaran Gaji Karyawan', 'type' => 'operating'],
            ['name' => 'Pembayaran Beban Operasional', 'type' => 'operating'],
            ['name' => 'Penerimaan Pendapatan Sewa', 'type' => 'operating'],
            ['name' => 'Pembayaran Pajak', 'type' => 'operating'],
            
            // Investing Activities
            ['name' => 'Pembelian Aset Tetap', 'type' => 'investing'],
            ['name' => 'Penjualan Aset Tetap', 'type' => 'investing'],
            ['name' => 'Investasi Jangka Panjang', 'type' => 'investing'],
            ['name' => 'Pencairan Investasi', 'type' => 'investing'],
            
            // Financing Activities
            ['name' => 'Penerimaan Pinjaman Bank', 'type' => 'financing'],
            ['name' => 'Pembayaran Pinjaman Bank', 'type' => 'financing'],
            ['name' => 'Setoran Modal', 'type' => 'financing'],
            ['name' => 'Pembayaran Dividen', 'type' => 'financing'],
            ['name' => 'Pembayaran Bunga Pinjaman', 'type' => 'financing'],
        ];

        foreach ($categories as $category) {
            CashflowCategory::create($category);
        }
    }
}