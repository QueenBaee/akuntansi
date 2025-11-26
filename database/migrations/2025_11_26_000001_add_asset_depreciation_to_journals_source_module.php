<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('journals', function (Blueprint $table) {
            $table->string('source_module', 50)->change();
        });
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE journals MODIFY COLUMN source_module ENUM('manual', 'cash', 'bank', 'asset', 'depreciation', 'maklon', 'rent_income', 'rent_expense')");
    }
};