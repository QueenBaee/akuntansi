<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('journals', function (Blueprint $table) {
            $table->string('pic')->nullable()->after('description'); // Person in Charge
            $table->string('proof_number')->nullable()->after('pic'); // No. Bukti
            $table->decimal('cash_in', 15, 2)->default(0)->after('proof_number'); // Kas Masuk
            $table->decimal('cash_out', 15, 2)->default(0)->after('cash_in'); // Kas Keluar
            $table->unsignedBigInteger('debit_account_id')->nullable()->after('cash_out'); // Akun Debit
            $table->unsignedBigInteger('credit_account_id')->nullable()->after('debit_account_id'); // Akun Kredit
            $table->unsignedBigInteger('cashflow_category_id')->nullable()->after('credit_account_id'); // Cashflow
            $table->decimal('balance', 15, 2)->default(0)->after('cashflow_category_id'); // Saldo
            
            // Add foreign key constraints
            $table->foreign('debit_account_id')->references('id')->on('accounts')->onDelete('set null');
            $table->foreign('credit_account_id')->references('id')->on('accounts')->onDelete('set null');
            $table->foreign('cashflow_category_id')->references('id')->on('cashflow_categories')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('journals', function (Blueprint $table) {
            $table->dropForeign(['debit_account_id']);
            $table->dropForeign(['credit_account_id']);
            $table->dropForeign(['cashflow_category_id']);
            
            $table->dropColumn([
                'pic',
                'proof_number',
                'cash_in',
                'cash_out',
                'debit_account_id',
                'credit_account_id',
                'cashflow_category_id',
                'balance'
            ]);
        });
    }
};