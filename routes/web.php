<?php

use App\Http\Controllers\Admin\BahanController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\MenuController;
use App\Http\Controllers\Admin\PesananController;
use App\Http\Controllers\Admin\StafController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

$allowLocalLoginBypass = app()->environment('local');

Route::get('/', function () {
    return view('login.login');
})->name('login');

Route::get('/login', function () {
    return view('login.login');
});

Route::post('/login', function (Request $request) use ($allowLocalLoginBypass) {
    if ($allowLocalLoginBypass) {
        return redirect()->intended(route('admin.dashboard'));
    }

    $credentials = $request->validate([
        'email' => ['required', 'email'],
        'password' => ['required', 'string'],
    ]);

    if (! Auth::attempt($credentials)) {
        return back()->withErrors(['email' => 'Email atau kata sandi tidak sesuai.'])->onlyInput('email');
    }

    $request->session()->regenerate();

    return redirect()->intended(route('admin.dashboard'));
})->name('login.submit');

Route::post('/logout', function (Request $request) {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect()->route('login');
})->middleware($allowLocalLoginBypass ? [] : ['auth'])->name('logout');

Route::prefix('admin')->name('admin.')->middleware($allowLocalLoginBypass ? [] : ['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/menu', [MenuController::class, 'index'])->name('menu');
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
