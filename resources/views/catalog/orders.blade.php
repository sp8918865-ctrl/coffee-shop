@extends('catalog.layout')

@section('title', 'Pesanan Saya')

@section('content')
    <section class="rounded-2xl border border-amber-400/15 bg-gradient-to-br from-[#332311] via-[#211b15] to-[#191c22] p-5">
        <p class="text-[9px] font-bold uppercase tracking-[.24em] text-amber-300">Pelanggan</p>
        <h1 class="mt-2 text-xl font-black text-white">Pesanan saya</h1>
        <p class="mt-2 text-xs leading-relaxed text-slate-300">Lihat kembali pesanan online terakhir dari perangkat ini.</p>
    </section>

    @if ($order)
        <section class="mt-4 rounded-2xl border border-white/[0.07] bg-[#191c22] p-4">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <p class="text-[9px] font-bold uppercase tracking-widest text-amber-300">Nomor pesanan</p>
                    <p class="mt-1 text-sm font-black text-white">{{ $order->order_number }}</p>
                    <p class="mt-1 text-[10px] text-slate-400">{{ $order->created_at->format('d M Y · H:i') }}</p>
                </div>
                <span class="rounded-full border border-amber-300/30 bg-amber-300/10 px-3 py-1.5 text-[9px] font-bold text-amber-200">{{ str_replace('_', ' ', $order->order_status) }}</span>
            </div>
            <p class="mt-4 text-xs font-bold text-white">Total Rp {{ number_format($order->total_amount, 0, ',', '.') }}</p>
            <a href="{{ route('orders.track', $order->public_tracking_token) }}" class="mt-4 flex items-center justify-center gap-2 rounded-xl bg-amber-300 px-4 py-3 text-xs font-extrabold text-slate-950">
                Lihat status pesanan <i data-lucide="arrow-right" class="h-4 w-4"></i>
            </a>
        </section>
    @else
        <section class="mt-4 rounded-2xl border border-dashed border-white/10 bg-[#191c22] p-8 text-center">
            <i data-lucide="receipt-text" class="mx-auto h-8 w-8 text-amber-300"></i>
            <h2 class="mt-3 text-sm font-bold text-white">Belum ada pesanan</h2>
            <p class="mt-1 text-xs leading-relaxed text-slate-400">Pesanan online yang dibuat dari perangkat ini akan muncul di sini.</p>
            <a href="{{ route('catalog.index') }}" class="mt-4 inline-flex rounded-xl bg-amber-300 px-4 py-3 text-xs font-extrabold text-slate-950">Lihat menu</a>
        </section>
    @endif
@endsection
