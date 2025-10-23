<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repository\CartRepository;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(CartRepository::class, function ($app) {
            return new CartRepository($app);
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
