<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Journal;
use App\Models\JournalDetail;
use App\Models\Account;
use App\Models\Cashflow;
use App\Models\User;

class JournalSampleSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::first();
        $cashAccount = Account::where('code', '1101')->first(); // Kas
        $bankAccount = Account::where('code', '1102')->first(); // Bank
        $salesAccount = Account::where('type', 'revenue')->first(); // Pendapatan
        $expenseAccount = Account::where('type', 'expense')->first(); // Beban
        $cashflowOperating = Cashflow::first();

        if (!$user || !$cashAccount || !$salesAccount) {
            $this->command->warn('Required accounts or users not found. Skipping journal sample seeder.');
            return;
        }

        // Sample Journal 1: Penerimaan Kas dari Penjualan
        $journal1 = Journal::create([
            'date' => now()->subDays(5),
            'number' => 'JRN-' . now()->format('Ym') . '-0001',
            'reference' => 'INV-001',
            'description' => 'Penerimaan kas dari penjualan barang',
            'pic' => 'John Doe',
            'proof_number' => 'BKM-001',
            'cash_in' => 5000000,
            'cash_out' => 0,
            'debit_account_id' => $cashAccount->id,
            'credit_account_id' => $salesAccount->id,
            'cashflow_id' => $cashflowOperating?->id,
            'balance' => 5000000,
            'total_debit' => 5000000,
            'total_credit' => 5000000,
            'source_module' => 'manual',
            'is_posted' => true,
            'created_by' => $user->id,
        ]);

        // Journal Details for Journal 1
        JournalDetail::create([
            'journal_id' => $journal1->id,
            'account_id' => $cashAccount->id,
            'description' => 'Penerimaan kas dari penjualan',
            'debit' => 5000000,
            'credit' => 0,
        ]);

        JournalDetail::create([
            'journal_id' => $journal1->id,
            'account_id' => $salesAccount->id,
            'description' => 'Penjualan barang',
            'debit' => 0,
            'credit' => 5000000,
        ]);

        // Sample Journal 2: Pengeluaran Kas untuk Beban
        if ($expenseAccount) {
            $journal2 = Journal::create([
                'date' => now()->subDays(3),
                'number' => 'JRN-' . now()->format('Ym') . '-0002',
                'reference' => 'BKK-001',
                'description' => 'Pembayaran beban operasional',
                'pic' => 'Jane Smith',
                'proof_number' => 'BKK-001',
                'cash_in' => 0,
                'cash_out' => 1500000,
                'debit_account_id' => $expenseAccount->id,
                'credit_account_id' => $cashAccount->id,
                'cashflow_id' => $cashflowOperating?->id,
                'balance' => 3500000,
                'total_debit' => 1500000,
                'total_credit' => 1500000,
                'source_module' => 'manual',
                'is_posted' => true,
                'created_by' => $user->id,
            ]);

            // Journal Details for Journal 2
            JournalDetail::create([
                'journal_id' => $journal2->id,
                'account_id' => $expenseAccount->id,
                'description' => 'Beban operasional',
                'debit' => 1500000,
                'credit' => 0,
            ]);

            JournalDetail::create([
                'journal_id' => $journal2->id,
                'account_id' => $cashAccount->id,
                'description' => 'Pengeluaran kas',
                'debit' => 0,
                'credit' => 1500000,
            ]);
        }

        // Sample Journal 3: Transfer Bank ke Kas
        if ($bankAccount) {
            $journal3 = Journal::create([
                'date' => now()->subDays(1),
                'number' => 'JRN-' . now()->format('Ym') . '-0003',
                'reference' => 'TRF-001',
                'description' => 'Transfer dari bank ke kas',
                'pic' => 'Admin',
                'proof_number' => 'TRF-001',
                'cash_in' => 2000000,
                'cash_out' => 0,
                'debit_account_id' => $cashAccount->id,
                'credit_account_id' => $bankAccount->id,
                'cashflow_id' => null, // Internal transfer
                'balance' => 5500000,
                'total_debit' => 2000000,
                'total_credit' => 2000000,
                'source_module' => 'manual',
                'is_posted' => true,
                'created_by' => $user->id,
            ]);

            // Journal Details for Journal 3
            JournalDetail::create([
                'journal_id' => $journal3->id,
                'account_id' => $cashAccount->id,
                'description' => 'Penerimaan dari bank',
                'debit' => 2000000,
                'credit' => 0,
            ]);

            JournalDetail::create([
                'journal_id' => $journal3->id,
                'account_id' => $bankAccount->id,
                'description' => 'Transfer ke kas',
                'debit' => 0,
                'credit' => 2000000,
            ]);
        }

        $this->command->info('Sample journals with complete data created successfully.');
    }
}