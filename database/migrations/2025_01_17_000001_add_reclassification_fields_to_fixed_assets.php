<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('fixed_assets', function (Blueprint $table) {
            // Add reclassification tracking fields
            $table->boolean('is_converted')->default(false)->after('is_merged');
            $table->foreignId('parent_asset_id')->nullable()->after('is_converted')
                  ->constrained('fixed_assets')->onDelete('set null');
            $table->timestamp('converted_at')->nullable()->after('parent_asset_id');
            $table->foreignId('converted_by')->nullable()->after('converted_at')
                  ->constrained('users')->onDelete('set null');
            
            // Add index for performance
            $table->index(['is_converted', 'group']);
            $table->index(['parent_asset_id']);
        });
    }

    public function down(): void
    {
        Schema::table('fixed_assets', function (Blueprint $table) {
            $table->dropForeign(['parent_asset_id']);
            $table->dropForeign(['converted_by']);
            $table->dropColumn([
                'is_converted',
                'parent_asset_id', 
                'converted_at',
                'converted_by'
            ]);
        });
    }
};