<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // Update sort_order based on first letter of kode
        DB::statement("
            UPDATE trial_balances 
            SET sort_order = CASE 
                WHEN UPPER(LEFT(kode, 1)) = 'A' THEN 1
                WHEN UPPER(LEFT(kode, 1)) = 'L' THEN 2
                WHEN UPPER(LEFT(kode, 1)) = 'C' THEN 3
                WHEN UPPER(LEFT(kode, 1)) = 'R' THEN 4
                ELSE 5
            END
        ");
    }

    public function down()
    {
        // Reset sort_order to default
        DB::table('trial_balances')->update(['sort_order' => 0]);
    }
};