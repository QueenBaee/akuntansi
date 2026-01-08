<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TrialBalance;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

class TrialBalanceReportController extends Controller
{
    private const BASE_YEAR = 2024;
    private const START_BASE_YEAR = 2025;
    private const MONTHS_IN_YEAR = 12;
    private const MEMORY_LIMIT = '512M';
    private const TIME_LIMIT = 300;

    public function index(Request $request)
    {
        $this->setResourceLimits();
        
        $year = $request->year ?? date('Y');
        $previousYear = $year - 1;
        
        $items = TrialBalance::orderBy('id')->get();
        $baseSaldo = $this->extractBaseSaldo($items);
        
        $openingBalance = $this->calculateOpeningBalance($items, $baseSaldo, $previousYear);
        $journalMonthly = $this->getMonthlyJournalMutations($year);
        
        $data = $this->calculateMonthlyBalances($items, $openingBalance, $journalMonthly);
        $data = $this->applyC21CustomRules($items, $data, $journalMonthly, $year);
        
        return view('trial_balance_report.index', compact('items', 'data', 'year'));
    }

    private function setResourceLimits(): void
    {
        set_time_limit(self::TIME_LIMIT);
        ini_set('memory_limit', self::MEMORY_LIMIT);
    }

    private function extractBaseSaldo(Collection $items): array
    {
        return $items->mapWithKeys(fn($item) => [$item->id => $item->tahun_2024 ?? 0])->toArray();
    }

    private function calculateOpeningBalance(Collection $items, array $baseSaldo, int $previousYear): array
    {
        if ($previousYear < self::START_BASE_YEAR) {
            return $baseSaldo;
        }

        $mutations = $this->getYearRangeMutations(self::START_BASE_YEAR, $previousYear);
        
        return $items->mapWithKeys(function ($item) use ($baseSaldo, $mutations) {
            $rows = $mutations[$item->id] ?? collect();
            $netMutation = $rows->sum('debit_amount') - $rows->sum('credit_amount');
            
            return [$item->id => ($baseSaldo[$item->id] ?? 0) + $netMutation];
        })->toArray();
    }

    private function getYearRangeMutations(int $startYear, int $endYear): Collection
    {
        $debitQuery = $this->buildMutationQuery('debit_account_id', 'total_debit', $startYear, $endYear, true);
        $creditQuery = $this->buildMutationQuery('credit_account_id', 'total_credit', $startYear, $endYear, false);
        
        return $debitQuery->unionAll($creditQuery)->get()->groupBy('account_id');
    }

    private function getMonthlyJournalMutations(int $year): Collection
    {
        $debitQuery = $this->buildMonthlyMutationQuery('debit_account_id', 'total_debit', $year, true);
        $creditQuery = $this->buildMonthlyMutationQuery('credit_account_id', 'total_credit', $year, false);
        
        return $debitQuery->unionAll($creditQuery)->get()->groupBy('account_id');
    }

    private function buildMutationQuery(string $accountField, string $amountField, int $startYear, int $endYear, bool $isDebit)
    {
        return DB::table('journals')
            ->select([
                DB::raw("$accountField AS account_id"),
                DB::raw($isDebit ? "SUM($amountField) AS debit_amount" : "0 AS debit_amount"),
                DB::raw($isDebit ? "0 AS credit_amount" : "SUM($amountField) AS credit_amount")
            ])
            ->whereYear('date', '>=', $startYear)
            ->whereYear('date', '<=', $endYear)
            ->whereNull('deleted_at')
            ->groupBy('account_id');
    }

    private function buildMonthlyMutationQuery(string $accountField, string $amountField, int $year, bool $isDebit)
    {
        return DB::table('journals')
            ->select([
                DB::raw("$accountField AS account_id"),
                DB::raw("MONTH(date) AS month"),
                DB::raw($isDebit ? "SUM($amountField) AS debit_amount" : "0 AS debit_amount"),
                DB::raw($isDebit ? "0 AS credit_amount" : "SUM($amountField) AS credit_amount")
            ])
            ->whereYear('date', $year)
            ->whereNull('deleted_at')
            ->groupBy('account_id', 'month');
    }

