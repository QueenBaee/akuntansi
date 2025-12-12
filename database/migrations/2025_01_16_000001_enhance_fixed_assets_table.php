<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('fixed_assets', function (Blueprint $table) {
            // Add missing fields according to requirements
            $table->string('asset_number', 50)->after('id')->nullable();
            $table->string('asset_name')->after('name')->nullable();
            $table->integer('quantity')->default(1)->after('asset_name');
            $table->string('location')->nullable()->after('quantity');
            $table->enum('group', ['Permanent', 'Non-permanent', 'Group 1', 'Group 2'])->default('Permanent')->after('location');
            $table->enum('condition', ['Good', 'Damaged'])->default('Good')->after('group');
            $table->enum('status', ['Active', 'Inactive'])->default('Active')->after('condition');
            $table->enum('depreciation_method', ['Straight Line', 'Declining Balance'])->default('Straight Line')->after('depreciation_rate');
            $table->integer('useful_life_years')->nullable()->after('depreciation_method');
            $table->date('depreciation_start_date')->nullable()->after('useful_life_years');
            $table->string('account_acquisition', 20)->nullable()->after('depreciation_start_date');
            $table->string('account_accumulated', 20)->nullable()->after('account_acquisition');
            $table->string('account_expense', 20)->nullable()->after('account_accumulated');
            
            // Make code nullable since we now have asset_number
            $table->string('code', 50)->nullable()->change();
            
            // Add indexes for new fields
            $table->index(['asset_number']);
            $table->index(['group', 'status']);
            $table->index(['depreciation_start_date']);
        });
    }

    public function down(): void
    {
        Schema::table('fixed_assets', function (Blueprint $table) {
            $table->dropColumn([
                'asset_number',
                'asset_name', 
                'quantity',
                'location',
                'group',
                'condition',
                'status',
                'depreciation_method',
                'useful_life_years',
                'depreciation_start_date',
                'account_acquisition',
                'account_accumulated',
                'account_expense'
            ]);
            
            $table->string('code', 50)->nullable(false)->change();
        });
    }
};