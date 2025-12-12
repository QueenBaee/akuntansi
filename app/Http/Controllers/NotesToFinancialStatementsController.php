<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TrialBalance;
use Illuminate\Support\Facades\DB;

class NotesToFinancialStatementsController extends Controller
{
    private $accountGroups = [
        '1. KAS & SETARA KAS' => ['A11-01', 'A11-21', 'A11-22', 'A11-23'],
        '2. PIUTANG USAHA' => ['A12-01', 'A12-02', 'A12-03'],
        '3. PIUTANG LAIN-LAIN' => ['A13-01', 'A13-02', 'A13-03', 'A13-98', 'A13-99'],
        'INVESTASI JANGKA PENDEK' => ['A14-01', 'A14-02', 'A14-99'],
        'PERSEDIAAN' => ['A15-01', 'A15-02', 'A15-99'],
        '4. BIAYA DIBAYAR DI MUKA' => ['A16-01', 'A16-02'],
        '5. UANG MUKA PAJAK' => ['A17-01', 'A17-02', 'A17-03', 'A17-04', 'A17-11'],
        'ASET LANCAR LAINNYA' => ['A18-01', 'A18-02'],
        'PIUTANG LAIN-LAIN - JANGKA PANJANG' => ['A21-01', 'A21-02'],
        'INVESTASI JANGKA PANJANG' => ['A22-01', 'A22-02'],
        '6. ASET TETAP BERSIH - HARGA PEROLEHAN' => ['A23-01', 'A23-02', 'A23-03', 'A23-04', 'A23-99'],
        '6. ASET TETAP BERSIH - AKUMULASI PENYUSUTAN' => ['A24-01', 'A24-02', 'A24-03'],
        'ASET TIDAK BERWUJUD' => ['A25-01', 'A25-02'],
        '7. ASET TIDAK LANCAR LAINNYA' => ['A26-01', 'A26-02'],
        '8. UTANG USAHA' => ['L11-01', 'L11-99'],
        'UTANG LAIN-LAIN' => ['L12-01', 'L12-02'],
        '9. BIAYA YANG HARUS DIBAYAR' => ['L13-01', 'L13-02', 'L13-03', 'L13-04', 'L13-05', 'L13-06', 'L13-99'],
        '10. UTANG PAJAK' => ['L14-01', 'L14-02', 'L14-03', 'L14-04', 'L14-05', 'L14-11', 'L14-12'],
        'UANG MUKA PENDAPATAN' => ['L15-01', 'L15-02', 'L15-99'],
        'PINJAMAN JANGKA PENDEK' => ['L16-01', 'L16-02'],
        '11. KEWAJIBAN IMBALAN PASCA KERJA' => ['L17-01', 'L17-02'],
        'UTANG USAHA - JK. PANJANG' => ['L21-01', 'L21-02'],
        'UTANG LAIN-LAIN - JK. PANJANG' => ['L22-01', 'L22-02'],
        'PINJAMAN JANGKA PANJANG' => ['L23-01', 'L23-02'],
        'KEWAJIBAN IMBALAN PASCA KERJA - JK. PANJANG' => ['L24-01', 'L24-02'],
        'KEWAJIBAN JANGKA PANJANG LAINNYA' => ['L25-01', 'L25-02'],
        '12. MODAL DISETOR' => ['C11-01', 'C11-02', 'C11-03'],
        '13. PENDAPATAN' => ['R11-01', 'R11-02', 'R11-03'],
        '14. BEBAN PRODUKSI' => ['E11-01', 'E11-02', 'E11-03', 'E11-04', 'E11-05', 'E11-06', 'E11-07'],
        'PEMASARAN' => ['E21-01', 'E21-02'],
        '15. ADMINISTRASI & UMUM' => ['E22-01', 'E22-02', 'E22-03', 'E22-04', 'E22-05', 'E22-06', 'E22-07', 'E22-08', 'E22-09', 'E22-10', 'E22-11', 'E22-89', 'E22-96', 'E22-97', 'E22-98', 'E22-99'],
        '16. PENDAPATAN LAIN-LAIN' => ['R21-01', 'R21-02', 'R21-99'],
        '17. BEBAN LAIN-LAIN' => ['E31-01', 'E31-02', 'E31-03']
    ];

