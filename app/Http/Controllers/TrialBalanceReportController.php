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
         * 1. Saldo Dasar (tahun_2024)
         * =======================================================
         */
        $baseSaldo = [];
        foreach ($items as $it) {
            $baseSaldo[$it->id] = $it->tahun_2024 ?? 0;
        }

        /**
         * =======================================================
         * 2. Saldo Awal (Opening Balance) — akumulatif mulai 2025
         * =======================================================
         * Rumus:
         *   opening = tahun_2024 + mutasi(2025..previousYear)
         */
        $startBaseYear = 2025;
        $openingBalance = [];

        if ($previousYear >= $startBaseYear) {

            // Mutasi DEBIT rentang tahun
            $debitsRange = DB::table('journals')
                ->select(
                    DB::raw("debit_account_id AS account_id"),
                    DB::raw("SUM(cash_in) AS total_in"),
                    DB::raw("0 AS total_out")
                )
                ->whereYear('date', '>=', $startBaseYear)
                ->whereYear('date', '<=', $previousYear)
                ->whereNotNull('debit_account_id')
                ->groupBy('account_id');

            // Mutasi KREDIT rentang tahun
            $creditsRange = DB::table('journals')
                ->select(
                    DB::raw("credit_account_id AS account_id"),
                    DB::raw("0 AS total_in"),
                    DB::raw("SUM(cash_out) AS total_out")
                )
                ->whereYear('date', '>=', $startBaseYear)
                ->whereYear('date', '<=', $previousYear)
                ->whereNotNull('credit_account_id')
                ->groupBy('account_id');

            // Gabung mutasi
            $rangeQuery = $debitsRange
                ->unionAll($creditsRange)
                ->get()
                ->groupBy('account_id');

            // Hitung saldo awal final
            foreach ($items as $it) {
                $rows = $rangeQuery[$it->id] ?? collect();

                $totalIn = $rows->sum('total_in');
                $totalOut = $rows->sum('total_out');

                $openingBalance[$it->id] = ($baseSaldo[$it->id] ?? 0) + ($totalIn - $totalOut);
            }

        } else {
            // Jika previousYear = 2024 → gunakan tahun_2024
            foreach ($items as $it) {
                $openingBalance[$it->id] = $baseSaldo[$it->id] ?? 0;
            }
        }

        /**
         * =======================================================
         * 3. Mutasi Tahun Berjalan (per bulan)
         * =======================================================
         */

        // debit ke akun (cash_in)
        $debits = DB::table('journals')
            ->select(
                DB::raw("debit_account_id AS account_id"),
                DB::raw("MONTH(date) AS month"),
                DB::raw("SUM(cash_in) AS total_in"),
                DB::raw("0 AS total_out")
            )
            ->whereYear('date', $year)
            ->whereNotNull('debit_account_id')
            ->groupBy('account_id', 'month');

        // kredit dari akun (cash_out)
        $credits = DB::table('journals')
            ->select(
                DB::raw("credit_account_id AS account_id"),
                DB::raw("MONTH(date) AS month"),
                DB::raw("0 AS total_in"),
                DB::raw("SUM(cash_out) AS total_out")
            )
            ->whereYear('date', $year)
            ->whereNotNull('credit_account_id')
            ->groupBy('account_id', 'month');

        // gabung debit + kredit
        $journalMonthly = $debits
            ->unionAll($credits)
            ->get()
            ->groupBy('account_id');

        /**
         * =======================================================
         * 4. Saldo Per Bulan
         * =======================================================
         */

        $data = [];

        foreach ($items as $item) {

            // saldo awal
            $saldo = $openingBalance[$item->id] ?? 0;

            $row = [];

            for ($m = 1; $m <= 12; $m++) {

                $trx = $journalMonthly[$item->id] ?? collect();

                $cashIn = $trx->where('month', $m)->sum('total_in');
                $cashOut = $trx->where('month', $m)->sum('total_out');

                // saldo berjalan = saldo + debit - kredit
                $saldo = $saldo + $cashIn - $cashOut;

                $row["month_$m"] = $saldo;
            }

            $row['total'] = $saldo;
            $row['opening'] = $openingBalance[$item->id] ?? 0;
            $data[$item->id] = $row;
        }

        return view('trial_balance_report.index', compact('items', 'data', 'year'));
    }
}
