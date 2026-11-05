@extends('admin.data.layout')

@section('title', 'Laporan Penjualan')
@section('heading', 'Laporan Penjualan')

@section('content')
    <style>
        @media print {
            @page { size: landscape; margin: 12mm; }
            body, main, .min-h-screen { background: #fff !important; color: #111 !important; }
            aside, header { display: none !important; }
            main { padding: 0 !important; }
            section { break-inside: avoid; }
            th, td { padding: 6px !important; }
        }
    </style>
    @php
        $months = [
            1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember',
        ];
        $days = [
            0 => 'Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu',
        ];
    @endphp

    <div class="flex flex-wrap items-center justify-between gap-3 print:hidden">
        <p class="text-sm text-slate-400">Ringkasan transaksi selesai tahun {{ $now->year }}</p>
        <button type="button" onclick="window.print()" class="inline-flex items-center gap-2 rounded-lg bg-amber-500 px-4 py-2 text-xs font-bold text-slate-950 hover:bg-amber-400">
            <i data-lucide="printer" class="h-4 w-4"></i>
            Cetak laporan
        </button>
    </div>

    <section class="space-y-4">
        <div class="hidden print:block">
            <h2 class="text-xl font-bold text-black">WARUNG LUWAK — Ringkasan Penjualan</h2>
            <p class="text-sm text-black">Periode tahun {{ $now->year }} · Dicetak {{ $now->format('d/m/Y H:i') }}</p>
        </div>
        <div class="grid gap-3 sm:grid-cols-3">
            @foreach ($salesSummary as $period => $amount)
                <article class="rounded-xl border border-[#1e293b] bg-[#121824] p-4 print:border-gray-400 print:bg-white print:text-black">
                    <h3 class="text-xs font-semibold text-slate-400 print:text-black">{{ $period }}</h3>
                    <p class="mt-2 text-xl font-extrabold text-white print:text-black">Rp {{ number_format($amount, 0, ',', '.') }}</p>
                </article>
            @endforeach
        </div>
    </section>

    <section class="space-y-3">
        <div>
            <h2 class="text-lg font-bold text-white print:text-black">Rincian transaksi selesai</h2>
            <p class="text-xs text-slate-400 print:text-black">{{ $orders->count() }} transaksi tahun {{ $now->year }}</p>
        </div>
        <div class="overflow-x-auto rounded-xl border border-[#1e293b] print:overflow-visible print:border-gray-400">
            <table class="w-full min-w-[760px] border-collapse text-left text-xs print:min-w-0 print:text-[10px]">
                <thead class="bg-[#121824] text-slate-300 print:bg-gray-100 print:text-black">
                    <tr>
                        <th class="p-3">No. Pesanan</th>
                        <th class="p-3">Tahun</th>
                        <th class="p-3">Bulan</th>
                        <th class="p-3">Tanggal</th>
                        <th class="p-3">Hari</th>
                        <th class="p-3">Jam</th>
                        <th class="p-3">Menit</th>
                        <th class="p-3 text-right">Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#1e293b] print:divide-gray-300">
                    @forelse ($orders as $order)
                        <tr class="text-slate-200 print:text-black">
                            <td class="whitespace-nowrap p-3">{{ $order->order_number ?? '#'.$order->order_id }}</td>
                            <td class="p-3">{{ $order->created_at->format('Y') }}</td>
                            <td class="p-3">{{ $months[(int) $order->created_at->format('n')] }}</td>
                            <td class="p-3">{{ $order->created_at->format('d') }}</td>
                            <td class="p-3">{{ $days[(int) $order->created_at->format('w')] }}</td>
                            <td class="p-3">{{ $order->created_at->format('H') }}</td>
                            <td class="p-3">{{ $order->created_at->format('i') }}</td>
                            <td class="whitespace-nowrap p-3 text-right font-semibold">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="p-6 text-center text-slate-400 print:text-black">Belum ada transaksi selesai pada tahun ini.</td>
                        </tr>
                    @endforelse
                </tbody>
                @if ($orders->isNotEmpty())
                    <tfoot class="border-t-2 border-[#1e293b] bg-[#121824] font-bold text-white print:border-gray-500 print:bg-gray-100 print:text-black">
                        <tr>
                            <td colspan="7" class="p-3">Total penjualan tahun {{ $now->year }}</td>
                            <td class="whitespace-nowrap p-3 text-right">Rp {{ number_format($orders->sum('total_amount'), 0, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                @endif
            </table>
        </div>
    </section>
@endsection
