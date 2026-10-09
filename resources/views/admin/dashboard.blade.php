@extends('admin.data.layout')

@section('title', 'Dasbor & Pendapatan')
@section('heading', 'Dasbor & Pendapatan')

@section('content')
<div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
    <div class="rounded-xl border border-[#1e293b] bg-[#121824] p-4">
        <div class="mb-3 flex items-center justify-between text-[10px] font-semibold uppercase tracking-wider text-slate-400">
            <span>Total pendapatan</span>
            <i data-lucide="wallet" class="h-4 w-4 text-amber-400"></i>
        </div>
        <div class="text-3xl font-extrabold text-white">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</div>
        <div class="mt-2 text-[11px] text-emerald-400">{{ $totalPesananHariIni }} pesanan hari ini</div>
    </div>

    <div class="rounded-xl border border-[#1e293b] bg-[#121824] p-4">
        <div class="mb-3 flex items-center justify-between text-[10px] font-semibold uppercase tracking-wider text-slate-400">
            <span>Rata-rata nilai pesanan</span>
            <i data-lucide="banknote" class="h-4 w-4 text-emerald-400"></i>
        </div>
        <div class="text-3xl font-extrabold text-white">Rp {{ number_format($rataRataPesanan, 0, ',', '.') }}</div>
        <div class="mt-2 text-[11px] text-slate-400">per pesanan</div>
    </div>

    <div class="rounded-xl border border-[#1e293b] bg-[#121824] p-4">
        <div class="mb-3 flex items-center justify-between text-[10px] font-semibold uppercase tracking-wider text-slate-400">
            <span>Ketersediaan menu</span>
            <i data-lucide="package-check" class="h-4 w-4 text-amber-400"></i>
        </div>
        <div class="text-3xl font-extrabold text-white">{{ $jumlahMenu }}</div>
        <div class="mt-2 text-[11px] text-amber-400">{{ $jumlahMenuHabis }} menu habis</div>
    </div>

    <div class="rounded-xl border border-[#1e293b] bg-[#121824] p-4">
        <div class="mb-3 flex items-center justify-between text-[10px] font-semibold uppercase tracking-wider text-slate-400">
            <span>Stok gudang</span>
            <i data-lucide="warehouse" class="h-4 w-4 text-rose-400"></i>
        </div>
        <div class="text-3xl font-extrabold text-white">{{ $bahanKritis->count() }}</div>
        <div class="mt-2 text-[11px] text-rose-400">{{ $daftarBahanKritis ?: 'Tidak ada bahan kritis' }}</div>
    </div>
</div>

<div class="rounded-xl border border-[#1e293b] bg-[#121824] p-5">
    <div class="mb-4 flex items-center justify-between gap-3">
        <div>
            <h3 class="text-lg font-bold text-white">Pesanan terbaru</h3>
            <p class="text-xs text-slate-400">Data transaksi terakhir yang masuk ke sistem.</p>
        </div>
        <a href="{{ route('admin.pesanan') }}" class="text-xs font-semibold text-amber-400 hover:text-amber-300">Lihat semua →</a>
    </div>

    @if ($pesananTerbaru->isEmpty())
        <div class="rounded-xl border border-dashed border-[#2b3a4d] bg-[#101827] p-6 text-center text-xs text-slate-400">Belum ada pesanan.</div>
    @else
        <div class="overflow-x-auto">
            <table class="w-full min-w-[620px] text-left text-xs">
                <thead class="text-[10px] uppercase tracking-wider text-slate-500">
                    <tr>
                        <th class="pb-3 pr-3">Nomor</th>
                        <th class="pb-3 pr-3">Waktu</th>
                        <th class="pb-3 pr-3">Item</th>
                        <th class="pb-3 pr-3">Status</th>
                        <th class="pb-3">Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#1e293b] text-slate-300">
                    @foreach ($pesananTerbaru as $order)
                        <tr>
                            <td class="py-3 pr-3 font-semibold text-white">#{{ $order->order_id }}</td>
                            <td class="py-3 pr-3">{{ $order->created_at->format('d M Y, H:i') }}</td>
                            <td class="py-3 pr-3">
                                @foreach ($order->items->take(2) as $item)
                                    <span class="block">{{ $item->product->name ?? 'Produk' }}</span>
                                @endforeach
                                @if ($order->items->count() > 2)
                                    <span class="text-slate-500">+{{ $order->items->count() - 2 }} item lain</span>
                                @endif
                            </td>
                            <td class="py-3 pr-3">
                                <span class="rounded-full border px-2 py-1 text-[10px] font-semibold {{ $order->status === 'completed' ? 'border-emerald-500/20 bg-emerald-500/10 text-emerald-400' : 'border-amber-500/20 bg-amber-500/10 text-amber-400' }}">{{ ucfirst($order->status) }}</span>
                            </td>
                            <td class="py-3 font-semibold text-white">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection
