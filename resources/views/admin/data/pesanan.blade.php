@extends('admin.data.layout')

@section('title', 'Pesanan Diterima')
@section('heading', 'Pesanan Diterima')

@section('content')
<div class="space-y-5">
    <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
        <div class="rounded-xl border border-amber-500/20 bg-[#121824] p-4"><span class="text-[10px] font-semibold uppercase tracking-wider text-slate-400">PESANAN BARU</span><strong class="mt-2 block text-2xl font-extrabold text-amber-400">{{ $pesananBaru->count() }}</strong><span class="text-[10px] text-slate-500">Menunggu diproses</span></div>
        <div class="rounded-xl border border-emerald-500/20 bg-[#121824] p-4"><span class="text-[10px] font-semibold uppercase tracking-wider text-slate-400">SEDANG DIRACIK</span><strong class="mt-2 block text-2xl font-extrabold text-emerald-400">{{ $pesananBrewing->count() }}</strong><span class="text-[10px] text-slate-500">Di stasiun bar</span></div>
        <div class="rounded-xl border border-sky-500/20 bg-[#121824] p-4"><span class="text-[10px] font-semibold uppercase tracking-wider text-slate-400">SIAP DISAJIKAN</span><strong class="mt-2 block text-2xl font-extrabold text-sky-400">{{ $pesananReady->count() }}</strong><span class="text-[10px] text-slate-500">Menunggu pelanggan</span></div>
        <div class="rounded-xl border border-[#1e293b] bg-[#121824] p-4"><span class="text-[10px] font-semibold uppercase tracking-wider text-slate-400">SELESAI HARI INI</span><strong class="mt-2 block text-2xl font-extrabold text-white">{{ $pesananSelesai->count() }}</strong><span class="text-[10px] text-slate-500">Transaksi tuntas</span></div>
    </div>
    <details class="rounded-xl border border-[#1e293b] bg-[#121824] p-5"><summary class="flex cursor-pointer list-none items-center justify-between text-xs font-bold text-amber-400"><span class="flex items-center gap-2"><i data-lucide="plus" class="h-4 w-4"></i>Tambah pesanan</span><i data-lucide="chevron-down" class="h-4 w-4"></i></summary><form method="POST" action="{{ route('admin.pesanan.store') }}" class="mt-4">@csrf
        <h2 class="mb-4 text-sm font-semibold text-white">Buat pesanan</h2>
        <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-5">
            <label class="text-xs text-slate-300">Menu<select name="product_id" required class="mt-1 w-full rounded-lg border border-[#26334d] bg-[#0b0f17] px-3 py-2 text-white"><option value="">Pilih menu</option>@foreach ($products as $product)<option value="{{ $product->id }}">{{ $product->name }} - Rp {{ number_format($product->price, 0, ',', '.') }}</option>@endforeach</select></label>
            <label class="text-xs text-slate-300">Jumlah<input name="quantity" type="number" min="1" value="1" required class="mt-1 w-full rounded-lg border border-[#26334d] bg-[#0b0f17] px-3 py-2 text-white"></label>
            <label class="text-xs text-slate-300">Jenis<select name="order_type" class="mt-1 w-full rounded-lg border border-[#26334d] bg-[#0b0f17] px-3 py-2 text-white"><option value="takeaway">Takeaway</option><option value="dine_in">Dine-in</option></select></label>
            <label class="text-xs text-slate-300">Nomor meja<input name="table_number" maxlength="30" class="mt-1 w-full rounded-lg border border-[#26334d] bg-[#0b0f17] px-3 py-2 text-white"></label>
            <label class="text-xs text-slate-300">Nama pelanggan<input name="customer_name" maxlength="255" class="mt-1 w-full rounded-lg border border-[#26334d] bg-[#0b0f17] px-3 py-2 text-white"></label>
        </div>
        <label class="mt-3 block text-xs text-slate-300">Catatan<input name="notes" maxlength="1000" class="mt-1 w-full rounded-lg border border-[#26334d] bg-[#0b0f17] px-3 py-2 text-white"></label>
        <button @disabled($products->isEmpty()) class="mt-4 rounded-lg bg-amber-500 px-4 py-2 text-xs font-bold text-slate-950 disabled:opacity-50">Simpan pesanan</button>
    </form></details>
    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
        @foreach ([['Pesanan baru', $pesananBaru, 'new'], ['Sedang diracik', $pesananBrewing, 'brewing'], ['Siap disajikan', $pesananReady, 'ready'], ['Selesai hari ini', $pesananSelesai, 'completed']] as [$label, $orders, $status])
        <section class="min-w-0 space-y-3"><h2 class="border-b border-[#1e293b] pb-2 text-sm font-semibold text-white">{{ $label }} <span class="text-xs text-slate-500">{{ $orders->count() }}</span></h2>
            @forelse ($orders as $order)
            <article class="rounded-lg border border-[#1e293b] bg-[#121824] p-4">
                <div class="flex items-start justify-between gap-2"><div><h3 class="font-bold text-amber-400">{{ $order->order_number ?? ('#' . $order->id) }}</h3><p class="mt-1 text-[11px] text-slate-400">{{ $order->order_type === 'dine_in' ? 'Meja ' . ($order->table_number ?: '-') : 'Takeaway' }} · {{ $order->customer_name ?: 'Pelanggan' }}</p></div><span class="text-xs text-white">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span></div>
                <ul class="my-3 space-y-1 border-y border-[#1e293b] py-2 text-xs">@foreach ($order->items as $item)<li>{{ $item->quantity }}x {{ $item->product_name }}</li>@endforeach</ul>
                @if ($order->notes)<p class="mb-3 text-[11px] text-slate-400">{{ $order->notes }}</p>@endif
                @if ($status !== 'completed')<form method="POST" action="{{ route('admin.pesanan.status', $order) }}" class="flex gap-2">@csrf @method('PATCH')<select name="status" class="min-w-0 flex-1 rounded bg-[#0b0f17] px-2 py-1 text-xs text-white"><option value="new" @selected($status === 'new')>Baru</option><option value="brewing" @selected($status === 'brewing')>Diracik</option><option value="ready" @selected($status === 'ready')>Siap</option><option value="completed">Selesai</option><option value="cancelled">Batalkan</option></select><button class="rounded bg-emerald-500 px-2 py-1 text-xs font-bold text-slate-950">Ubah</button></form>@endif
                <form method="POST" action="{{ route('admin.pesanan.destroy', $order) }}" onsubmit="return confirm('Hapus pesanan ini?')" class="mt-2">@csrf @method('DELETE')<button class="text-[11px] text-rose-400">Hapus pesanan</button></form>
            </article>
            @empty
            <p class="rounded-lg border border-dashed border-[#26334d] p-4 text-xs text-slate-500">Tidak ada pesanan.</p>
            @endforelse
        </section>
        @endforeach
    </div>
</div>
@endsection
