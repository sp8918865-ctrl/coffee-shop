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
use Illuminate\Support\Facades\URL;

Route::get('/menu/qr-entry', function (Request $request) {
    $request->session()->put('customer_qr_access', true);
    $request->session()->regenerate();

    return redirect()->route('catalog.index');
})->middleware(['signed:relative', 'public.qr'])->name('customer.qr.enter');

Route::get('/', function (Request $request) {
    return redirect()->route('login.admin');
});

Route::middleware(['public.qr', 'customer.qr'])->group(function () {
    Route::get('/menu', [CatalogController::class, 'index'])->name('catalog.index');
    Route::post('/cart/items/{product}', [PublicOrderController::class, 'addToCart'])->whereNumber('product')->name('cart.add');
    Route::patch('/cart/items/{product}', [PublicOrderController::class, 'updateCart'])->whereNumber('product')->name('cart.update');
    Route::delete('/cart/items/{product}', [PublicOrderController::class, 'removeFromCart'])->whereNumber('product')->name('cart.remove');
    Route::get('/checkout', [PublicOrderController::class, 'checkout'])->name('checkout');
    Route::post('/checkout', [PublicOrderController::class, 'placeOrder'])->middleware('throttle:10,1')->name('checkout.place');
    Route::get('/orders/track/{token}', [PublicOrderController::class, 'track'])
        ->where('token', '[A-Za-z0-9]{64}')
        ->name('orders.track');
    Route::get('/orders', [PublicOrderController::class, 'orders'])->name('orders.index');
});

Route::get('/orders/track-entry/{token}', function (Request $request, string $token) {
    $request->session()->put('customer_qr_access', true);
    $request->session()->regenerate();

    return redirect()->route('orders.track', $token);
})->where('token', '[A-Za-z0-9]{64}')
    ->middleware(['signed:relative', 'public.qr'])
    ->name('customer.order.track.enter');

Route::get('/login', function () {
    return view('login.login', ['loginRole' => 'admin']);
})->name('login');

Route::get('/login/admin', function () {
    return view('login.login', ['loginRole' => 'admin']);
})->name('login.admin');

Route::get('/login/barista', function () {
    return view('login.login', ['loginRole' => 'barista']);
})->name('login.barista');

$authenticateRole = function (Request $request, string $guard, array $allowedRoles, string $dashboardRoute) {
    $credentials = $request->validate([
        'username' => ['required', 'string', 'max:255'],
        'password' => ['required', 'string'],
    ]);

    if (! Auth::guard($guard)->attempt($credentials)) {
        return back()->withErrors(['username' => 'Username atau kata sandi tidak sesuai.'])->onlyInput('username');
    }

    if (! in_array(Auth::guard($guard)->user()->role, $allowedRoles, true)) {
        Auth::guard($guard)->logout();
        $request->session()->regenerate();

        return back()->withErrors(['username' => 'Akun ini tidak memiliki akses ke halaman login tersebut.'])->onlyInput('username');
    }

    $request->session()->regenerate();

    return redirect()->route($dashboardRoute);
};

Route::post('/login', fn (Request $request) => $authenticateRole($request, 'web', ['admin', 'owner'], 'admin.dashboard'))
    ->name('login.submit');
Route::post('/login/admin', fn (Request $request) => $authenticateRole($request, 'web', ['admin', 'owner'], 'admin.dashboard'))
    ->name('login.admin.submit');
Route::post('/login/barista', fn (Request $request) => $authenticateRole($request, 'barista', ['barista'], 'barista.dashboard'))
    ->name('login.barista.submit');

Route::post('/logout', function (Request $request) {
    Auth::guard('web')->logout();
    $request->session()->regenerate();

    return redirect()->route('login.admin');
})->middleware('auth:web')->name('logout');

$registerPanelRoutes = function (string $prefix, string $guard, string $accessMiddleware, bool $includeStaff): void {
    Route::middleware(["auth:$guard", $accessMiddleware, 'desktop'])
        ->prefix($prefix)
        ->name("$prefix.")
        ->group(function () use ($prefix, $guard, $includeStaff): void {
            Route::get('/', fn () => redirect()->route("$prefix.dashboard"));
            Route::post('/logout', function (Request $request) use ($guard, $prefix) {
                Auth::guard($guard)->logout();
                $request->session()->regenerate();

                return redirect()->route("login.$prefix");
            })->name('logout');
            Route::get('/qr-menu', [PublicQrController::class, 'index'])->name('qr-menu');
            Route::post('/qr-menu/toggle', [PublicQrController::class, 'toggle'])->name('qr-menu.toggle');
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
            Route::get('/pesanan/{order}/bon', [PesananController::class, 'receipt'])->name('pesanan.receipt');
            Route::post('/pesanan/{order}/bon/pembayaran-tunai', [PesananController::class, 'recordCashPayment'])->name('pesanan.cash-payment');
            Route::post('/pesanan', [PesananController::class, 'store'])->name('pesanan.store');
            Route::patch('/pesanan/{order}/status', [PesananController::class, 'updateStatus'])->name('pesanan.status');
            Route::delete('/pesanan/{order}', [PesananController::class, 'destroy'])->name('pesanan.destroy');
            Route::get('/bahan', [BahanController::class, 'index'])->name('bahan');
            Route::post('/bahan', [BahanController::class, 'store'])->name('bahan.store');
            Route::put('/bahan/{ingredient}', [BahanController::class, 'update'])->name('bahan.update');
            Route::delete('/bahan/{ingredient}', [BahanController::class, 'destroy'])->name('bahan.destroy');

            if ($includeStaff) {
                Route::middleware('manager')->group(function (): void {
                    Route::get('/laporan-penjualan', [DashboardController::class, 'salesReport'])->name('sales-report');
                    Route::get('/staf', [StafController::class, 'index'])->name('staf');
                    Route::post('/staf', [StafController::class, 'store'])->name('staf.store');
                    Route::put('/staf/{user}', [StafController::class, 'update'])->name('staf.update');
                    Route::delete('/staf/{user}', [StafController::class, 'destroy'])->name('staf.destroy');
                });
            }
        });
};

$registerPanelRoutes('admin', 'web', 'admin.panel', true);
$registerPanelRoutes('barista', 'barista', 'barista.panel', false);
