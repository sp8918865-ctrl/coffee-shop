@extends('admin.data.layout')

@section('title', 'QR Menu Publik')
@section('heading', 'QR Menu Publik')

@section('content')
<section class="mx-auto max-w-2xl rounded-2xl border border-[#1e293b] bg-[#121824] p-5 text-center sm:p-8">
    <p class="text-sm text-slate-300">Pindai kode ini untuk membuka katalog dan pemesanan publik Warung Luwak.</p>
    <div class="mx-auto mt-6 flex min-h-64 w-full max-w-72 items-center justify-center rounded-2xl bg-white p-4">
        <div id="public-menu-qr" role="img" aria-label="QR code katalog menu publik" data-url="{{ $publicUrl }}"></div>
    </div>
    <p id="qr-error" class="mt-4 hidden text-sm text-rose-300" role="alert">QR belum dapat dibuat. Pastikan perangkat terhubung ke internet lalu muat ulang halaman.</p>
    <p class="mt-5 text-xs font-semibold text-amber-300">Alamat menu publik untuk QR ini:</p>
    <a href="{{ $publicUrl }}" class="mt-2 inline-block break-all text-sm text-slate-200 underline decoration-amber-400 underline-offset-4">{{ $publicUrl }}</a>
    <p class="mt-5 text-xs leading-relaxed text-slate-500">Ponsel pelanggan harus berada di jaringan Wi-Fi yang sama dengan komputer. Jika alamat IP komputer berubah, sesuaikan pengaturan <code>PUBLIC_MENU_URL</code> di file .env lalu mulai ulang server Laravel.</p>
</section>
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js" referrerpolicy="no-referrer"></script>
<script>
    const qrContainer = document.getElementById('public-menu-qr');
    const qrUrl = qrContainer.dataset.url;

    if (window.QRCode) {
        new QRCode(qrContainer, {
            text: qrUrl,
            width: 240,
            height: 240,
            colorDark: '#0b0f17',
            colorLight: '#ffffff',
            correctLevel: QRCode.CorrectLevel.H,
        });
    } else {
        document.getElementById('qr-error').classList.remove('hidden');
    }
</script>
@endsection
