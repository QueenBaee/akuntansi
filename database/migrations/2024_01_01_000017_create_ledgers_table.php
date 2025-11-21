<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('ledgers', function (Blueprint $table) {
            $table->id();
            $table->string('kode', 20)->unique();
            $table->string('nama', 100);
            $table->enum('tipe_akun', ['aset', 'kewajiban', 'ekuitas', 'pendapatan', 'beban']);
            $table->string('grup', 50)->nullable();
            $table->enum('saldo_normal', ['debit', 'kredit']);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('ledgers');
    }
};