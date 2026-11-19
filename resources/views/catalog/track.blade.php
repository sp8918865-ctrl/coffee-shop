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
        $payment = $order->payments->first();
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
                    <span class="text-slate-300">{{ $item->quantity }}× {{ $item->product_name }} · Rp {{ number_format($item->unit_price, 0, ',', '.') }} / item</span>
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

    @if ($payment)
        <section class="mt-4 rounded-2xl border border-white/[0.07] bg-[#191c22] p-4">
            <div class="flex items-center justify-between">
                <h2 class="text-xs font-extrabold text-white">Bon pembayaran</h2>
                <span class="rounded-full border border-amber-300/20 bg-amber-300/[0.07] px-2.5 py-1 text-[8px] font-bold uppercase text-amber-200">{{ $payment->payment_status === 'paid' ? 'Lunas' : 'Belum dibayar' }}</span>
            </div>
            <p class="mt-2 text-[9px] text-slate-500">No. bon {{ $order->order_number }} · {{ $order->created_at->format('d M Y · H:i') }}</p>
            @if ($order->customer_name)
                <p class="mt-1 text-[9px] text-slate-400">Pelanggan: {{ $order->customer_name }}</p>
            @endif
            @if ($payment->payment_method === 'cash')
                <div class="mt-3 flex justify-between gap-3 rounded-xl bg-[#101319] p-3 text-[10px]"><span class="text-slate-400">Metode pembayaran</span><span class="font-semibold text-white">Tunai · menunggu konfirmasi staf</span></div>
            @endif
            @if ($payment->payment_method === 'transfer')
                <div class="mt-3 flex justify-between gap-3 rounded-xl bg-[#101319] p-3 text-[10px]"><span class="text-slate-400">Metode pembayaran</span><span class="font-semibold text-white">Transfer bank · {{ $payment->payment_status === 'paid' ? 'terkonfirmasi' : 'menunggu konfirmasi staf' }}</span></div>
            @endif
            @if ($payment->payment_method === 'qris')
                <div class="mt-3 rounded-xl border border-dashed border-amber-300/20 bg-[#101319] p-4 text-center">
                    @if ($qrisImageUrl)
                        <img src="{{ $qrisImageUrl }}" alt="QRIS resmi untuk pembayaran pesanan" class="mx-auto max-h-72 w-auto rounded-xl bg-white p-2">
                        <p class="mt-3 text-[10px] font-black tracking-widest text-amber-300">QRIS USAHA</p>
                        <p class="mt-1 text-[9px] leading-relaxed text-slate-400">Scan QRIS resmi dan bayar tepat Rp {{ number_format($payment->amount, 0, ',', '.') }}. Pembayaran akan dikonfirmasi staf kedai.</p>
                    @else
                        <p class="mt-3 text-[10px] font-black tracking-widest text-amber-300">QRIS BELUM DIATUR</p>
                        <p class="mt-1 text-[9px] leading-relaxed text-slate-500">Kode QRIS resmi belum tersedia. Silakan pilih metode lain atau hubungi staf kedai.</p>
                    @endif
                </div>
            @elseif ($payment->payment_method === 'cash')
                <p class="mt-3 rounded-xl bg-[#101319] p-3 text-[10px] leading-relaxed text-slate-400">Silakan serahkan uang tunai kepada staf kedai. Bon ini merupakan rincian pesanan; pembayaran baru dianggap lunas setelah dikonfirmasi staf.</p>
            @elseif ($payment->payment_method === 'transfer')
                <div class="mt-3 rounded-xl bg-[#101319] p-3 text-[10px] leading-relaxed text-slate-400">
                    @if ($payment->bank_pengirim)
                        <p>Bank yang akan kamu gunakan: <strong class="text-white">{{ $payment->bank_pengirim }}</strong>.</p>
                    @endif
                    @if ($payment->nama_pemilik_rekening)
                        <p class="mt-1">Nama pemilik rekening: <strong class="text-white">{{ $payment->nama_pemilik_rekening }}</strong>.</p>
                    @endif
                    @if ($payment->nomor_rekening)
                        <p class="mt-1">Nomor rekening: <strong class="text-white">{{ $payment->nomor_rekening }}</strong>.</p>
                    @endif
                    <p class="mt-3 font-semibold text-slate-300">Rekening tujuan transfer</p>
                    @forelse ($transferRecipients as $recipient)
                        <div class="mt-2 border-t border-white/[0.06] pt-2">
                            <p>Bank: <strong class="text-white">{{ $recipient->bank }}</strong></p>
                            <p class="mt-1">Atas nama: <strong class="text-white">{{ $recipient->account_name }}</strong></p>
                            <p class="mt-1">Nomor rekening: <strong class="text-white">{{ $recipient->account_number }}</strong></p>
                        </div>
                    @empty
                        <p class="mt-1 text-amber-200">Rekening tujuan belum diatur. Silakan hubungi staf kedai sebelum transfer.</p>
                    @endforelse
                    <p class="mt-2">Pembayaran baru dianggap lunas setelah dikonfirmasi staf.</p>
                </div>
            @endif
            @if ($payment->payment_status === 'pending')
                <div class="mt-3">
                    <button id="open-payment-method-form" type="button" class="w-full rounded-lg border border-white/10 px-3 py-2.5 text-[10px] font-semibold text-amber-200">Ganti metode pembayaran</button>
                    <form id="change-payment-method-form" method="POST" action="{{ route('orders.payment-method', $order->public_tracking_token) }}" @if (! $errors->has('payment_method') && ! $errors->has('bank_pengirim') && ! $errors->has('bank_pengirim_lainnya') && ! $errors->has('nama_pemilik_rekening') && ! $errors->has('nomor_rekening')) hidden @endif class="mt-3 space-y-3 rounded-xl border border-white/[0.07] bg-[#101319] p-3">
                        @csrf
                        <fieldset>
                            <legend class="text-[10px] font-semibold text-slate-300">Pilih metode pembayaran</legend>
                            <div class="mt-2 grid grid-cols-3 gap-2">
                                @foreach (['qris' => 'QRIS', 'cash' => 'Tunai', 'transfer' => 'Transfer'] as $method => $label)
                                    <label class="flex cursor-pointer items-center justify-center gap-1.5 rounded-lg border border-white/10 px-2 py-2 text-[10px] has-[:checked]:border-amber-300/60 has-[:checked]:bg-amber-300/[0.08]">
                                        <input type="radio" name="payment_method" value="{{ $method }}" @checked(old('payment_method', $payment->payment_method) === $method) class="accent-amber-300">
                                        <span class="text-white">{{ $label }}</span>
                                    </label>
                                @endforeach
                            </div>
                            @error('payment_method')<p class="mt-1 text-[10px] text-rose-300">{{ $message }}</p>@enderror
                        </fieldset>
                        <div id="change-transfer-fields" @if (old('payment_method', $payment->payment_method) !== 'transfer') hidden @endif class="space-y-3">
                            <div>
                                <label for="change-bank" class="text-[10px] font-semibold text-slate-300">Bank pengirim</label>
                                <select id="change-bank" name="bank_pengirim" class="mt-1 w-full rounded-lg border border-white/10 bg-[#191c22] px-3 py-2.5 text-xs text-white">
                                    <option value="">Pilih bank</option>
                                    @foreach (\App\Support\IndonesianBanks::OPTIONS as $bank)
                                        <option value="{{ $bank }}" @selected(old('bank_pengirim', $payment->bank_pengirim) === $bank)>{{ $bank }}</option>
                                    @endforeach
                                    <option value="{{ \App\Support\IndonesianBanks::OTHER }}" @selected(old('bank_pengirim') === \App\Support\IndonesianBanks::OTHER)>Bank lainnya</option>
                                </select>
                                @error('bank_pengirim')<p class="mt-1 text-[10px] text-rose-300">{{ $message }}</p>@enderror
                            </div>
                            <div id="change-other-bank-field" @if (old('bank_pengirim', $payment->bank_pengirim) !== \App\Support\IndonesianBanks::OTHER) hidden @endif>
                                <label for="change-other-bank" class="text-[10px] font-semibold text-slate-300">Nama bank</label>
                                <input id="change-other-bank" name="bank_pengirim_lainnya" value="{{ old('bank_pengirim_lainnya') }}" maxlength="100" class="mt-1 w-full rounded-lg border border-white/10 bg-[#191c22] px-3 py-2.5 text-xs text-white">
                                @error('bank_pengirim_lainnya')<p class="mt-1 text-[10px] text-rose-300">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label for="change-account-name" class="text-[10px] font-semibold text-slate-300">Nama pemilik rekening</label>
                                <input id="change-account-name" name="nama_pemilik_rekening" value="{{ old('nama_pemilik_rekening', $payment->nama_pemilik_rekening) }}" maxlength="100" class="mt-1 w-full rounded-lg border border-white/10 bg-[#191c22] px-3 py-2.5 text-xs text-white">
                                @error('nama_pemilik_rekening')<p class="mt-1 text-[10px] text-rose-300">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label for="change-account-number" class="text-[10px] font-semibold text-slate-300">Nomor rekening</label>
                                <input id="change-account-number" name="nomor_rekening" value="{{ old('nomor_rekening', $payment->nomor_rekening) }}" maxlength="50" inputmode="numeric" class="mt-1 w-full rounded-lg border border-white/10 bg-[#191c22] px-3 py-2.5 text-xs text-white">
                                @error('nomor_rekening')<p class="mt-1 text-[10px] text-rose-300">{{ $message }}</p>@enderror
                            </div>
                        </div>
                        <div class="flex gap-2">
                            <button type="submit" class="flex-1 rounded-lg bg-amber-300 px-3 py-2.5 text-[10px] font-black text-slate-950">Simpan metode</button>
                            <button id="cancel-payment-method-change" type="button" class="rounded-lg border border-white/10 px-3 py-2.5 text-[10px] font-semibold text-slate-300">Batal</button>
                        </div>
                    </form>
                </div>
            @endif
            <button type="button" onclick="window.print()" class="mt-3 flex w-full items-center justify-center gap-2 rounded-lg border border-white/10 px-3 py-2 text-[10px] font-semibold text-amber-200 print:hidden">
                <i data-lucide="printer" class="h-4 w-4"></i> Cetak bon
            </button>
        </section>
    @endif