    public function index(Request $request)
    {
        $year = $request->year ?? date('Y');
        $previousYear = $year - 1;

        // Use exact same logic as TrialBalanceReportController
        $items = TrialBalance::orderBy('id')->get();

        $baseSaldo = [];
        foreach ($items as $it) {
            $baseSaldo[$it->id] = $it->tahun_2024 ?? 0;
        }

        $startBaseYear = 2025;
        $openingBalance = [];

        if ($previousYear >= $startBaseYear) {
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

            $prevQuery = $debitPrev->unionAll($creditPrev)->get()->groupBy('account_id');

            foreach ($items as $it) {
                $rows = $prevQuery[$it->id] ?? collect();
                $debit = $rows->sum('debit_amount');
                $credit = $rows->sum('credit_amount');
                $openingBalance[$it->id] = ($baseSaldo[$it->id] ?? 0) + ($debit - $credit);
            }
        } else {
            foreach ($items as $it) {
                $openingBalance[$it->id] = $baseSaldo[$it->id] ?? 0;
            }
        }

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

        $journalMonthly = $debits->unionAll($credits)->get()->groupBy('account_id');

        $data = [];
        foreach ($items as $item) {
            $saldo = $openingBalance[$item->id] ?? 0;
            $row = [];

            for ($m = 1; $m <= 12; $m++) {
                $trx = $journalMonthly[$item->id] ?? collect();
                $debit = $trx->where('month', $m)->sum('debit_amount');
                $credit = $trx->where('month', $m)->sum('credit_amount');
                $saldo = $saldo + $debit - $credit;
                $row["month_$m"] = $saldo;
            }

            $row['total'] = $saldo;
            $row['opening'] = $openingBalance[$item->id] ?? 0;
            $data[$item->id] = $row;
        }

        // Apply custom calculation rules (same as TrialBalanceReportController)
        $c2101 = $items->where('kode', 'C21-01')->first();
        $c2102 = $items->where('kode', 'C21-02')->first();
        $c2199 = $items->where('kode', 'C21-99')->first();

        if ($c2101 && $c2102 && $c2199) {
            $originalC2101Opening = $data[$c2101->id]['opening'];
            $originalC2102Opening = $data[$c2102->id]['opening'];
            $originalC2199Opening = $data[$c2199->id]['opening'];
            
            $c2101OpeningSum = $originalC2101Opening + $originalC2102Opening + $originalC2199Opening;
            $data[$c2101->id]['opening'] = $c2101OpeningSum;
            $data[$c2199->id]['opening'] = $originalC2199Opening;
            $data[$c2102->id]['opening'] = 0;
            
            $c2199Changes = [];
            
            for ($m = 1; $m <= 12; $m++) {
                $data[$c2199->id]["month_$m"] = 0;
                
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
                
                $c2199Changes[$m] = $hasTransactionInHigherAccounts ? $monthlyChange : 0;
                
                if ($hasTransactionInHigherAccounts) {
                    $data[$c2199->id]["month_$m"] = $monthlyChange;
                }
            }
            
            for ($m = 1; $m <= 12; $m++) {
                if ($m == 1) {
                    $data[$c2101->id]["month_$m"] = $c2101OpeningSum + $c2199Changes[$m];
                } else {
                    $prevMonth = $m - 1;
                    $data[$c2101->id]["month_$m"] = $data[$c2101->id]["month_$prevMonth"] + $c2199Changes[$m];
                }
            }

            $data[$c2101->id]['total'] = $data[$c2101->id]['month_12'];
            $data[$c2199->id]['total'] = array_sum($c2199Changes);
        }

        return view('notes_to_financial_statements.index', compact('items', 'data', 'year'));
    }
}