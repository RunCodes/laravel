<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\SqlValidator;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(SqlValidator::class, function ($app) {
            return new SqlValidator();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
