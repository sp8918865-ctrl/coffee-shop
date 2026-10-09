@extends('catalog.layout')

@section('title', 'Status Pesanan')
@section('meta')
    <meta name="referrer" content="no-referrer">
    <meta http-equiv="refresh" content="30">
@endsection

@section('content')
    @php
        $statusSteps = [
            'pending' => ['Pesanan diterima', 'Pesanan kamu sudah masuk ke kedai.', 1],
            'received' => ['Pesanan diterima', 'Pesanan kamu sudah dikonfirmasi oleh kedai.', 1],
            'in_progress' => ['Sedang disiapkan', 'Barista sedang meracik pesananmu.', 2],
            'ready' => ['Siap diambil / dinikmati', 'Pesananmu siap disajikan atau diambil.', 3],
            'completed' => ['Pesanan selesai', 'Terima kasih sudah mampir ke Warung Luwak.', 4],
            'cancelled' => ['Pesanan dibatalkan', 'Hubungi staf kedai untuk informasi lebih lanjut.', 0],
        ];
        [$statusTitle, $statusDescription, $activeStep] = $statusSteps[$order->order_status] ?? ['Status diperbarui', 'Status pesanan sedang diperbarui.', 1];
        $steps = ['Diterima', 'Diracik', 'Siap', 'Selesai'];
    @endphp
    <div class="mb-4 flex items-center justify-between">
        <div>
            <p class="text-[9px] font-bold uppercase tracking-[.2em] text-amber-300">Status pesanan</p>
            <h1 class="mt-1 text-lg font-extrabold text-white">Lagi diproses</h1>
        </div>
        <a href="{{ route('catalog.index') }}" class="rounded-lg border border-white/10 px-3 py-2 text-[10px] font-bold text-amber-200">Pesan lagi</a>
    </div>

    <section class="rounded-2xl border border-amber-300/20 bg-gradient-to-br from-[#292116] to-[#191c22] p-4">
        <div class="flex items-start justify-between gap-3">
            <div>
                <p class="text-[9px] font-bold uppercase tracking-widest text-amber-300">Nomor pesanan</p>
                <p class="mt-1 text-base font-black text-white">{{ $order->order_number }}</p>
                <p class="mt-1 text-[10px] text-slate-400">{{ $order->created_at->format('d M Y · H:i') }}</p>
            </div>
            <span class="rounded-full border border-amber-300/30 bg-amber-300/10 px-3 py-1.5 text-[9px] font-bold text-amber-200">{{ $statusTitle }}</span>
        </div>

        <div class="mt-5 grid grid-cols-4">
            @foreach ($steps as $index => $step)
                @php($stepNumber = $index + 1)
                <div class="relative flex flex-col items-center text-center">
                    @if ($index > 0)
                        <span class="absolute right-1/2 top-3 h-px w-full {{ $activeStep >= $stepNumber ? 'bg-amber-300' : 'bg-white/10' }}"></span>
                    @endif
                    <span class="relative z-10 grid h-6 w-6 place-items-center rounded-full border text-[9px] font-black {{ $activeStep >= $stepNumber ? 'border-amber-300 bg-amber-300 text-slate-950' : 'border-white/15 bg-[#191c22] text-slate-500' }}">
                        @if ($activeStep > $stepNumber)<i data-lucide="check" class="h-3 w-3"></i>@else{{ $stepNumber }}@endif
                    </span>
                    <span class="mt-2 text-[8px] {{ $activeStep >= $stepNumber ? 'font-bold text-amber-200' : 'text-slate-500' }}">{{ $step }}</span>
                </div>
            @endforeach
        </div>
        <p class="mt-4 rounded-xl border border-white/[0.06] bg-black/10 px-3 py-2.5 text-[10px] leading-relaxed text-slate-300">{{ $statusDescription }}</p>
        <p class="mt-2 text-center text-[9px] text-slate-600">Status diperbarui otomatis setiap 30 detik.</p>
    </section>

    <section class="mt-4 rounded-2xl border border-white/[0.07] bg-[#191c22] p-4">
        <div class="flex items-center justify-between">
            <h2 class="text-xs font-extrabold text-white">Rincian pesanan</h2>
            <span class="text-[9px] text-slate-500">{{ $order->items->sum('quantity') }} item</span>
        </div>
        <div class="mt-2 divide-y divide-white/[0.06]">
            @foreach ($order->items as $item)
                <div class="flex justify-between gap-3 py-3 text-[10px]">
                    <span class="text-slate-300">{{ $item->quantity }}× {{ $item->product_name }}</span>
                    <span class="shrink-0 font-bold text-white">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
                </div>
            @endforeach
        </div>
        <div class="flex justify-between border-t border-white/10 pt-3 text-xs font-extrabold">
            <span class="text-slate-300">Total</span><span class="text-amber-300">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
        </div>
        <div class="mt-3 flex items-center gap-2 rounded-lg bg-[#101319] px-3 py-2 text-[9px] text-slate-400">
            <i data-lucide="{{ $order->order_type === 'dine_in' ? 'armchair' : 'shopping-bag' }}" class="h-4 w-4 text-amber-300"></i>
            {{ $order->order_type === 'dine_in' ? 'Makan di tempat · Meja '.$order->table_number : 'Dibawa pulang' }}
        </div>
    </section>

    @php($payment = $order->payments->first())
    @if ($payment)
        <section class="mt-4 rounded-2xl border border-white/[0.07] bg-[#191c22] p-4">
            <div class="flex items-center justify-between">
                <h2 class="text-xs font-extrabold text-white">Pembayaran</h2>
                <span class="rounded-full border border-amber-300/20 bg-amber-300/[0.07] px-2.5 py-1 text-[8px] font-bold uppercase text-amber-200">Belum dibayar</span>
            </div>
            @if ($payment->payment_method === 'qris')
                <div class="mt-3 rounded-xl border border-dashed border-amber-300/20 bg-[#101319] p-4 text-center">
                    <div class="mx-auto grid h-32 w-32 place-items-center rounded-xl bg-white text-slate-950">
                        <div class="grid h-24 w-24 grid-cols-5 grid-rows-5 gap-1 opacity-20" aria-hidden="true">
                            @for ($pixel = 0; $pixel < 25; $pixel++)
                                <span class="{{ ($pixel * 7 + $pixel % 4) % 3 ? 'bg-slate-900' : 'bg-transparent' }}"></span>
                            @endfor
                        </div>
                    </div>
                    <p class="mt-3 text-[10px] font-black tracking-widest text-amber-300">QRIS SIMULASI</p>
                    <p class="mt-1 text-[9px] leading-relaxed text-slate-500">Ini bukan kode QR pembayaran aktif. Pembayaran belum diproses dan tidak ada tagihan sungguhan.</p>
                </div>
            @else
                <p class="mt-3 rounded-xl bg-[#101319] p-3 text-[10px] leading-relaxed text-slate-400">Silakan bayar tunai kepada staf kedai. Pembayaran belum dicatat sebagai lunas.</p>
            @endif
        </section>
    @endif
@endsection