    private function calculateMonthlyBalances(Collection $items, array $openingBalance, Collection $journalMonthly): array
    {
        return $items->mapWithKeys(function ($item) use ($openingBalance, $journalMonthly) {
            $isRevenueExpense = $this->isRevenueOrExpense($item->kode);
            $transactions = $journalMonthly[$item->id] ?? collect();
            
            $row = $this->processMonthlyTransactions(
                $transactions,
                $openingBalance[$item->id] ?? 0,
                $isRevenueExpense
            );
            
            $row['opening'] = $openingBalance[$item->id] ?? 0;
            
            return [$item->id => $row];
        })->toArray();
    }

    private function processMonthlyTransactions(Collection $transactions, float $opening, bool $isRevenueExpense): array
    {
        $row = [];
        $runningBalance = $opening;
        $totalMutasi = 0;

        for ($month = 1; $month <= self::MONTHS_IN_YEAR; $month++) {
            $netMutation = $this->calculateNetMutation($transactions, $month);
            
            if ($isRevenueExpense) {
                $row["month_$month"] = $netMutation;
                $totalMutasi += $netMutation;
            } else {
                $runningBalance += $netMutation;
                $row["month_$month"] = $runningBalance;
            }
        }

        $row['total'] = $isRevenueExpense ? $totalMutasi : $runningBalance;
        
        return $row;
    }

    private function calculateNetMutation(Collection $transactions, int $month): float
    {
        $monthTransactions = $transactions->where('month', $month);
        return $monthTransactions->sum('debit_amount') - $monthTransactions->sum('credit_amount');
    }

    private function isRevenueOrExpense(string $code): bool
    {
        return str_starts_with($code, 'R') || str_starts_with($code, 'E');
    }

    private function applyC21CustomRules(Collection $items, array $data, Collection $journalMonthly, int $year): array
    {
        $c21Accounts = $this->getC21Accounts($items);
        
        if (!$this->hasAllC21Accounts($c21Accounts)) {
            return $data;
        }

        $c2199Opening = $this->calculateC2199Opening($items, $year);
        $c2101Opening = $this->calculateC2101Opening($items, $year, $c2199Opening);

        $c2102Monthly = $this->getAccountMonthlyMutation($c21Accounts['c2102']->id, $journalMonthly);
        $c2199Monthly = $this->getC2199MonthlyMutation($items, $journalMonthly);

        $data = $this->applyC2101Rules($c21Accounts['c2101'], $data, $c2101Opening, $c2199Opening, $c2199Monthly);
        $data = $this->applyC2102Rules($c21Accounts['c2102'], $data, $c2102Monthly);
        $data = $this->applyC2199Rules($c21Accounts['c2199'], $data, $c2199Opening, $c2199Monthly);

        return $data;
    }

    private function getC21Accounts(Collection $items): array
    {
        return [
            'c2101' => $items->where('kode', 'C21-01')->first(),
            'c2102' => $items->where('kode', 'C21-02')->first(),
            'c2199' => $items->where('kode', 'C21-99')->first(),
        ];
    }

    private function hasAllC21Accounts(array $accounts): bool
    {
        return $accounts['c2101'] && $accounts['c2102'] && $accounts['c2199'];
    }

    private function calculateC2199Opening(Collection $items, int $year): float
    {
        if ($year <= self::BASE_YEAR) {
            return 0;
        }

        $c2199 = $items->where('kode', 'C21-99')->first();
        $base2024 = $c2199?->tahun_2024 ?? 0;

        if ($year == self::START_BASE_YEAR) {
            return $base2024;
        }

        $revenueExpenseIds = $this->getRevenueExpenseIds($items);
        
        if ($revenueExpenseIds->isEmpty()) {
            return $base2024;
        }

        $netMutation = $this->calculateRevenueExpenseMutation($revenueExpenseIds, self::START_BASE_YEAR, $year - 1);
        
        return $netMutation;
    }

