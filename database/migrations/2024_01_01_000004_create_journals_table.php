<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('journals', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->string('number', 50)->unique();
            $table->string('reference')->nullable();
            $table->string('description');
            $table->enum('source_module', ['manual', 'cash', 'bank', 'asset', 'depreciation', 'asset_depreciation', 'maklon', 'rent_income', 'rent_expense']);
            $table->unsignedBigInteger('source_id')->nullable();
            $table->decimal('total_debit', 15, 2);
            $table->decimal('total_credit', 15, 2);
            $table->boolean('is_posted')->default(false);
            $table->unsignedBigInteger('created_by');
            $table->timestamps();
            
            $table->index(['date', 'number']);
            $table->index(['source_module', 'source_id']);
            $table->foreign('created_by')->references('id')->on('users');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('journals');
    }
};