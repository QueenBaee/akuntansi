<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            PermissionSeeder::class,
            AccountSeeder::class,
            KasBankAccountSeeder::class,
            CashflowCategorySeeder::class,
            UserSeeder::class,
        ]);
    }
}