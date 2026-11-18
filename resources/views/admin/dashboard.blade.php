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

@if ($salesSummary !== null)
    <section class="space-y-4">
        <div>
            <h2 class="text-lg font-bold text-white">Ringkasan penjualan</h2>
            <p class="text-xs text-slate-400">Menghitung pesanan berstatus selesai.</p>
        </div>
        <a href="{{ route('admin.sales-report') }}" class="inline-flex items-center gap-2 rounded-lg bg-amber-500 px-4 py-2 text-xs font-bold text-slate-950 hover:bg-amber-400">
            <i data-lucide="printer" class="h-4 w-4"></i>
            Cetak ringkasan penjualan
        </a>
        <div class="grid gap-4 md:grid-cols-3">
            @foreach ($salesSummary as $period => $amount)
                <article class="rounded-xl border border-[#1e293b] bg-[#121824] p-4">
                    <div class="flex items-center justify-between text-xs font-semibold text-slate-400">
                        <span>{{ $period }}</span>
                        <i data-lucide="calendar-days" class="h-4 w-4 text-amber-400"></i>
                    </div>
                    <p class="mt-3 text-2xl font-extrabold text-white">Rp {{ number_format($amount, 0, ',', '.') }}</p>
                </article>
            @endforeach
        </div>

        <div class="grid gap-4 xl:grid-cols-2">
            <section class="rounded-xl border border-[#1e293b] bg-[#121824] p-4">
                <div class="mb-4">
                    <h3 class="font-bold text-white">Penjualan harian (7 hari terakhir)</h3>
                    <p class="text-xs text-slate-400">Berdasarkan pesanan berstatus selesai.</p>
                </div>
                <div class="overflow-x-auto">
                    <div class="grid min-w-[560px] grid-cols-7 gap-3">
                        @foreach ($dailySales as $day)
                            <div class="text-center">
                                <div class="flex h-36 items-end rounded-lg bg-[#0b0f17] p-1">
                                    <div
                                        class="w-full rounded-t bg-gradient-to-t from-amber-700 to-amber-400"
                                        style="height: {{ $dailySalesMax > 0 ? max(6, ($day['amount'] / $dailySalesMax) * 100) : 0 }}%"
                                        role="img"
                                        aria-label="{{ $day['label'] }}: Rp {{ number_format($day['amount'], 0, ',', '.') }}"
                                    ></div>
                                </div>
                                <p class="mt-2 text-[10px] text-slate-400">{{ $day['label'] }}</p>
                                <p class="mt-1 text-[10px] font-semibold text-white">Rp {{ number_format($day['amount'], 0, ',', '.') }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>

            <section class="rounded-xl border border-[#1e293b] bg-[#121824] p-4">
                <div class="mb-4">
                    <h3 class="font-bold text-white">Menu terlaris (30 hari terakhir)</h3>
                    <p class="text-xs text-slate-400">Diurutkan berdasarkan jumlah yang terjual.</p>
                </div>
                @forelse ($bestSellingMenus as $menu)
                    <div class="flex items-center justify-between gap-3 border-t border-[#1e293b] py-3 first:border-t-0">
                        <div class="min-w-0">
                            <p class="truncate text-sm font-semibold text-white">{{ $menu->product_name }}</p>
                            <p class="text-xs text-slate-400">{{ number_format($menu->sold_quantity) }} terjual</p>
                        </div>
                        <p class="whitespace-nowrap text-xs font-semibold text-amber-400">Rp {{ number_format($menu->sales_amount, 0, ',', '.') }}</p>
                    </div>
                @empty
                    <p class="border-t border-[#1e293b] py-4 text-sm text-slate-400">Belum ada menu terjual dalam 30 hari terakhir.</p>
                @endforelse
            </section>
        </div>
    </section>
@endif
@endsection
