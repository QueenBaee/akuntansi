<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Account;

class KasBankAccountSeeder extends Seeder
{
    public function run(): void
    {
        $accounts = [
            [
                'code' => '1001',
                'name' => 'Kas Kecil',
                'type' => 'kas',
                'category' => 'current_asset',
                'opening_balance' => 1000000,
                'is_active' => true,
            ],
            [
                'code' => '1002',
                'name' => 'Kas Besar',
                'type' => 'kas',
                'category' => 'current_asset',
                'opening_balance' => 5000000,
                'is_active' => true,
            ],
            [
                'code' => '1101',
                'name' => 'Bank BCA',
                'type' => 'bank',
                'category' => 'current_asset',
                'opening_balance' => 10000000,
                'is_active' => true,
            ],
            [
                'code' => '1102',
                'name' => 'Bank Mandiri',
                'type' => 'bank',
                'category' => 'current_asset',
                'opening_balance' => 15000000,
                'is_active' => true,
            ],
        ];

        foreach ($accounts as $account) {
            Account::updateOrCreate(
                ['code' => $account['code']],
                $account
            );
        }
    }
}