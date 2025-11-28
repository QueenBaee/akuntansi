<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CashflowSeeder extends Seeder
{
    public function run()
    {
        $now = Carbon::now();

        // Daftar akun dalam bentuk nested array
        $accounts = [
            [
                'kode' => 'R',
                'keterangan' => 'PEMASUKAN',
                'children' => [
                    [
                        'kode' => 'R1',
                        'keterangan' => 'PENDAPATAN JASA',
                        'children' => [
                            ['kode' => 'R1-01', 'keterangan' => 'Jasa Maklon'],
                            ['kode' => 'R1-02', 'keterangan' => 'Jasa Manajemen Fee'],
                            ['kode' => 'R1-03', 'keterangan' => 'Jasa Sewa'],
                        ],
                    ],
                    [
                        'kode' => 'R2',
                        'keterangan' => 'PENDAPATAN LAIN-LAIN',
                        'children' => [
                            ['kode' => 'R2-01', 'keterangan' => 'Jasa Giro/Bunga Bank'],
                            ['kode' => 'R2-02', 'keterangan' => 'Bagi Hasil'],
                            ['kode' => 'R2-98', 'keterangan' => 'Hasil Investasi'],
                            ['kode' => 'R2-99', 'keterangan' => 'Penjualan Aset Tetap'],
                        ],
                    ],
                ],
            ],

            // ===============================
            // PENGELUARAN
            // ===============================
            [
                'kode' => 'E',
                'keterangan' => 'PENGELUARAN',
                'children' => [

                    // E1 PRODUKSI
                    [
                        'kode' => 'E1',
                        'keterangan' => 'PRODUKSI',
                        'children' => [
                            ['kode' => 'E1-01', 'keterangan' => 'Upah Mingguan'],
                            ['kode' => 'E1-02', 'keterangan' => 'Cuti Tahunan'],
                            ['kode' => 'E1-03', 'keterangan' => 'Pesangon Produksi'],
                            ['kode' => 'E1-04', 'keterangan' => 'THR Produksi'],
                            ['kode' => 'E1-05', 'keterangan' => 'Bonus Karyawan Produksi'],
                            ['kode' => 'E1-06', 'keterangan' => 'Tunjangan Lain-Lain Karyawan Produksi'],
                            ['kode' => 'E1-07', 'keterangan' => 'Bonus & Insentif Mandor'],
                            ['kode' => 'E1-08', 'keterangan' => 'BTKTL'],
                            ['kode' => 'E1-09', 'keterangan' => 'Cuti Melahirkan'],
                            ['kode' => 'E1-10', 'keterangan' => 'Tunjangan BPJS TK Produksi'],
                            ['kode' => 'E1-11', 'keterangan' => 'Beban BPJS TK Produksi'],
                            ['kode' => 'E1-12', 'keterangan' => 'Tunjangan BPJS Kesehatan Produksi'],
                            ['kode' => 'E1-13', 'keterangan' => 'JPK Produksi'],
                            ['kode' => 'E1-14', 'keterangan' => 'Tunjangan Kesehatan'],
                            ['kode' => 'E1-15', 'keterangan' => 'Konsumsi Pabrik'],
                            ['kode' => 'E1-16', 'keterangan' => 'Natura Pabrik'],
                            ['kode' => 'E1-17', 'keterangan' => 'Seragam Produksi'],
                            ['kode' => 'E1-18', 'keterangan' => 'Suplies Produksi'],
                            ['kode' => 'E1-19', 'keterangan' => 'Beban Perlengkapan Produksi'],
                            ['kode' => 'E1-20', 'keterangan' => 'Kep Produksi Lain2'],
                            ['kode' => 'E1-21', 'keterangan' => 'Solar Genset'],
                            ['kode' => 'E1-22', 'keterangan' => 'Riset & Pengembangan'],
                            ['kode' => 'E1-23', 'keterangan' => 'Hadiah Produksi'],
                            ['kode' => 'E1-24', 'keterangan' => '……..……..……..'],
                        ],
                    ],

                    // E2 PEMASARAN
                    [
                        'kode' => 'E2',
                        'keterangan' => 'PEMASARAN',
                        'children' => [
                            ['kode' => 'E2-01', 'keterangan' => '……..……..……..'],
                            ['kode' => 'E2-02', 'keterangan' => '……..……..……..'],
                        ],
                    ],

                    // E3 UMUM & ADMINISTRASI
                    [
                        'kode' => 'E3',
                        'keterangan' => 'UMUM & ADMINISTRASI',
                        'children' => [
                            ['kode' => 'E3-01', 'keterangan' => 'Gaji Karyawan Bulanan'],
                            ['kode' => 'E3-02', 'keterangan' => 'THR Bulanan'],
                            ['kode' => 'E3-03', 'keterangan' => 'Bonus Karyawan Bulanan'],
                            ['kode' => 'E3-04', 'keterangan' => 'Tunjangan Karyawan Bulanan Lain-Lain'],
                            ['kode' => 'E3-05', 'keterangan' => 'Pesangon Bulanan'],
                            ['kode' => 'E3-06', 'keterangan' => 'Natura Karyawan Bulanan'],
                            ['kode' => 'E3-07', 'keterangan' => 'Gaji Bukan Karyawan'],
                            ['kode' => 'E3-08', 'keterangan' => 'Imbalan Pasca Kerja'],
                            ['kode' => 'E3-09', 'keterangan' => 'Gaji Out Sourcing'],
                            ['kode' => 'E3-10', 'keterangan' => 'Tunjangan BPJS TK Bulanan'],
                            ['kode' => 'E3-11', 'keterangan' => 'Beban BPJS TK Bulanan'],
                            ['kode' => 'E3-12', 'keterangan' => 'Tunjangan BPJS Kesehatan Bulanan'],
                            ['kode' => 'E3-13', 'keterangan' => 'JPK Bulanan'],
                            ['kode' => 'E3-14', 'keterangan' => 'Seragam Kantor'],
                            ['kode' => 'E3-15', 'keterangan' => 'Pelatihan Karyawan Produksi'],
                            ['kode' => 'E3-16', 'keterangan' => 'Hadiah'],
                            ['kode' => 'E3-17', 'keterangan' => 'SMK3'],
                            ['kode' => 'E3-18', 'keterangan' => 'Seragam SMK3'],
                            ['kode' => 'E3-19', 'keterangan' => 'Perlengkapan SMK3'],
                            ['kode' => 'E3-20', 'keterangan' => 'Lain-lain SMK3'],
                            ['kode' => 'E3-21', 'keterangan' => 'Suplies Kantor'],
                            ['kode' => 'E3-22', 'keterangan' => 'Listrik'],
                            ['kode' => 'E3-23', 'keterangan' => 'Telepon'],
                            ['kode' => 'E3-25', 'keterangan' => 'Transport & BBM'],
                            ['kode' => 'E3-26', 'keterangan' => 'Perjalanan Dinas'],
                            ['kode' => 'E3-27', 'keterangan' => 'Rapat & Tamu'],
                            ['kode' => 'E3-28', 'keterangan' => 'Konsumsi Kantor'],
                            ['kode' => 'E3-29', 'keterangan' => 'Pengawalan'],
                            ['kode' => 'E3-30', 'keterangan' => 'Adm Umum lain'],
                            ['kode' => 'E3-24', 'keterangan' => 'Sewa Tanah & Bangunan'],
                            ['kode' => 'E3-31', 'keterangan' => 'Sparepart Pemeliharaan Inventaris'],
                            ['kode' => 'E3-32', 'keterangan' => 'Sparepart Pemeliharaan Kendaraan'],
                            ['kode' => 'E3-33', 'keterangan' => 'Pemeliharaan Inventaris'],
                            ['kode' => 'E3-34', 'keterangan' => 'Material Pemeliharaan Bangunan'],
                            ['kode' => 'E3-35', 'keterangan' => 'Pemeliharaan Bangunan'],
                            ['kode' => 'E3-36', 'keterangan' => 'Asuransi Gedung Dan Kendaraan'],
                            ['kode' => 'E3-37', 'keterangan' => 'Retribusi & Pajak Daerah'],
                            ['kode' => 'E3-38', 'keterangan' => 'Ijin Dan Notaris'],
                            ['kode' => 'E3-39', 'keterangan' => 'Beban Pajak'],
                            ['kode' => 'E3-40', 'keterangan' => 'Beban PPh 21'],
                            ['kode' => 'E3-41', 'keterangan' => 'Beban PPh 23'],
                            ['kode' => 'E3-42', 'keterangan' => 'Beban Fiskal'],
                            ['kode' => 'E3-43', 'keterangan' => 'PPN (K)'],
                            ['kode' => 'E3-44', 'keterangan' => 'Konsultan & Tenaga Ahli'],
                            ['kode' => 'E3-45', 'keterangan' => 'Jasa Pihak Ke 3'],
                            ['kode' => 'E3-46', 'keterangan' => 'Sumbangan & Zakat'],
                            ['kode' => 'E3-47', 'keterangan' => 'CSR'],
                            ['kode' => 'E3-48', 'keterangan' => 'Beban Entertain'],
                            ['kode' => 'E3-49', 'keterangan' => 'House Keeping'],
                            ['kode' => 'E3-50', 'keterangan' => 'Beban Piutang Tak Tertagih'],
                            ['kode' => 'E3-51', 'keterangan' => 'Beban Covid'],
                            ['kode' => 'E3-52', 'keterangan' => 'Administrasi Bank'],
                            ['kode' => 'E3-53', 'keterangan' => '……..……..……..'],
                            ['kode' => 'E3-99', 'keterangan' => 'Beban Usaha Lainnya'],
                        ],
                    ],

                    // E4 PENGELUARAN LAIN-LAIN
                    [
                        'kode' => 'E4',
                        'keterangan' => 'PENGELUARAN LAIN-LAIN',
                        'children' => [
                            ['kode' => 'E4-01', 'keterangan' => 'Pajak Giro & Bank'],
                            ['kode' => 'E4-02', 'keterangan' => '……..……..……..'],
                        ],
                    ],
                ],
            ],

            // ====================================
            // F INVESTASI DAN PENDANAAN
            // ====================================
            [
                'kode' => 'F',
                'keterangan' => 'INVESTASI DAN PENDANAAN',
                'children' => [
                    [
                        'kode' => 'F1',
                        'keterangan' => 'INVESTASI',
                        'children' => [
                            ['kode' => 'F1-01', 'keterangan' => 'Pengadaan Tanah'],
                            ['kode' => 'F1-02', 'keterangan' => 'Pengadaan Bangunan'],
                            ['kode' => 'F1-03', 'keterangan' => 'Pengadaan Mesin & Peralatan'],
                            ['kode' => 'F1-04', 'keterangan' => 'Pengadaan Kendaraaan'],
                            ['kode' => 'F1-05', 'keterangan' => 'Pengadaan Inventaris'],
                            ['kode' => 'F1-06', 'keterangan' => 'Investasi A'],
                            ['kode' => 'F1-07', 'keterangan' => 'Investasi B'],
                            ['kode' => 'F1-08', 'keterangan' => '……..……..……..'],
                        ],
                    ],
                    [
                        'kode' => 'F2',
                        'keterangan' => 'PENDANAAN',
                        'children' => [
                            ['kode' => 'F2-01', 'keterangan' => 'Utang/(Piutang) ke Pemegang Saham'],
                            ['kode' => 'F2-02', 'keterangan' => 'Utang/(Piutang) Pungutan BPJS'],
                            ['kode' => 'F2-03', 'keterangan' => 'Utang/(Piutang) PPh Pasal 21'],
                            ['kode' => 'F2-04', 'keterangan' => 'Utang/(Piutang) Klaim BPJS'],
                            ['kode' => 'F2-05', 'keterangan' => '……..……..……..'],
                            ['kode' => 'F2-90', 'keterangan' => 'Utang/(Piutang) Lain-lain'],
                            ['kode' => 'F2-96', 'keterangan' => 'Deviden'],
                            ['kode' => 'F2-97', 'keterangan' => 'Modal Disetor - Hj. Rosita Aniati, S.H.'],
                            ['kode' => 'F2-98', 'keterangan' => 'Modal Disetor - Hj. Rusti Widayati, S.E.'],
                            ['kode' => 'F2-99', 'keterangan' => 'Modal Disetor - Siti Zahroh'],
                        ],
                    ],
                    ['kode' => 'PB', 'keterangan' => 'Pindah Buku (Harus Nol)'],
                ],
            ],
        ];

        // Proses rekursif untuk insert
        $this->insertAccounts($accounts, null, 1);
    }

    private function insertAccounts(array $items, $parentId, $level)
    {
        foreach ($items as $item) {
            $id = DB::table('cashflows')->insertGetId([
                'kode' => $item['kode'],
                'keterangan' => $item['keterangan'],
                'level' => $level,
                'parent_id' => $parentId,
                'trial_balance_id' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            if (isset($item['children'])) {
                $this->insertAccounts($item['children'], $id, $level + 1);
            }
        }
    }
}
