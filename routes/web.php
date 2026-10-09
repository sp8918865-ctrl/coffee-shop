<?php

use App\Http\Controllers\Admin\BahanController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\DiningTableController;
use App\Http\Controllers\Admin\MenuController;
use App\Http\Controllers\Admin\PesananController;
use App\Http\Controllers\Admin\PublicQrController;
use App\Http\Controllers\Admin\StafController;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\PublicOrderController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

Route::get('/', [CatalogController::class, 'index'])->name('catalog.index');
Route::post('/cart/items/{product}', [PublicOrderController::class, 'addToCart'])->whereNumber('product')->name('cart.add');
Route::patch('/cart/items/{product}', [PublicOrderController::class, 'updateCart'])->whereNumber('product')->name('cart.update');
Route::delete('/cart/items/{product}', [PublicOrderController::class, 'removeFromCart'])->whereNumber('product')->name('cart.remove');
Route::get('/checkout', [PublicOrderController::class, 'checkout'])->name('checkout');
Route::post('/checkout', [PublicOrderController::class, 'placeOrder'])->middleware('throttle:10,1')->name('checkout.place');
Route::get('/orders/track/{token}', [PublicOrderController::class, 'track'])
    ->where('token', '[A-Za-z0-9]{64}')
    ->name('orders.track');
Route::get('/orders', [PublicOrderController::class, 'orders'])->name('orders.index');

Route::get('/login', function () {
    return view('login.login');
})->name('login');

Route::post('/login/demo', function (Request $request) {
    abort_unless(app()->environment('local'), 404);

    $demoUser = \App\Models\User::whereIn('role', ['admin', 'owner'])
        ->where('email', 'demo-admin@localhost')
        ->first();

    if (! $demoUser) {
        $demoUser = \App\Models\User::create([
            'name' => 'Admin Demo',
            'email' => 'demo-admin@localhost',
            'password_hash' => Str::random(64),
            'role' => 'owner',
        ]);
    }

    Auth::login($demoUser);
    $request->session()->regenerate();

    return redirect()->intended(route('admin.dashboard'));
})->middleware('throttle:5,1')->name('login.demo');

Route::post('/login', function (Request $request) {
    $credentials = $request->validate([
        'email' => ['required', 'email'],
        'password' => ['required', 'string'],
    ]);

    if (! Auth::attempt($credentials)) {
        return back()->withErrors(['email' => 'Email atau kata sandi tidak sesuai.'])->onlyInput('email');
    }

    $request->session()->regenerate();

    if (! in_array($request->user()->role, ['admin', 'owner'], true)) {
        return redirect()->route('catalog.index')
            ->with('success', 'Anda berhasil masuk. Dashboard tersedia untuk admin atau owner.');
    }

    return redirect()->intended(route('admin.dashboard'));
})->name('login.submit');

Route::post('/logout', function (Request $request) {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect()->route('login');
})->middleware('auth')->name('logout');

Route::middleware(['auth', 'manager', 'desktop'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', fn () => redirect()->route('admin.dashboard'));
    Route::get('/qr-menu', [PublicQrController::class, 'index'])->name('qr-menu');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/menu', [MenuController::class, 'index'])->name('menu');
    Route::get('/meja', [DiningTableController::class, 'index'])->name('tables');
    Route::get('/meja/status', [DiningTableController::class, 'status'])->name('tables.status');
    Route::post('/meja', [DiningTableController::class, 'store'])->name('tables.store');
    Route::put('/meja/{table}', [DiningTableController::class, 'update'])->name('tables.update');
    Route::delete('/meja/{table}', [DiningTableController::class, 'destroy'])->name('tables.destroy');
    Route::post('/menu', [MenuController::class, 'store'])->name('menu.store');
    Route::put('/menu/{product}', [MenuController::class, 'update'])->name('menu.update');
    Route::delete('/menu/{product}', [MenuController::class, 'destroy'])->name('menu.destroy');
    Route::post('/kategori', [MenuController::class, 'storeCategory'])->name('categories.store');
    Route::delete('/kategori/{category}', [MenuController::class, 'destroyCategory'])->name('categories.destroy');
    Route::get('/pesanan', [PesananController::class, 'index'])->name('pesanan');
    Route::post('/pesanan', [PesananController::class, 'store'])->name('pesanan.store');
    Route::patch('/pesanan/{order}/status', [PesananController::class, 'updateStatus'])->name('pesanan.status');
    Route::delete('/pesanan/{order}', [PesananController::class, 'destroy'])->name('pesanan.destroy');
    Route::get('/staf', [StafController::class, 'index'])->name('staf');
    Route::post('/staf', [StafController::class, 'store'])->name('staf.store');
    Route::put('/staf/{user}', [StafController::class, 'update'])->name('staf.update');
    Route::delete('/staf/{user}', [StafController::class, 'destroy'])->name('staf.destroy');
    Route::get('/bahan', [BahanController::class, 'index'])->name('bahan');
    Route::post('/bahan', [BahanController::class, 'store'])->name('bahan.store');
    Route::put('/bahan/{ingredient}', [BahanController::class, 'update'])->name('bahan.update');
    Route::delete('/bahan/{ingredient}', [BahanController::class, 'destroy'])->name('bahan.destroy');
});
