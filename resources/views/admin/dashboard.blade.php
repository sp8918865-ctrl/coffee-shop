<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Warung Luwak Console - Dasbor & Pendapatan</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        body { 
            font-family: 'Inter', sans-serif; 
            background-color: #0b0f17; 
            color: #e2e8f0; 
        }
    </style>
</head>
<body class="min-h-screen flex flex-col md:flex-row antialiased">

    <!-- SIDEBAR KIRI -->
    <aside class="w-full md:w-64 bg-[#0e1420] border-r border-[#1e293b] p-5 flex flex-col justify-between shrink-0">
        <div>
            <!-- LOGO SIDEBAR -->
            <div class="mb-8 flex items-center gap-3">
                <span class="flex h-11 w-11 shrink-0 items-start justify-center overflow-hidden rounded-xl border border-amber-500/30 bg-[#080b10]"><img src="{{ asset('logo.png') }}" alt="Logo Warung Luwak" class="h-[68px] w-[68px] max-w-none object-cover object-top"></span>
                <span><span class="text-[10px] text-amber-500 font-mono tracking-widest uppercase block mb-1">ROASTERY ENGINE OS</span><h1 class="text-base font-bold text-white tracking-wide leading-tight">Warung Luwak</h1><span class="text-[11px] text-slate-400">Specialty & Slow Bar</span></span>
            </div>

            <!-- NAVIGASI -->
            <nav class="space-y-1 text-xs font-medium">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl bg-amber-500/10 text-amber-500 border border-amber-500/20 font-semibold">
                    <i data-lucide="layout-dashboard" class="w-4 h-4"></i>
                    <span>Dasbor & Pendapatan</span>
                </a>
                <a href="{{ route('admin.pesanan') }}" class="flex items-center justify-between px-3 py-2.5 rounded-xl text-slate-400 hover:text-white hover:bg-[#182030] transition">
                    <div class="flex items-center gap-3">
                        <i data-lucide="receipt" class="w-4 h-4"></i>
                        <span>Pesanan Diterima</span>
                    </div>
                </a>
                <a href="{{ route('admin.staf') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-slate-400 hover:text-white hover:bg-[#182030] transition">
                    <i data-lucide="users" class="w-4 h-4"></i>
                    <span>Data Barista & Staf</span>
                </a>
                <a href="{{ route('admin.menu') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-slate-400 hover:text-white hover:bg-[#182030] transition">
                    <i data-lucide="coffee" class="w-4 h-4"></i>
                    <span>Kelola Menu & Stok</span>
                </a>
                <a href="{{ route('admin.bahan') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-slate-400 hover:text-white hover:bg-[#182030] transition">
                    <i data-lucide="shopping-bag" class="w-4 h-4"></i>
                    <span>Belanja Bahan Baku</span>
                </a>
            </nav>
        </div>

        <!-- FOOTER SIDEBAR -->
        <div class="pt-6 border-t border-[#1e293b] space-y-4">
            <div class="text-[11px]">
                <span class="text-amber-500 font-semibold block text-[10px] tracking-wider">TERMINAL READY</span>
                <p class="text-slate-300 font-medium">Mesin Espresso Synesso #01</p>
                <p class="text-emerald-400 text-[10px] flex items-center gap-1 mt-0.5">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span> Terhubung
                </p>
            </div>

            <form method="POST" action="{{ route('logout') }}" class="contents">
                @csrf
                <button type="submit" class="flex items-center gap-2 text-xs font-medium text-rose-400 hover:text-rose-300 transition">
                    <i data-lucide="log-out" class="w-4 h-4"></i>
                    <span>Keluar / Logout</span>
                </button>
            </form>
        </div>
    </aside>

    <!-- KONTEN UTAMA -->
    <main class="flex-1 p-6 lg:p-8 space-y-6 overflow-y-auto">
        
        <!-- HEADER TOP BAR -->
        <header class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-4 border-b border-[#1e293b]">
            <div>
                <div class="flex items-center gap-3 text-xs text-slate-400 mb-1">
                    <span class="flex items-center gap-1.5 font-bold text-white">
                        <span class="w-2 h-2 rounded-full bg-amber-500"></span> Warung Luwak Console
                    </span>
                    <span>• BAR UTAMA</span>
                    <span>• TERMINAL 01</span>
                </div>
                <div class="text-[11px] text-slate-400 flex items-center gap-2">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                    <span>SISTEM OPERASI KEDAI AKTIF</span>
                    <span>• 18 MEI 2026</span>
                </div>
            </div>

            <!-- PROFILE -->
            <div class="flex items-center gap-4">
                <div class="flex items-center gap-2 pl-2 border-l border-[#1e293b]">
                    <div class="w-8 h-8 rounded-full bg-amber-500/20 text-amber-500 flex items-center justify-center font-bold text-xs">
                        <i data-lucide="user" class="w-4 h-4"></i>
                    </div>
                    <div class="text-xs">
                        <p class="font-bold text-white leading-none">Farhan</p>
                        <p class="text-[10px] text-slate-400">Super Admin</p>
                    </div>
                </div>
            </div>
        </header>

        <!-- WELCOME BANNER -->
        <div>
            <h2 class="text-2xl font-bold text-white tracking-tight">Selamat datang kembali, Farhan!</h2>
            <p class="text-xs text-slate-400 mt-1">Ringkasan performa penjualan harian, stabilitas margin seduh, dan pantauan langsung stok bahan slow-bar Anda.</p>
        </div>

        <!-- METRIC CARDS -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            
            <!-- TOTAL PENDAPATAN -->
            <div class="bg-[#121824] border border-[#1e293b] rounded-2xl p-4 flex flex-col justify-between">
                <div class="flex justify-between items-start text-xs text-slate-400 mb-2">
                    <span class="font-semibold tracking-wider text-[11px] uppercase">TOTAL PENDAPATAN</span>
                    <i data-lucide="wallet" class="w-4 h-4 text-amber-500"></i>
                </div>
                <div>
                    <h3 class="text-2xl font-extrabold text-white">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</h3>
                    <div class="flex items-center justify-between text-[11px] mt-2">
                        <span class="text-emerald-400 font-semibold">{{ $totalPesananHariIni }} pesanan hari ini</span>
                        <span class="text-slate-400">Status selesai hari ini</span>
                    </div>
                </div>
            </div>

            <!-- RATA-RATA NILAI PESANAN -->
            <div class="bg-[#121824] border border-[#1e293b] rounded-2xl p-4 flex flex-col justify-between">
                <div class="flex justify-between items-start text-xs text-slate-400 mb-2">
                    <span class="font-semibold tracking-wider text-[11px] uppercase">RATA-RATA NILAI PESANAN</span>
                    <i data-lucide="banknote" class="w-4 h-4 text-emerald-400"></i>
                </div>
                <div>
                    <h3 class="text-2xl font-extrabold text-white">Rp {{ number_format($rataRataPesanan, 0, ',', '.') }}<span class="text-xs font-normal text-slate-400"> /pesanan</span></h3>
                    <div class="flex items-center justify-between text-[11px] mt-2">
                        <span class="text-slate-400">Rata-rata pesanan selesai</span>
                        <span class="text-slate-400">{{ $pesananTerbaru->count() }} terbaru</span>
                    </div>
                </div>
            </div>

            <!-- KETERSEDIAAN MENU -->
            <div class="bg-[#121824] border border-[#1e293b] rounded-2xl p-4 flex flex-col justify-between">
                <div class="flex justify-between items-start text-xs text-slate-400 mb-2">
                    <span class="font-semibold tracking-wider text-[11px] uppercase">KETERSEDIAAN MENU</span>
                    <span class="px-2 py-0.5 rounded bg-rose-500/20 text-rose-400 text-[10px] font-bold">{{ $jumlahMenuHabis }} Menu HABIS</span>
                </div>
                <div>
                    <p class="text-xs text-slate-300 font-medium">{{ $jumlahMenu }} menu terdaftar</p>
                    <a href="{{ route('admin.menu') }}" class="inline-flex items-center gap-1 text-[11px] text-amber-500 hover:underline mt-3 font-semibold">
                        Periksa Katalog Menu <i data-lucide="arrow-right" class="w-3 h-3"></i>
                    </a>
                </div>
            </div>

            <!-- RESTOK GUDANG -->
            <div class="bg-[#121824] border border-[#1e293b] rounded-2xl p-4 flex flex-col justify-between">
                <div class="flex justify-between items-start text-xs text-slate-400 mb-2">
                    <span class="font-semibold tracking-wider text-[11px] uppercase">RESTOK GUDANG</span>
                    <span class="px-2 py-0.5 rounded bg-amber-500/20 text-amber-400 text-[10px] font-bold">{{ $bahanKritis->count() }} Bahan KRITIS</span>
                </div>
                <div>
                    <p class="text-xs text-slate-300 font-medium truncate">{{ $daftarBahanKritis }}</p>
                    <a href="{{ route('admin.bahan') }}" class="w-full mt-3 py-1.5 bg-amber-500/10 hover:bg-amber-500/20 border border-amber-500/30 text-amber-400 rounded-lg text-xs font-semibold transition text-center block">
                        Beli Bahan Baku
                    </a>
                </div>
            </div>

        </div>

        <section class="overflow-x-auto rounded-2xl border border-[#1e293b] bg-[#121824]">
            <div class="border-b border-[#1e293b] p-5">
                <h3 class="text-sm font-bold text-white">Pesanan terbaru</h3>
                <p class="mt-1 text-xs text-slate-400">Data langsung dari transaksi tersimpan.</p>
            </div>
            <table class="w-full min-w-[600px] text-left text-xs">
                <thead class="bg-[#0e1420] text-[10px] uppercase text-slate-400"><tr><th class="p-3">Nomor</th><th class="p-3">Waktu</th><th class="p-3">Item</th><th class="p-3">Status</th><th class="p-3">Total</th></tr></thead>
                <tbody class="divide-y divide-[#1e293b]">
                    @forelse ($pesananTerbaru as $pesanan)
                    <tr><td class="p-3 font-semibold text-amber-400">{{ $pesanan->order_number ?? ('#' . $pesanan->id) }}</td><td class="p-3">{{ $pesanan->created_at?->format('H:i d/m') }}</td><td class="p-3">{{ $pesanan->items->pluck('product_name')->join(', ') ?: '-' }}</td><td class="p-3">{{ ucfirst($pesanan->status) }}</td><td class="p-3">Rp {{ number_format($pesanan->total_amount, 0, ',', '.') }}</td></tr>
                    @empty
                    <tr><td colspan="5" class="p-6 text-center text-slate-400">Belum ada pesanan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </section>

    </main>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            lucide.createIcons();
        });
    </script>
</body>
</html>