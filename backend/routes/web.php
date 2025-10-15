<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CMS\AuthController as CMSAuthController;

Route::get('/', function () {
    return view('welcome');
});

// CMS Routes
Route::prefix('cms')->name('cms.')->group(function () {
    // Guest routes (not authenticated)
    Route::middleware('guest')->group(function () {
        Route::get('login', [CMSAuthController::class, 'showLoginForm'])->name('login');
        Route::post('login', [CMSAuthController::class, 'login'])->name('login.post');
        Route::get('register', [CMSAuthController::class, 'showRegisterForm'])->name('register');
        Route::post('register', [CMSAuthController::class, 'register'])->name('register.post');
    });

    // Authenticated routes
    Route::middleware('auth')->group(function () {
        Route::get('dashboard', function () {
            return view('cms.dashboard');
        })->name('dashboard');
        
        Route::post('logout', [CMSAuthController::class, 'logout'])->name('logout');
    });
});

// Password reset routes (optional)
Route::get('password/reset', function () {
    return 'Password reset form';
})->name('password.request');
