@extends('catalog.layout')

@section('title', 'Checkout')

@section('content')
    <div class="mb-5 flex items-center gap-2 text-[9px] font-bold uppercase tracking-widest">
        <span class="rounded-full bg-amber-300 px-3 py-1.5 text-slate-950">1 · Menu</span>
        <span class="h-px flex-1 bg-white/10"></span>
        <span class="rounded-full border border-amber-300/40 px-3 py-1.5 text-amber-200">2 · Checkout</span>
        <span class="h-px flex-1 bg-white/10"></span>
        <span class="rounded-full border border-white/10 px-3 py-1.5 text-slate-500">3 · Selesai</span>
    </div>

    <section class="rounded-2xl border border-white/[0.07] bg-[#191c22] p-4">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-[9px] font-bold uppercase tracking-[.2em] text-amber-300">Ringkasan pesanan</p>
                <h1 class="mt-1 text-lg font-extrabold text-white">{{ $items->sum('cart_quantity') }} item dipilih</h1>
            </div>
            <a href="{{ route('catalog.index') }}" class="text-[10px] font-bold text-amber-300">Tambah menu</a>
        </div>
        <div class="mt-3 divide-y divide-white/[0.06]">
            @foreach ($items as $item)
                <div class="flex items-center justify-between gap-3 py-3">
                    <div class="min-w-0">
                        <p class="truncate text-xs font-bold text-white">{{ $item->name }}</p>
                        <p class="mt-1 text-[10px] text-slate-400">Rp {{ number_format($item->price, 0, ',', '.') }} / item</p>
                    </div>
                    <div class="flex shrink-0 items-center gap-2">
                        <form method="POST" action="{{ route('cart.update', $item->product_id) }}" class="flex items-center gap-1">
                            @csrf @method('PATCH')
                            <input aria-label="Jumlah {{ $item->name }}" type="number" min="1" max="20" name="quantity" value="{{ $item->cart_quantity }}" class="w-12 rounded-lg border border-white/10 bg-[#101319] px-2 py-1.5 text-center text-xs text-white">
                            <button class="rounded-lg border border-white/10 px-2 py-1.5 text-[10px] text-slate-300">Ubah</button>
                        </form>
                        <form method="POST" action="{{ route('cart.remove', $item->product_id) }}">@csrf @method('DELETE')<button aria-label="Hapus {{ $item->name }}" class="rounded-lg p-1.5 text-rose-300"><i data-lucide="trash-2" class="h-4 w-4"></i></button></form>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="mt-1 flex justify-between border-t border-white/10 pt-3 text-xs">
            <span class="text-slate-400">Total pembayaran</span>
            <strong class="text-base text-amber-300">Rp {{ number_format($total, 0, ',', '.') }}</strong>
        </div>
    </section>

    <section class="mt-4 rounded-2xl border border-white/[0.07] bg-[#191c22] p-4">
        <p class="text-[9px] font-bold uppercase tracking-[.2em] text-amber-300">Detail pesanan</p>
        <h2 class="mt-1 text-sm font-extrabold text-white">Lengkapi pesananmu</h2>
        @if ($errors->any())
            <div role="alert" class="mt-3 rounded-xl border border-rose-400/20 bg-rose-400/[0.08] p-3 text-xs text-rose-200">Periksa kembali: {{ $errors->first() }}</div>
        @endif
        <form id="checkout-form" method="POST" action="{{ route('checkout.place') }}" class="mt-4 space-y-4">
            @csrf
            @php($defaultOrderType = $tables->isNotEmpty() ? 'dine_in' : 'take_away')
            <div>
                <label for="customer_name" class="text-[10px] font-semibold text-slate-300">Nama pelanggan</label>
                <input id="customer_name" name="customer_name" value="{{ old('customer_name') }}" required maxlength="255" autocomplete="name" placeholder="Nama kamu" class="mt-1.5 w-full rounded-xl border border-white/10 bg-[#101319] px-3 py-3 text-sm text-white placeholder:text-slate-600 focus:border-amber-300 focus:outline-none">
            </div>
            <div>
                <label for="customer_phone" class="text-[10px] font-semibold text-slate-300">Nomor WhatsApp <span class="font-normal text-slate-500">(opsional)</span></label>
                <input id="customer_phone" name="customer_phone" value="{{ old('customer_phone') }}" maxlength="30" autocomplete="tel" inputmode="tel" placeholder="08xxxxxxxxxx" class="mt-1.5 w-full rounded-xl border border-white/10 bg-[#101319] px-3 py-3 text-sm text-white placeholder:text-slate-600 focus:border-amber-300 focus:outline-none">
            </div>

            <fieldset>
                <legend class="text-[10px] font-semibold text-slate-300">Cara menikmati pesanan</legend>
                <div class="mt-2 grid grid-cols-2 gap-2">
                    <label class="flex cursor-pointer items-center gap-2 rounded-xl border border-white/10 bg-[#101319] p-3 text-[11px] has-[:checked]:border-amber-300/60 has-[:checked]:bg-amber-300/[0.08]">
                        <input type="radio" name="order_type" value="dine_in" @checked(old('order_type', $defaultOrderType) === 'dine_in') @disabled($tables->isEmpty()) class="accent-amber-300">
                        <span><strong class="block text-white">Makan di sini</strong><span class="text-[9px] text-slate-500">{{ $tables->isNotEmpty() ? 'Pilih meja tersedia' : 'Meja belum tersedia' }}</span></span>
                    </label>
                    <label class="flex cursor-pointer items-center gap-2 rounded-xl border border-white/10 bg-[#101319] p-3 text-[11px] has-[:checked]:border-amber-300/60 has-[:checked]:bg-amber-300/[0.08]">
                        <input type="radio" name="order_type" value="take_away" @checked(old('order_type', $defaultOrderType) === 'take_away') class="accent-amber-300">
                        <span><strong class="block text-white">Dibawa pulang</strong><span class="text-[9px] text-slate-500">Takeaway</span></span>
                    </label>
                </div>
                <div id="table-field" class="mt-3">
                    <label for="table_id" class="text-[10px] font-semibold text-slate-300">Pilih meja</label>
                    <select id="table_id" name="table_id" class="mt-1.5 w-full rounded-xl border border-white/10 bg-[#101319] px-3 py-3 text-sm text-white focus:border-amber-300 focus:outline-none">
                        <option value="">Pilih meja</option>
                        @foreach ($tables as $table)
                            <option value="{{ $table->table_id }}" @selected((string) old('table_id') === (string) $table->table_id)>Meja {{ $table->table_number }} · {{ $table->capacity }} orang</option>
                        @endforeach
                    </select>
                    @error('table_id')<p class="mt-1 text-[10px] text-rose-300">{{ $message }}</p>@enderror
                </div>
            </fieldset>

            <fieldset>
                <legend class="text-[10px] font-semibold text-slate-300">Metode pembayaran</legend>
                <div id="payment-method-options" class="mt-2 space-y-2">
                    <label class="flex cursor-pointer items-start gap-3 rounded-xl border border-white/10 bg-[#101319] p-3 has-[:checked]:border-amber-300/60 has-[:checked]:bg-amber-300/[0.08]">
                        <input type="radio" name="payment_method" value="qris" @checked(old('payment_method', 'qris') === 'qris') class="mt-0.5 accent-amber-300">
                        <span class="flex-1">
                            <strong class="block text-xs text-white">QRIS</strong>
                            <span class="mt-1 block text-[9px] leading-relaxed text-slate-500">
                                @if (\App\Support\StaticQris::imageUrl())
                                    Pindai QRIS resmi setelah pesanan dibuat. Pembayaran dikonfirmasi staf.
                                @else
                                    QRIS belum tersedia. Kamu bisa mengganti metode pembayaran di halaman status pesanan.
                                @endif
                            </span>
                        </span>
                        <i data-lucide="qr-code" class="h-5 w-5 text-amber-300"></i>
                    </label>
                    <label class="flex cursor-pointer items-start gap-3 rounded-xl border border-white/10 bg-[#101319] p-3 has-[:checked]:border-amber-300/60 has-[:checked]:bg-amber-300/[0.08]">
                        <input type="radio" name="payment_method" value="cash" @checked(old('payment_method') === 'cash') class="mt-0.5 accent-amber-300">
                        <span><strong class="block text-xs text-white">Bayar tunai di kedai</strong><span class="mt-1 block text-[9px] text-slate-500">Belum dibayar online.</span></span>
                    </label>
                    <label class="flex cursor-pointer items-start gap-3 rounded-xl border border-white/10 bg-[#101319] p-3 has-[:checked]:border-amber-300/60 has-[:checked]:bg-amber-300/[0.08]">
                        <input type="radio" name="payment_method" value="transfer" @checked(old('payment_method') === 'transfer') class="mt-0.5 accent-amber-300">
                        <span><strong class="block text-xs text-white">Transfer bank</strong><span class="mt-1 block text-[9px] text-slate-500">Pilih bank yang akan kamu gunakan untuk transfer.</span></span>
                    </label>
                </div>
                @error('payment_method')<p class="mt-1 text-[10px] text-rose-300">{{ $message }}</p>@enderror
            </fieldset>

            <div id="cash-payment-note" @if (old('payment_method', 'qris') !== 'cash') hidden @endif class="rounded-xl border border-amber-300/20 bg-amber-300/[0.04] p-3">
                <p class="text-[10px] leading-relaxed text-slate-400">Pembayaran tunai dilakukan di kedai. Staf akan mengonfirmasi pembayaran saat pesanan dibayar.</p>
            </div>
            <div id="transfer-payment-note" @if (old('payment_method') !== 'transfer') hidden @endif class="rounded-xl border border-amber-300/20 bg-amber-300/[0.04] p-3">
                <div id="transfer-bank-picker">
                    <p class="text-[10px] font-semibold text-slate-300">Pilih bank yang kamu gunakan</p>
                    <div class="mt-2 grid grid-cols-2 gap-2 sm:grid-cols-3">
                        @foreach (\App\Support\IndonesianBanks::visualOptions() as $bank)
                            <label class="flex cursor-pointer items-center gap-2 rounded-xl border border-white/10 bg-[#101319] p-2.5 transition hover:border-amber-300/40 has-[:checked]:border-amber-300/70 has-[:checked]:bg-amber-300/[0.08]">
                                <input type="radio" name="bank_pengirim" value="{{ $bank['name'] }}" @checked(old('bank_pengirim') === $bank['name']) class="sr-only">
                                <span class="grid h-9 w-11 shrink-0 place-items-center rounded-lg px-1 text-[9px] font-black tracking-tight" style="background-color: {{ $bank['background'] }}; color: {{ $bank['foreground'] }}">{{ $bank['mark'] }}</span>
                                <span class="text-[10px] font-semibold leading-tight text-white">{{ $bank['name'] }}</span>
                            </label>
                        @endforeach
                        <label class="flex cursor-pointer items-center gap-2 rounded-xl border border-white/10 bg-[#101319] p-2.5 transition hover:border-amber-300/40 has-[:checked]:border-amber-300/70 has-[:checked]:bg-amber-300/[0.08]">
                            <input type="radio" name="bank_pengirim" value="{{ \App\Support\IndonesianBanks::OTHER }}" @checked(old('bank_pengirim') === \App\Support\IndonesianBanks::OTHER) class="sr-only">
                            <span class="grid h-9 w-11 shrink-0 place-items-center rounded-lg bg-slate-700 text-[10px] font-black text-white">LAIN</span>
                            <span class="text-[10px] font-semibold leading-tight text-white">Bank lainnya</span>
                        </label>
                    </div>
                    @error('bank_pengirim')<p class="mt-1 text-[10px] text-rose-300">{{ $message }}</p>@enderror
                </div>

                <div id="transfer-bank-details" @if (! old('bank_pengirim')) hidden @endif>
                    <div class="flex items-center justify-between gap-3">
                        <div>
                            <p class="text-[9px] font-semibold uppercase tracking-widest text-slate-500">Bank pengirim</p>
                            <p id="selected-bank-name" class="mt-1 text-sm font-black text-amber-300">{{ old('bank_pengirim') === \App\Support\IndonesianBanks::OTHER ? old('bank_pengirim_lainnya') : old('bank_pengirim') }}</p>
                        </div>
                        <button id="change-bank-button" type="button" class="rounded-lg border border-white/10 px-3 py-2 text-[10px] font-bold text-slate-300">Ganti bank</button>
                    </div>

                    <div id="other-bank-field" @if (old('bank_pengirim') !== \App\Support\IndonesianBanks::OTHER) hidden @endif class="mt-3">
                        <label for="bank_pengirim_lainnya" class="text-[10px] font-semibold text-slate-300">Nama bank</label>
                        <input id="bank_pengirim_lainnya" name="bank_pengirim_lainnya" value="{{ old('bank_pengirim_lainnya') }}" maxlength="100" placeholder="Masukkan nama bank" class="mt-1.5 w-full rounded-xl border border-white/10 bg-[#101319] px-3 py-3 text-sm text-white placeholder:text-slate-600 focus:border-amber-300 focus:outline-none">
                        @error('bank_pengirim_lainnya')<p class="mt-1 text-[10px] text-rose-300">{{ $message }}</p>@enderror
                    </div>

                    <div class="mt-3 grid gap-3 sm:grid-cols-2">
                        <div>
                            <label for="nama_pemilik_rekening" class="text-[10px] font-semibold text-slate-300">Nama pemilik rekening</label>
                            <input id="nama_pemilik_rekening" name="nama_pemilik_rekening" value="{{ old('nama_pemilik_rekening') }}" required maxlength="100" autocomplete="name" placeholder="Nama sesuai rekening" class="mt-1.5 w-full rounded-xl border border-white/10 bg-[#101319] px-3 py-3 text-sm text-white placeholder:text-slate-600 focus:border-amber-300 focus:outline-none">
                            @error('nama_pemilik_rekening')<p class="mt-1 text-[10px] text-rose-300">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="nomor_rekening" class="text-[10px] font-semibold text-slate-300">Nomor rekening</label>
                            <input id="nomor_rekening" name="nomor_rekening" value="{{ old('nomor_rekening') }}" required maxlength="50" inputmode="numeric" placeholder="Masukkan nomor rekening" class="mt-1.5 w-full rounded-xl border border-white/10 bg-[#101319] px-3 py-3 text-sm text-white placeholder:text-slate-600 focus:border-amber-300 focus:outline-none">
                            @error('nomor_rekening')<p class="mt-1 text-[10px] text-rose-300">{{ $message }}</p>@enderror
                        </div>
                    </div>
                </div>
                <div class="mt-3 rounded-lg border border-white/[0.06] bg-[#101319] p-3 text-[10px]">
                    <p class="font-semibold text-slate-300">Rekening tujuan transfer</p>
                    @forelse ($transferRecipients as $recipient)
                        <div class="mt-2 border-t border-white/[0.06] pt-2">
                            <p class="text-slate-400">Bank: <strong class="text-white">{{ $recipient->bank }}</strong></p>
                            <p class="mt-1 text-slate-400">Atas nama: <strong class="text-white">{{ $recipient->account_name }}</strong></p>
                            <p class="mt-1 text-slate-400">Nomor rekening: <strong class="text-white">{{ $recipient->account_number }}</strong></p>
                        </div>
                    @empty
                        <p class="mt-1 text-amber-200">Rekening tujuan belum diatur. Silakan hubungi staf kedai sebelum transfer.</p>
                    @endforelse
                    <p class="mt-2 text-[9px] leading-relaxed text-slate-500">Pembayaran transfer dikonfirmasi secara manual oleh staf.</p>
                </div>
            </div>

            <button class="flex w-full items-center justify-center gap-2 rounded-xl bg-amber-300 px-4 py-3.5 text-xs font-black text-slate-950 shadow-lg shadow-amber-950/20 transition hover:bg-amber-200">
                Buat pesanan <i data-lucide="arrow-right" class="h-4 w-4"></i><span class="ml-1">· Rp {{ number_format($total, 0, ',', '.') }}</span>
            </button>
            <p class="text-center text-[9px] text-slate-600">Dengan melanjutkan, pesanan akan dikirim ke kedai.</p>
        </form>
    </section>
