<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('depreciations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('asset_id');
            $table->date('period_date');
            $table->decimal('depreciation_amount', 15, 2);
            $table->decimal('accumulated_depreciation', 15, 2);
            $table->decimal('book_value', 15, 2);
            $table->unsignedBigInteger('journal_id')->nullable();
            $table->boolean('is_posted')->default(false);
            $table->timestamps();
            
            $table->foreign('asset_id')->references('id')->on('assets')->onDelete('cascade');
            $table->foreign('journal_id')->references('id')->on('journals')->onDelete('set null');
            $table->unique(['asset_id', 'period_date']);
            $table->index(['period_date', 'is_posted']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('depreciations');
    }
};