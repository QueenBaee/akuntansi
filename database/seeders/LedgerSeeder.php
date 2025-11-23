<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Ledger;

class LedgerSeeder extends Seeder
{
    public function run()
    {
        // Kas Ledgers
        Ledger::create([
            'nama_ledger' => 'Kas Kecil',
            'kode_ledger' => 'KAS001',
            'tipe_ledger' => 'kas',
            'deskripsi' => 'Kas untuk keperluan operasional harian',
            'is_active' => true
        ]);

        Ledger::create([
            'nama_ledger' => 'Kas Besar',
            'kode_ledger' => 'KAS002',
            'tipe_ledger' => 'kas',
            'deskripsi' => 'Kas untuk transaksi besar',
            'is_active' => true
        ]);

        // Bank Ledgers
        Ledger::create([
            'nama_ledger' => 'Bank BCA',
            'kode_ledger' => 'BANK001',
            'tipe_ledger' => 'bank',
            'deskripsi' => 'Rekening BCA untuk operasional',
            'is_active' => true
        ]);

        Ledger::create([
            'nama_ledger' => 'Bank Mandiri',
            'kode_ledger' => 'BANK002',
            'tipe_ledger' => 'bank',
            'deskripsi' => 'Rekening Mandiri untuk investasi',
            'is_active' => true
        ]);
    }
}