<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('maklon_transactions', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->string('number', 50)->unique();
            $table->string('customer_name');
            $table->string('product_name');
            $table->decimal('quantity', 10, 2);
            $table->string('unit');
            $table->decimal('unit_cost', 15, 2);
            $table->decimal('total_cost', 15, 2);
            $table->unsignedBigInteger('expense_account_id');
            $table->unsignedBigInteger('allocation_account_id');
            $table->string('description');
            $table->unsignedBigInteger('journal_id')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->timestamps();
            
            $table->foreign('expense_account_id')->references('id')->on('accounts');
            $table->foreign('allocation_account_id')->references('id')->on('accounts');
            $table->foreign('journal_id')->references('id')->on('journals')->onDelete('set null');
            $table->foreign('created_by')->references('id')->on('users');
            $table->index(['date', 'number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('maklon_transactions');
    }
};