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
        Schema::table('journals', function (Blueprint $table) {
            $table->index('date');
            $table->index('debit_account_id');
            $table->index('credit_account_id');
            $table->index('deleted_at');
            $table->index(['date', 'debit_account_id']);
            $table->index(['date', 'credit_account_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('journals', function (Blueprint $table) {
            $table->dropIndex(['date']);
            $table->dropIndex(['debit_account_id']);
            $table->dropIndex(['credit_account_id']);
            $table->dropIndex(['deleted_at']);
            $table->dropIndex(['date', 'debit_account_id']);
            $table->dropIndex(['date', 'credit_account_id']);
        });
    }
};
