<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CashAccount;
use App\Models\BankAccount;

class CashBankAccountSeeder extends Seeder
{
    public function run()
    {
        // Cash Accounts
        CashAccount::create([
            'name' => 'Kas Kecil',
            'account_number' => '1001',
            'description' => 'Kas untuk keperluan operasional harian',
            'is_active' => true
        ]);

        CashAccount::create([
            'name' => 'Kas Besar',
            'account_number' => '1002',
            'description' => 'Kas untuk transaksi besar',
            'is_active' => true
        ]);

        // Bank Accounts
        BankAccount::create([
            'name' => 'Rekening Operasional',
            'bank_name' => 'Bank BCA',
            'account_number' => '1234567890',
            'description' => 'Rekening untuk operasional perusahaan',
            'is_active' => true
        ]);

        BankAccount::create([
            'name' => 'Rekening Investasi',
            'bank_name' => 'Bank Mandiri',
            'account_number' => '0987654321',
            'description' => 'Rekening untuk investasi jangka panjang',
            'is_active' => true
        ]);
    }
}