<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('maklon', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->string('description');
            $table->string('pic')->nullable();
            $table->string('proof_number')->nullable();
            $table->decimal('batang', 15, 2)->default(0);
            $table->decimal('dpp', 15, 2)->default(0);
            $table->decimal('ppn', 15, 2)->default(0);
            $table->decimal('pph23', 15, 2)->default(0);
            $table->boolean('is_posted')->default(false);
            $table->unsignedBigInteger('created_by');
            $table->timestamps();
            
            $table->foreign('created_by')->references('id')->on('users');
            $table->index(['date', 'is_posted']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('maklon');
    }
};