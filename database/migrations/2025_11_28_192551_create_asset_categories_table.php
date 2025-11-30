<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('asset_categories', function (Blueprint $table) {
            $table->id();
            $table->string('kode', 20);
            $table->string('nama');
            $table->string('parent_kode', 20)->nullable();
            $table->integer('level')->default(1);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index(['parent_kode', 'level']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asset_categories');
    }
};