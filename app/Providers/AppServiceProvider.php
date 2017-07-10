<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
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
//        if (app()->environment('local')) {
//            \DB::listen(function ($query) {
//                if (str_contains($query->sql, 'select')) \Log::info(preg_replace('/ \?|,/', '', $query->sql));
////                \Log::info(json_encode($query->bindings));
//            });
//        }
    }
}
