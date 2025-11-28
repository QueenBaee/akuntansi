<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cashflow;
use App\Models\TrialBalance;
use Illuminate\Support\Facades\DB;

class CashflowReportController extends Controller
{
    public function index(Request $request)
    {
        $year = $request->year ?? date('Y');
        $previousYear = $year - 1;

        // Load cashflow master
        $items = Cashflow::orderBy('kode')->get();

        // Load base saldo from trial balance (tahun_2024)
        $trialBalances = TrialBalance::all()->keyBy('id');
        $baseSaldo = [];

        foreach ($items as $item) {
            $tb = $trialBalances[$item->trial_balance_id] ?? null;
            $baseSaldo[$item->id] = $tb ? ($tb->tahun_2024 ?? 0) : 0;
        }

        /**
         * =======================================================
         * 1. OPENING BALANCE (2025..previousYear)
         *    DEBIT  → + total_debit
         *    CREDIT → - total_credit
         * =======================================================
         */
        $startBaseYear = 2025;
        $openingBalance = [];

        if ($previousYear >= $startBaseYear) {

            // DEBIT → +
            $debitPrev = DB::table('journals as j')
                ->join('cashflows as c', 'j.cashflow_id', '=', 'c.id')
                ->select(
                    'j.cashflow_id',
                    DB::raw('SUM(j.total_debit) as plus'),
                    DB::raw('0 as minus')
                )
                ->whereRaw('j.debit_account_id = c.trial_balance_id')
                ->whereBetween(DB::raw('YEAR(j.date)'), [$startBaseYear, $previousYear])
                ->groupBy('j.cashflow_id');

            // CREDIT → -
            $creditPrev = DB::table('journals as j')
                ->join('cashflows as c', 'j.cashflow_id', '=', 'c.id')
                ->select(
                    'j.cashflow_id',
                    DB::raw('0 as plus'),
                    DB::raw('SUM(j.total_credit) as minus')
                )
                ->whereRaw('j.credit_account_id = c.trial_balance_id')
                ->whereBetween(DB::raw('YEAR(j.date)'), [$startBaseYear, $previousYear])
                ->groupBy('j.cashflow_id');

            $prevQuery = $debitPrev->unionAll($creditPrev)->get()->groupBy('cashflow_id');

            foreach ($items as $item) {
                $rows = $prevQuery[$item->id] ?? collect();
                $net = $rows->sum('plus') - $rows->sum('minus');

                $openingBalance[$item->id] = ($baseSaldo[$item->id] ?? 0) + $net;
            }

        } else {
            // Tahun 2025 → saldo awal = master TB
            foreach ($items as $item) {
                $openingBalance[$item->id] = $baseSaldo[$item->id] ?? 0;
            }
        }

        /**
         * =======================================================
         * 2. MUTASI TAHUN BERJALAN (per bulan)
         *    DEBIT  → + total_debit
         *    CREDIT → - total_credit
         * =======================================================
         */

        // DEBIT → +
        $debitsMonthly = DB::table('journals as j')
            ->join('cashflows as c', 'j.cashflow_id', '=', 'c.id')
            ->select(
                'j.cashflow_id',
                DB::raw('MONTH(j.date) as month'),
                DB::raw('SUM(j.total_debit) as plus'),
                DB::raw('0 as minus')
            )
            ->whereRaw('j.debit_account_id = c.trial_balance_id')
            ->whereYear('j.date', $year)
            ->groupBy('j.cashflow_id', 'month');

        // CREDIT → -
        $creditsMonthly = DB::table('journals as j')
            ->join('cashflows as c', 'j.cashflow_id', '=', 'c.id')
            ->select(
                'j.cashflow_id',
                DB::raw('MONTH(j.date) as month'),
                DB::raw('0 as plus'),
                DB::raw('SUM(j.total_credit) as minus')
            )
            ->whereRaw('j.credit_account_id = c.trial_balance_id')
            ->whereYear('j.date', $year)
            ->groupBy('j.cashflow_id', 'month');

        $journalMonthly = $debitsMonthly
            ->unionAll($creditsMonthly)
            ->get()
            ->groupBy('cashflow_id');

        /**
         * =======================================================
         * 3. HITUNG SALDO PER BULAN
         * =======================================================
         */

        $data = [];

        foreach ($items as $item) {

            $saldo = $openingBalance[$item->id] ?? 0;
            $row = [];

            $trx = $journalMonthly[$item->id] ?? collect();

            for ($m = 1; $m <= 12; $m++) {

                $plus  = $trx->where('month', $m)->sum('plus');
                $minus = $trx->where('month', $m)->sum('minus');

                $saldo = $saldo + $plus - $minus;

                $row["month_$m"] = $saldo;
            }

            $row['total']   = $saldo;
            $row['opening'] = $openingBalance[$item->id] ?? 0;

            $data[$item->id] = $row;
        }

        return view('cashflow_report.index', compact('items', 'data', 'year'));
    }
}
