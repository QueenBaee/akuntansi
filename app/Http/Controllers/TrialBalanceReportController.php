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

        // Ambil master TB
        $items = TrialBalance::orderBy('kode')->get();

        // Ambil transaksi jurnal (cash_in/cash_out)
        $journalMonthly = DB::table('journals')
            ->select(
                DB::raw("debit_account_id AS account_id"),
                DB::raw("MONTH(date) AS month"),
                DB::raw("SUM(cash_in) AS total_in"),
                DB::raw("SUM(cash_out) AS total_out")
            )
            ->whereYear('date', $year)
            ->whereNotNull('debit_account_id')
            ->groupBy('account_id', 'month')
            ->get()
            ->groupBy('account_id');

        // Hasil per akun TB
        $data = [];

        foreach ($items as $item) {

            $saldo = $item->tahun_2024; // saldo awal di master TB
            $row   = [];

            for ($m = 1; $m <= 12; $m++) {

                $trx = $journalMonthly[$item->id] ?? collect();

                $cashIn  = $trx->where('month', $m)->sum('total_in');
                $cashOut = $trx->where('month', $m)->sum('total_out');

                // Sama persis seperti laporan kas:
                // saldo = saldo + masuk - keluar
                $saldo = $saldo + $cashIn - $cashOut;

                $row["month_$m"] = $saldo;
            }

            $row['total'] = $saldo;
            $data[$item->id] = $row;
        }

        return view('trial_balance_report.index', compact('items', 'data', 'year'));
    }
}
