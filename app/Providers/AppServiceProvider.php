<?php

namespace App\Providers;

use App\Services\SteganographyService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Daftarkan SteganographyService sebagai singleton
        // (satu instance selama lifetime request)
        $this->app->singleton(SteganographyService::class, function ($app) {
            return new SteganographyService();
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
