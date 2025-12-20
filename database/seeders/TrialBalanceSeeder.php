<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TrialBalanceSeeder extends Seeder
{
    public function run()
    {
        $rows = [

            // =============================
            // ASET
            // =============================
            ['A', 'ASET', '-'],
            ['A1', 'ASET LANCAR', '-'],
            ['A11', 'Kas & Setara Kas', '-'],
            ['A11-01', 'Kas Pabrik', '2,747,632,592'],
            ['A11-02', 'Kas Tunai 2', '-'],
            ['A11-21', 'BNI - Giro - 0053984843', '20,632,776,125'],
            ['A11-22', 'BCA - Giro - 1993234234', '50,671,422'],
            ['A11-23', 'BNI - Tab - 0354707039', '2,548,000,615'],
            ['A11-24', 'BNI - Tab - 0533170487', '-'],
            ['A11-25', 'Bank 5', '-'],
            ['A11-51', 'Deposito (<3 Bln)', '-'],

            ['A12', 'Piutang Usaha', '-'],
            ['A12-01', 'PU - HM Sampoerna Tbk', '1,052,472,867'],
            ['A12-02', 'PU - Si B', '-'],
            ['A12-03', 'PU - Si C', '-'],

            ['A13', 'Piutang Lain-lain', '-'],
            ['A13-01', 'PL - Pemegang Saham', '-'],
            ['A13-02', 'PL - Pungutan BPJS', '-'],
            ['A13-03', 'PL - PPh Pasal 21', '155,195,306'],
            ['A13-98', 'PL - Lainnya', '-'],
            ['A13-99', 'Piutang/(Hutang) - Harus Nol', '-'],

            ['A14', 'Investasi Jangka Pendek', '-'],
            ['A14-01', 'Deposito A (3 s.d 12 bln)', '-'],
            ['A14-02', 'Deposito B (3 s.d 12 bln)', '-'],
            ['A14-99', 'Investasi Semua (Saldo Harus Nol)', '-'],

            ['A15', 'Persediaan', '-'],
            ['A15-01', 'Barang Perlengkapan', '-'],
            ['A15-02', 'Stock B', '-'],
            ['A15-99', '……………', '-'],

            ['A16', 'Biaya Dibayar Di muka', '-'],
            ['A16-01', 'BDM - Sewa', '-'],
            ['A16-02', 'BDM - Rupa-rupa Biaya', '198,750,001'],

            ['A17', 'Uang Muka Pajak', '-'],
            ['A17-01', 'UMP - PPh Pasal 21', '22,083,332'],
            ['A17-02', 'UMP - PPh Pasal 23', '231,128'],
            ['A17-03', 'UMP - PPh Pasal 25', '-'],
            ['A17-04', 'UMP - PPh Pasal 4(2)', '-'],
            ['A17-11', 'UMP - PPN Masukan', '-'],

            ['A18', 'Aset Lancar Lainnya', '-'],
            ['A18-01', 'Aset Lancar A', '-'],
            ['A18-02', 'Aset Lancar B', '-'],

            ['A2', 'ASET TIDAK LANCAR', '-'],

            ['A21', 'Piutang Lain-lain - Jangka Panjang', '-'],
            ['A21-01', 'Debitor A', '-'],
            ['A21-02', 'Debitor B', '-'],

            ['A22', 'Investasi Jangka Panjang', '-'],
            ['A22-01', 'Deposito A (> 12 bln)', '-'],
            ['A22-02', 'Deposito B (> 12 bln)', '-'],

            ['A23', 'Harga Perolehan Aset Tetap', '-'],
            ['A23-01', 'HP - Tanah', '498,817,500'],
            ['A23-02', 'HP - Bangunan', '2,780,478,238'],
            ['A23-03', 'HP - Kendaraaan', '3,614,628,807'],
            ['A23-04', 'HP - Mesin & Peralatan', '3,334,459,406'],
            ['A23-05', 'HP - Inventaris', '-'],
            ['A23-99', 'Aset dalam Penyelesaian', '-'],

            ['A24', 'Akumulasi Penyusutan Aset Tetap', '-'],
            ['A24-01', 'AP - Bangunan', '(287,849,679)'],
            ['A24-02', 'AP - Kendaraaan', '(2,261,883,440)'],
            ['A24-03', 'AP - Mesin & Peralatan', '(2,400,329,723)'],
            ['A24-04', 'AP - Inventaris', '-'],

            ['A25', 'Aset dalam Penyelesaian', '-'],
            ['A25-01', 'ADP - Tanah', '-'],
            ['A25-02', 'ADP - Bangunan', '-'],
            ['A25-03', 'ADP - Kendaraaan', '-'],
            ['A25-04', 'ADP - Mesin & Peralatan', '-'],
            ['A25-05', 'ADP - Inventaris', '-'],

            ['A26', 'Aset Tidak Berwujud', '-'],
            ['A26-01', 'Merk', '-'],
            ['A26-02', 'Software', '-'],

            ['A27', 'Aset Tidak Lancar Lainnya', '-'],
            ['A27-01', 'BNI - Tab - 1753560912', '5,000,806,795'],
            ['A27-02', '…………….', '-'],

            // =============================
            // KEWAJIBAN
            // =============================
            ['L', 'KEWAJIBAN', '-'],
            ['L1', 'KEWAJIBAN JANGKA PENDEK', '-'],

            ['L11', 'Utang Usaha', '-'],
            ['L11-01', 'Kreditor1', '-'],
            ['L11-02', 'Kreditor2', '-'],
            ['L11-03', 'Kreditor3', '-'],
            ['L11-99', '…...............', '-'],

            ['L12', 'Utang Lain-lain', '-'],
            ['L12-01', 'Utang Deviden', '-'],
            ['L12-02', 'Utang ke Kreditor B', '-'],

            ['L13', 'Biaya yang Harus Dibayar', '-'],
            ['L13-01', 'BHD - Gaji & Upah', '-'],
            ['L13-02', 'BHD - Listrik & Air', '-'],
            ['L13-03', 'BHD - Telpon & Internet', '-'],
            ['L13-04', 'BHD - Klaim BPJS', '-'],
            ['L13-05', 'BHD - BPJS TK', '-'],
            ['L13-06', 'BHD - Sewa', '(1,800,000,000)'],
            ['L13-99', 'BHD - Beban Lainnya', '-'],

            ['L14', 'Utang Pajak', '-'],
            ['L14-01', 'Utang Pajak PPh Pasal 21', '(1,901,155,330)'],
            ['L14-02', 'Utang Pajak PPh Pasal 22', '-'],
            ['L14-03', 'Utang Pajak PPh Pasal 23', '(2,743,832)'],
            ['L14-04', 'Utang Pajak PPh Pasal 25', '(259,905,444)'],
            ['L14-05', 'Utang Pajak PPh Pasal 29', '(218,247,682)'],
            ['L14-11', 'Utang Pajak PPh Pasal 4(2)', '(204,999,999)'],
            ['L14-12', 'Utang Pajak PPN - Keluaran', '(1,577,332,916)'],

            ['L15', 'Uang Muka Pendapatan', '-'],
            ['L15-01', 'UMP - Dana Pendidikan', '-'],
            ['L15-02', 'UMP - Dana Manasik', '-'],
            ['L15-99', 'UMP - Lainnya', '-'],

            ['L16', 'Pinjaman Jangka Pendek', '-'],
            ['L16-01', 'PJ Pendek - Bank', '-'],
            ['L16-02', 'PJ Pendek - Sewa Guna Usaha', '-'],

            ['L17', 'Kewajiban Imbalan Pasca Kerja', '-'],
            ['L17-01', 'Kewajiban Imbalan Pasca Kerja', '(4,937,083,600)'],
            ['L17-02', '……………………..', '-'],

            // =============================
            // EKUITAS
            // =============================
            ['C', 'EKUITAS', '-'],
            ['C1', 'MODAL', '-'],
            ['C11', 'Modal Disetor', '-'],
            ['C11-01', 'Modal Disetor - Hj. Rosita Aniati, S.H.', '(750,000,000)'],
            ['C11-02', 'Modal Disetor - Hj. Rusti Widayati, S.E.', '(750,000,000)'],
            ['C11-03', 'Modal Disetor - Siti Zahroh', '(1,000,000,000)'],

            ['C2', 'SALDO (LABA)/RUGI', '-'],
            ['C21', 'Saldo (Laba)/Rugi', '-'],
            ['C21-01', '(Laba)/Rugi Ditahan', '(13,260,910,873)'],
            ['C21-02', 'Deviden', '-'],
            ['C21-99', '(Laba)/Rugi Berjalan', '(11,024,561,618)'],

            // =============================
            // PENDAPATAN
            // =============================
            ['R', 'PENDAPATAN', '-'],
            ['R1', 'PENDAPATAN', '-'],
            ['R11', 'Pendapatan', '-'],
            ['R11-01', 'Jasa Maklon', '(144,414,014,072)'],
            ['R11-02', 'Jasa Sewa', '-'],
            ['R11-03', 'Jasa Manajemen Fee', '(1,999,388,181)'],

            ['R2', 'PENDAPATAN LAIN-LAIN', '-'],
            ['R21', 'Pendapatan Lain-Lain', '-'],
            ['R21-01', 'Jasa Giro & Bank', '(468,685,044)'],
            ['R21-02', '…................', '-'],
            ['R21-98', 'Laba Investasi', '-'],
            ['R21-99', 'Laba Penjualan Aset Tetap', '-'],

            // =============================
            // BEBAN
            // =============================
            ['E', 'BEBAN', '-'],
            ['E1', 'BEBAN PRODUKSI', '-'],
            ['E11', 'Beban Produksi', '-'],

            ['E11-01', 'Gaji, Upah & Tunjangan', '104,342,443,900'],
            ['E11-02', 'Kesejahteraan & BPJS', '7,015,138,963'],
            ['E11-03', 'Natura & Fasilitas Pabrik', '2,536,284,952'],
            ['E11-04', 'Bahan & Perlengkapan', '380,495,235'],
            ['E11-05', 'Energi & Utilitas', '-'],
            ['E11-06', 'Riset & Pengembangan', '67,030,942'],
            ['E11-07', '…................', '-'],
            ['E11-99', 'Beban Produksi Lainnya', '-'],

            ['E2', 'BEBAN USAHA', '-'],
            ['E21', 'Pemasaran', '-'],
            ['E21-01', 'Gaji, Upah & Tunjangan', '-'],
            ['E21-02', '…............', '-'],

            ['E22', 'Administrasi & Umum', '-'],
            ['E22-01', 'Gaji, Upah & Tunjangan', '10,549,707,284'],
            ['E22-02', 'Kesejahteraan & BPJS', '290,286,139'],
            ['E22-03', 'Pelatihan & Pengembangan', '181,240,704'],
            ['E22-04', 'SMK3', '37,524,849'],
            ['E22-05', 'Operasional Kantor', '1,824,975,124'],
            ['E22-06', 'Sewa', '2,399,000,000'],
            ['E22-07', 'Pemeliharaan', '257,882,000'],
            ['E22-08', 'Asuransi, Pajak & Perijinan', '257,559,713'],
            ['E22-09', 'Konsultan & Jasa Pihak Ketiga', '46,875,564'],
            ['E22-10', 'Sosial & CSR', '923,961,752'],
            ['E22-11', '…....................', '-'],
            ['E22-89', 'Beban Usaha Lainnya', '579,079,346'],
            ['E22-96', 'Penyusutan Bangunan', '867,612,564'],
            ['E22-97', 'Penyusutan Kendaraan', '-'],
            ['E22-98', 'Penyusutan Mesin & Peralatan', '-'],
            ['E22-99', 'Penyusutan Inventaris', '-'],

            ['E3', 'BEBAN LAIN-LAIN', '-'],
            ['E31', 'Beban Lain-lain', '-'],
            ['E31-01', 'Pajak Bunga & Jasa Giro', '93,737,009'],
            ['E31-02', 'Bunga Pinjaman', '-'],
            ['E31-03', 'Rugi Penjualan Aset Tetap', '-'],

            ['E9', 'BEBAN PAJAK PENGHASILAN', '-'],
            ['E91', 'Beban Pajak Penghasilan', '-'],
            ['E91-01', 'Beban Pajak Penghasilan', '3,206,689,640'],

            ['AM', 'Akun Memorial', '-'],
            ['PB', 'Pindah Buku', '-'],
        ];

        // Build parent relationships
        $idMap = [];

        foreach ($rows as $row) {

            [$kode, $keterangan, $val] = $row;

            // Determine parent
            $parentKode = $this->getParentCode($kode);
            $parentId = $parentKode && isset($idMap[$parentKode]) ? $idMap[$parentKode] : null;

            // Determine level
            $level = $this->getLevel($kode);

            $id = DB::table('trial_balances')->insertGetId([
                'kode'          => $kode,
                'keterangan'    => $keterangan,
                'parent_id'     => $parentId,
                'level'         => $level,
                'tahun_2024'    => $this->parseNumber($val),
                'created_at'    => now(),
                'updated_at'    => now(),
            ]);

            $idMap[$kode] = $id;
        }
    }

    private function parseNumber($value)
    {
        if ($value === '-' || $value === null) return 0;

        // If in parentheses → negative
        if (preg_match('/^\((.*)\)$/', $value, $m)) {
            return -1 * intval(str_replace(',', '', $m[1]));
        }

        return intval(str_replace(',', '', $value));
    }

    private function getParentCode($kode)
    {
        // Examples:
        // A11-01 -> parent A11
        // A12 -> parent A1
        // A11 -> parent A1

        if (str_contains($kode, '-')) {
            return explode('-', $kode)[0];
        }

        if (strlen($kode) === 3) { // A11, L21, E22
            return substr($kode, 0, 2);
        }

        if (strlen($kode) === 2) { // A1, L2, R1
            return substr($kode, 0, 1);
        }

        return null; // level 1 (A, L, E, R, C)
    }

    private function getLevel($kode)
    {
        if (str_contains($kode, '-')) return 4;
        if (strlen($kode) === 3) return 3;
        if (strlen($kode) === 2) return 2;
        return 1;
    }
}
