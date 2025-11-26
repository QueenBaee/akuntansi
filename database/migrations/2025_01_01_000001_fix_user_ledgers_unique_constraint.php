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
        Schema::table('user_ledgers', function (Blueprint $table) {
            // Drop existing unique constraint if exists
            try {
                $table->dropUnique(['user_id', 'ledger_id']);
            } catch (\Exception $e) {
                // Constraint might not exist, continue
            }
            
            // Add new unique constraint with proper name
            $table->unique(['user_id', 'ledger_id'], 'user_ledgers_user_ledger_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_ledgers', function (Blueprint $table) {
            $table->dropUnique('user_ledgers_user_ledger_unique');
            $table->unique(['user_id', 'ledger_id']);
        });
    }
};