<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        DB::statement("
            UPDATE trial_balances 
            SET sort_order = CASE 
                WHEN UPPER(LEFT(kode, 1)) = 'A' THEN 1
                WHEN UPPER(LEFT(kode, 1)) = 'L' THEN 2
                WHEN UPPER(LEFT(kode, 1)) = 'C' THEN 3
                WHEN UPPER(LEFT(kode, 1)) = 'R' THEN 4
                WHEN UPPER(keterangan) LIKE '%MEMORIAL%' OR UPPER(keterangan) LIKE '%PINDAH BUKU%' THEN 6
                ELSE 5
            END
        ");
    }

    public function down()
    {
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
};