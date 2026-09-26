@extends('layouts.app')

@section('title', 'Beranda - Coffee Shop')

@section('content')
<div class="p-5 mb-4 bg-white rounded-3 shadow-sm">
    <div class="container-fluid py-3">
        <h1 class="display-5 fw-bold">Selamat Datang di Coffee Shop</h1>
        <p class="col-md-8 fs-4">Aplikasi sistem informasi penjualan dan manajemen warkop/coffee shop.</p>
        <a class="btn btn-primary btn-lg" href="/healthcheck" role="button">Cek Status Koneksi Database</a>
    </div>
</div>
@endsection