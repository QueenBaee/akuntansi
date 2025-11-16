<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

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
        //
    }
}