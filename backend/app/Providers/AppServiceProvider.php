<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
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
        View::composer('*', function ($view) {
            if (Auth::check()) {
                $cartRepository = app(CartRepository::class);
                $cartCount = $cartRepository->findByUser(Auth::id())->count();
            } else {
                $cart = Session::get('cart', []);
                $cartCount = count($cart);
            }

            Session::put('cart_count', $cartCount);
            $view->with('cartCount', $cartCount);
        });
    }
}
