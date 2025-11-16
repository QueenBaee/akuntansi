<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rent_expenses', function (Blueprint $table) {
            $table->id();
            $table->string('contract_number', 50)->unique();
            $table->string('landlord_name');
            $table->string('property_description');
            $table->date('start_date');
            $table->date('end_date');
            $table->decimal('total_amount', 15, 2);
            $table->integer('period_months');
            $table->decimal('monthly_amount', 15, 2);
            $table->unsignedBigInteger('expense_account_id');
            $table->unsignedBigInteger('prepaid_account_id');
            $table->decimal('amortized_amount', 15, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->unsignedBigInteger('created_by');
            $table->timestamps();
            
            $table->foreign('expense_account_id')->references('id')->on('accounts');
            $table->foreign('prepaid_account_id')->references('id')->on('accounts');
            $table->foreign('created_by')->references('id')->on('users');
            $table->index(['contract_number', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rent_expenses');
    }
};