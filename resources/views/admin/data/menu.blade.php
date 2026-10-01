@extends('admin.data.layout')

@section('title', 'Kelola Menu')
@section('heading', 'Katalog Menu & Ketersediaan Stok')

@section('content')
<div class="grid gap-3 sm:grid-cols-3">
    <div class="rounded-xl border border-[#1e293b] bg-[#121824] p-4"><span class="text-[10px] font-semibold uppercase tracking-wider text-slate-400">MENU TERDAFTAR</span><strong class="mt-2 block text-2xl font-extrabold text-white">{{ $totalProducts }}</strong><span class="text-[10px] text-slate-500">Item katalog</span></div>
    <div class="rounded-xl border border-emerald-500/20 bg-[#121824] p-4"><span class="text-[10px] font-semibold uppercase tracking-wider text-slate-400">SIAP DIJUAL</span><strong class="mt-2 block text-2xl font-extrabold text-emerald-400">{{ $availableProducts }}</strong><span class="text-[10px] text-slate-500">Tersedia untuk dipesan</span></div>
    <div class="rounded-xl border border-rose-500/20 bg-[#121824] p-4"><span class="text-[10px] font-semibold uppercase tracking-wider text-slate-400">NONAKTIF</span><strong class="mt-2 block text-2xl font-extrabold text-rose-400">{{ $unavailableProducts }}</strong><span class="text-[10px] text-slate-500">Perlu diperiksa</span></div>
