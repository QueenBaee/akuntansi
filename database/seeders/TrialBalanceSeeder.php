<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TrialBalanceSeeder extends Seeder
{
    public function run()
    {
        $data = [

            // ================================
            // ASET
            // ================================
            [
                'kode' => 'A',
                'keterangan' => 'ASET',
                'children' => [
                    [
                        'kode' => 'A1',
                        'keterangan' => 'ASET LANCAR',
                        'children' => [
                            [
                                'kode' => 'A11',
                                'keterangan' => 'Kas & Setara Kas',
                                'children' => [
                                    ['kode' => 'A11-01', 'keterangan' => 'Kas Pabrik'],
                                    ['kode' => 'A11-02', 'keterangan' => 'Kas Tunai 2'],
                                    ['kode' => 'A11-21', 'keterangan' => 'BNI - Giro - 0053984843'],
                                    ['kode' => 'A11-22', 'keterangan' => 'BCA - Giro - 1993234234'],
                                    ['kode' => 'A11-23', 'keterangan' => 'BNI - Tab - 0354707039'],
                                    ['kode' => 'A11-24', 'keterangan' => 'BNI - Tab - 0533170487'],
                                    ['kode' => 'A11-25', 'keterangan' => 'Bank 5'],
                                    ['kode' => 'A11-51', 'keterangan' => 'Deposito (<3 Bln)'],
                                ],
                            ],

                            [
                                'kode' => 'A12',
                                'keterangan' => 'Piutang Usaha',
                                'children' => [
                                    ['kode' => 'A12-01', 'keterangan' => 'PU - HM Sampoerna Tbk'],
                                    ['kode' => 'A12-02', 'keterangan' => 'PU - Si B'],
                                    ['kode' => 'A12-03', 'keterangan' => 'PU - Si C'],
                                ],
                            ],

                            [
                                'kode' => 'A13',
                                'keterangan' => 'Piutang Lain-lain',
                                'children' => [
                                    ['kode' => 'A13-01', 'keterangan' => 'PL - Pemegang Saham'],
                                    ['kode' => 'A13-02', 'keterangan' => 'PL - Pungutan BPJS'],
                                    ['kode' => 'A13-03', 'keterangan' => 'PL - PPh Pasal 21'],
                                    ['kode' => 'A13-98', 'keterangan' => 'PL - Lainnya'],
                                    ['kode' => 'A13-99', 'keterangan' => 'Piutang/(Hutang) - Harus Nol'],
                                ],
                            ],

                            [
                                'kode' => 'A14',
                                'keterangan' => 'Investasi Jangka Pendek',
                                'children' => [
                                    ['kode' => 'A14-01', 'keterangan' => 'Deposito A (3 s.d 12 bln)'],
                                    ['kode' => 'A14-02', 'keterangan' => 'Deposito B (3 s.d 12 bln)'],
                                    ['kode' => 'A14-99', 'keterangan' => 'Investasi Semua (Saldo Harus Nol)'],
                                ],
                            ],

                            [
                                'kode' => 'A15',
                                'keterangan' => 'Persediaan',
                                'children' => [
                                    ['kode' => 'A15-01', 'keterangan' => 'Barang Perlengkapan'],
                                    ['kode' => 'A15-02', 'keterangan' => 'Stock B'],
                                    ['kode' => 'A15-99', 'keterangan' => '……………'],
                                ],
                            ],

                            [
                                'kode' => 'A16',
                                'keterangan' => 'Biaya Dibayar Di muka',
                                'children' => [
                                    ['kode' => 'A16-01', 'keterangan' => 'BDM - Sewa'],
                                    ['kode' => 'A16-02', 'keterangan' => 'BDM - Rupa-rupa Biaya'],
                                ],
                            ],

                            [
                                'kode' => 'A17',
                                'keterangan' => 'Uang Muka Pajak',
                                'children' => [
                                    ['kode' => 'A17-01', 'keterangan' => 'UMP - PPh Pasal 21'],
                                    ['kode' => 'A17-02', 'keterangan' => 'UMP - PPh Pasal 23'],
                                    ['kode' => 'A17-03', 'keterangan' => 'UMP - PPh Pasal 25'],
                                    ['kode' => 'A17-04', 'keterangan' => 'UMP - PPh Pasal 4(2)'],
                                    ['kode' => 'A17-11', 'keterangan' => 'UMP - PPN Masukan'],
                                ],
                            ],

                            [
                                'kode' => 'A18',
                                'keterangan' => 'Aset Lancar Lainnya',
                                'children' => [
                                    ['kode' => 'A18-01', 'keterangan' => 'Aset Lancar A'],
                                    ['kode' => 'A18-02', 'keterangan' => 'Aset Lancar B'],
                                ],
                            ],
                        ],
                    ],

                    // ASET TIDAK LANCAR
                    [
                        'kode' => 'A2',
                        'keterangan' => 'ASET TIDAK LANCAR',
                        'children' => [
                            [
                                'kode' => 'A21',
                                'keterangan' => 'Piutang Lain-lain - Jangka Panjang',
                                'children' => [
                                    ['kode' => 'A21-01', 'keterangan' => 'Debitor A'],
                                    ['kode' => 'A21-02', 'keterangan' => 'Debitor B'],
                                ],
                            ],

                            [
                                'kode' => 'A22',
                                'keterangan' => 'Investasi Jangka Panjang',
                                'children' => [
                                    ['kode' => 'A22-01', 'keterangan' => 'Deposito A (> 12 bln)'],
                                    ['kode' => 'A22-02', 'keterangan' => 'Deposito B (> 12 bln)'],
                                ],
                            ],

                            [
                                'kode' => 'A23',
                                'keterangan' => 'Harga Perolehan Aset Tetap',
                                'children' => [
                                    ['kode' => 'A23-01', 'keterangan' => 'HP - Tanah'],
                                    ['kode' => 'A23-02', 'keterangan' => 'HP - Bangunan'],
                                    ['kode' => 'A23-03', 'keterangan' => 'HP - Kendaraaan'],
                                    ['kode' => 'A23-04', 'keterangan' => 'HP - Mesin & Peralatan'],
                                    ['kode' => 'A23-05', 'keterangan' => 'HP - Inventaris'],
                                    ['kode' => 'A23-99', 'keterangan' => 'Aset dalam Penyelesaian'],
                                ],
                            ],

                            [
                                'kode' => 'A24',
                                'keterangan' => 'Akumulasi Penyusutan Aset Tetap',
                                'children' => [
                                    ['kode' => 'A24-01', 'keterangan' => 'AP - Bangunan'],
                                    ['kode' => 'A24-02', 'keterangan' => 'AP - Kendaraaan'],
                                    ['kode' => 'A24-03', 'keterangan' => 'AP - Mesin & Peralatan'],
                                    ['kode' => 'A24-04', 'keterangan' => 'AP - Inventaris'],
                                ],
                            ],

                            [
                                'kode' => 'A25',
                                'keterangan' => 'Aset Tidak Berwujud',
                                'children' => [
                                    ['kode' => 'A25-01', 'keterangan' => 'Merk'],
                                    ['kode' => 'A25-02', 'keterangan' => 'Software'],
                                ],
                            ],

                            [
                                'kode' => 'A26',
                                'keterangan' => 'Aset Tidak Lancar Lainnya',
                                'children' => [
                                    ['kode' => 'A26-01', 'keterangan' => 'BNI - Tab - 1753560912'],
                                    ['kode' => 'A26-02', 'keterangan' => '…………….'],
                                ],
                            ],
                        ],
                    ],
                ],
            ],

            // ================================
            // KEWAJIBAN
            // ================================
            [
                'kode' => 'L',
                'keterangan' => 'KEWAJIBAN',
                'children' => [
                    [
                        'kode' => 'L1',
                        'keterangan' => 'KEWAJIBAN JANGKA PENDEK',
                        'children' => [
                            [
                                'kode' => 'L11',
                                'keterangan' => 'Utang Usaha',
                                'children' => [
                                    ['kode' => 'L11-01', 'keterangan' => 'Kreditor1'],
                                    ['kode' => 'L11-02', 'keterangan' => 'Kreditor2'],
                                    ['kode' => 'L11-03', 'keterangan' => 'Kreditor3'],
                                    ['kode' => 'L11-99', 'keterangan' => '…...............'],
                                ],
                            ],

                            [
                                'kode' => 'L12',
                                'keterangan' => 'Utang Lain-lain',
                                'children' => [
                                    ['kode' => 'L12-01', 'keterangan' => 'Utang Deviden'],
                                    ['kode' => 'L12-02', 'keterangan' => 'Utang ke Kreditor B'],
                                ],
                            ],

                            [
                                'kode' => 'L13',
                                'keterangan' => 'Biaya yang Harus Dibayar',
                                'children' => [
                                    ['kode' => 'L13-01', 'keterangan' => 'BHD - Gaji & Upah'],
                                    ['kode' => 'L13-02', 'keterangan' => 'BHD - Listrik & Air'],
                                    ['kode' => 'L13-03', 'keterangan' => 'BHD - Telpon & Internet'],
                                    ['kode' => 'L13-04', 'keterangan' => 'BHD - Klaim BPJS'],
                                    ['kode' => 'L13-05', 'keterangan' => 'BHD - BPJS TK'],
                                    ['kode' => 'L13-06', 'keterangan' => 'BHD - Sewa'],
                                    ['kode' => 'L13-99', 'keterangan' => 'BHD - Beban Lainnya'],
                                ],
                            ],

                            [
                                'kode' => 'L14',
                                'keterangan' => 'Utang Pajak',
                                'children' => [
                                    ['kode' => 'L14-01', 'keterangan' => 'Utang Pajak PPh Pasal 21'],
                                    ['kode' => 'L14-02', 'keterangan' => 'Utang Pajak PPh Pasal 22'],
                                    ['kode' => 'L14-03', 'keterangan' => 'Utang Pajak PPh Pasal 23'],
                                    ['kode' => 'L14-04', 'keterangan' => 'Utang Pajak PPh Pasal 25'],
                                    ['kode' => 'L14-05', 'keterangan' => 'Utang Pajak PPh Pasal 29'],
                                    ['kode' => 'L14-11', 'keterangan' => 'Utang Pajak PPh Pasal 4(2)'],
                                    ['kode' => 'L14-12', 'keterangan' => 'Utang Pajak PPN - Keluaran'],
                                ],
                            ],

                            [
                                'kode' => 'L15',
                                'keterangan' => 'Uang Muka Pendapatan',
                                'children' => [
                                    ['kode' => 'L15-01', 'keterangan' => 'UMP - Dana Pendidikan'],
                                    ['kode' => 'L15-02', 'keterangan' => 'UMP - Dana Manasik'],
                                    ['kode' => 'L15-99', 'keterangan' => 'UMP - Lainnya'],
                                ],
                            ],

                            [
                                'kode' => 'L16',
                                'keterangan' => 'Pinjaman Jangka Pendek',
                                'children' => [
                                    ['kode' => 'L16-01', 'keterangan' => 'PJ Pendek - Bank'],
                                    ['kode' => 'L16-02', 'keterangan' => 'PJ Pendek - Sewa Guna Usaha'],
                                ],
                            ],

                            [
                                'kode' => 'L17',
                                'keterangan' => 'Kewajiban Imbalan Pasca Kerja',
                                'children' => [
                                    ['kode' => 'L17-01', 'keterangan' => 'Kewajiban Imbalan Pasca Kerja'],
                                    ['kode' => 'L17-02', 'keterangan' => '……………………..'],
                                ],
                            ],
                        ],
                    ],

                    // KEWAJIBAN JANGKA PANJANG
                    [
                        'kode' => 'L2',
                        'keterangan' => 'KEWAJIBAN JANGKA PANJANG',
                        'children' => [
                            [
                                'kode' => 'L21',
                                'keterangan' => 'Utang Usaha - Jk. Panjang',
                                'children' => [
                                    ['kode' => 'L21-01', 'keterangan' => 'Kreditor A'],
                                    ['kode' => 'L21-02', 'keterangan' => '……………………………..'],
                                ],
                            ],

                            [
                                'kode' => 'L22',
                                'keterangan' => 'Utang Lain-lain - Jk. Panjang',
                                'children' => [
                                    ['kode' => 'L22-01', 'keterangan' => 'Kreditor A'],
                                    ['kode' => 'L22-02', 'keterangan' => 'Kreditor B'],
                                ],
                            ],

                            [
                                'kode' => 'L23',
                                'keterangan' => 'Pinjaman Jangka Panjang',
                                'children' => [
                                    ['kode' => 'L23-01', 'keterangan' => 'Bank - Jk. Panjang'],
                                    ['kode' => 'L23-02', 'keterangan' => 'Sewa Guna Usaha - Jk. Panjang'],
                                ],
                            ],

                            [
                                'kode' => 'L24',
                                'keterangan' => 'Kewajiban Imbalan Pasca Kerja',
                                'children' => [
                                    ['kode' => 'L24-01', 'keterangan' => 'Kewajiban Imbalan Pasca Kerja - Jk. Panjang'],
                                    ['kode' => 'L24-02', 'keterangan' => '……………………………..'],
                                ],
                            ],

                            [
                                'kode' => 'L25',
                                'keterangan' => 'Kewajiban Jangka Panjang Lainnya',
                                'children' => [
                                    ['kode' => 'L25-01', 'keterangan' => 'Kewajiban Jangka Panjang Lainnya'],
                                    ['kode' => 'L25-02', 'keterangan' => '……………………………..'],
                                ],
                            ],
                        ],
                    ],
                ],
            ],

            // ================================
            // EKUITAS
            // ================================
            [
                'kode' => 'C',
                'keterangan' => 'EKUITAS',
                'children' => [
                    [
                        'kode' => 'C1',
                        'keterangan' => 'MODAL',
                        'children' => [
                            [
                                'kode' => 'C11',
                                'keterangan' => 'Modal Disetor',
                                'children' => [
                                    ['kode' => 'C11-01', 'keterangan' => 'Modal Disetor - Hj. Rosita Aniati, S.H.'],
                                    ['kode' => 'C11-02', 'keterangan' => 'Modal Disetor - Hj. Rusti Widayati, S.E.'],
                                    ['kode' => 'C11-03', 'keterangan' => 'Modal Disetor - Siti Zahroh'],
                                ],
                            ],
                        ],
                    ],

                    [
                        'kode' => 'C2',
                        'keterangan' => 'SALDO (LABA)/RUGI',
                        'children' => [
                            [
                                'kode' => 'C21',
                                'keterangan' => 'Saldo (Laba)/Rugi',
                                'children' => [
                                    ['kode' => 'C21-01', 'keterangan' => '(Laba)/Rugi Ditahan'],
                                    ['kode' => 'C21-02', 'keterangan' => 'Deviden'],
                                    ['kode' => 'C21-99', 'keterangan' => '(Laba)/Rugi Berjalan'],
                                ],
                            ],
                        ],
                    ],
                ],
            ],

            // ================================
            // PENDAPATAN
            // ================================
            [
                'kode' => 'R',
                'keterangan' => 'PENDAPATAN',
                'children' => [
                    [
                        'kode' => 'R1',
                        'keterangan' => 'PENDAPATAN',
                        'children' => [
                            [
                                'kode' => 'R11',
                                'keterangan' => 'Pendapatan',
                                'children' => [
                                    ['kode' => 'R11-01', 'keterangan' => 'Jasa Maklon'],
                                    ['kode' => 'R11-02', 'keterangan' => 'Jasa Sewa'],
                                    ['kode' => 'R11-03', 'keterangan' => 'Jasa Manajemen Fee'],
                                ],
                            ],
                        ],
                    ],

                    [
                        'kode' => 'R2',
                        'keterangan' => 'PENDAPATAN LAIN-LAIN',
                        'children' => [
                            [
                                'kode' => 'R21',
                                'keterangan' => 'Pendapatan Lain-Lain',
                                'children' => [
                                    ['kode' => 'R21-01', 'keterangan' => 'Jasa Giro & Bank'],
                                    ['kode' => 'R21-02', 'keterangan' => '…................'],
                                    ['kode' => 'R21-98', 'keterangan' => 'Laba Investasi'],
                                    ['kode' => 'R21-99', 'keterangan' => 'Laba Penjualan Aset Tetap'],
                                ],
                            ],
                        ],
                    ],
                ],
            ],

            // ================================
            // BEBAN
            // ================================
            [
                'kode' => 'E',
                'keterangan' => 'BEBAN',
                'children' => [
                    [
                        'kode' => 'E1',
                        'keterangan' => 'BEBAN PRODUKSI',
                        'children' => [
                            [
                                'kode' => 'E11',
                                'keterangan' => 'Beban Produksi',
                                'children' => [
                                    ['kode' => 'E11-01', 'keterangan' => 'Gaji, Upah & Tunjangan'],
                                    ['kode' => 'E11-02', 'keterangan' => 'Kesejahteraan & BPJS'],
                                    ['kode' => 'E11-03', 'keterangan' => 'Natura & Fasilitas Pabrik'],
                                    ['kode' => 'E11-04', 'keterangan' => 'Bahan & Perlengkapan'],
                                    ['kode' => 'E11-05', 'keterangan' => 'Energi & Utilitas'],
                                    ['kode' => 'E11-06', 'keterangan' => 'Riset & Pengembangan'],
                                    ['kode' => 'E11-07', 'keterangan' => '…................'],
                                    ['kode' => 'E11-99', 'keterangan' => 'Beban Produksi Lainnya'],
                                ],
                            ],
                        ],
                    ],

                    [
                        'kode' => 'E2',
                        'keterangan' => 'BEBAN USAHA',
                        'children' => [
                            [
                                'kode' => 'E21',
                                'keterangan' => 'Pemasaran',
                                'children' => [
                                    ['kode' => 'E21-01', 'keterangan' => 'Gaji, Upah & Tunjangan'],
                                    ['kode' => 'E21-02', 'keterangan' => '…............'],
                                ],
                            ],

                            [
                                'kode' => 'E22',
                                'keterangan' => 'Administrasi & Umum',
                                'children' => [
                                    ['kode' => 'E22-01', 'keterangan' => 'Gaji, Upah & Tunjangan'],
                                    ['kode' => 'E22-02', 'keterangan' => 'Kesejahteraan & BPJS'],
                                    ['kode' => 'E22-03', 'keterangan' => 'Pelatihan & Pengembangan'],
                                    ['kode' => 'E22-04', 'keterangan' => 'SMK3'],
                                    ['kode' => 'E22-05', 'keterangan' => 'Operasional Kantor'],
                                    ['kode' => 'E22-06', 'keterangan' => 'Sewa'],
                                    ['kode' => 'E22-07', 'keterangan' => 'Pemeliharaan'],
                                    ['kode' => 'E22-08', 'keterangan' => 'Asuransi, Pajak & Perijinan'],
                                    ['kode' => 'E22-09', 'keterangan' => 'Konsultan & Jasa Pihak Ketiga'],
                                    ['kode' => 'E22-10', 'keterangan' => 'Sosial & CSR'],
                                    ['kode' => 'E22-11', 'keterangan' => '…....................'],
                                    ['kode' => 'E22-89', 'keterangan' => 'Beban Usaha Lainnya'],
                                    ['kode' => 'E22-96', 'keterangan' => 'Penyusutan Bangunan'],
                                    ['kode' => 'E22-97', 'keterangan' => 'Penyusutan Kendaraan'],
                                    ['kode' => 'E22-98', 'keterangan' => 'Penyusutan Mesin & Peralatan'],
                                    ['kode' => 'E22-99', 'keterangan' => 'Penyusutan Inventaris'],
                                ],
                            ],
                        ],
                    ],

                    [
                        'kode' => 'E3',
                        'keterangan' => 'BEBAN LAIN-LAIN',
                        'children' => [
                            [
                                'kode' => 'E31',
                                'keterangan' => 'Beban Lain-lain',
                                'children' => [
                                    ['kode' => 'E31-01', 'keterangan' => 'Pajak Bunga & Jasa Giro'],
                                    ['kode' => 'E31-02', 'keterangan' => 'Bunga Pinjaman'],
                                    ['kode' => 'E31-03', 'keterangan' => 'Rugi Penjualan Aset Tetap'],
                                ],
                            ],
                        ],
                    ],

                    [
                        'kode' => 'E9',
                        'keterangan' => 'BEBAN PAJAK PENGHASILAN',
                        'children' => [
                            [
                                'kode' => 'E91',
                                'keterangan' => 'Beban Pajak Penghasilan',
                                'children' => [
                                    ['kode' => 'E91-01', 'keterangan' => 'Beban Pajak Penghasilan'],
                                ],
                            ],
                        ],
                    ],
                ],
            ],

            // ================================
            // PINDAH BUKU
            // ================================
            [
                'kode' => 'PB',
                'keterangan' => 'Pindah Buku',
            ],
        ];

        $this->insertRecursive($data, null, 1);
    }

    private function insertRecursive($items, $parentId, $level)
    {
        $sortOrder = 1;
        foreach ($items as $item) {
            $id = DB::table('trial_balances')->insertGetId([
                'kode'          => $item['kode'],
                'keterangan'    => $item['keterangan'],
                'parent_id'     => $parentId,
                'level'         => $level,
                'sort_order'    => $sortOrder,
                'tahun_2024'    => null,
                'created_at'    => now(),
                'updated_at'    => now(),
            ]);

            if (isset($item['children'])) {
                $this->insertRecursive($item['children'], $id, $level + 1);
            }
            
            $sortOrder++;
        }
    }
}
