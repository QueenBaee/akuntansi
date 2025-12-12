<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Fix enum values by recreating columns with correct values
        DB::statement("ALTER TABLE fixed_assets MODIFY COLUMN `condition` ENUM('Baik', 'Rusak') DEFAULT 'Baik'");
        DB::statement("ALTER TABLE fixed_assets MODIFY COLUMN `status` ENUM('active', 'inactive') DEFAULT 'active'");
        DB::statement("ALTER TABLE fixed_assets MODIFY COLUMN `group` ENUM('Permanent', 'Non-permanent', 'Group 1', 'Group 2')");
        DB::statement("ALTER TABLE fixed_assets MODIFY COLUMN `depreciation_method` ENUM('garis lurus', 'saldo menurun') DEFAULT 'garis lurus'");
    }

    public function down(): void
    {
        // Rollback not needed as this is a fix
    }
};