</div>
@if ($unavailableProducts > 0)
<div class="flex items-center gap-3 rounded-xl border border-rose-500/30 bg-rose-500/10 p-4 text-xs text-rose-200"><i data-lucide="alert-triangle" class="h-5 w-5 shrink-0 text-rose-400"></i><span><strong>{{ $unavailableProducts }} menu nonaktif.</strong> Periksa stok dan aktifkan kembali dari daftar menu.</span></div>
@endif
<form method="GET" action="{{ route('admin.menu') }}" class="flex flex-col gap-3 rounded-xl border border-[#1e293b] bg-[#121824] p-3 sm:flex-row sm:items-center">
    <label class="relative min-w-0 flex-1"><i data-lucide="search" class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-500"></i><input name="search" value="{{ request('search') }}" placeholder="Cari nama menu atau SKU..." class="w-full rounded-lg border border-[#26334d] bg-[#0b0f17] py-2 pl-9 pr-3 text-xs text-white placeholder-slate-500"></label>
    <select name="category_id" class="rounded-lg border border-[#26334d] bg-[#0b0f17] px-3 py-2 text-xs text-white"><option value="">Semua kategori ({{ $totalProducts }})</option>@foreach ($categories as $category)<option value="{{ $category->id }}" @selected((string) request('category_id') === (string) $category->id)>{{ $category->name }} ({{ $category->products_count }})</option>@endforeach</select>
    <label class="flex items-center gap-2 whitespace-nowrap text-xs text-slate-300"><input type="checkbox" name="unavailable" value="1" @checked(request()->boolean('unavailable')) class="rounded border-[#26334d] bg-[#0b0f17] text-amber-500">Hanya menu habis</label>
    <button class="rounded-lg border border-amber-500/30 bg-amber-500/10 px-3 py-2 text-xs font-semibold text-amber-300 hover:bg-amber-500/20">Terapkan</button>
</form>
<div class="grid min-w-0 gap-5 xl:grid-cols-[minmax(0,2fr)_minmax(260px,1fr)]">
    <section class="min-w-0 space-y-5">
        <details class="rounded-xl border border-[#1e293b] bg-[#121824] p-5">
            <summary class="flex cursor-pointer list-none items-center justify-between text-xs font-bold text-amber-400"><span class="flex items-center gap-2"><i data-lucide="plus" class="h-4 w-4"></i>Tambah menu baru</span><i data-lucide="chevron-down" class="h-4 w-4"></i></summary>
        <form method="POST" action="{{ route('admin.menu.store') }}" class="mt-4">
            @csrf
            <h2 class="mb-4 text-sm font-semibold text-white">Tambah menu</h2>
            <div class="grid gap-3 sm:grid-cols-2">
                <label class="text-xs text-slate-300">Nama menu<input name="name" required maxlength="255" value="{{ old('name') }}" class="mt-1 w-full rounded-lg border border-[#26334d] bg-[#0b0f17] px-3 py-2 text-white"></label>
                <label class="text-xs text-slate-300">Kategori<select name="category_id" class="mt-1 w-full rounded-lg border border-[#26334d] bg-[#0b0f17] px-3 py-2 text-white"><option value="">Tanpa kategori</option>@foreach ($categories as $category)<option value="{{ $category->id }}">{{ $category->name }}</option>@endforeach</select></label>
                <label class="text-xs text-slate-300">SKU<input name="sku" maxlength="255" value="{{ old('sku') }}" class="mt-1 w-full rounded-lg border border-[#26334d] bg-[#0b0f17] px-3 py-2 text-white"></label>
                <label class="text-xs text-slate-300">Harga (Rp)<input name="price" type="number" min="0" step="0.01" required value="{{ old('price') }}" class="mt-1 w-full rounded-lg border border-[#26334d] bg-[#0b0f17] px-3 py-2 text-white"></label>
            </div>
            <label class="mt-3 flex items-center gap-2 text-xs text-slate-300"><input name="is_available" type="checkbox" value="1" checked class="rounded"> Tersedia untuk dipesan</label>
            <button class="mt-4 rounded-lg bg-amber-500 px-4 py-2 text-xs font-bold text-slate-950 hover:bg-amber-400">Simpan menu</button>
        </form>
        </details>
        <section class="overflow-x-auto rounded-xl border border-[#1e293b] bg-[#121824]">
            <table class="w-full min-w-[700px] text-left text-xs">
                <thead class="bg-[#0e1420] text-[10px] uppercase text-slate-400"><tr><th class="p-3">Menu</th><th class="p-3">Kategori</th><th class="p-3">Harga</th><th class="p-3">Status</th><th class="p-3">Aksi</th></tr></thead>
                <tbody class="divide-y divide-[#1e293b]">
                    @forelse ($products as $product)
                    <tr>
                        <td class="p-3"><strong class="text-white">{{ $product->name }}</strong><span class="block font-mono text-[10px] text-slate-500">{{ $product->sku ?: 'Tanpa SKU' }}</span></td>
                        <td class="p-3">{{ $product->category?->name ?? '-' }}</td>
                        <td class="p-3">Rp {{ number_format($product->price, 0, ',', '.') }}</td>
                        <td class="p-3"><span class="rounded-full border px-2.5 py-1 text-[10px] font-semibold {{ $product->is_available ? 'border-emerald-500/20 bg-emerald-500/10 text-emerald-400' : 'border-rose-500/20 bg-rose-500/10 text-rose-400' }}">{{ $product->is_available ? 'Tersedia' : 'Nonaktif' }}</span></td>
                        <td class="p-3"><details><summary class="cursor-pointer text-amber-400">Edit</summary>
                            <form method="POST" action="{{ route('admin.menu.update', $product) }}" class="mt-2 grid gap-2 rounded-lg border border-[#26334d] p-3">
                                @csrf @method('PUT')
                                <input name="name" required value="{{ $product->name }}" class="rounded bg-[#0b0f17] px-2 py-1 text-white">
                                <select name="category_id" class="rounded bg-[#0b0f17] px-2 py-1 text-white"><option value="">Tanpa kategori</option>@foreach ($categories as $category)<option value="{{ $category->id }}" @selected($product->category_id === $category->id)>{{ $category->name }}</option>@endforeach</select>
                                <input name="sku" value="{{ $product->sku }}" placeholder="SKU" class="rounded bg-[#0b0f17] px-2 py-1 text-white">
                                <input name="price" type="number" min="0" step="0.01" required value="{{ $product->price }}" class="rounded bg-[#0b0f17] px-2 py-1 text-white">
                                <label class="flex items-center gap-2"><input name="is_available" type="checkbox" value="1" @checked($product->is_available)> Tersedia</label>
                                <button class="rounded bg-amber-500 px-3 py-1 font-semibold text-slate-950">Simpan perubahan</button>
                            </form>
                        </details>
                        <form method="POST" action="{{ route('admin.menu.destroy', $product) }}" onsubmit="return confirm('Hapus menu ini?')" class="mt-2">@csrf @method('DELETE')<button class="text-rose-400">Hapus</button></form></td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="p-6 text-center text-slate-400">Belum ada menu. Tambahkan menu melalui formulir di atas.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </section>
    </section>
    <section class="h-fit min-w-0 rounded-xl border border-[#1e293b] bg-[#121824] p-5">
        <h2 class="mb-4 text-sm font-semibold text-white">Kategori</h2>
        <form method="POST" action="{{ route('admin.categories.store') }}" class="flex gap-2">@csrf<input name="name" required maxlength="255" placeholder="Nama kategori" class="min-w-0 flex-1 rounded-lg border border-[#26334d] bg-[#0b0f17] px-3 py-2 text-xs text-white"><button class="rounded-lg bg-amber-500 px-3 py-2 text-xs font-bold text-slate-950">Tambah</button></form>
        <ul class="mt-4 divide-y divide-[#1e293b]">@forelse ($categories as $category)<li class="flex items-center justify-between gap-3 py-2 text-xs"><span>{{ $category->name }}</span><form method="POST" action="{{ route('admin.categories.destroy', $category) }}" onsubmit="return confirm('Hapus kategori ini?')">@csrf @method('DELETE')<button class="text-rose-400">Hapus</button></form></li>@empty<li class="py-3 text-xs text-slate-500">Belum ada kategori.</li>@endforelse</ul>
    </section>
</div>
@endsection
