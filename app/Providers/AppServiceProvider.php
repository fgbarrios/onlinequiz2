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
            // $this->app->register(\NunoMaduro\Collision\Adapters\Laravel\CollisionServiceProvider::class);
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
