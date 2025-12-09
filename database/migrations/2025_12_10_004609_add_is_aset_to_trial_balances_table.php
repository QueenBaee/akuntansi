<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('trial_balances', function (Blueprint $table) {
            $table->boolean('is_aset')->default(false)->after('is_kas_bank');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('trial_balances', function (Blueprint $table) {
            $table->dropColumn('is_aset');
        });
    }
};
