<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CMS\AuthController as CMSAuthController;
use App\Http\Controllers\CMS\ProductController;
use App\Http\Controllers\CMS\UserController;
use App\Http\Controllers\CMS\OrderController;

Route::get('/', function () {
    return view('user.home');
})->name('home');

Route::get('/hot-deals', function () {
    return view('user.hot-deals');
})->name('hot-deals');

Route::get('/categories/{slug}', function ($slug) {
    return view('user.category', compact('slug'));
})->name('categories.show');

Route::get('/products/search', function () {
    return redirect()->route('home');
})->name('products.search');

Route::get('/cart', function () {
    return view('user.cart');
})->name('cart.index');

Route::get('/wishlist', function () {
    return view('user.wishlist');
})->name('wishlist.index');

Route::post('/newsletter/subscribe', function () {
    return redirect()->back()->with('success', 'Successfully subscribed to newsletter!');
})->name('newsletter.subscribe');

Route::middleware('guest')->group(function () {
    Route::get('login', [CMSAuthController::class, 'showLoginForm'])->name('login');
    Route::post('login', [CMSAuthController::class, 'login'])->name('login.post');
    Route::get('register', [CMSAuthController::class, 'showRegisterForm'])->name('register');
    Route::post('register', [CMSAuthController::class, 'register'])->name('register.post');
    Route::post('logout', [CMSAuthController::class, 'logout'])->name('logout');
});

Route::prefix('admin')->name('admin.')->group(function () {
    Route::middleware(['auth', 'role.restriction'])->group(function () {
        Route::get('dashboard', function () {
            return view('admin.dashboard');
        })->name('dashboard');

        Route::post('logout', [CMSAuthController::class, 'logout'])->name('logout');

        // Products Management
        Route::prefix('products')->name('products.')->group(function () {
            Route::get('/', [ProductController::class, 'index'])->name('index');
            Route::get('create', [ProductController::class, 'create'])->name('create');
            Route::post('/', [ProductController::class, 'store'])->name('store');
            Route::get('{product}', [ProductController::class, 'show'])->name('show');
            Route::get('{product}/edit', [ProductController::class, 'edit'])->name('edit');
            Route::put('{product}', [ProductController::class, 'update'])->name('update');
            Route::delete('{product}', [ProductController::class, 'destroy'])->name('destroy');

            // AJAX routes
            Route::post('{product}/toggle-status', [ProductController::class, 'toggleStatus'])->name('toggle-status');
            Route::post('{product}/toggle-hot', [ProductController::class, 'toggleHot'])->name('toggle-hot');
        });

        // Users Management
        Route::prefix('users')->name('users.')->group(function () {
            Route::get('/', [UserController::class, 'index'])->name('index');
            Route::get('create', [UserController::class, 'create'])->name('create');
            Route::post('/', [UserController::class, 'store'])->name('store');
            Route::get('{user}', [UserController::class, 'show'])->name('show');
            Route::get('{user}/edit', [UserController::class, 'edit'])->name('edit');
            Route::put('{user}', [UserController::class, 'update'])->name('update');
            Route::delete('{user}', [UserController::class, 'destroy'])->name('destroy');

            // AJAX routes
            Route::post('{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('toggle-status');
            Route::post('{user}/restore', [UserController::class, 'restore'])->name('restore');
            Route::post('{user}/force-delete', [UserController::class, 'forceDelete'])->name('force-delete');
        });

        // Orders Management
        Route::prefix('orders')->name('orders.')->group(function () {
            Route::get('/', [OrderController::class, 'index'])->name('index');
            Route::get('{order}', [OrderController::class, 'show'])->name('show');
        });
    });
});

Route::get('password/reset', function () {
    return 'Password reset form';
})->name('password.request');
