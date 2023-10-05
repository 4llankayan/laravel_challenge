<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ShoppingListController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::controller(AuthController::class)->group(function () {
    Route::prefix('/auth')->group(function () {
        Route::post('login', 'login');
        Route::post('register', 'register');
    });
});

Route::middleware(['auth:api'])->controller(AuthController::class)->group(function () {
    Route::prefix('/auth')->group(function () {
        Route::post('logout', 'logout');
    });
});

Route::middleware(['auth:api'])->controller(ProductController::class)->group(function () {
    Route::prefix('/products')->group(function () {
        Route::get('/', 'index');
        Route::post('/', 'store');
        Route::get('/{id}', 'show');
        Route::put('/{id}', 'update');
        Route::delete('/{id}', 'destroy');
    });
});

Route::middleware(['auth:api'])->controller(ShoppingListController::class)->group(function () {
    Route::prefix('/shopping_lists')->group(function () {
        Route::get('/', 'index');
        Route::post('/', 'store');
        Route::get('/{id}', 'show');
        Route::post('/{id}/checkout', 'checkout');

        Route::prefix('/{id}/products')->group(function () {
            Route::post('/', 'addProduct');
            Route::delete('/', 'removeProduct');
        });
    });
});
