<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rent_expense_schedules', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('rent_expense_id');
            $table->date('period_date');
            $table->decimal('amount', 15, 2);
            $table->unsignedBigInteger('journal_id')->nullable();
            $table->boolean('is_posted')->default(false);
            $table->timestamps();
            
            $table->foreign('rent_expense_id')->references('id')->on('rent_expenses')->onDelete('cascade');
            $table->foreign('journal_id')->references('id')->on('journals')->onDelete('set null');
            $table->unique(['rent_expense_id', 'period_date']);
            $table->index(['period_date', 'is_posted']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rent_expense_schedules');
    }
};