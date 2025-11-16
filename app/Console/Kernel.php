<?php

namespace App\Console;

use App\Jobs\ProcessMonthlyDepreciation;
use App\Jobs\ProcessMonthlyRentIncome;
use Carbon\Carbon;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected $commands = [
        //
    ];

    protected function schedule(Schedule $schedule): void
    {
        // Process monthly depreciation on the 1st of each month at 02:00
        $schedule->call(function () {
            $periodDate = Carbon::now()->startOfMonth();
            ProcessMonthlyDepreciation::dispatch($periodDate);
        })->monthlyOn(1, '02:00')->name('monthly-depreciation');

        // Process monthly rent income on the 1st of each month at 03:00
        $schedule->call(function () {
            $periodDate = Carbon::now()->startOfMonth();
            ProcessMonthlyRentIncome::dispatch($periodDate);
        })->monthlyOn(1, '03:00')->name('monthly-rent-income');

        // Process monthly rent expense amortization on the 1st of each month at 04:00
        $schedule->call(function () {
            $periodDate = Carbon::now()->startOfMonth();
            // ProcessMonthlyRentExpense::dispatch($periodDate);
        })->monthlyOn(1, '04:00')->name('monthly-rent-expense');

        // Backup database daily at 01:00
        $schedule->command('backup:run --only-db')
            ->dailyAt('01:00')
            ->name('daily-database-backup');

        // Clean old logs weekly
        $schedule->command('log:clear')
            ->weekly()
            ->sundays()
            ->at('00:00')
            ->name('weekly-log-cleanup');
    }

    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}