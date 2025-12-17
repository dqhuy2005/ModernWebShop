<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CMS\AuthController as CMSAuthController;
use App\Http\Controllers\CMS\DashboardController;
use App\Http\Controllers\CMS\ProductController;
use App\Http\Controllers\CMS\CategoryController;
use App\Http\Controllers\CMS\UserController;
use App\Http\Controllers\CMS\OrderController;
use App\Http\Controllers\CMS\FileManagerController;
use App\Http\Controllers\User\HomeController;
use App\Http\Controllers\User\CartController;
use App\Http\Controllers\User\CheckoutController;
use App\Http\Controllers\User\ProfileController;
use App\Http\Controllers\User\PurchaseController;
use App\Http\Controllers\User\ProductController as UserProductController;
use App\Http\Controllers\ReviewController;

// Health check endpoint for Render (no database required)
Route::get('/health', function () {
    return response()->json([
        'status' => 'ok',
        'app' => config('app.name'),
        'env' => config('app.env'),
        'time' => now()->toIso8601String(),
    ]);
})->name('health');

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/hot-deals', [HomeController::class, 'hotDeals'])->name('hot-deals');
Route::get('/hot-products', [UserProductController::class, 'hotProducts'])->name('products.hot');
Route::get('/danh-muc/{slug}.html', [HomeController::class, 'showCategory'])->name('categories.show');
Route::get('/products/{slug}.html', [UserProductController::class, 'show'])->name('products.show');

Route::get('/search', [HomeController::class, 'search'])->name('products.search');

// Search History API Routes với Rate Limiting (60 requests/minute)
Route::middleware(['throttle:60,1'])->group(function () {
    Route::get('/api/search-history', [HomeController::class, 'getSearchHistory'])->name('api.search-history.index');
    Route::get('/api/search-history/popular', [HomeController::class, 'getPopularKeywords'])->name('api.search-history.popular');
    Route::delete('/api/search-history/clear', [HomeController::class, 'clearSearchHistory'])->name('api.search-history.clear');
    Route::delete('/api/search-history/{id}', [HomeController::class, 'deleteSearchHistory'])->name('api.search-history.delete');
});

Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
Route::post('/cart/update', [CartController::class, 'update'])->name('cart.update');
Route::post('/cart/remove', [CartController::class, 'remove'])->name('cart.remove');
Route::delete('/cart/clear', [CartController::class, 'clear'])->name('cart.clear');

