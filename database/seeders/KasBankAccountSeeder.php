<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Account;

class KasBankAccountSeeder extends Seeder
{
    public function run()
    {
        $accounts = [
            [
                'code' => '1001',
                'name' => 'Kas Kecil',
                'type' => 'kas',
                'opening_balance' => 1000000,
                'is_active' => true,
            ],
            [
                'code' => '1002',
                'name' => 'Kas Besar',
                'type' => 'kas',
                'opening_balance' => 5000000,
                'is_active' => true,
            ],
            [
                'code' => '1101',
                'name' => 'Bank BCA',
                'type' => 'bank',
                'opening_balance' => 10000000,
                'is_active' => true,
            ],
            [
                'code' => '1102',
                'name' => 'Bank Mandiri',
                'type' => 'bank',
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