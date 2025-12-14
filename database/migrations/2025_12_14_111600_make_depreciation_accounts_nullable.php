<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('fixed_assets', function (Blueprint $table) {
            $table->foreignId('accumulated_account_id')->nullable()->change();
            $table->foreignId('expense_account_id')->nullable()->change();
            $table->integer('useful_life_months')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('fixed_assets', function (Blueprint $table) {
            $table->foreignId('accumulated_account_id')->nullable(false)->change();
            $table->foreignId('expense_account_id')->nullable(false)->change();
            $table->integer('useful_life_months')->nullable(false)->change();
        });
    }
};