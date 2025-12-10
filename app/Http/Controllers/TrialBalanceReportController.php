<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TrialBalance;
use Illuminate\Support\Facades\DB;

class TrialBalanceReportController extends Controller
{
    public function index(Request $request)
    {
        $year = $request->year ?? date('Y');
        $previousYear = $year - 1;

        // Ambil master akun TB
        $items = TrialBalance::orderBy('id')->get();

        /**
         * =======================================================
         * 1. Saldo dasar dari tahun_2024
         * =======================================================
         */
        $baseSaldo = [];
        foreach ($items as $it) {
            $baseSaldo[$it->id] = $it->tahun_2024 ?? 0;
        }

        /**
         * =======================================================
         * 2. Hitung SALDO AWAL untuk tahun berjalan
         *    opening = tahun_2024 + mutasi(2025..previousYear)
         * =======================================================
         */
        $startBaseYear = 2025;
        $openingBalance = [];

        if ($previousYear >= $startBaseYear) {

            // Mutasi debit
            $debitPrev = DB::table('journals')
                ->select(
                    DB::raw("debit_account_id AS account_id"),
                    DB::raw("SUM(total_debit) AS debit_amount"),
                    DB::raw("0 AS credit_amount")
                )
                ->whereYear('date', '>=', $startBaseYear)
                ->whereYear('date', '<=', $previousYear)
                ->whereNull('deleted_at')
                ->groupBy('account_id');

            // Mutasi kredit
            $creditPrev = DB::table('journals')
                ->select(
                    DB::raw("credit_account_id AS account_id"),
                    DB::raw("0 AS debit_amount"),
                    DB::raw("SUM(total_credit) AS credit_amount")
                )
                ->whereYear('date', '>=', $startBaseYear)
                ->whereYear('date', '<=', $previousYear)
                ->whereNull('deleted_at')
                ->groupBy('account_id');

            $prevQuery = $debitPrev
                ->unionAll($creditPrev)
                ->get()
                ->groupBy('account_id');

            foreach ($items as $it) {

                $rows = $prevQuery[$it->id] ?? collect();

                $debit  = $rows->sum('debit_amount');
                $credit = $rows->sum('credit_amount');

                $openingBalance[$it->id] = ($baseSaldo[$it->id] ?? 0) + ($debit - $credit);
            }

        } else {
            // Tahun 2025 → saldo awal = master TB
            foreach ($items as $it) {
                $openingBalance[$it->id] = $baseSaldo[$it->id] ?? 0;
            }
        }

        /**
         * =======================================================
         * 3. Mutasi tahun berjalan (per bulan)
         *    debit(+) kredit(-)
         * =======================================================
         */

        // Mutasi DEBIT per bulan
        $debits = DB::table('journals')
            ->select(
                DB::raw("debit_account_id AS account_id"),
                DB::raw("MONTH(date) AS month"),
                DB::raw("SUM(total_debit) AS debit_amount"),
                DB::raw("0 AS credit_amount")
            )
            ->whereYear('date', $year)
            ->whereNull('deleted_at')
            ->groupBy('account_id', 'month');

        // Mutasi KREDIT per bulan
        $credits = DB::table('journals')
            ->select(
                DB::raw("credit_account_id AS account_id"),
                DB::raw("MONTH(date) AS month"),
                DB::raw("0 AS debit_amount"),
                DB::raw("SUM(total_credit) AS credit_amount")
            )
            ->whereYear('date', $year)
            ->whereNull('deleted_at')
            ->groupBy('account_id', 'month');

        // Gabungkan debit + kredit
        $journalMonthly = $debits
            ->unionAll($credits)
            ->get()
            ->groupBy('account_id');

        /**
         * =======================================================
         * 4. Hitung saldo per bulan
         * =======================================================
         */
        $data = [];

        foreach ($items as $item) {

            $saldo = $openingBalance[$item->id] ?? 0;
            $row = [];

            for ($m = 1; $m <= 12; $m++) {

                $trx = $journalMonthly[$item->id] ?? collect();

                $debit  = $trx->where('month', $m)->sum('debit_amount');
                $credit = $trx->where('month', $m)->sum('credit_amount');

                $saldo = $saldo + $debit - $credit;

                $row["month_$m"] = $saldo;
            }

            $row['total']   = $saldo;
            $row['opening'] = $openingBalance[$item->id] ?? 0;

            $data[$item->id] = $row;
        }

        /**
         * =======================================================
         * 5. Apply custom calculation rules
         * =======================================================
         */
        $c2101 = $items->where('kode', 'C21-01')->first();
        $c2102 = $items->where('kode', 'C21-02')->first();
        $c2199 = $items->where('kode', 'C21-99')->first();

        if ($c2101 && $c2102 && $c2199) {
            // Simpan nilai original sebelum custom rules
            $originalC2101Opening = $data[$c2101->id]['opening'];
            $originalC2102Opening = $data[$c2102->id]['opening'];
            $originalC2199Opening = $data[$c2199->id]['opening'];
            
            // Hitung C21-01 opening balance dari penjumlahan 3 variabel
            $c2101OpeningSum = $originalC2101Opening + $originalC2102Opening + $originalC2199Opening;
            $data[$c2101->id]['opening'] = $c2101OpeningSum;
            
            // Tetap tampilkan C21-99 opening original untuk display
            $data[$c2199->id]['opening'] = $originalC2199Opening;
            
            // Reset C21-02 opening ke 0 karena sudah dijumlahkan ke C21-01  
            $data[$c2102->id]['opening'] = 0;
            
            // Simpan perubahan C21-99 per bulan untuk digunakan di C21-01
            $c2199Changes = [];
            
            // Hitung perubahan C21-99 per bulan dan reset semua bulan ke 0 dulu
            for ($m = 1; $m <= 12; $m++) {
                $data[$c2199->id]["month_$m"] = 0; // Reset semua bulan ke 0
                
                $monthlyChange = 0;
                $hasTransactionInHigherAccounts = false;
                
                foreach ($items as $item) {
                    if ($item->id > $c2199->id) {
                        $trx = $journalMonthly[$item->id] ?? collect();
                        $debit = $trx->where('month', $m)->sum('debit_amount');
                        $credit = $trx->where('month', $m)->sum('credit_amount');
                        
                        if ($debit > 0 || $credit > 0) {
                            $hasTransactionInHigherAccounts = true;
                            $monthlyChange += ($debit - $credit);
                        }
                    }
                }
                
                // Simpan perubahan untuk digunakan di C21-01
                $c2199Changes[$m] = $hasTransactionInHigherAccounts ? $monthlyChange : 0;
                
                // C21-99 hanya tampilkan perubahan jika ada transaksi
                if ($hasTransactionInHigherAccounts) {
                    $data[$c2199->id]["month_$m"] = $monthlyChange;
                }
            }
            
            // Hitung C21-01 menggunakan opening yang sudah dijumlahkan + perubahan C21-99
            for ($m = 1; $m <= 12; $m++) {
                if ($m == 1) {
                    // Month 1: gunakan opening yang sudah dijumlahkan + perubahan C21-99 bulan ini
                    $data[$c2101->id]["month_$m"] = $c2101OpeningSum + $c2199Changes[$m];
                } else {
                    // Month 2-12: gunakan bulan sebelumnya + perubahan C21-99 bulan ini
                    $prevMonth = $m - 1;
                    $data[$c2101->id]["month_$m"] = $data[$c2101->id]["month_$prevMonth"] + $c2199Changes[$m];
                }
            }

            // Update totals for modified accounts
            $data[$c2101->id]['total'] = $data[$c2101->id]['month_12'];
            $data[$c2199->id]['total'] = array_sum($c2199Changes);
        }

        return view('trial_balance_report.index', compact('items', 'data', 'year'));
    }
}
