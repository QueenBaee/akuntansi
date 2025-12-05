<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cashflows', function (Blueprint $table) {
            if (!Schema::hasColumn('cashflows', 'parent_id')) {
                $table->unsignedBigInteger('parent_id')->nullable()->after('keterangan');
            }
            if (!Schema::hasColumn('cashflows', 'level')) {
                $table->integer('level')->default(0)->after('parent_id');
            }
        });
        
        // Add foreign key in separate schema call to avoid conflicts
        if (Schema::hasColumn('cashflows', 'parent_id')) {
            try {
                Schema::table('cashflows', function (Blueprint $table) {
                    $table->foreign('parent_id')->references('id')->on('cashflows')->onDelete('cascade');
                });
            } catch (\Exception $e) {
                // Foreign key might already exist, ignore the error
            }
        }
    }

    public function down(): void
    {
        Schema::table('cashflows', function (Blueprint $table) {
            $table->dropForeign(['parent_id']);
            $table->dropColumn(['parent_id', 'level']);
        });
    }
};