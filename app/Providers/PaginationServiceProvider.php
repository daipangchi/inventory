<?php

namespace App\Providers;

use Illuminate\Pagination\BootstrapFourPresenter;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;

class PaginationServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->register();
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        Paginator::presenter(function($paginator)
        {
            return new BootstrapFourPresenter($paginator);
        });
    }
}
