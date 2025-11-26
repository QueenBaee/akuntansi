<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fixed_assets', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique();
            $table->string('name');
            $table->foreignId('parent_id')->nullable()->constrained('fixed_assets')->onDelete('set null');
            $table->date('acquisition_date');
            $table->decimal('acquisition_price', 15, 2);
            $table->decimal('residual_value', 15, 2)->default(0);
            $table->integer('useful_life_months');
            $table->decimal('depreciation_rate', 5, 2)->nullable();
            $table->foreignId('asset_account_id')->constrained('trial_balances');
            $table->foreignId('accumulated_account_id')->constrained('trial_balances');
            $table->foreignId('expense_account_id')->constrained('trial_balances');
            $table->decimal('accumulated_depreciation', 15, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();

            $table->index(['code', 'is_active']);
            $table->index(['acquisition_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fixed_assets');
    }
};