<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('trial_balances', function (Blueprint $table) {
            $table->enum('tipe_ledger', ['kas', 'bank'])->nullable()->after('level');
        });
    }

    public function down()
    {
        Schema::table('trial_balances', function (Blueprint $table) {
            $table->dropColumn('tipe_ledger');
        });
    }
};