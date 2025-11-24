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
            $table->string('role')->nullable()->after('ledger_id');
            $table->boolean('is_active')->default(true)->after('role');
            
            $table->index(['user_id', 'is_active']);
            $table->index(['ledger_id', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_ledgers', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'is_active']);
            $table->dropIndex(['ledger_id', 'is_active']);
            $table->dropColumn(['role', 'is_active']);
        });
    }
};