    private function calculateC2101Opening(Collection $items, int $year, float $c2199Opening): float
    {
        if ($year <= self::BASE_YEAR) {
            return 0;
        }

        $c2101 = $items->where('kode', 'C21-01')->first();
        $c2199 = $items->where('kode', 'C21-99')->first();
        
        $base2024C2101 = $c2101?->tahun_2024 ?? 0;
        $base2024C2199 = $c2199?->tahun_2024 ?? 0;

        if ($year == self::START_BASE_YEAR) {
            return $base2024C2101;
        }

        $revenueExpenseIds = $this->getRevenueExpenseIds($items);
        
        if ($revenueExpenseIds->isEmpty()) {
            return $base2024C2101;
        }

        $netMutation = $this->calculateRevenueExpenseMutation($revenueExpenseIds, self::START_BASE_YEAR, $year - 1);
        
        return $base2024C2101 + $base2024C2199 + $netMutation - $c2199Opening;
    }

    private function getRevenueExpenseIds(Collection $items): Collection
    {
        return $items->filter(fn($item) => $this->isRevenueOrExpense($item->kode))->pluck('id');
    }

    private function calculateRevenueExpenseMutation(Collection $accountIds, int $startYear, int $endYear): float
    {
        $debit = DB::table('journals')
            ->whereIn('debit_account_id', $accountIds)
            ->whereYear('date', '>=', $startYear)
            ->whereYear('date', '<=', $endYear)
            ->whereNull('deleted_at')
            ->sum('total_debit');

        $credit = DB::table('journals')
            ->whereIn('credit_account_id', $accountIds)
            ->whereYear('date', '>=', $startYear)
            ->whereYear('date', '<=', $endYear)
            ->whereNull('deleted_at')
            ->sum('total_credit');

        return $debit - $credit;
    }

    private function getAccountMonthlyMutation(int $accountId, Collection $journalMonthly): array
    {
        $transactions = $journalMonthly[$accountId] ?? collect();
        $monthly = [];

        for ($month = 1; $month <= self::MONTHS_IN_YEAR; $month++) {
            $monthly[$month] = $this->calculateNetMutation($transactions, $month);
        }

        return $monthly;
    }

    private function getC2199MonthlyMutation(Collection $items, Collection $journalMonthly): array
    {
        $revenueExpenseItems = $items->filter(fn($item) => $this->isRevenueOrExpense($item->kode));
        $monthly = array_fill(1, self::MONTHS_IN_YEAR, 0);

        foreach ($revenueExpenseItems as $item) {
            $transactions = $journalMonthly[$item->id] ?? collect();
            
            for ($month = 1; $month <= self::MONTHS_IN_YEAR; $month++) {
                $monthly[$month] += $this->calculateNetMutation($transactions, $month);
            }
        }

        return $monthly;
    }

    private function applyC2101Rules($account, array $data, float $opening, float $c2199Opening, array $c2199Monthly): array
    {
        $data[$account->id]['opening'] = $opening;

        for ($month = 1; $month <= self::MONTHS_IN_YEAR; $month++) {
            if ($month === 1) {
                $data[$account->id]["month_1"] = $opening + $c2199Opening;
            } else {
                $data[$account->id]["month_$month"] = $data[$account->id]["month_" . ($month - 1)] + $c2199Monthly[$month - 1];
            }
        }

        $data[$account->id]['total'] = $opening + $c2199Opening;

        return $data;
    }

    private function applyC2102Rules($account, array $data, array $monthly): array
    {
        $data[$account->id]['opening'] = 0;

        for ($month = 1; $month <= self::MONTHS_IN_YEAR; $month++) {
            $data[$account->id]["month_$month"] = $monthly[$month];
        }

        $data[$account->id]['total'] = array_sum($monthly);

        return $data;
    }

    private function applyC2199Rules($account, array $data, float $opening, array $monthly): array
    {
        $data[$account->id]['opening'] = $opening;

        for ($month = 1; $month <= self::MONTHS_IN_YEAR; $month++) {
            $data[$account->id]["month_$month"] = $monthly[$month];
        }

        $data[$account->id]['total'] = array_sum($monthly);

        return $data;
    }
}