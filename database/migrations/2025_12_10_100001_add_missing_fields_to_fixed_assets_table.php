<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('fixed_assets', function (Blueprint $table) {
            // Check if columns exist before adding them
            if (!Schema::hasColumn('fixed_assets', 'location')) {
                $table->string('location')->nullable()->after('name');
            }
            if (!Schema::hasColumn('fixed_assets', 'group')) {
                $table->enum('group', ['Permanent', 'Non-permanent', 'Group 1', 'Group 2'])->after('location');
            }
            if (!Schema::hasColumn('fixed_assets', 'condition')) {
                $table->enum('condition', ['Baik', 'Rusak'])->default('Baik')->after('group');
            }
            if (!Schema::hasColumn('fixed_assets', 'status')) {
                $table->enum('status', ['active', 'inactive'])->default('active')->after('condition');
            }
            if (!Schema::hasColumn('fixed_assets', 'depreciation_method')) {
                $table->enum('depreciation_method', ['garis lurus', 'saldo menurun'])->default('garis lurus')->after('useful_life_months');
            }
            if (!Schema::hasColumn('fixed_assets', 'useful_life_years')) {
                $table->integer('useful_life_years')->nullable()->after('depreciation_method');
            }
            if (!Schema::hasColumn('fixed_assets', 'depreciation_start_date')) {
                $table->date('depreciation_start_date')->nullable()->after('useful_life_years');
            }
        });
    }

    public function down(): void
    {
        Schema::table('fixed_assets', function (Blueprint $table) {
            $columnsToCheck = [
                'location', 
                'group',
                'condition',
                'status',
                'depreciation_method',
                'useful_life_years',
                'depreciation_start_date'
            ];
            
            foreach ($columnsToCheck as $column) {
                if (Schema::hasColumn('fixed_assets', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};