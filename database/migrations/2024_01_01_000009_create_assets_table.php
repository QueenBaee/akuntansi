<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assets', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique();
            $table->string('name');
            $table->date('purchase_date');
            $table->decimal('purchase_price', 15, 2);
            $table->decimal('residual_value', 15, 2)->default(0);
            $table->integer('useful_life_months');
            $table->enum('depreciation_method', ['straight_line'])->default('straight_line');
            $table->unsignedBigInteger('asset_account_id');
            $table->unsignedBigInteger('depreciation_account_id');
            $table->unsignedBigInteger('expense_account_id');
            $table->decimal('accumulated_depreciation', 15, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->unsignedBigInteger('created_by');
            $table->timestamps();
            
            $table->foreign('asset_account_id')->references('id')->on('accounts');
            $table->foreign('depreciation_account_id')->references('id')->on('accounts');
            $table->foreign('expense_account_id')->references('id')->on('accounts');
            $table->foreign('created_by')->references('id')->on('users');
            $table->index(['code', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assets');
    }
};