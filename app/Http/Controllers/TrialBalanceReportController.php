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
        $prevYear = 2024; // kolom TB sebelumnya tetap 2024

        // Ambil semua akun trial balance
        $items = TrialBalance::orderBy('kode')->get();

        // Ambil saldo dari journal_details untuk tahun berjalan
        $balances = DB::table('journal_details')
            ->join('journals', 'journals.id', '=', 'journal_details.journal_id')
            ->select(
                'trial_balance_id',
                DB::raw('SUM(CASE WHEN YEAR(journals.date) = '.$year.' AND MONTH(journals.date) = 1 THEN debit - credit ELSE 0 END) AS month_1'),
                DB::raw('SUM(CASE WHEN YEAR(journals.date) = '.$year.' AND MONTH(journals.date) = 2 THEN debit - credit ELSE 0 END) AS month_2'),
                DB::raw('SUM(CASE WHEN YEAR(journals.date) = '.$year.' AND MONTH(journals.date) = 3 THEN debit - credit ELSE 0 END) AS month_3'),
                DB::raw('SUM(CASE WHEN YEAR(journals.date) = '.$year.' AND MONTH(journals.date) = 4 THEN debit - credit ELSE 0 END) AS month_4'),
                DB::raw('SUM(CASE WHEN YEAR(journals.date) = '.$year.' AND MONTH(journals.date) = 5 THEN debit - credit ELSE 0 END) AS month_5'),
                DB::raw('SUM(CASE WHEN YEAR(journals.date) = '.$year.' AND MONTH(journals.date) = 6 THEN debit - credit ELSE 0 END) AS month_6'),
                DB::raw('SUM(CASE WHEN YEAR(journals.date) = '.$year.' AND MONTH(journals.date) = 7 THEN debit - credit ELSE 0 END) AS month_7'),
                DB::raw('SUM(CASE WHEN YEAR(journals.date) = '.$year.' AND MONTH(journals.date) = 8 THEN debit - credit ELSE 0 END) AS month_8'),
                DB::raw('SUM(CASE WHEN YEAR(journals.date) = '.$year.' AND MONTH(journals.date) = 9 THEN debit - credit ELSE 0 END) AS month_9'),
                DB::raw('SUM(CASE WHEN YEAR(journals.date) = '.$year.' AND MONTH(journals.date) = 10 THEN debit - credit ELSE 0 END) AS month_10'),
                DB::raw('SUM(CASE WHEN YEAR(journals.date) = '.$year.' AND MONTH(journals.date) = 11 THEN debit - credit ELSE 0 END) AS month_11'),
                DB::raw('SUM(CASE WHEN YEAR(journals.date) = '.$year.' AND MONTH(journals.date) = 12 THEN debit - credit ELSE 0 END) AS month_12'),
                DB::raw('SUM(CASE WHEN YEAR(journals.date) = '.$year.' THEN debit - credit ELSE 0 END) AS total_this_year')
            )
            ->groupBy('trial_balance_id')
            ->get()
            ->keyBy('trial_balance_id');

        // Siapkan data untuk view
        $data = [];
        foreach ($items as $tb) {
            $row = [];
            if (isset($balances[$tb->id])) {
                for ($m = 1; $m <= 12; $m++) {
                    $row['month_'.$m] = $balances[$tb->id]->{'month_'.$m} ?? 0;
                }
                $row['total_this_year'] = $balances[$tb->id]->total_this_year ?? 0;
            } else {
                for ($m = 1; $m <= 12; $m++) {
                    $row['month_'.$m] = 0;
                }
                $row['total_this_year'] = 0;
            }
            $data[$tb->id] = $row;
        }

        return view('trial_balance_report.index', compact('items', 'data', 'year', 'prevYear'));
    }
}
