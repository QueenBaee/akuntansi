<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('trial_balances', function (Blueprint $table) {
            if (!Schema::hasColumn('trial_balances', 'is_kas_bank')) {
                $table->enum('is_kas_bank', ['kas', 'bank'])->nullable()->after('sort_order');
            }
        });
    }

    public function down()
    {
        Schema::table('trial_balances', function (Blueprint $table) {
            if (Schema::hasColumn('trial_balances', 'is_kas_bank')) {
                $table->dropColumn('is_kas_bank');
            }
        });
    }
};