<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Change group column to varchar to accommodate longer names
        DB::statement("ALTER TABLE fixed_assets MODIFY COLUMN `group` VARCHAR(50) DEFAULT 'Permanent'");
    }

    public function down(): void
    {
        // Revert back to original enum
        DB::statement("ALTER TABLE fixed_assets MODIFY COLUMN `group` ENUM('Permanent', 'Non-permanent', 'Group 1', 'Group 2') DEFAULT 'Permanent'");
    }
};