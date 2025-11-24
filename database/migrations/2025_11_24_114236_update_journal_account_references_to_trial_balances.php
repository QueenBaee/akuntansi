<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('journals', function (Blueprint $table) {
            // Drop existing foreign key constraints
            $table->dropForeign(['debit_account_id']);
            $table->dropForeign(['credit_account_id']);
            
            // Add new foreign key constraints to trial_balances
            $table->foreign('debit_account_id')->references('id')->on('trial_balances')->onDelete('set null');
            $table->foreign('credit_account_id')->references('id')->on('trial_balances')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('journals', function (Blueprint $table) {
            // Drop trial_balances foreign key constraints
            $table->dropForeign(['debit_account_id']);
            $table->dropForeign(['credit_account_id']);
            
            // Restore accounts foreign key constraints
            $table->foreign('debit_account_id')->references('id')->on('accounts')->onDelete('set null');
            $table->foreign('credit_account_id')->references('id')->on('accounts')->onDelete('set null');
        });
    }
};