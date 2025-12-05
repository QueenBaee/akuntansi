<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Maklon;
use App\Models\User;

class MaklonSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::first();
        
        if (!$user) {
            return;
        }

        Maklon::create([
            'date' => now()->subDays(5),
            'description' => 'Jasa Maklon Produk A',
            'pic' => 'John Doe',
            'proof_number' => 'MAK001',
            'batang' => 1000,
            'dpp' => 5000000,
            'ppn' => 11, // 11%
            'pph23' => 2, // 2%
            'created_by' => $user->id,
        ]);

        Maklon::create([
            'date' => now()->subDays(3),
            'description' => 'Jasa Maklon Produk B',
            'pic' => 'Jane Smith',
            'proof_number' => 'MAK002',
            'batang' => 500,
            'dpp' => 2500000,
            'ppn' => 11, // 11%
            'pph23' => 2, // 2%
            'created_by' => $user->id,
        ]);
    }
}