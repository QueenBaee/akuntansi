<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Remove foreign key from cash_transactions first
        Schema::table('cash_transactions', function (Blueprint $table) {
            $table->dropForeign(['cashflow_category_id']);
            $table->dropColumn('cashflow_category_id');
        });

        // Remove foreign key and column from journals
        Schema::table('journals', function (Blueprint $table) {
            $table->dropForeign(['cashflow_category_id']);
            $table->dropColumn('cashflow_category_id');
            $table->unsignedBigInteger('cashflow_id')->nullable()->after('credit_account_id');
            $table->foreign('cashflow_id')->references('id')->on('cashflows')->onDelete('set null');
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

        // Update journals table
        Schema::table('journals', function (Blueprint $table) {
            $table->dropForeign(['cashflow_id']);
            $table->dropColumn('cashflow_id');
            $table->unsignedBigInteger('cashflow_category_id')->nullable()->after('credit_account_id');
            $table->foreign('cashflow_category_id')->references('id')->on('cashflow_categories')->onDelete('set null');
        });
    }
};