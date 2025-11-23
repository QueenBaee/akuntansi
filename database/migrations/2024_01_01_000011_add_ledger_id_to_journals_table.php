<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('journals', function (Blueprint $table) {
            $table->foreignId('ledger_id')->nullable()->after('created_by')->constrained()->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('journals', function (Blueprint $table) {
            $table->dropForeign(['ledger_id']);
            $table->dropColumn('ledger_id');
        });
    }
};