<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('accounts', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20)->unique();
            $table->string('name');
            $table->enum('type', ['asset', 'liability', 'equity', 'revenue', 'expense']);
            $table->enum('category', ['current_asset', 'fixed_asset', 'current_liability', 'long_term_liability', 'equity', 'operating_revenue', 'other_revenue', 'operating_expense', 'other_expense']);
            $table->decimal('opening_balance', 15, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->timestamps();
            
            $table->index(['code', 'is_active']);
            $table->index(['type', 'category']);
            $table->foreign('parent_id')->references('id')->on('accounts')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accounts');
    }
};