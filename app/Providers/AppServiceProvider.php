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
    

public function boot()
{
    Paginator::useBootstrap(); // If you are using Bootstrap for pagination styling
    Paginator::defaultView('pagination::bootstrap-4'); // You can specify the pagination view
    Paginator::defaultSimpleView('pagination::simple-bootstrap-4'); // You can specify the simple pagination view
}

}
