<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Warung Luwak Console - Dashboard</title>
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
            <div class="mb-8">
                <span class="text-[10px] text-amber-500 font-mono tracking-widest uppercase block mb-1">ROASTERY ENGINE OS</span>
                <h1 class="text-base font-bold text-white tracking-wide leading-tight">Warung Luwak</h1>
                <p class="text-[11px] text-slate-400">Specialty & Slow Bar</p>
            </div>

            <!-- NAVIGASI -->
            <nav class="space-y-1 text-xs font-medium">
                <a href="#" class="flex items-center gap-3 px-3 py-2.5 rounded-xl bg-amber-500/10 text-amber-500 border border-amber-500/20 font-semibold">
                    <i data-lucide="layout-dashboard" class="w-4 h-4"></i>
                    <span>Dasbor & Pendapatan</span>
                </a>
                <a href="#" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-slate-400 hover:text-white hover:bg-[#182030] transition">
                    <i data-lucide="coffee" class="w-4 h-4"></i>
                    <span>Kelola Menu & Stok</span>
                </a>
                <a href="#" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-slate-400 hover:text-white hover:bg-[#182030] transition">
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

            <a href="{{ route('login') }}" class="flex items-center gap-2 text-xs font-medium text-rose-400 hover:text-rose-300 transition">
                <i data-lucide="log-out" class="w-4 h-4"></i>
                <span>Keluar / Logout</span>
            </a>
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
                    <span>• BARUTAMA</span>
                    <span>• TERMINAL 01</span>
                </div>
                <div class="text-[11px] text-slate-400 flex items-center gap-2">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                    <span>SISTEM OPERASI KEDAI AKTIF</span>
                    <span>• 18 MEI 2026</span>
                </div>
            </div>

            <!-- PROFILE & FILTER TANGGAL -->
            <div class="flex items-center gap-4">
                <div class="flex items-center gap-1 p-1 bg-[#121824] border border-[#1e293b] rounded-xl text-xs font-medium">
                    <button class="px-3 py-1 rounded-lg bg-amber-500 text-slate-950 font-bold shadow">Hari Ini</button>
                    <button class="px-3 py-1 rounded-lg text-slate-400 hover:text-white">7 Hari</button>
                    <button class="px-3 py-1 rounded-lg text-slate-400 hover:text-white">Bulan Ini</button>
                </div>
                
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

        <!-- METRIC CARDS (4 KOTAK METRIK) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- CARD 1 -->
            <div class="bg-[#121824] border border-[#1e293b] rounded-2xl p-4 flex flex-col justify-between">
                <div class="flex justify-between items-start text-xs text-slate-400 mb-2">
                    <span class="font-semibold tracking-wider text-[11px]">TOTAL PENDAPATAN</span>
                    <i data-lucide="wallet" class="w-4 h-4 text-amber-500"></i>
                </div>
                <div>
                    <h3 class="text-xl font-extrabold text-white">Rp 3.840.000</h3>
                    <div class="flex items-center justify-between text-[11px] mt-2">
                        <span class="text-emerald-400 font-semibold">+14% vs kemarin</span>
                        <span class="text-slate-400">Rerata jam sibuk: <strong class="text-slate-200">Rp 480k/jam</strong></span>
                    </div>
                </div>
            </div>

            <!-- CARD 2 -->
            <div class="bg-[#121824] border border-[#1e293b] rounded-2xl p-4 flex flex-col justify-between">
                <div class="flex justify-between items-start text-xs text-slate-400 mb-2">
                    <span class="font-semibold tracking-wider text-[11px]">RATA-RATA NILAI PESANAN</span>
                    <i data-lucide="receipt" class="w-4 h-4 text-emerald-400"></i>
                </div>
                <div>
                    <h3 class="text-xl font-extrabold text-white">Rp 38.500<span class="text-xs font-normal text-slate-400"> /Meja</span></h3>
                    <div class="flex items-center justify-between text-[11px] mt-2">
                        <span class="text-emerald-400 font-semibold">+4.2% target Rp 35.000</span>
                        <span class="text-slate-400">Items per tiket: <strong class="text-slate-200">2.3 produk</strong></span>
                    </div>
                </div>
            </div>

            <!-- CARD 3 -->
            <div class="bg-[#121824] border border-[#1e293b] rounded-2xl p-4 flex flex-col justify-between">
                <div class="flex justify-between items-start text-xs text-slate-400 mb-2">
                    <span class="font-semibold tracking-wider text-[11px]">KETERSEDIAAN MENU</span>
                    <span class="px-2 py-0.5 rounded bg-rose-500/20 text-rose-400 text-[10px] font-bold">3 Menu HABIS</span>
                </div>
                <div>
                    <p class="text-xs text-slate-300 font-medium">Paling terdampak: V60 Gayo Wine & Cannelé Vanilla</p>
                    <a href="#" class="inline-flex items-center gap-1 text-[11px] text-amber-500 hover:underline mt-3 font-semibold">
                        Periksa Katalog Menu <i data-lucide="arrow-right" class="w-3 h-3"></i>
                    </a>
                </div>
            </div>

            <!-- CARD 4 -->
            <div class="bg-[#121824] border border-[#1e293b] rounded-2xl p-4 flex flex-col justify-between">
                <div class="flex justify-between items-start text-xs text-slate-400 mb-2">
                    <span class="font-semibold tracking-wider text-[11px]">RESTOK GUDANG</span>
                    <span class="px-2 py-0.5 rounded bg-amber-500/20 text-amber-400 text-[10px] font-bold">4 Bahan KRITIS</span>
                </div>
                <div>
                    <p class="text-xs text-slate-300 font-medium truncate">Susu Oat Barista (2L), Fresh Milk (4L), Biji Arabika</p>
                    <button class="w-full mt-3 py-1.5 bg-amber-500/10 hover:bg-amber-500/20 border border-amber-500/30 text-amber-400 rounded-lg text-xs font-semibold transition">
                        Beli Bahan Baku
                    </button>
                </div>
            </div>
        </div>

        <!-- GRAFIK & STATISTIK SIBUK -->
        <div class="bg-[#121824] border border-[#1e293b] rounded-2xl p-6">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-2 mb-6">
                <div>
                    <h3 class="text-sm font-bold text-white flex items-center gap-2">
                        <i data-lucide="trending-up" class="w-4 h-4 text-amber-500"></i>
                        Laju Penjualan & Volume Transaksi Per Jam
                    </h3>
                    <p class="text-xs text-slate-400 mt-0.5">Lonjakan utama tercatat pada jam makan siang (11:00-14:00) dan sesi santai sore/malam (16:00-20:00).</p>
                </div>
                <div class="flex items-center gap-4 text-xs">
                    <span class="flex items-center gap-1.5 text-slate-300">
                        <span class="w-2.5 h-2.5 rounded-sm bg-amber-500"></span> Pendapatan
                    </span>
                    <span class="flex items-center gap-1.5 text-slate-300">
                        <span class="w-2.5 h-2.5 rounded-sm bg-emerald-400"></span> Volume Struk
                    </span>
                </div>
            </div>

            <!-- AREA VISUALISASI GRAFIK (PLACEHOLDER MOCKUP) -->
            <div class="h-48 w-full bg-[#0b0f17] border border-[#1e293b] rounded-xl flex items-end justify-between p-4 relative overflow-hidden">
                <!-- Peak Label -->
                <div class="absolute top-3 left-1/2 -translate-x-1/2 bg-amber-500/20 border border-amber-500/40 px-3 py-1 rounded-full text-[10px] text-amber-400 font-bold">
                    Puncak: 12:30 (Rp 640rb / 22 cup)
                </div>
                
                <!-- Simple Mock Bars/Lines -->
                <div class="w-full h-full flex items-end justify-between gap-2 pt-8">
                    <div class="w-full bg-amber-500/20 h-[20%] rounded-t"></div>
                    <div class="w-full bg-amber-500/30 h-[35%] rounded-t"></div>
                    <div class="w-full bg-amber-500/40 h-[50%] rounded-t"></div>
                    <div class="w-full bg-amber-500/90 h-[90%] rounded-t relative"></div>
                    <div class="w-full bg-amber-500/70 h-[70%] rounded-t"></div>
                    <div class="w-full bg-amber-500/40 h-[45%] rounded-t"></div>
                    <div class="w-full bg-amber-500/50 h-[55%] rounded-t"></div>
                    <div class="w-full bg-amber-500/80 h-[85%] rounded-t"></div>
                    <div class="w-full bg-amber-500/75 h-[75%] rounded-t"></div>
                    <div class="w-full bg-amber-500/30 h-[30%] rounded-t"></div>
                </div>
            </div>

            <div class="mt-4 flex items-center gap-2 text-[11px] text-slate-400 bg-[#182030] p-2.5 rounded-xl border border-[#26334d]">
                <i data-lucide="info" class="w-4 h-4 text-amber-500 shrink-0"></i>
                <span><strong>Catatan Barista:</strong> Efisiensi grinder Synesso terjaga prima di 19.2 detik/shot selama lunch rush.</span>
            </div>
        </div>

        <!-- BOTTOM GRID (MENU TERLARIS & METODE PEMBAYARAN) -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            
            <!-- MENU TERLARIS -->
            <div class="bg-[#121824] border border-[#1e293b] rounded-2xl p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-sm font-bold text-white flex items-center gap-2">
                        <i data-lucide="flame" class="w-4 h-4 text-amber-500"></i>
                        5 Menu Terlaris Hari Ini
                    </h3>
                    <span class="text-[11px] text-emerald-400 font-semibold">Gross Margin &gt; 68%</span>
                </div>

                <div class="space-y-3 text-xs">
                    <!-- Item 1 -->
                    <div class="flex items-center justify-between p-2.5 rounded-xl bg-[#0b0f17] border border-[#1e293b]">
                        <div class="flex items-center gap-3">
                            <span class="w-6 h-6 rounded-lg bg-amber-500/20 text-amber-500 font-bold flex items-center justify-center text-xs">1</span>
                            <div>
                                <p class="font-bold text-white">Kopi Susu Gula Aren Tubruk</p>
                                <p class="text-[10px] text-slate-400">Signature Houseblend • Margin 76%</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="font-bold text-white">58 Cup</p>
                            <p class="text-[10px] text-slate-400">Rp 1.276.000</p>
                        </div>
                    </div>

                    <!-- Item 2 -->
                    <div class="flex items-center justify-between p-2.5 rounded-xl bg-[#0b0f17] border border-[#1e293b]">
                        <div class="flex items-center gap-3">
                            <span class="w-6 h-6 rounded-lg bg-slate-800 text-slate-300 font-bold flex items-center justify-center text-xs">2</span>
                            <div>
                                <p class="font-bold text-white">Butter Croissant Bakar</p>
                                <p class="text-[10px] text-slate-400">Bakery Artisanal • Margin 64%</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="font-bold text-white">34 Pcs</p>
                            <p class="text-[10px] text-slate-400">Rp 816.000</p>
                        </div>
                    </div>

                    <!-- Item 3 -->
                    <div class="flex items-center justify-between p-2.5 rounded-xl bg-[#0b0f17] border border-[#1e293b]">
                        <div class="flex items-center gap-3">
                            <span class="w-6 h-6 rounded-lg bg-slate-800 text-slate-300 font-bold flex items-center justify-center text-xs">3</span>
                            <div>
                                <p class="font-bold text-white">Luwak Drip Single-Origin</p>
                                <p class="text-[10px] text-slate-400">Slow Bar Manual • Margin 82%</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="font-bold text-white">26 Cup</p>
                            <p class="text-[10px] text-slate-400">Rp 1.040.000</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- METODE PEMBAYARAN & REKAP KAS -->
            <div class="bg-[#121824] border border-[#1e293b] rounded-2xl p-6 flex flex-col justify-between">
                <div>
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-sm font-bold text-white flex items-center gap-2">
                            <i data-lucide="pie-chart" class="w-4 h-4 text-emerald-400"></i>
                            Metode Pembayaran
                        </h3>
                        <span class="text-[11px] text-slate-400">142 Struk</span>
                    </div>

                    <!-- RUNDOWN PEMBAYARAN -->
                    <div class="grid grid-cols-2 gap-4 items-center">
                        <div class="flex flex-col items-center justify-center p-4 bg-[#0b0f17] rounded-xl border border-[#1e293b]">
                            <div class="w-20 h-20 rounded-full border-4 border-emerald-400 border-t-amber-500 flex items-center justify-center font-extrabold text-sm text-emerald-400">
                                72%
                            </div>
                            <span class="text-[10px] text-slate-400 mt-2">QRIS Dominan</span>
                        </div>

                        <div class="space-y-2 text-xs">
                            <div class="flex justify-between items-center">
                                <span class="text-slate-400">QRIS Dinamis</span>
                                <span class="font-bold text-white">Rp 2.764.800</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-slate-400">Tunai / Kasir</span>
                                <span class="font-bold text-white">Rp 768.000</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-slate-400">BCA Transfer</span>
                                <span class="font-bold text-white">Rp 307.200</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- LAPORAN BUKU KAS HARIAN -->
                <div class="mt-6 pt-4 border-t border-[#1e293b] flex items-center justify-between bg-[#0b0f17] p-3 rounded-xl">
                    <div>
                        <p class="text-xs font-bold text-white">Laporan Buku Kas Harian</p>
                        <p class="text-[10px] text-slate-400">Format resmi siap cetak untuk rekapitulasi shift.</p>
                    </div>
                    <button class="px-3 py-1.5 bg-amber-500 hover:bg-amber-600 text-slate-950 font-bold rounded-lg text-xs flex items-center gap-1.5 transition">
                        <i data-lucide="download" class="w-3.5 h-3.5"></i>
                        <span>Unduh PDF</span>
                    </button>
                </div>
            </div>

        </div>

    </main>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            lucide.createIcons();
        });
    </script>
</body>
</html>