@endsection

@push('scripts')
<script>
    const checkoutForm = document.getElementById('checkout-form');
    const cashNote = document.getElementById('cash-payment-note');
    const transferNote = document.getElementById('transfer-payment-note');
    const transferBankPicker = document.getElementById('transfer-bank-picker');
    const transferBankDetails = document.getElementById('transfer-bank-details');
    const selectedBankName = document.getElementById('selected-bank-name');
    const changeBankButton = document.getElementById('change-bank-button');
    const senderBanks = document.querySelectorAll('input[name="bank_pengirim"]');
    const otherBankField = document.getElementById('other-bank-field');
    const otherBankInput = document.getElementById('bank_pengirim_lainnya');
    const accountNameInput = document.getElementById('nama_pemilik_rekening');
    const accountNumberInput = document.getElementById('nomor_rekening');

    function syncPaymentNotes() {
        const method = document.querySelector('input[name="payment_method"]:checked')?.value;
        const selectedBank = document.querySelector('input[name="bank_pengirim"]:checked')?.value;
        cashNote.hidden = method !== 'cash';
        transferNote.hidden = method !== 'transfer';
        senderBanks.forEach(input => input.required = method === 'transfer');
        transferBankPicker.hidden = method !== 'transfer' || Boolean(selectedBank);
        transferBankDetails.hidden = method !== 'transfer' || !selectedBank;
        otherBankField.hidden = selectedBank !== '{{ \App\Support\IndonesianBanks::OTHER }}';
        otherBankInput.required = method === 'transfer' && selectedBank === '{{ \App\Support\IndonesianBanks::OTHER }}';
        accountNameInput.required = method === 'transfer' && Boolean(selectedBank);
        accountNumberInput.required = method === 'transfer' && Boolean(selectedBank);
        if (selectedBank === '{{ \App\Support\IndonesianBanks::OTHER }}') {
            selectedBankName.textContent = otherBankInput.value.trim() || 'Bank lainnya';
        } else if (selectedBank) {
            selectedBankName.textContent = selectedBank;
        }
    }

    const tableField = document.getElementById('table-field');
    const tableInput = document.getElementById('table_id');
    function syncOrderType() {
        const dineIn = document.querySelector('input[name="order_type"]:checked')?.value === 'dine_in';
        tableField.hidden = !dineIn;
        tableInput.required = dineIn;
    }
    document.querySelectorAll('input[name="order_type"]').forEach(input => input.addEventListener('change', syncOrderType));
    document.querySelectorAll('input[name="payment_method"]').forEach(input => input.addEventListener('change', syncPaymentNotes));
    senderBanks.forEach(input => input.addEventListener('change', syncPaymentNotes));
    otherBankInput.addEventListener('input', syncPaymentNotes);
    changeBankButton.addEventListener('click', () => {
        senderBanks.forEach(input => input.checked = false);
        syncPaymentNotes();
    });
    syncOrderType();
    syncPaymentNotes();
</script>
@endpush
