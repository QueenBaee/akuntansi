<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('cashflows', function (Blueprint $table) {

        // hapus kolom kategori
        if (Schema::hasColumn('cashflows', 'kategori')) {
            $table->dropColumn('kategori');
        }

        // pastikan kolom trial_balance_id nullable (boleh kosong)
        $table->unsignedBigInteger('trial_balance_id')->nullable()->change();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('cashflows', function (Blueprint $table) {
        $table->string('kategori')->nullable();

        $table->unsignedBigInteger('trial_balance_id')->nullable(false)->change();
        });
    }
};
