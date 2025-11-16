<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('journal_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('journal_id');
            $table->unsignedBigInteger('account_id');
            $table->string('description');
            $table->decimal('debit', 15, 2)->default(0);
            $table->decimal('credit', 15, 2)->default(0);
            $table->timestamps();
            
            $table->foreign('journal_id')->references('id')->on('journals')->onDelete('cascade');
            $table->foreign('account_id')->references('id')->on('accounts');
            $table->index(['journal_id', 'account_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('journal_details');
    }
};