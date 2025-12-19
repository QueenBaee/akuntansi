<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // Add new boolean column
        Schema::table('trial_balances', function (Blueprint $table) {
            $table->boolean('is_kas_bank_new')->default(false);
        });

        // Convert existing data
        DB::statement("UPDATE trial_balances SET is_kas_bank_new = CASE 
            WHEN is_kas_bank = 'kas' OR is_kas_bank = 'bank' THEN 1 
            ELSE 0 
        END");

        // Drop old column and rename new one
        Schema::table('trial_balances', function (Blueprint $table) {
            $table->dropColumn('is_kas_bank');
        });
        
        Schema::table('trial_balances', function (Blueprint $table) {
            $table->renameColumn('is_kas_bank_new', 'is_kas_bank');
        });
    }

    public function down()
    {
        Schema::table('trial_balances', function (Blueprint $table) {
            $table->enum('is_kas_bank', ['kas', 'bank'])->nullable()->change();
        });
    }
};