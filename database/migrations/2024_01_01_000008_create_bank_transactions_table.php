<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bank_transactions', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->string('number', 50)->unique();
            $table->enum('type', ['in', 'out']);
            $table->unsignedBigInteger('bank_account_id');
            $table->unsignedBigInteger('contra_account_id');
            $table->unsignedBigInteger('cashflow_category_id');
            $table->decimal('amount', 15, 2);
            $table->string('description');
            $table->string('reference')->nullable();
            $table->unsignedBigInteger('journal_id')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->timestamps();
            
            $table->foreign('bank_account_id')->references('id')->on('accounts');
            $table->foreign('contra_account_id')->references('id')->on('accounts');
            $table->foreign('cashflow_category_id')->references('id')->on('cashflow_categories');
            $table->foreign('journal_id')->references('id')->on('journals')->onDelete('set null');
            $table->foreign('created_by')->references('id')->on('users');
            $table->index(['date', 'type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bank_transactions');
    }
};