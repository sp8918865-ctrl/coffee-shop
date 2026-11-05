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
    </section>
@endif
@endsection
