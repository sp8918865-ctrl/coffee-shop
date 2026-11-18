@extends('admin.data.layout')

@section('title', 'Rekening Transfer')
@section('heading', 'Rekening Transfer')

@section('content')
    <section class="max-w-2xl space-y-5">
        <div class="rounded-xl border border-amber-500/20 bg-amber-500/5 p-4 text-sm leading-relaxed text-amber-100">
            Isi rekening toko yang akan ditampilkan kepada pelanggan. Pelanggan dapat transfer dari bank atau aplikasi bank mana pun yang mendukung transfer ke bank tujuan. Pembayaran tetap dikonfirmasi manual oleh staf.
        </div>

        <form method="POST" action="{{ route('admin.bank-transfer.update') }}" class="space-y-4 rounded-xl border border-[#1e293b] bg-[#121824] p-5">
            @csrf
            @method('PUT')

            <div>
                <label for="bank_name" class="text-xs font-semibold text-slate-300">Nama bank tujuan</label>
                <input id="bank_name" name="bank_name" value="{{ old('bank_name', $details['bank_name']) }}" required maxlength="100" placeholder="Contoh: BCA, BRI, BNI, Mandiri, atau BSI" class="mt-1.5 w-full rounded-lg border border-[#334155] bg-[#0b0f17] px-3 py-2.5 text-sm text-white placeholder:text-slate-500 focus:border-amber-400 focus:outline-none">
                @error('bank_name')<p class="mt-1 text-xs text-rose-300">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="account_number" class="text-xs font-semibold text-slate-300">Nomor rekening</label>
                <input id="account_number" name="account_number" value="{{ old('account_number', $details['account_number']) }}" required maxlength="40" inputmode="numeric" autocomplete="off" placeholder="Masukkan nomor rekening toko" class="mt-1.5 w-full rounded-lg border border-[#334155] bg-[#0b0f17] px-3 py-2.5 text-sm text-white placeholder:text-slate-500 focus:border-amber-400 focus:outline-none">
                @error('account_number')<p class="mt-1 text-xs text-rose-300">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="account_holder" class="text-xs font-semibold text-slate-300">Nama pemilik rekening</label>
                <input id="account_holder" name="account_holder" value="{{ old('account_holder', $details['account_holder']) }}" required maxlength="255" autocomplete="off" placeholder="Nama sesuai rekening" class="mt-1.5 w-full rounded-lg border border-[#334155] bg-[#0b0f17] px-3 py-2.5 text-sm text-white placeholder:text-slate-500 focus:border-amber-400 focus:outline-none">
                @error('account_holder')<p class="mt-1 text-xs text-rose-300">{{ $message }}</p>@enderror
            </div>

            <button type="submit" class="rounded-lg bg-amber-500 px-4 py-2.5 text-xs font-bold text-slate-950 transition hover:bg-amber-400">Simpan rekening</button>
        </form>

        @unless ($details['configured'])
            <p class="text-xs text-slate-400">Rekening belum lengkap. Pilihan transfer belum dapat digunakan pelanggan sampai semua detail disimpan.</p>
        @endunless
    </section>
@endsection
