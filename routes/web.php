<?php

use Illuminate\Support\Facades\Route;

// 1. Tampilkan Halaman Login jika diakses dari '/' atau '/login'
Route::get('/', function () {
    return view('login.login');
})->name('login');

Route::get('/login', function () {
    return view('login.login');
});

// 2. Tampilkan Halaman Dashboard saat Form Disubmit
Route::get('/admin/dashboard', function () {
    return view('admin.dashboard');
})->name('admin.dashboard');

Route::get('/admin/menu', function () {
    return view('admin.menu');
})->name('admin.menu');

Route::get('/admin/pesanan', function () {
    return view('admin.pesanan');
})->name('admin.pesanan');