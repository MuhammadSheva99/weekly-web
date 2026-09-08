<?php

namespace App\Providers;

use App\Models\WeeklyCommitment;
use App\Observers\WeeklyCommitmentObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        WeeklyCommitment::observe(WeeklyCommitmentObserver::class);
    }
}