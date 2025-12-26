<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
{
    Schema::table('fixed_assets', function (Blueprint $table) {
        // Reclassification tracking
        $table->boolean('is_converted')->default(false);
        $table->foreignId('parent_asset_id')
              ->nullable()
              ->constrained('fixed_assets')
              ->nullOnDelete();

        $table->timestamp('converted_at')->nullable();

        $table->foreignId('converted_by')
              ->nullable()
              ->constrained('users')
              ->nullOnDelete();

        // Indexes
        $table->index(['is_converted', 'group']);
        $table->index('parent_asset_id');
    });
}


    public function down(): void
{
    Schema::table('fixed_assets', function (Blueprint $table) {
        $table->dropForeign(['parent_asset_id']);
        $table->dropForeign(['converted_by']);

        $table->dropIndex(['fixed_assets_is_converted_group_index']);
        $table->dropIndex(['fixed_assets_parent_asset_id_index']);

        $table->dropColumn([
            'is_converted',
            'parent_asset_id',
            'converted_at',
            'converted_by',
        ]);
    });
}

};