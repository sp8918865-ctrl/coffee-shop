@extends('admin.data.layout')

@section('title', 'QR Menu Publik')
@section('heading', 'QR Menu Publik')

@section('content')
@if (session('success'))
    <div role="status" class="mx-auto mb-5 max-w-2xl rounded-lg border border-emerald-500/30 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-200">{{ session('success') }}</div>
@endif
<section class="mx-auto mb-5 flex max-w-2xl flex-col gap-3 rounded-2xl border {{ $isEnabled ? 'border-emerald-500/30' : 'border-rose-500/30' }} bg-[#121824] p-5 sm:flex-row sm:items-center sm:justify-between">
    <div>
        <p class="text-sm font-semibold text-white">Status QR menu publik</p>
        <p class="mt-1 text-xs {{ $isEnabled ? 'text-emerald-300' : 'text-rose-300' }}">{{ $isEnabled ? 'ON · Pelanggan dapat membuka menu melalui QR.' : 'OFF · Akses pelanggan melalui QR sedang ditutup.' }}</p>
    </div>
    <form method="POST" action="{{ \App\Support\PanelRoute::url('admin.qr-menu.toggle') }}">
        @csrf
        <input type="hidden" name="enabled" value="{{ $isEnabled ? '0' : '1' }}">
        <button class="rounded-lg px-4 py-2 text-xs font-bold {{ $isEnabled ? 'bg-rose-500 text-white' : 'bg-emerald-500 text-slate-950' }}">{{ $isEnabled ? 'Matikan QR' : 'Nyalakan QR' }}</button>
    </form>
</section>
<section class="mx-auto max-w-2xl rounded-2xl border border-[#1e293b] bg-[#121824] p-5 text-center sm:p-8">
    <p class="text-sm text-slate-300">Pindai kode ini untuk membuka katalog dan pemesanan publik Warung Luwak.</p>
    <div class="mx-auto mt-6 flex min-h-64 w-full max-w-72 items-center justify-center rounded-2xl bg-white p-4">
        <div id="public-menu-qr" role="img" aria-label="QR code katalog menu publik" data-url="{{ $publicUrl }}"></div>
    </div>
    <p id="qr-error" class="mt-4 hidden text-sm text-rose-300" role="alert">QR belum dapat dibuat. Pastikan perangkat terhubung ke internet lalu muat ulang halaman.</p>
    <p class="mt-5 text-xs font-semibold text-amber-300">Alamat menu publik untuk QR ini:</p>
    <a href="{{ $publicUrl }}" class="mt-2 inline-block break-all text-sm text-slate-200 underline decoration-amber-400 underline-offset-4">{{ $publicUrl }}</a>
    <p class="mt-5 text-xs leading-relaxed text-slate-500">Agar pelanggan dapat membuka menu melalui kuota seluler maupun Wi-Fi, isi <code>PUBLIC_MENU_URL</code> dengan domain atau alamat HTTPS publik yang mengarah ke aplikasi. Alamat <code>localhost</code> atau IP lokal hanya dapat digunakan pada jaringan yang sama.</p>
</section>
<section class="mx-auto mt-5 max-w-2xl rounded-2xl border border-[#1e293b] bg-[#121824] p-5 text-center sm:p-8">
    <p class="text-sm text-slate-300">QR ini untuk aplikasi pelanggan yang terhubung ke REST API.</p>
    <div class="mx-auto mt-6 flex min-h-64 w-full max-w-72 items-center justify-center rounded-2xl bg-white p-4">
        <div id="customer-api-qr" role="img" aria-label="QR code akses API pelanggan" data-url="{{ $apiUrl }}"></div>
    </div>
    <p id="api-qr-error" class="mt-4 hidden text-sm text-rose-300" role="alert">QR API belum dapat dibuat. Pastikan perangkat terhubung ke internet lalu muat ulang halaman.</p>
    <p class="mt-5 text-xs font-semibold text-amber-300">Tautan akses API pelanggan:</p>
    <a href="{{ $apiUrl }}" class="mt-2 inline-block break-all text-sm text-slate-200 underline decoration-amber-400 underline-offset-4">{{ $apiUrl }}</a>
</section>
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js" referrerpolicy="no-referrer"></script>
<script>
    [
        ['public-menu-qr', 'qr-error'],
        ['customer-api-qr', 'api-qr-error'],
    ].forEach(([containerId, errorId]) => {
        const container = document.getElementById(containerId);
        if (window.QRCode) {
            new QRCode(container, {
                text: container.dataset.url,
                width: 240,
                height: 240,
                colorDark: '#0b0f17',
                colorLight: '#ffffff',
                correctLevel: QRCode.CorrectLevel.H,
            });
        } else {
            document.getElementById(errorId).classList.remove('hidden');
        }
    });
</script>
@endsection
