<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Pagination\Paginator;
use App\Http\View\Composers\MenuComposer;
use App\View\Composers\LedgerMenuComposer;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(\App\Services\JournalService::class, function ($app) {
            return new \App\Services\JournalService();
        });
        
        $this->app->bind(\App\Services\TransactionService::class, function ($app) {
            return new \App\Services\TransactionService($app->make(\App\Services\JournalService::class));
        });
    }

    public function boot(): void
    {
        View::composer('layouts.app', MenuComposer::class);
        View::composer(['layouts.app', 'dashboard.*'], LedgerMenuComposer::class);
        Paginator::defaultView('pagination.tabler');
    }
}