@extends('admin.data.layout')

@section('title', 'Menu')
@section('heading', 'Daftar Menu')

@section('content')
<section class="overflow-x-auto rounded-xl border border-[#1e293b] bg-[#121824]">
    <table class="w-full min-w-[560px] text-left text-xs">
        <thead class="bg-[#0e1420] text-[10px] uppercase text-slate-400">
            <tr>
                <th class="p-3">Menu</th>
                <th class="p-3">Kategori</th>
                <th class="p-3">Harga</th>
                <th class="p-3">Stok</th>
                <th class="p-3">Ubah Stok</th>
                <th class="p-3">Status</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-[#1e293b]">
            @forelse ($products as $product)
                @php($isAvailable = $product->is_available && $product->stok > 0)
                <tr>
                    <td class="p-3 font-semibold text-white">{{ $product->name }}</td>
                    <td class="p-3 text-slate-300">{{ $product->category?->name ?? '-' }}</td>
                    <td class="p-3 text-slate-300">Rp {{ number_format($product->price, 0, ',', '.') }}</td>
                    <td class="p-3 text-slate-300">{{ $product->stok ?? 0 }}</td>
                    <td class="p-3">
                        <form method="POST" action="{{ route('barista.menu.stock', $product) }}" class="flex items-center gap-2">
                            @csrf
                            @method('PATCH')
                            <input name="stok" type="number" min="0" max="2147483647" step="1" required value="{{ $product->stok ?? 0 }}" aria-label="Stok {{ $product->name }}" class="w-24 rounded-lg border border-[#26334d] bg-[#0b0f17] px-2 py-1.5 text-white">
                            <button class="rounded-lg bg-amber-500 px-3 py-1.5 text-[10px] font-bold text-slate-950">Simpan</button>
                        </form>
                    </td>
                    <td class="p-3">
                        <span class="rounded-full border px-2.5 py-1 text-[10px] font-semibold {{ $isAvailable ? 'border-emerald-500/20 bg-emerald-500/10 text-emerald-400' : 'border-rose-500/20 bg-rose-500/10 text-rose-400' }}">
                            {{ ! $product->is_available ? 'Nonaktif' : ($product->stok > 0 ? 'Tersedia' : 'Stok habis') }}
                        </span>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="p-6 text-center text-slate-400">Belum ada menu yang ditambahkan admin.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</section>
@endsection
