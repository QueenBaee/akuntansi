<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        Schema::table('cashflows', function (Blueprint $table) {
            $table->integer('sort_order')->default(4)->after('level');
        });

        // Update sort_order based on cashflow kode
        DB::table('cashflows')
            ->where('kode', 'like', 'R%')
            ->update(['sort_order' => 1]);

        DB::table('cashflows')
            ->where('kode', 'like', 'E%')
            ->update(['sort_order' => 2]);

        DB::table('cashflows')
            ->where('kode', 'like', 'F%')
            ->update(['sort_order' => 3]);
    }

    public function down()
    {
        Schema::table('cashflows', function (Blueprint $table) {
            $table->dropColumn('sort_order');
        });
    }
};