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
        $items = TrialBalance::orderBy('kode')->get();

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

        return view('trial_balance_report.index', compact('items', 'data', 'year'));
    }
}
