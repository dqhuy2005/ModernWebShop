<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Repository\impl\CartRepository;
use App\Models\Product;
use App\Models\Category;
use App\Models\Order;
use App\Models\ProductReview;
use App\Observers\ProductObserver;
use App\Observers\CategoryObserver;
use App\Observers\OrderObserver;
use App\Observers\ProductReviewObserver;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;

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
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }

        try {
            Product::observe(ProductObserver::class);
            Category::observe(CategoryObserver::class);
            Order::observe(OrderObserver::class);
            ProductReview::observe(ProductReviewObserver::class);
        } catch (\Exception $e) {
            Log::warning('Failed to register observers: ' . $e->getMessage());
        }

        View::composer('*', function ($view) {
            try {
                if (Auth::check()) {
                    $cartRepository = app(CartRepository::class);
                    $cartCount = $cartRepository->findByUser(Auth::id())->count();
                } else {
                    $cart = Session::get('cart', []);
                    $cartCount = count($cart);
                }

                Session::put('cart_count', $cartCount);
                $view->with('cartCount', $cartCount);
            } catch (\Exception $e) {
                // Fallback if database is unavailable
                $view->with('cartCount', 0);
            }
        });
    }
}
