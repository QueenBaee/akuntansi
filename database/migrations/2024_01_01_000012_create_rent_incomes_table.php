<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rent_incomes', function (Blueprint $table) {
            $table->id();
            $table->string('contract_number', 50)->unique();
            $table->string('tenant_name');
            $table->string('property_description');
            $table->date('start_date');
            $table->date('end_date');
            $table->decimal('monthly_amount', 15, 2);
            $table->unsignedBigInteger('revenue_account_id');
            $table->unsignedBigInteger('receivable_account_id');
            $table->boolean('is_active')->default(true);
            $table->unsignedBigInteger('created_by');
            $table->timestamps();
            
            $table->foreign('revenue_account_id')->references('id')->on('accounts');
            $table->foreign('receivable_account_id')->references('id')->on('accounts');
            $table->foreign('created_by')->references('id')->on('users');
            $table->index(['contract_number', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rent_incomes');
    }
};