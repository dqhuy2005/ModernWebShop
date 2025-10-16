<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CMS\AuthController as CMSAuthController;
use App\Http\Controllers\CMS\ProductController;

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

    Route::resource('products', ProductController::class);
});

Route::get('password/reset', function () {
    return 'Password reset form';
})->name('password.request');
