<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::create([
            'name' => 'Administrator',
            'email' => 'admin@example.com',
            'password' => Hash::make('password123'),
            'is_active' => true,
        ]);
        $admin->assignRole('admin');

        $staff = User::create([
            'name' => 'Staff Akuntansi',
            'email' => 'staff@example.com',
            'password' => Hash::make('password123'),
            'is_active' => true,
        ]);
        $staff->assignRole('staff_akuntansi');

        $manager = User::create([
            'name' => 'Manajer',
            'email' => 'manager@example.com',
            'password' => Hash::make('password123'),
            'is_active' => true,
        ]);
        $manager->assignRole('manajer');
    }
}