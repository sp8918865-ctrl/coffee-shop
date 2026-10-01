@extends('admin.data.layout')

@section('title', 'Data Staf')
@section('heading', 'Data Barista & Staf Operasional')

@section('content')
<div class="space-y-5">
    <div class="grid gap-3 sm:grid-cols-2">
        <div class="rounded-xl border border-[#1e293b] bg-[#121824] p-4"><span class="text-[10px] font-semibold uppercase tracking-wider text-slate-400">TOTAL PERSONEL</span><strong class="mt-2 block text-2xl font-extrabold text-white">{{ $stafs->count() }}</strong><span class="text-[10px] text-slate-500">Akun staf terdaftar</span></div>
        <div class="rounded-xl border border-emerald-500/20 bg-[#121824] p-4"><span class="text-[10px] font-semibold uppercase tracking-wider text-slate-400">AKUN DIBUAT BULAN INI</span><strong class="mt-2 block text-2xl font-extrabold text-emerald-400">{{ $stafs->filter(fn ($staf) => $staf->created_at?->isCurrentMonth())->count() }}</strong><span class="text-[10px] text-slate-500">Personel baru</span></div>
    </div>
    <details class="rounded-xl border border-[#1e293b] bg-[#121824] p-5"><summary class="flex cursor-pointer list-none items-center justify-between text-xs font-bold text-amber-400"><span class="flex items-center gap-2"><i data-lucide="user-plus" class="h-4 w-4"></i>Tambah barista / staf</span><i data-lucide="chevron-down" class="h-4 w-4"></i></summary><form method="POST" action="{{ route('admin.staf.store') }}" class="mt-4">@csrf
        <h2 class="mb-4 text-sm font-semibold text-white">Tambah akun staf</h2>
        <div class="grid gap-3 sm:grid-cols-3">
            <label class="text-xs text-slate-300">Nama<input name="name" required maxlength="255" value="{{ old('name') }}" class="mt-1 w-full rounded-lg border border-[#26334d] bg-[#0b0f17] px-3 py-2 text-white"></label>
            <label class="text-xs text-slate-300">Email<input name="email" type="email" required value="{{ old('email') }}" class="mt-1 w-full rounded-lg border border-[#26334d] bg-[#0b0f17] px-3 py-2 text-white"></label>
            <label class="text-xs text-slate-300">Kata sandi (minimal 8 karakter)<input name="password" type="password" required minlength="8" class="mt-1 w-full rounded-lg border border-[#26334d] bg-[#0b0f17] px-3 py-2 text-white"></label>
        </div>
        <button class="mt-4 rounded-lg bg-amber-500 px-4 py-2 text-xs font-bold text-slate-950">Simpan staf</button>
    </form></details>
    <div class="grid gap-3 xl:grid-cols-2">
        @forelse ($stafs as $staf)
        <article class="rounded-xl border border-[#1e293b] bg-[#121824] p-4 transition hover:border-amber-500/30">
            <div class="flex items-start justify-between gap-3"><div class="flex min-w-0 items-center gap-3"><span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl border border-amber-500/20 bg-amber-500/10 text-sm font-bold text-amber-400">{{ strtoupper(substr($staf->name, 0, 1)) }}</span><div class="min-w-0"><h2 class="truncate text-sm font-bold text-white">{{ $staf->name }}</h2><p class="truncate text-xs text-slate-400">{{ $staf->email }}</p></div></div><span class="rounded-full border border-emerald-500/20 bg-emerald-500/10 px-2.5 py-1 text-[10px] font-semibold text-emerald-400">Akun aktif</span></div>
            <div class="mt-4 flex items-center justify-between border-t border-[#1e293b] pt-3 text-[10px] text-slate-500"><span>Terdaftar {{ $staf->created_at?->format('d M Y') }}</span><details class="relative"><summary class="cursor-pointer text-amber-400">Edit profil</summary><form method="POST" action="{{ route('admin.staf.update', $staf) }}" class="absolute right-0 z-10 mt-2 grid w-64 gap-2 rounded-xl border border-[#26334d] bg-[#0e1420] p-3 shadow-xl">@csrf @method('PUT')<input name="name" required value="{{ $staf->name }}" class="rounded bg-[#0b0f17] px-2 py-1.5 text-xs text-white"><input name="email" type="email" required value="{{ $staf->email }}" class="rounded bg-[#0b0f17] px-2 py-1.5 text-xs text-white"><input name="password" type="password" minlength="8" placeholder="Kata sandi baru (opsional)" class="rounded bg-[#0b0f17] px-2 py-1.5 text-xs text-white"><button class="rounded bg-amber-500 px-3 py-1.5 text-xs font-semibold text-slate-950">Simpan perubahan</button></form></details></div>
            <form method="POST" action="{{ route('admin.staf.destroy', $staf) }}" onsubmit="return confirm('Hapus akun staf ini?')" class="mt-2">@csrf @method('DELETE')<button class="text-[11px] text-rose-400">Hapus akun</button></form>
        </article>
        @empty
        <p class="rounded-xl border border-dashed border-[#26334d] p-6 text-center text-xs text-slate-400 xl:col-span-2">Belum ada akun staf.</p>
        @endforelse
    </div>
</div>
@endsection
