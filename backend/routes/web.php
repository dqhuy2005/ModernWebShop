<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CMS\AuthController as CMSAuthController;
use App\Http\Controllers\CMS\ProductController;
use App\Http\Controllers\CMS\UserController;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::prefix('admin')->name('admin.')->group(function () {
    Route::middleware('guest')->group(function () {
        Route::get('login', [CMSAuthController::class, 'showLoginForm'])->name('login');
        Route::post('login', [CMSAuthController::class, 'login'])->name('login.post');
        Route::get('register', [CMSAuthController::class, 'showRegisterForm'])->name('register');
        Route::post('register', [CMSAuthController::class, 'register'])->name('register.post');
    });

    Route::middleware('auth')->group(function () {
        Route::get('dashboard', function () {
            return view('admin.dashboard');
        })->name('dashboard');

        Route::post('logout', [CMSAuthController::class, 'logout'])->name('logout');
    });


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
});

Route::get('password/reset', function () {
    return 'Password reset form';
})->name('password.request');
