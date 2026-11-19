@extends('admin.data.layout')

@section('title', 'Penerima Transfer')
@section('heading', 'Penerima Transfer')

@section('content')
    <section class="mx-auto max-w-3xl rounded-2xl border border-[#1e293b] bg-[#121824] p-5 sm:p-7">
        <p class="text-sm font-semibold text-white">Tambah rekening tujuan pembayaran</p>
        <p class="mt-1 text-xs leading-relaxed text-slate-400">Rekening yang disimpan akan ditampilkan kepada pelanggan yang memilih transfer bank.</p>

        <form method="POST" action="{{ \App\Support\PanelRoute::url('admin.transfer-recipient.store') }}" class="mt-5 grid gap-4 sm:grid-cols-3">
            @csrf
            <div>
                <label for="bank" class="text-xs font-semibold text-slate-300">Nama bank</label>
                <input id="bank" name="bank" value="{{ old('bank') }}" required maxlength="100" placeholder="Contoh: BCA" class="mt-1.5 w-full rounded-xl border border-white/10 bg-[#0b0f17] px-3 py-3 text-sm text-white placeholder:text-slate-600 focus:border-amber-400 focus:outline-none">
                @error('bank')<p class="mt-1 text-xs text-rose-300">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="account_name" class="text-xs font-semibold text-slate-300">Nama pemilik rekening</label>
                <input id="account_name" name="account_name" value="{{ old('account_name') }}" required maxlength="100" placeholder="Nama sesuai rekening" class="mt-1.5 w-full rounded-xl border border-white/10 bg-[#0b0f17] px-3 py-3 text-sm text-white placeholder:text-slate-600 focus:border-amber-400 focus:outline-none">
                @error('account_name')<p class="mt-1 text-xs text-rose-300">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="account_number" class="text-xs font-semibold text-slate-300">Nomor rekening</label>
                <input id="account_number" name="account_number" value="{{ old('account_number') }}" required maxlength="50" inputmode="numeric" placeholder="Nomor rekening tujuan" class="mt-1.5 w-full rounded-xl border border-white/10 bg-[#0b0f17] px-3 py-3 text-sm text-white placeholder:text-slate-600 focus:border-amber-400 focus:outline-none">
                @error('account_number')<p class="mt-1 text-xs text-rose-300">{{ $message }}</p>@enderror
            </div>
            <div class="sm:col-span-3">
                <button class="rounded-xl bg-amber-400 px-5 py-3 text-xs font-black text-slate-950 transition hover:bg-amber-300">Tambah penerima transfer</button>
            </div>
        </form>
    </section>

    <section class="mx-auto mt-5 max-w-3xl">
        <div class="mb-3 flex items-center justify-between">
            <h2 class="text-sm font-semibold text-white">Daftar penerima transfer</h2>
            <span class="text-xs text-slate-500">{{ $recipients->count() }} rekening</span>
        </div>
        @forelse ($recipients as $recipient)
            <article class="mb-3 rounded-2xl border border-[#1e293b] bg-[#121824] p-5">
                <form method="POST" action="{{ \App\Support\PanelRoute::url('admin.transfer-recipient.update', $recipient) }}" class="grid gap-4 sm:grid-cols-3">
                    @csrf
                    @method('PUT')
                    <div>
                        <label for="bank-{{ $recipient->id }}" class="text-xs font-semibold text-slate-300">Nama bank</label>
                        <input id="bank-{{ $recipient->id }}" name="bank" value="{{ $recipient->bank }}" required maxlength="100" class="mt-1.5 w-full rounded-xl border border-white/10 bg-[#0b0f17] px-3 py-3 text-sm text-white">
                    </div>
                    <div>
                        <label for="account-name-{{ $recipient->id }}" class="text-xs font-semibold text-slate-300">Nama pemilik rekening</label>
                        <input id="account-name-{{ $recipient->id }}" name="account_name" value="{{ $recipient->account_name }}" required maxlength="100" class="mt-1.5 w-full rounded-xl border border-white/10 bg-[#0b0f17] px-3 py-3 text-sm text-white">
                    </div>
                    <div>
                        <label for="account-number-{{ $recipient->id }}" class="text-xs font-semibold text-slate-300">Nomor rekening</label>
                        <input id="account-number-{{ $recipient->id }}" name="account_number" value="{{ $recipient->account_number }}" required maxlength="50" inputmode="numeric" class="mt-1.5 w-full rounded-xl border border-white/10 bg-[#0b0f17] px-3 py-3 text-sm text-white">
                    </div>
                    <div class="flex gap-2 sm:col-span-3">
                        <button class="rounded-lg bg-amber-400 px-4 py-2 text-xs font-bold text-slate-950">Simpan perubahan</button>
                    </div>
                </form>
                <form method="POST" action="{{ \App\Support\PanelRoute::url('admin.transfer-recipient.destroy', $recipient) }}" onsubmit="return confirm('Hapus rekening penerima transfer ini?')" class="mt-2">
                    @csrf
                    @method('DELETE')
                    <button class="rounded-lg border border-rose-500/30 px-4 py-2 text-xs font-semibold text-rose-300">Hapus rekening</button>
                </form>
            </article>
        @empty
            <div class="rounded-2xl border border-dashed border-[#26334d] bg-[#121824] p-6 text-center text-xs text-slate-400">Belum ada rekening tujuan. Tambahkan rekening untuk ditampilkan kepada pelanggan.</div>
        @endforelse
    </section>
@endsection
