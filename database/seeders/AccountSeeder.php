<?php

namespace Database\Seeders;

use App\Models\Account;
use Illuminate\Database\Seeder;

class AccountSeeder extends Seeder
{
    public function run(): void
    {
        $accounts = [
            // ASET LANCAR
            ['code' => '1100', 'name' => 'Kas', 'type' => 'asset', 'category' => 'current_asset', 'opening_balance' => 0],
            ['code' => '1110', 'name' => 'Kas Kecil', 'type' => 'asset', 'category' => 'current_asset', 'opening_balance' => 0],
            ['code' => '1200', 'name' => 'Bank BCA', 'type' => 'asset', 'category' => 'current_asset', 'opening_balance' => 0],
            ['code' => '1210', 'name' => 'Bank Mandiri', 'type' => 'asset', 'category' => 'current_asset', 'opening_balance' => 0],
            ['code' => '1300', 'name' => 'Piutang Usaha', 'type' => 'asset', 'category' => 'current_asset', 'opening_balance' => 0],
            ['code' => '1310', 'name' => 'Piutang Sewa', 'type' => 'asset', 'category' => 'current_asset', 'opening_balance' => 0],
            ['code' => '1400', 'name' => 'Persediaan', 'type' => 'asset', 'category' => 'current_asset', 'opening_balance' => 0],
            ['code' => '1500', 'name' => 'Biaya Dibayar Dimuka', 'type' => 'asset', 'category' => 'current_asset', 'opening_balance' => 0],
            ['code' => '1510', 'name' => 'Sewa Dibayar Dimuka', 'type' => 'asset', 'category' => 'current_asset', 'opening_balance' => 0],
            
            // ASET TETAP
            ['code' => '1600', 'name' => 'Tanah', 'type' => 'asset', 'category' => 'fixed_asset', 'opening_balance' => 0],
            ['code' => '1610', 'name' => 'Bangunan', 'type' => 'asset', 'category' => 'fixed_asset', 'opening_balance' => 0],
            ['code' => '1611', 'name' => 'Akumulasi Penyusutan Bangunan', 'type' => 'asset', 'category' => 'fixed_asset', 'opening_balance' => 0],
            ['code' => '1620', 'name' => 'Kendaraan', 'type' => 'asset', 'category' => 'fixed_asset', 'opening_balance' => 0],
            ['code' => '1621', 'name' => 'Akumulasi Penyusutan Kendaraan', 'type' => 'asset', 'category' => 'fixed_asset', 'opening_balance' => 0],
            ['code' => '1630', 'name' => 'Peralatan', 'type' => 'asset', 'category' => 'fixed_asset', 'opening_balance' => 0],
            ['code' => '1631', 'name' => 'Akumulasi Penyusutan Peralatan', 'type' => 'asset', 'category' => 'fixed_asset', 'opening_balance' => 0],
            
            // KEWAJIBAN LANCAR
            ['code' => '2100', 'name' => 'Hutang Usaha', 'type' => 'liability', 'category' => 'current_liability', 'opening_balance' => 0],
            ['code' => '2200', 'name' => 'Hutang Gaji', 'type' => 'liability', 'category' => 'current_liability', 'opening_balance' => 0],
            ['code' => '2300', 'name' => 'Hutang Pajak', 'type' => 'liability', 'category' => 'current_liability', 'opening_balance' => 0],
            ['code' => '2400', 'name' => 'Pendapatan Diterima Dimuka', 'type' => 'liability', 'category' => 'current_liability', 'opening_balance' => 0],
            
            // KEWAJIBAN JANGKA PANJANG
            ['code' => '2500', 'name' => 'Hutang Bank', 'type' => 'liability', 'category' => 'long_term_liability', 'opening_balance' => 0],
            ['code' => '2600', 'name' => 'Hutang Jangka Panjang', 'type' => 'liability', 'category' => 'long_term_liability', 'opening_balance' => 0],
            
            // EKUITAS
            ['code' => '3100', 'name' => 'Modal Saham', 'type' => 'equity', 'category' => 'equity', 'opening_balance' => 0],
            ['code' => '3200', 'name' => 'Laba Ditahan', 'type' => 'equity', 'category' => 'equity', 'opening_balance' => 0],
            ['code' => '3300', 'name' => 'Laba Tahun Berjalan', 'type' => 'equity', 'category' => 'equity', 'opening_balance' => 0],
            
            // PENDAPATAN
            ['code' => '4100', 'name' => 'Pendapatan Penjualan', 'type' => 'revenue', 'category' => 'operating_revenue', 'opening_balance' => 0],
            ['code' => '4200', 'name' => 'Pendapatan Jasa Maklon', 'type' => 'revenue', 'category' => 'operating_revenue', 'opening_balance' => 0],
            ['code' => '4300', 'name' => 'Pendapatan Sewa', 'type' => 'revenue', 'category' => 'operating_revenue', 'opening_balance' => 0],
            ['code' => '4900', 'name' => 'Pendapatan Lain-lain', 'type' => 'revenue', 'category' => 'other_revenue', 'opening_balance' => 0],
            
            // BEBAN OPERASIONAL
            ['code' => '5100', 'name' => 'Beban Pokok Penjualan', 'type' => 'expense', 'category' => 'operating_expense', 'opening_balance' => 0],
            ['code' => '5200', 'name' => 'Beban Gaji', 'type' => 'expense', 'category' => 'operating_expense', 'opening_balance' => 0],
            ['code' => '5300', 'name' => 'Beban Sewa', 'type' => 'expense', 'category' => 'operating_expense', 'opening_balance' => 0],
            ['code' => '5400', 'name' => 'Beban Listrik', 'type' => 'expense', 'category' => 'operating_expense', 'opening_balance' => 0],
            ['code' => '5500', 'name' => 'Beban Telepon', 'type' => 'expense', 'category' => 'operating_expense', 'opening_balance' => 0],
            ['code' => '5600', 'name' => 'Beban Penyusutan Bangunan', 'type' => 'expense', 'category' => 'operating_expense', 'opening_balance' => 0],
            ['code' => '5610', 'name' => 'Beban Penyusutan Kendaraan', 'type' => 'expense', 'category' => 'operating_expense', 'opening_balance' => 0],
            ['code' => '5620', 'name' => 'Beban Penyusutan Peralatan', 'type' => 'expense', 'category' => 'operating_expense', 'opening_balance' => 0],
            ['code' => '5700', 'name' => 'Beban Maklon', 'type' => 'expense', 'category' => 'operating_expense', 'opening_balance' => 0],
            
            // BEBAN LAIN-LAIN
            ['code' => '5900', 'name' => 'Beban Bunga', 'type' => 'expense', 'category' => 'other_expense', 'opening_balance' => 0],
            ['code' => '5910', 'name' => 'Beban Administrasi Bank', 'type' => 'expense', 'category' => 'other_expense', 'opening_balance' => 0],
            ['code' => '5920', 'name' => 'Beban Lain-lain', 'type' => 'expense', 'category' => 'other_expense', 'opening_balance' => 0],
        ];

        foreach ($accounts as $account) {
            Account::create($account);
        }
    }
}