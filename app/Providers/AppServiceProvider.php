<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use URL;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
        public function register(): void
        {
             if ($this->app->environment('local', 'testing')) {
                $this->app->register(\Spatie\LaravelIgnition\IgnitionServiceProvider::class);
            }
        }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Schema::defaultStringLength(191);
        URL::forceScheme('https');
    }
}
