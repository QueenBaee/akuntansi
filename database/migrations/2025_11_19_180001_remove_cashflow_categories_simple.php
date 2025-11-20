<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Remove foreign keys from all tables that reference cashflow_categories
        $tables = ['cash_transactions', 'bank_transactions'];
        
        foreach ($tables as $table) {
            if (Schema::hasTable($table) && Schema::hasColumn($table, 'cashflow_category_id')) {
                Schema::table($table, function (Blueprint $tableSchema) {
                    $tableSchema->dropForeign(['cashflow_category_id']);
                    $tableSchema->dropColumn('cashflow_category_id');
                });
            }
        }

        // Update journals table
        Schema::table('journals', function (Blueprint $table) {
            if (Schema::hasColumn('journals', 'cashflow_category_id')) {
                $table->dropForeign(['cashflow_category_id']);
                $table->dropColumn('cashflow_category_id');
            }
            if (!Schema::hasColumn('journals', 'cashflow_id')) {
                $table->unsignedBigInteger('cashflow_id')->nullable()->after('credit_account_id');
                $table->foreign('cashflow_id')->references('id')->on('cashflows')->onDelete('set null');
            }
        });

        // Drop cashflow_categories table
        Schema::dropIfExists('cashflow_categories');
    }

    public function down(): void
    {
        // Recreate cashflow_categories table
        Schema::create('cashflow_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('type', ['operating', 'investing', 'financing']);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }
};