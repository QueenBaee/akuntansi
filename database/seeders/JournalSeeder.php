<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Journal;
use App\Models\JournalDetail;
use App\Models\TrialBalance;
use App\Models\Cashflow;

use App\Models\User;
use Carbon\Carbon;

class JournalSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::first();
        if (!$user) {
            $this->command->warn('No users found. Please run UserSeeder first.');
            return;
        }

        // Check if journals already exist
        if (Journal::count() > 0) {
            $this->command->warn('Journals already exist. Skipping seeder.');
            return;
        }

        // Get key accounts from trial balance
        $kasAccount = TrialBalance::where('kode', 'A11-01')->first(); // Kas Pabrik
        $bankBNIAccount = TrialBalance::where('kode', 'A11-21')->first(); // BNI Giro
        $bankBCAAccount = TrialBalance::where('kode', 'A11-22')->first(); // BCA Giro
        $piutangSampoernaAccount = TrialBalance::where('kode', 'A12-01')->first(); // PU HM Sampoerna
        $pendapatanMaklonAccount = TrialBalance::where('kode', 'R11-01')->first(); // Jasa Maklon
        $pendapatanManajemenAccount = TrialBalance::where('kode', 'R11-03')->first(); // Jasa Manajemen Fee
        $pendapatanBungaAccount = TrialBalance::where('kode', 'R21-01')->first(); // Jasa Giro & Bank
        $bebanGajiProduksiAccount = TrialBalance::where('kode', 'E11-01')->first(); // Gaji Produksi
        $bebanGajiAdminAccount = TrialBalance::where('kode', 'E22-01')->first(); // Gaji Admin
        $bebanSewaAccount = TrialBalance::where('kode', 'E22-06')->first(); // Sewa
        $utangPPh21Account = TrialBalance::where('kode', 'L14-01')->first(); // Utang PPh 21
        $utangSewaAccount = TrialBalance::where('kode', 'L13-06')->first(); // BHD Sewa

        // Skip cashflow categories - table doesn't exist

        // Get cashflow accounts
        $maklonCashflow = Cashflow::where('kode', 'R1-01')->first(); // Jasa Maklon
        $manajemenCashflow = Cashflow::where('kode', 'R1-02')->first(); // Jasa Manajemen Fee
        $bungaCashflow = Cashflow::where('kode', 'R2-01')->first(); // Jasa Giro/Bunga Bank
        $gajiProduksiCashflow = Cashflow::where('kode', 'E1-01')->first(); // Upah Mingguan
        $gajiAdminCashflow = Cashflow::where('kode', 'E3-01')->first(); // Gaji Karyawan Bulanan
        $sewaCashflow = Cashflow::where('kode', 'E3-24')->first(); // Sewa Tanah & Bangunan

        $journalNumber = 1;
        $currentBalance = 0;

        // 1. Penerimaan Jasa Maklon dari HM Sampoerna
        if ($kasAccount && $pendapatanMaklonAccount && $maklonCashflow) {
            $amount = 50000000;
            $currentBalance += $amount;
            
            $journal = Journal::create([
                'date' => Carbon::now()->subDays(30),
                'number' => 'JRN-' . Carbon::now()->format('Ym') . '-' . str_pad($journalNumber++, 4, '0', STR_PAD_LEFT),
                'reference' => 'INV-MAKLON-001',
                'description' => 'Penerimaan jasa maklon dari HM Sampoerna Tbk',
                'pic' => 'Manager Produksi',
                'proof_number' => 'BKM-001',
                'cash_in' => $amount,
                'cash_out' => 0,
                'debit_account_id' => $kasAccount->id,
                'credit_account_id' => $pendapatanMaklonAccount->id,
                'cashflow_id' => $maklonCashflow->id,
                'balance' => $currentBalance,
                'total_debit' => $amount,
                'total_credit' => $amount,
                'source_module' => 'manual',
                'is_posted' => true,
                'created_by' => $user->id,
            ]);

            JournalDetail::create([
                'journal_id' => $journal->id,
                'account_id' => $kasAccount->id,
                'description' => 'Penerimaan kas dari jasa maklon',
                'debit' => $amount,
                'credit' => 0,
            ]);

            JournalDetail::create([
                'journal_id' => $journal->id,
                'account_id' => $pendapatanMaklonAccount->id,
                'description' => 'Pendapatan jasa maklon HM Sampoerna',
                'debit' => 0,
                'credit' => $amount,
            ]);
        }

        // 2. Pembayaran Gaji Produksi
        if ($kasAccount && $bebanGajiProduksiAccount && $gajiProduksiCashflow) {
            $amount = 15000000;
            $currentBalance -= $amount;
            
            $journal = Journal::create([
                'date' => Carbon::now()->subDays(25),
                'number' => 'JRN-' . Carbon::now()->format('Ym') . '-' . str_pad($journalNumber++, 4, '0', STR_PAD_LEFT),
                'reference' => 'PAYROLL-PROD-001',
                'description' => 'Pembayaran gaji dan upah karyawan produksi',
                'pic' => 'HRD Manager',
                'proof_number' => 'BKK-001',
                'cash_in' => 0,
                'cash_out' => $amount,
                'debit_account_id' => $bebanGajiProduksiAccount->id,
                'credit_account_id' => $kasAccount->id,
                'cashflow_id' => $gajiProduksiCashflow->id,
                'balance' => $currentBalance,
                'total_debit' => $amount,
                'total_credit' => $amount,
                'source_module' => 'manual',
                'is_posted' => true,
                'created_by' => $user->id,
            ]);

            JournalDetail::create([
                'journal_id' => $journal->id,
                'account_id' => $bebanGajiProduksiAccount->id,
                'description' => 'Beban gaji karyawan produksi',
                'debit' => $amount,
                'credit' => 0,
            ]);

            JournalDetail::create([
                'journal_id' => $journal->id,
                'account_id' => $kasAccount->id,
                'description' => 'Pembayaran gaji produksi',
                'debit' => 0,
                'credit' => $amount,
            ]);
        }

        // 3. Transfer dari Bank BNI ke Kas
        if ($kasAccount && $bankBNIAccount) {
            $amount = 25000000;
            $currentBalance += $amount;
            
            $journal = Journal::create([
                'date' => Carbon::now()->subDays(20),
                'number' => 'JRN-' . Carbon::now()->format('Ym') . '-' . str_pad($journalNumber++, 4, '0', STR_PAD_LEFT),
                'reference' => 'TRF-BNI-001',
                'description' => 'Transfer dari BNI Giro ke kas pabrik',
                'pic' => 'Finance Manager',
                'proof_number' => 'TRF-001',
                'cash_in' => $amount,
                'cash_out' => 0,
                'debit_account_id' => $kasAccount->id,
                'credit_account_id' => $bankBNIAccount->id,
                'cashflow_id' => null, // Internal transfer
                'balance' => $currentBalance,
                'total_debit' => $amount,
                'total_credit' => $amount,
                'source_module' => 'bank',
                'is_posted' => true,
                'created_by' => $user->id,
            ]);

            JournalDetail::create([
                'journal_id' => $journal->id,
                'account_id' => $kasAccount->id,
                'description' => 'Penerimaan transfer dari BNI',
                'debit' => $amount,
                'credit' => 0,
            ]);

            JournalDetail::create([
                'journal_id' => $journal->id,
                'account_id' => $bankBNIAccount->id,
                'description' => 'Transfer ke kas pabrik',
                'debit' => 0,
                'credit' => $amount,
            ]);
        }

        // 4. Penerimaan Jasa Manajemen Fee
        if ($bankBNIAccount && $pendapatanManajemenAccount && $manajemenCashflow) {
            $amount = 8000000;
            
            $journal = Journal::create([
                'date' => Carbon::now()->subDays(18),
                'number' => 'JRN-' . Carbon::now()->format('Ym') . '-' . str_pad($journalNumber++, 4, '0', STR_PAD_LEFT),
                'reference' => 'INV-MGMT-001',
                'description' => 'Penerimaan jasa manajemen fee',
                'pic' => 'General Manager',
                'proof_number' => 'BBM-001',
                'cash_in' => $amount,
                'cash_out' => 0,
                'debit_account_id' => $bankBNIAccount->id,
                'credit_account_id' => $pendapatanManajemenAccount->id,
                'cashflow_id' => $manajemenCashflow->id,
                'balance' => $currentBalance,
                'total_debit' => $amount,
                'total_credit' => $amount,
                'source_module' => 'bank',
                'is_posted' => true,
                'created_by' => $user->id,
            ]);

            JournalDetail::create([
                'journal_id' => $journal->id,
                'account_id' => $bankBNIAccount->id,
                'description' => 'Penerimaan jasa manajemen fee',
                'debit' => $amount,
                'credit' => 0,
            ]);

            JournalDetail::create([
                'journal_id' => $journal->id,
                'account_id' => $pendapatanManajemenAccount->id,
                'description' => 'Pendapatan jasa manajemen fee',
                'debit' => 0,
                'credit' => $amount,
            ]);
        }

        // 5. Pembayaran Gaji Admin
        if ($bankBCAAccount && $bebanGajiAdminAccount && $gajiAdminCashflow) {
            $amount = 12000000;
            
            $journal = Journal::create([
                'date' => Carbon::now()->subDays(15),
                'number' => 'JRN-' . Carbon::now()->format('Ym') . '-' . str_pad($journalNumber++, 4, '0', STR_PAD_LEFT),
                'reference' => 'PAYROLL-ADM-001',
                'description' => 'Pembayaran gaji karyawan administrasi',
                'pic' => 'HRD Manager',
                'proof_number' => 'BBK-001',
                'cash_in' => 0,
                'cash_out' => $amount,
                'debit_account_id' => $bebanGajiAdminAccount->id,
                'credit_account_id' => $bankBCAAccount->id,
                'cashflow_id' => $gajiAdminCashflow->id,
                'balance' => $currentBalance,
                'total_debit' => $amount,
                'total_credit' => $amount,
                'source_module' => 'bank',
                'is_posted' => true,
                'created_by' => $user->id,
            ]);

            JournalDetail::create([
                'journal_id' => $journal->id,
                'account_id' => $bebanGajiAdminAccount->id,
                'description' => 'Beban gaji karyawan administrasi',
                'debit' => $amount,
                'credit' => 0,
            ]);

            JournalDetail::create([
                'journal_id' => $journal->id,
                'account_id' => $bankBCAAccount->id,
                'description' => 'Pembayaran gaji administrasi',
                'debit' => 0,
                'credit' => $amount,
            ]);
        }

        // 6. Penerimaan Bunga Bank
        if ($bankBNIAccount && $pendapatanBungaAccount && $bungaCashflow) {
            $amount = 2500000;
            
            $journal = Journal::create([
                'date' => Carbon::now()->subDays(10),
                'number' => 'JRN-' . Carbon::now()->format('Ym') . '-' . str_pad($journalNumber++, 4, '0', STR_PAD_LEFT),
                'reference' => 'BUNGA-BNI-001',
                'description' => 'Penerimaan bunga bank BNI',
                'pic' => 'Finance Manager',
                'proof_number' => 'BBM-002',
                'cash_in' => $amount,
                'cash_out' => 0,
                'debit_account_id' => $bankBNIAccount->id,
                'credit_account_id' => $pendapatanBungaAccount->id,
                'cashflow_id' => $bungaCashflow->id,
                'balance' => $currentBalance,
                'total_debit' => $amount,
                'total_credit' => $amount,
                'source_module' => 'bank',
                'is_posted' => true,
                'created_by' => $user->id,
            ]);

            JournalDetail::create([
                'journal_id' => $journal->id,
                'account_id' => $bankBNIAccount->id,
                'description' => 'Penerimaan bunga bank',
                'debit' => $amount,
                'credit' => 0,
            ]);

            JournalDetail::create([
                'journal_id' => $journal->id,
                'account_id' => $pendapatanBungaAccount->id,
                'description' => 'Pendapatan bunga bank BNI',
                'debit' => 0,
                'credit' => $amount,
            ]);
        }

        // 7. Accrual Beban Sewa
        if ($bebanSewaAccount && $utangSewaAccount && $sewaCashflow) {
            $amount = 200000000; // Monthly rent
            
            $journal = Journal::create([
                'date' => Carbon::now()->subDays(5),
                'number' => 'JRN-' . Carbon::now()->format('Ym') . '-' . str_pad($journalNumber++, 4, '0', STR_PAD_LEFT),
                'reference' => 'ACCRUAL-SEWA-001',
                'description' => 'Accrual beban sewa bulanan',
                'pic' => 'Finance Manager',
                'proof_number' => 'MEM-001',
                'cash_in' => 0,
                'cash_out' => 0,
                'debit_account_id' => $bebanSewaAccount->id,
                'credit_account_id' => $utangSewaAccount->id,
                'cashflow_id' => null, // Accrual entry
                'balance' => $currentBalance,
                'total_debit' => $amount,
                'total_credit' => $amount,
                'source_module' => 'manual',
                'is_posted' => true,
                'created_by' => $user->id,
            ]);

            JournalDetail::create([
                'journal_id' => $journal->id,
                'account_id' => $bebanSewaAccount->id,
                'description' => 'Beban sewa bulanan',
                'debit' => $amount,
                'credit' => 0,
            ]);

            JournalDetail::create([
                'journal_id' => $journal->id,
                'account_id' => $utangSewaAccount->id,
                'description' => 'Utang beban sewa',
                'debit' => 0,
                'credit' => $amount,
            ]);
        }

        // 8. Pembayaran Utang Sewa
        if ($kasAccount && $utangSewaAccount && $sewaCashflow) {
            $amount = 150000000;
            $currentBalance -= $amount;
            
            $journal = Journal::create([
                'date' => Carbon::now()->subDays(2),
                'number' => 'JRN-' . Carbon::now()->format('Ym') . '-' . str_pad($journalNumber++, 4, '0', STR_PAD_LEFT),
                'reference' => 'PAY-SEWA-001',
                'description' => 'Pembayaran utang sewa',
                'pic' => 'Finance Manager',
                'proof_number' => 'BKK-002',
                'cash_in' => 0,
                'cash_out' => $amount,
                'debit_account_id' => $utangSewaAccount->id,
                'credit_account_id' => $kasAccount->id,
                'cashflow_id' => $sewaCashflow->id,
                'balance' => $currentBalance,
                'total_debit' => $amount,
                'total_credit' => $amount,
                'source_module' => 'manual',
                'is_posted' => true,
                'created_by' => $user->id,
            ]);

            JournalDetail::create([
                'journal_id' => $journal->id,
                'account_id' => $utangSewaAccount->id,
                'description' => 'Pembayaran utang sewa',
                'debit' => $amount,
                'credit' => 0,
            ]);

            JournalDetail::create([
                'journal_id' => $journal->id,
                'account_id' => $kasAccount->id,
                'description' => 'Pengeluaran kas untuk sewa',
                'debit' => 0,
                'credit' => $amount,
            ]);
        }

        $this->command->info('Journal entries with logical relationships created successfully.');
        $this->command->info("Total journals created: " . ($journalNumber - 1));
    }
}