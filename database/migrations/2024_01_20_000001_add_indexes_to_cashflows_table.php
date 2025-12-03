<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('cashflows', function (Blueprint $table) {
            $table->index('parent_id');
            $table->index('trial_balance_id');
            $table->index('level');
            $table->index(['kode', 'keterangan']);
        });
    }

    public function down()
    {
        Schema::table('cashflows', function (Blueprint $table) {
            $table->dropIndex(['parent_id']);
            $table->dropIndex(['trial_balance_id']);
            $table->dropIndex(['level']);
            $table->dropIndex(['kode', 'keterangan']);
        });
    }
};