@endsection

@push('scripts')
    <script>
        const changePaymentForm = document.getElementById('change-payment-method-form');
        if (changePaymentForm) {
            const openPaymentForm = document.getElementById('open-payment-method-form');
            const cancelPaymentForm = document.getElementById('cancel-payment-method-change');
            const transferFields = document.getElementById('change-transfer-fields');
            const bankSelect = document.getElementById('change-bank');
            const otherBankField = document.getElementById('change-other-bank-field');
            const otherBankInput = document.getElementById('change-other-bank');
            const accountName = document.getElementById('change-account-name');
            const accountNumber = document.getElementById('change-account-number');

            function syncPaymentChangeFields() {
                const isTransfer = changePaymentForm.querySelector('input[name="payment_method"]:checked')?.value === 'transfer';
                transferFields.hidden = !isTransfer;
                bankSelect.required = isTransfer;
                otherBankField.hidden = !isTransfer || bankSelect.value !== '{{ \App\Support\IndonesianBanks::OTHER }}';
                otherBankInput.required = isTransfer && bankSelect.value === '{{ \App\Support\IndonesianBanks::OTHER }}';
                accountName.required = isTransfer;
                accountNumber.required = isTransfer;
            }

            openPaymentForm.addEventListener('click', () => {
                changePaymentForm.hidden = false;
                openPaymentForm.hidden = true;
            });
            cancelPaymentForm.addEventListener('click', () => {
                changePaymentForm.hidden = true;
                openPaymentForm.hidden = false;
            });
            changePaymentForm.querySelectorAll('input[name="payment_method"]').forEach(input => input.addEventListener('change', syncPaymentChangeFields));
            bankSelect.addEventListener('change', syncPaymentChangeFields);
            syncPaymentChangeFields();
        }
    </script>
    <style>
        @media print {
            body, .min-h-screen, main { background: #fff !important; color: #111 !important; }
            header, nav, button { display: none !important; }
            main { padding: 0 !important; }
            section { break-inside: avoid; border: 1px solid #ccc !important; background: #fff !important; color: #111 !important; }
            section * { color: #111 !important; }
        }
    </style>
@endpush
