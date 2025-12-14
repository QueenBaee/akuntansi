<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('journals', function (Blueprint $table) {
            $table->foreignId('fixed_asset_id')->nullable()->after('source_id')->constrained('fixed_assets')->onDelete('set null');
            $table->index(['fixed_asset_id']);
        });
    }

    public function down(): void
    {
        Schema::table('journals', function (Blueprint $table) {
            $table->dropForeign(['fixed_asset_id']);
            $table->dropColumn('fixed_asset_id');
        });
    }
};