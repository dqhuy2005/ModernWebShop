<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\HomeController;
use App\Http\Controllers\Api\CategoryController;

Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
    Route::post('refresh', [AuthController::class, 'refresh']);
    Route::post('logout', [AuthController::class, 'logout']);
});

Route::prefix('categories')->group(function () {
    Route::get('/', [CategoryController::class, 'index']);
    Route::get('/parent', [CategoryController::class, 'getParentCategories']);
    Route::get('/child', [CategoryController::class, 'getChildCategories']);
    Route::get('/{id}', [CategoryController::class, 'show']);
});

Route::get('/home', [HomeController::class, 'index']);

Route::middleware('auth:api')->group(function () {
    Route::prefix('auth')->group(function () {
        Route::get('me', [AuthController::class, 'me']);
        Route::post('revoke-all', [AuthController::class, 'revokeAllTokens']);
    });

    Route::prefix("/admin")->group(function () {
        Route::prefix('products')->group(function () {
            Route::post('/create', [ProductController::class, 'store']);
            Route::put('/{id}/update', [ProductController::class, 'update']);
            Route::delete('/{id}/delete', [ProductController::class, 'destroy']);
            Route::patch('/{id}/toggle-hot', [ProductController::class, 'toggleHotStatus']);

            Route::get('/', [ProductController::class, 'index']);
            Route::get('/hot', [ProductController::class, 'getHotProducts']);
            Route::get('/most-viewed', [ProductController::class, 'getMostViewed']);
            Route::get('/category/{categoryId}', [ProductController::class, 'getByCategory']);
            Route::get('/{id}', [ProductController::class, 'show']);
        });

        Route::prefix('categories')->group(function () {
            Route::post('/create', [CategoryController::class, 'store']);
            Route::put('/{id}/update', [CategoryController::class, 'update']);
            Route::delete('/{id}/delete', [CategoryController::class, 'destroy']);
        });
    });

    Route::get('/user', function (Request $request) {
        return $request->user();
    });
});
