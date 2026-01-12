<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use Illuminate\Pagination\Paginator;

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
        Paginator::useBootstrapFive();

        // Share company profile with all views
        if (!app()->runningInConsole()) {
            $companyProfile = \App\Models\CompanyProfile::first();
            view()->share('companyProfile', $companyProfile);
        }
    }
}
