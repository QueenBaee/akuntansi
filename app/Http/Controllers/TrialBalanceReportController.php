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
        $prevYear = 2024;

        // Ambil akun TB
        $items = TrialBalance::orderBy('kode')->get();

        // Hitung transaksi jurnal per bulan (debit - credit)
        $balances = DB::table('journals')
            ->select(
                DB::raw("debit_account_id AS account_id"),
                DB::raw("SUM(CASE WHEN YEAR(date) = $year AND MONTH(date)=1 THEN total_debit ELSE 0 END) AS debit_1"),
                DB::raw("SUM(CASE WHEN YEAR(date) = $year AND MONTH(date)=1 THEN total_credit ELSE 0 END) AS credit_1"),

                DB::raw("SUM(CASE WHEN YEAR(date) = $year AND MONTH(date)=2 THEN total_debit ELSE 0 END) AS debit_2"),
                DB::raw("SUM(CASE WHEN YEAR(date) = $year AND MONTH(date)=2 THEN total_credit ELSE 0 END) AS credit_2"),

                DB::raw("SUM(CASE WHEN YEAR(date) = $year AND MONTH(date)=3 THEN total_debit ELSE 0 END) AS debit_3"),
                DB::raw("SUM(CASE WHEN YEAR(date) = $year AND MONTH(date)=3 THEN total_credit ELSE 0 END) AS credit_3"),

                DB::raw("SUM(CASE WHEN YEAR(date) = $year AND MONTH(date)=4 THEN total_debit ELSE 0 END) AS debit_4"),
                DB::raw("SUM(CASE WHEN YEAR(date) = $year AND MONTH(date)=4 THEN total_credit ELSE 0 END) AS credit_4"),

                DB::raw("SUM(CASE WHEN YEAR(date) = $year AND MONTH(date)=5 THEN total_debit ELSE 0 END) AS debit_5"),
                DB::raw("SUM(CASE WHEN YEAR(date) = $year AND MONTH(date)=5 THEN total_credit ELSE 0 END) AS credit_5"),

                DB::raw("SUM(CASE WHEN YEAR(date) = $year AND MONTH(date)=6 THEN total_debit ELSE 0 END) AS debit_6"),
                DB::raw("SUM(CASE WHEN YEAR(date) = $year AND MONTH(date)=6 THEN total_credit ELSE 0 END) AS credit_6"),

                DB::raw("SUM(CASE WHEN YEAR(date) = $year AND MONTH(date)=7 THEN total_debit ELSE 0 END) AS debit_7"),
                DB::raw("SUM(CASE WHEN YEAR(date) = $year AND MONTH(date)=7 THEN total_credit ELSE 0 END) AS credit_7"),

                DB::raw("SUM(CASE WHEN YEAR(date) = $year AND MONTH(date)=8 THEN total_debit ELSE 0 END) AS debit_8"),
                DB::raw("SUM(CASE WHEN YEAR(date) = $year AND MONTH(date)=8 THEN total_credit ELSE 0 END) AS credit_8"),

                DB::raw("SUM(CASE WHEN YEAR(date) = $year AND MONTH(date)=9 THEN total_debit ELSE 0 END) AS debit_9"),
                DB::raw("SUM(CASE WHEN YEAR(date) = $year AND MONTH(date)=9 THEN total_credit ELSE 0 END) AS credit_9"),

                DB::raw("SUM(CASE WHEN YEAR(date) = $year AND MONTH(date)=10 THEN total_debit ELSE 0 END) AS debit_10"),
                DB::raw("SUM(CASE WHEN YEAR(date) = $year AND MONTH(date)=10 THEN total_credit ELSE 0 END) AS credit_10"),

                DB::raw("SUM(CASE WHEN YEAR(date) = $year AND MONTH(date)=11 THEN total_debit ELSE 0 END) AS debit_11"),
                DB::raw("SUM(CASE WHEN YEAR(date) = $year AND MONTH(date)=11 THEN total_credit ELSE 0 END) AS credit_11"),

                DB::raw("SUM(CASE WHEN YEAR(date) = $year AND MONTH(date)=12 THEN total_debit ELSE 0 END) AS debit_12"),
                DB::raw("SUM(CASE WHEN YEAR(date) = $year AND MONTH(date)=12 THEN total_credit ELSE 0 END) AS credit_12")
            )
            ->groupBy('debit_account_id')
            ->get()
            ->keyBy('account_id');

        // Susun hasil
        $data = [];
        foreach ($items as $item) {
            $row = [];

            $saldo = $item->tahun_2024; // saldo awal

            for ($m = 1; $m <= 12; $m++) {
                $debit = $balances[$item->id]->{"debit_$m"} ?? 0;
                $credit = $balances[$item->id]->{"credit_$m"} ?? 0;

                $saldo += ($debit - $credit);

                $row["month_$m"] = $saldo;
            }

            $row['total'] = $saldo;

            $data[$item->id] = $row;
        }

        return view('trial_balance_report.index', compact('items', 'data', 'year', 'prevYear'));
    }
}
