<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('ledgers', function (Blueprint $table) {
            $table->unsignedBigInteger('trial_balance_id')->nullable()->after('id');

            $table->foreign('trial_balance_id')
                ->references('id')
                ->on('trial_balances')
                ->onDelete('set null')
                ->onUpdate('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('ledgers', function (Blueprint $table) {
            $table->dropForeign(['trial_balance_id']);
            $table->dropColumn('trial_balance_id');
        });
    }
};