Route::middleware('auth')->group(function () {
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout/process', [CheckoutController::class, 'process'])->name('checkout.process');
    Route::get('/checkout/success/{orderId}', [CheckoutController::class, 'success'])->name('checkout.success');

    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::post('/profile/update', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/change-password', [ProfileController::class, 'changePassword'])->name('profile.change-password');

    Route::get('/purchase', [PurchaseController::class, 'index'])->name('purchase.index');
    Route::get('/purchase/{orderId}', [PurchaseController::class, 'show'])->name('purchase.show');
    Route::post('/purchase/{orderId}/cancel', [PurchaseController::class, 'cancel'])->name('purchase.cancel');

    Route::get('/orders/{order}/products/{product}/review', [ReviewController::class, 'create'])->name('reviews.create');
    Route::post('/reviews', [ReviewController::class, 'store'])->name('reviews.store');
    Route::get('/orders/{order}/products/{product}/review/edit', [ReviewController::class, 'edit'])->name('reviews.edit');
    Route::put('/reviews/{review}', [ReviewController::class, 'update'])->name('reviews.update');
    Route::delete('/reviews/{review}', [ReviewController::class, 'destroy'])->name('reviews.destroy');
});

Route::get('/wishlist', function () {
    return redirect()->route('home');
})->name('wishlist.index');

Route::post('/newsletter/subscribe', function () {
    return redirect()->back()->with('success', 'Successfully subscribed to newsletter!');
})->name('newsletter.subscribe');

Route::get('/products/{product}/reviews', [ReviewController::class, 'index'])->name('products.reviews');

Route::middleware('guest')->group(function () {
    Route::get('login', function () {
        return view('user.auth.login');
    })->name('login');
    Route::post('login', [CMSAuthController::class, 'login'])->name('login.post');

    Route::get('auth/google', [CMSAuthController::class, 'redirectToGoogle'])->name('auth.google');
    Route::get('auth/google/callback', [CMSAuthController::class, 'handleGoogleCallback'])->name('auth.google.callback');

    Route::get('register', function () {
        return view('user.auth.register');
    })->name('register');
    Route::post('register', [CMSAuthController::class, 'register'])->name('register.post');
});

Route::post('logout', [CMSAuthController::class, 'logout'])->name('logout');

Route::get('password/reset', function () {
    return 'Password reset form';
})->name('password.request');

Route::prefix('admin')->middleware(['auth', 'admin.access'])->group(function () {
    Route::group(['prefix' => 'filemanager'], function () {
        \UniSharp\LaravelFilemanager\Lfm::routes();
    });
});

/* ADMIN ROUTES */
Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin.access'])->group(function () {
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard.index');

    Route::post('logout', [CMSAuthController::class, 'logout'])->name('logout');

    Route::get('file-manager', [FileManagerController::class, 'index'])->name('filemanager.index');

    Route::prefix('products')->name('products.')->group(function () {
        Route::get('/', [ProductController::class, 'index'])->name('index');
        Route::get('create', [ProductController::class, 'create'])->name('create');
        Route::post('/', [ProductController::class, 'store'])->name('store');
        Route::get('{product}', [ProductController::class, 'show'])->name('show');
        Route::get('{product}/edit', [ProductController::class, 'edit'])->name('edit');
        Route::put('{product}', [ProductController::class, 'update'])->name('update');
        Route::delete('{product}', [ProductController::class, 'destroy'])->name('destroy');

        Route::post('{product}/toggle-status', [ProductController::class, 'toggleStatus'])->name('toggle-status');
        Route::post('{product}/toggle-hot', [ProductController::class, 'toggleHot'])->name('toggle-hot');
        Route::delete('{product}/images/{image}', [ProductController::class, 'deleteImage'])->name('images.delete');
    });

    Route::prefix('categories')->name('categories.')->group(function () {
        Route::get('/', [CategoryController::class, 'index'])->name('index');
        Route::get('create', [CategoryController::class, 'create'])->name('create');
        Route::post('/', [CategoryController::class, 'store'])->name('store');
        Route::get('{category}', [CategoryController::class, 'show'])->name('show');
        Route::get('{category}/edit', [CategoryController::class, 'edit'])->name('edit');
        Route::put('{category}', [CategoryController::class, 'update'])->name('update');
        Route::delete('{category}', [CategoryController::class, 'destroy'])->name('destroy');

        Route::post('{id}/restore', [CategoryController::class, 'restore'])->name('restore');
        Route::post('{id}/force-delete', [CategoryController::class, 'forceDelete'])->name('force-delete');
    });

    Route::prefix('users')->name('users.')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('index');
        Route::get('create', [UserController::class, 'create'])->name('create');

        Route::get('export', [UserController::class, 'export'])->name('export');
        Route::get('import-template', [UserController::class, 'downloadTemplate'])->name('import-template');
        Route::post('import', [UserController::class, 'import'])->name('import');

        Route::post('/', [UserController::class, 'store'])->name('store');
        Route::get('{user}', [UserController::class, 'show'])->name('show');
        Route::get('{user}/edit', [UserController::class, 'edit'])->name('edit');
        Route::put('{user}', [UserController::class, 'update'])->name('update');
        Route::delete('{user}', [UserController::class, 'destroy'])->name('destroy');

        Route::post('{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('toggle-status');
        Route::post('{user}/restore', [UserController::class, 'restore'])->name('restore');
        Route::post('{user}/force-delete', [UserController::class, 'forceDelete'])->name('force-delete');
    });

    Route::prefix('orders')->name('orders.')->group(function () {
        Route::get('/', [OrderController::class, 'index'])->name('index');
        Route::get('create', [OrderController::class, 'create'])->name('create');
        Route::get('search-customers', [OrderController::class, 'searchCustomers'])->name('search-customers');
        Route::get('export', [OrderController::class, 'export'])->name('export');

        Route::post('/', [OrderController::class, 'store'])->name('store');
        Route::get('{order}', [OrderController::class, 'show'])->name('show');
        Route::get('{order}/edit', [OrderController::class, 'edit'])->name('edit');
        Route::put('{order}', [OrderController::class, 'update'])->name('update');
        Route::delete('{order}', [OrderController::class, 'destroy'])->name('destroy');
        Route::post('{order}/restore', [OrderController::class, 'restore'])->name('restore');
    });
});
