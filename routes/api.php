<?php

use App\Http\Controllers\Api\V1\AdminController;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\CustomerController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => response()->json([
    'name' => 'Warung Luwak API',
    'version' => 'v1',
    'endpoints' => [
        'v1' => url('/api/v1'),
        'documentation' => url('/API.md'),
    ],
]));

Route::prefix('v1')->name('api.v1.')->group(function () {
    Route::get('/', fn () => response()->json([
        'name' => 'Warung Luwak API',
        'version' => 'v1',
        'endpoints' => [
            'admin_login' => url('/api/v1/auth/login'),
            'customer_qr_access' => url('/api/v1/customer/qr-access'),
            'customer_menu' => url('/api/v1/customer/menu'),
            'admin_dashboard' => url('/api/v1/admin/dashboard'),
        ],
    ]))->name('index');

    Route::post('/auth/login', [AuthController::class, 'login'])
        ->middleware(['desktop', 'throttle:5,1'])
        ->name('auth.login');

    Route::get('/customer/qr-access', [CustomerController::class, 'issueQrToken'])
        ->middleware(['signed:relative', 'throttle:10,1'])
        ->name('customer.qr-access');

    Route::middleware('customer.api')->prefix('customer')->name('customer.')->group(function () {
        Route::post('/auth/logout', [CustomerController::class, 'revokeQrToken'])->name('auth.logout');
        Route::get('/menu', [CustomerController::class, 'menu'])->name('menu');
        Route::get('/tables', [CustomerController::class, 'tables'])->name('tables');
        Route::post('/orders', [CustomerController::class, 'placeOrder'])
            ->middleware('throttle:10,1')
            ->name('orders.store');
        Route::get('/orders/{token}', [CustomerController::class, 'trackOrder'])
            ->where('token', '[A-Za-z0-9]{64}')
            ->name('orders.track');
    });

    Route::middleware(['auth:sanctum', 'manager', 'desktop', 'abilities:admin'])
        ->prefix('admin')
        ->name('admin.')
        ->group(function () {
            Route::get('/auth/me', [AuthController::class, 'me'])->name('auth.me');
            Route::post('/auth/logout', [AuthController::class, 'logout'])->name('auth.logout');
            Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');

            Route::get('/products', [AdminController::class, 'products'])->name('products.index');
            Route::post('/products', [AdminController::class, 'storeProduct'])->name('products.store');
            Route::put('/products/{product}', [AdminController::class, 'updateProduct'])->name('products.update');
            Route::delete('/products/{product}', [AdminController::class, 'deleteProduct'])->name('products.destroy');

            Route::get('/categories', [AdminController::class, 'categories'])->name('categories.index');
            Route::post('/categories', [AdminController::class, 'storeCategory'])->name('categories.store');
            Route::patch('/categories/{category}', [AdminController::class, 'updateCategory'])->name('categories.update');
            Route::delete('/categories/{category}', [AdminController::class, 'deleteCategory'])->name('categories.destroy');

            Route::get('/ingredients', [AdminController::class, 'ingredients'])->name('ingredients.index');
            Route::post('/ingredients', [AdminController::class, 'storeIngredient'])->name('ingredients.store');
            Route::put('/ingredients/{ingredient}', [AdminController::class, 'updateIngredient'])->name('ingredients.update');
            Route::delete('/ingredients/{ingredient}', [AdminController::class, 'deleteIngredient'])->name('ingredients.destroy');

            Route::get('/tables', [AdminController::class, 'tables'])->name('tables.index');
            Route::post('/tables', [AdminController::class, 'storeTable'])->name('tables.store');
            Route::put('/tables/{table}', [AdminController::class, 'updateTable'])->name('tables.update');
            Route::delete('/tables/{table}', [AdminController::class, 'deleteTable'])->name('tables.destroy');

            Route::get('/orders', [AdminController::class, 'orders'])->name('orders.index');
            Route::get('/orders/{order}', [AdminController::class, 'showOrder'])->name('orders.show');
            Route::patch('/orders/{order}/status', [AdminController::class, 'updateOrderStatus'])->name('orders.status');
            Route::delete('/orders/{order}', [AdminController::class, 'deleteOrder'])->name('orders.destroy');
        });
});
