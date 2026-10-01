@extends('admin.data.layout')

@section('title', 'Belanja Bahan Baku')
@section('heading', 'Daftar Belanja Bahan Baku')

@section('content')
<div class="space-y-5">
    <div class="grid gap-3 sm:grid-cols-3">
        <div class="rounded-xl border border-[#1e293b] bg-[#121824] p-4"><span class="text-[10px] font-semibold uppercase tracking-wider text-slate-400">BAHAN TERCATAT</span><strong class="mt-2 block text-2xl font-extrabold text-white">{{ $ingredients->count() }}</strong><span class="text-[10px] text-slate-500">Jenis bahan baku</span></div>
        <div class="rounded-xl border border-rose-500/20 bg-[#121824] p-4"><span class="text-[10px] font-semibold uppercase tracking-wider text-slate-400">STOK KRITIS</span><strong class="mt-2 block text-2xl font-extrabold text-rose-400">{{ $ingredients->whereIn('status', ['critical', 'low'])->count() }}</strong><span class="text-[10px] text-slate-500">Di bawah batas minimum</span></div>
        <div class="rounded-xl border border-emerald-500/20 bg-[#121824] p-4"><span class="text-[10px] font-semibold uppercase tracking-wider text-slate-400">STOK AMAN</span><strong class="mt-2 block text-2xl font-extrabold text-emerald-400">{{ $ingredients->where('status', 'available')->count() }}</strong><span class="text-[10px] text-slate-500">Bahan tersedia</span></div>
    </div>
    @if ($ingredients->whereIn('status', ['critical', 'low'])->isNotEmpty())
    <div class="flex items-center gap-3 rounded-xl border border-amber-500/30 bg-amber-500/10 p-4 text-xs text-amber-200"><i data-lucide="triangle-alert" class="h-5 w-5 shrink-0 text-amber-400"></i><span><strong>Perlu restok:</strong> {{ $ingredients->whereIn('status', ['critical', 'low'])->pluck('name')->join(', ') }}</span></div>
    @endif
    <details class="rounded-xl border border-[#1e293b] bg-[#121824] p-5">
        <summary class="flex cursor-pointer list-none items-center justify-between text-xs font-bold text-amber-400"><span class="flex items-center gap-2"><i data-lucide="plus" class="h-4 w-4"></i>Tambah bahan baku</span><i data-lucide="chevron-down" class="h-4 w-4"></i></summary>
    <form method="POST" action="{{ route('admin.bahan.store') }}" class="mt-4">
        @csrf
        <h2 class="mb-4 text-sm font-semibold text-white">Tambah bahan</h2>
        <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
            <label class="text-xs text-slate-300">Nama bahan<input name="name" required maxlength="255" value="{{ old('name') }}" class="mt-1 w-full rounded-lg border border-[#26334d] bg-[#0b0f17] px-3 py-2 text-white"></label>
            <label class="text-xs text-slate-300">Jumlah tersedia<input name="quantity" required type="number" min="0" step="0.001" value="{{ old('quantity', 0) }}" class="mt-1 w-full rounded-lg border border-[#26334d] bg-[#0b0f17] px-3 py-2 text-white"></label>
            <label class="text-xs text-slate-300">Satuan<input name="unit" required maxlength="30" placeholder="g, ml, pcs" value="{{ old('unit') }}" class="mt-1 w-full rounded-lg border border-[#26334d] bg-[#0b0f17] px-3 py-2 text-white"></label>
            <label class="text-xs text-slate-300">Batas minimum<input name="minimum_quantity" required type="number" min="0" step="0.001" value="{{ old('minimum_quantity', 0) }}" class="mt-1 w-full rounded-lg border border-[#26334d] bg-[#0b0f17] px-3 py-2 text-white"></label>
        </div>
        <button class="mt-4 rounded-lg bg-amber-500 px-4 py-2 text-xs font-bold text-slate-950">Simpan bahan</button>
    </form>
    </details>
    <div class="grid gap-3 lg:grid-cols-2">
        @forelse ($ingredients as $ingredient)
        @php($needsRestock = in_array($ingredient->status, ['critical', 'low'], true))
        <article class="rounded-xl border {{ $needsRestock ? 'border-amber-500/30' : 'border-[#1e293b]' }} bg-[#121824] p-4">
            <div class="flex items-start justify-between gap-3"><div class="flex items-center gap-3"><span class="flex h-10 w-10 items-center justify-center rounded-xl {{ $needsRestock ? 'bg-amber-500/10 text-amber-400' : 'bg-emerald-500/10 text-emerald-400' }}"><i data-lucide="package" class="h-5 w-5"></i></span><div><h2 class="text-sm font-bold text-white">{{ $ingredient->name }}</h2><p class="text-[10px] text-slate-500">Batas minimum {{ $ingredient->minimum_quantity }} {{ $ingredient->unit }}</p></div></div><span class="rounded-full border px-2.5 py-1 text-[10px] font-semibold {{ $ingredient->status === 'critical' ? 'border-rose-500/20 bg-rose-500/10 text-rose-400' : ($ingredient->status === 'low' ? 'border-amber-500/20 bg-amber-500/10 text-amber-400' : 'border-emerald-500/20 bg-emerald-500/10 text-emerald-400') }}">{{ ucfirst($ingredient->status) }}</span></div>
            <div class="mt-4 flex items-end justify-between border-t border-[#1e293b] pt-3"><div><span class="block text-[10px] uppercase tracking-wider text-slate-500">Stok tersedia</span><strong class="text-lg text-white">{{ $ingredient->quantity }} <span class="text-xs font-normal text-slate-400">{{ $ingredient->unit }}</span></strong></div><div class="flex items-center gap-3"><details><summary class="cursor-pointer text-xs font-semibold text-amber-400">Edit stok</summary><form method="POST" action="{{ route('admin.bahan.update', $ingredient) }}" class="absolute z-10 mt-2 grid w-64 gap-2 rounded-xl border border-[#26334d] bg-[#0e1420] p-3 shadow-xl">@csrf @method('PUT')<input name="name" required value="{{ $ingredient->name }}" class="rounded bg-[#0b0f17] px-2 py-1.5 text-xs text-white"><input name="quantity" type="number" min="0" step="0.001" required value="{{ $ingredient->quantity }}" class="rounded bg-[#0b0f17] px-2 py-1.5 text-xs text-white"><input name="unit" required value="{{ $ingredient->unit }}" class="rounded bg-[#0b0f17] px-2 py-1.5 text-xs text-white"><input name="minimum_quantity" type="number" min="0" step="0.001" required value="{{ $ingredient->minimum_quantity }}" class="rounded bg-[#0b0f17] px-2 py-1.5 text-xs text-white"><button class="rounded bg-amber-500 px-3 py-1.5 text-xs font-semibold text-slate-950">Simpan perubahan</button></form></details><form method="POST" action="{{ route('admin.bahan.destroy', $ingredient) }}" onsubmit="return confirm('Hapus bahan ini?')">@csrf @method('DELETE')<button class="text-xs text-rose-400">Hapus</button></form></div></div>
        </article>
        @empty
        <p class="rounded-xl border border-dashed border-[#26334d] p-6 text-center text-xs text-slate-400 lg:col-span-2">Belum ada bahan baku. Tambahkan bahan untuk mulai memantau stok.</p>
        @endforelse
    </div>
</div>
@endsection
