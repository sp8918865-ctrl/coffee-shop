<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Warung Luwak Console - Pesanan Diterima</title>
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
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-slate-400 hover:text-white hover:bg-[#182030] transition">
                    <i data-lucide="layout-dashboard" class="w-4 h-4"></i>
                    <span>Dasbor & Pendapatan</span>
                </a>
                <a href="{{ route('admin.pesanan') }}" class="flex items-center justify-between px-3 py-2.5 rounded-xl bg-amber-500/10 text-amber-500 border border-amber-500/20 font-semibold">
                    <div class="flex items-center gap-3">
                        <i data-lucide="receipt" class="w-4 h-4"></i>
                        <span>Pesanan Diterima</span>
                    </div>
                    <span class="px-1.5 py-0.5 rounded-full bg-amber-500 text-slate-950 text-[10px] font-bold">12</span>
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
                    <span>• BAR UTAMA</span>
                    <span>• TERMINAL 01</span>
                </div>
            </div>

            <!-- PROFILE & NOTIFIKASI -->
            <div class="flex items-center gap-3">
                <button class="p-2 rounded-xl bg-[#121824] border border-[#1e293b] text-slate-400 hover:text-white relative">
                    <i data-lucide="bell" class="w-4 h-4"></i>
                    <span class="w-2 h-2 rounded-full bg-amber-500 absolute top-1.5 right-1.5"></span>
                </button>
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

        <!-- TITLE & BAR STATUS -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-3">
                    <h2 class="text-2xl font-bold text-white tracking-tight">Pesanan Diterima</h2>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-[11px] font-semibold">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span> Live Sync: Terhubung (14 Antrean)
                    </span>
                </div>
                <p class="text-xs text-slate-400 mt-1">Pantau antrean pesanan masuk dari Meja QR & Kasir secara real-time.</p>
            </div>

            <div class="flex items-center gap-3">
                <div class="flex items-center gap-1 p-1 bg-[#121824] border border-[#1e293b] rounded-xl text-xs font-medium">
                    <button class="px-3 py-1 rounded-lg bg-amber-500 text-slate-950 font-bold">Semua Jenis</button>
                    <button class="px-3 py-1 rounded-lg text-slate-400 hover:text-white">Dine-In</button>
                    <button class="px-3 py-1 rounded-lg text-slate-400 hover:text-white">Takeaway</button>
                </div>
                <button class="inline-flex items-center gap-2 px-3.5 py-2 bg-amber-500/10 border border-amber-500/30 text-amber-400 hover:bg-amber-500/20 rounded-xl text-xs font-bold transition">
                    <i data-lucide="bell" class="w-4 h-4"></i>
                    <span>Bunyikan Bel Kasir</span>
                </button>
            </div>
        </div>

        <!-- STATS SUMMARY CARDS -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-[#121824] border border-[#1e293b] rounded-2xl p-4 flex items-center justify-between">
                <div>
                    <span class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider">PESANAN BARU</span>
                    <h3 class="text-xl font-extrabold text-amber-500 mt-0.5">3 <span class="text-xs font-normal text-slate-400">Tiket</span></h3>
                    <p class="text-[10px] text-amber-400 font-medium">Perlu Konfirmasi</p>
                </div>
                <div class="w-10 h-10 rounded-xl bg-amber-500/10 border border-amber-500/20 text-amber-500 flex items-center justify-center">
                    <i data-lucide="receipt" class="w-5 h-5"></i>
                </div>
            </div>

            <div class="bg-[#121824] border border-[#1e293b] rounded-2xl p-4 flex items-center justify-between">
                <div>
                    <span class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider">SEDANG DIRACIK</span>
                    <h3 class="text-xl font-extrabold text-emerald-400 mt-0.5">5 <span class="text-xs font-normal text-slate-400">Tiket</span></h3>
                    <p class="text-[10px] text-emerald-400 font-medium">Di Barista Bar</p>
                </div>
                <div class="w-10 h-10 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 flex items-center justify-center">
                    <i data-lucide="cup-soda" class="w-5 h-5"></i>
                </div>
            </div>

            <div class="bg-[#121824] border border-[#1e293b] rounded-2xl p-4 flex items-center justify-between">
                <div>
                    <span class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider">SIAP AMBIL / SAJI</span>
                    <h3 class="text-xl font-extrabold text-sky-400 mt-0.5">4 <span class="text-xs font-normal text-slate-400">Tiket</span></h3>
                    <p class="text-[10px] text-sky-400 font-medium">Menunggu Pelanggan</p>
                </div>
                <div class="w-10 h-10 rounded-xl bg-sky-500/10 border border-sky-500/20 text-sky-400 flex items-center justify-center">
                    <i data-lucide="bell-ring" class="w-5 h-5"></i>
                </div>
            </div>

            <div class="bg-[#121824] border border-[#1e293b] rounded-2xl p-4 flex items-center justify-between">
                <div>
                    <span class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider">SELESAI HARI INI</span>
                    <h3 class="text-xl font-extrabold text-white mt-0.5">138 <span class="text-xs font-normal text-slate-400">Selesai</span></h3>
                    <p class="text-[10px] text-slate-400">Omzet: <strong class="text-amber-400">Rp 3.842.000</strong></p>
                </div>
                <div class="w-10 h-10 rounded-xl bg-slate-800 border border-[#26334d] text-slate-400 flex items-center justify-center">
                    <i data-lucide="check-circle" class="w-5 h-5"></i>
                </div>
            </div>
        </div>

        <!-- KANBAN BOARD BOARD (4 COLUMNS) -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 items-start">
            
            <!-- KOLOM 1: PESANAN BARU -->
            <div class="space-y-3">
                <div class="flex items-center justify-between p-2.5 rounded-xl bg-[#121824] border border-[#1e293b]">
                    <div class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                        <h3 class="text-xs font-bold text-white uppercase">Pesanan Baru</h3>
                    </div>
                    <span class="text-[10px] text-slate-400">Scan Meja / Kasir</span>
                </div>

                <!-- CARD A-21 -->
                <div class="bg-[#121824] border border-amber-500/30 rounded-2xl p-4 space-y-3 relative shadow-lg">
                    <div class="flex justify-between items-start">
                        <div>
                            <div class="flex items-center gap-2">
                                <h4 class="text-lg font-black text-amber-500">#A-21</h4>
                                <span class="px-2 py-0.5 rounded bg-slate-800 text-slate-300 text-[10px] font-bold">Meja 06</span>
                            </div>
                            <p class="text-[10px] text-slate-400 mt-0.5">Dine-In • Masuk 1 mnt lalu</p>
                        </div>
                        <span class="px-2 py-0.5 rounded bg-emerald-500/20 text-emerald-400 text-[10px] font-bold border border-emerald-500/30">
                            Lunas QRIS
                        </span>
                    </div>

                    <div class="space-y-1.5 text-xs text-slate-300 border-t border-b border-[#1e293b] py-2.5">
                        <div class="flex justify-between font-bold text-white">
                            <span>2x Kopi Susu Aren</span>
                            <span>Rp 48k</span>
                        </div>
                        <p class="text-[11px] text-slate-400 pl-2 border-l border-amber-500/50">• 1x Normal Sweet</p>
                        <p class="text-[11px] text-slate-400 pl-2 border-l border-amber-500/50">• 1x Less Sugar (50%)</p>

                        <div class="flex justify-between font-bold text-white pt-1">
                            <span>1x Butter Croissant</span>
                            <span>Rp 26k</span>
                        </div>
                        <p class="text-[10px] text-amber-400 italic pl-2">Catatan: Hangatkan 1 menit</p>
                    </div>

                    <div class="flex justify-between items-center">
                        <span class="text-xs text-slate-400">Total: <strong class="text-white text-sm">Rp 74.000</strong></span>
                    </div>

                    <div class="grid grid-cols-2 gap-2 pt-1">
                        <button class="py-2 bg-rose-500/10 hover:bg-rose-500/20 text-rose-400 font-bold rounded-xl text-xs transition">Tolak</button>
                        <button class="py-2 bg-amber-500 hover:bg-amber-600 text-slate-950 font-bold rounded-xl text-xs transition">Mulai Racik →</button>
                    </div>
                </div>

                <!-- CARD A-22 -->
                <div class="bg-[#121824] border border-[#1e293b] rounded-2xl p-4 space-y-3 relative">
                    <div class="flex justify-between items-start">
                        <div>
                            <div class="flex items-center gap-2">
                                <h4 class="text-lg font-black text-white">#A-22</h4>
                                <span class="px-2 py-0.5 rounded bg-slate-800 text-slate-300 text-[10px] font-bold">Meja 02</span>
                            </div>
                            <p class="text-[10px] text-slate-400 mt-0.5">Dine-In • Masuk 3 mnt lalu</p>
                        </div>
                        <span class="px-2 py-0.5 rounded bg-emerald-500/20 text-emerald-400 text-[10px] font-bold border border-emerald-500/30">
                            Lunas QRIS
                        </span>
                    </div>

                    <div class="space-y-1.5 text-xs text-slate-300 border-t border-b border-[#1e293b] py-2.5">
                        <div class="flex justify-between font-bold text-white">
                            <span>1x V60 Luwak Liar Aceh</span>
                            <span>Rp 65k</span>
                        </div>
                        <p class="text-[11px] text-slate-400 pl-2 border-l border-amber-500/50">• Beans: Aceh Gayo Single Origin</p>
                        <p class="text-[11px] text-slate-400 pl-2 border-l border-amber-500/50">• Server: Ice Brewed (TDS 90)</p>
                    </div>

                    <div class="flex justify-between items-center">
                        <span class="text-xs text-slate-400">Total: <strong class="text-white text-sm">Rp 65.000</strong></span>
                    </div>

                    <div class="grid grid-cols-2 gap-2 pt-1">
                        <button class="py-2 bg-rose-500/10 hover:bg-rose-500/20 text-rose-400 font-bold rounded-xl text-xs transition">Tolak</button>
                        <button class="py-2 bg-amber-500 hover:bg-amber-600 text-slate-950 font-bold rounded-xl text-xs transition">Mulai Racik →</button>
                    </div>
                </div>

            </div>

            <!-- KOLOM 2: SEDANG DIRACIK -->
            <div class="space-y-3">
                <div class="flex items-center justify-between p-2.5 rounded-xl bg-[#121824] border border-[#1e293b]">
                    <div class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                        <h3 class="text-xs font-bold text-white uppercase">Sedang Diracik</h3>
                    </div>
                    <span class="text-[10px] text-slate-400">Barista Station</span>
                </div>

                <!-- CARD A-18 -->
                <div class="bg-[#121824] border border-emerald-500/30 rounded-2xl p-4 space-y-3">
                    <div class="flex justify-between items-start">
                        <div>
                            <div class="flex items-center gap-2">
                                <h4 class="text-lg font-black text-emerald-400">#A-18</h4>
                                <span class="px-2 py-0.5 rounded bg-slate-800 text-slate-300 text-[10px] font-bold">Meja 04</span>
                            </div>
                            <p class="text-[10px] text-slate-400 mt-0.5">Dine-In • Barista: Rangga</p>
                        </div>
                        <span class="flex items-center gap-1 text-amber-400 font-mono text-xs font-bold bg-amber-500/10 px-2 py-0.5 rounded-lg border border-amber-500/20">
                            <i data-lucide="clock" class="w-3 h-3"></i> 03:49
                        </span>
                    </div>

                    <div class="space-y-2 text-xs text-slate-300 border-t border-b border-[#1e293b] py-2.5">
                        <div class="flex items-start gap-2">
                            <input type="checkbox" checked class="mt-0.5 rounded bg-[#182030] border-[#1e293b] text-emerald-400 focus:ring-0">
                            <span class="line-through text-slate-500">1x Kopi Susu Aren (Oatmilk)</span>
                        </div>
                        <div class="flex items-start gap-2">
                            <input type="checkbox" class="mt-0.5 rounded bg-[#182030] border-[#1e293b] text-emerald-400 focus:ring-0">
                            <span class="text-white font-medium">1x Butter Croissant <span class="text-[10px] text-amber-400">(Sedang di-oven)</span></span>
                        </div>
                        <p class="text-[10px] text-slate-400 italic pl-5">Catatan: Es batu dipisah di gelas kecil</p>
                    </div>

                    <button class="w-full py-2 bg-emerald-500 hover:bg-emerald-600 text-slate-950 font-bold rounded-xl text-xs transition">
                        ✓ Tandai Siap Ambil / Saji
                    </button>
                </div>

                <!-- CARD A-19 -->
                <div class="bg-[#121824] border border-[#1e293b] rounded-2xl p-4 space-y-3">
                    <div class="flex justify-between items-start">
                        <div>
                            <div class="flex items-center gap-2">
                                <h4 class="text-lg font-black text-white">#A-19</h4>
                                <span class="px-2 py-0.5 rounded bg-slate-800 text-slate-300 text-[10px] font-bold">Meja 09</span>
                            </div>
                            <p class="text-[10px] text-slate-400 mt-0.5">Dine-In • Barista: Farhan</p>
                        </div>
                        <span class="flex items-center gap-1 text-emerald-400 font-mono text-xs font-bold bg-emerald-500/10 px-2 py-0.5 rounded-lg border border-emerald-500/20">
                            <i data-lucide="clock" class="w-3 h-3"></i> 01:24
                        </span>
                    </div>

                    <div class="space-y-2 text-xs text-slate-300 border-t border-b border-[#1e293b] py-2.5">
                        <div class="flex items-start gap-2">
                            <input type="checkbox" class="mt-0.5 rounded bg-[#182030] border-[#1e293b] text-emerald-400 focus:ring-0">
                            <span class="text-white font-medium">2x Espresso Double Shot (House Blend)</span>
                        </div>
                    </div>

                    <button class="w-full py-2 bg-emerald-500 hover:bg-emerald-600 text-slate-950 font-bold rounded-xl text-xs transition">
                        ✓ Tandai Siap Ambil / Saji
                    </button>
                </div>

            </div>

            <!-- KOLOM 3: SIAP AMBIL / SAJI -->
            <div class="space-y-3">
                <div class="flex items-center justify-between p-2.5 rounded-xl bg-[#121824] border border-[#1e293b]">
                    <div class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-sky-400"></span>
                        <h3 class="text-xs font-bold text-white uppercase">Siap Ambil / Saji</h3>
                    </div>
                    <span class="text-[10px] text-slate-400">Call Pelanggan</span>
                </div>

                <!-- CARD A-17 -->
                <div class="bg-[#121824] border border-sky-500/30 rounded-2xl p-4 space-y-3">
                    <div class="flex justify-between items-start">
                        <div>
                            <div class="flex items-center gap-2">
                                <h4 class="text-lg font-black text-sky-400">#A-17</h4>
                                <span class="px-2 py-0.5 rounded bg-slate-800 text-slate-300 text-[10px] font-bold">Meja 03</span>
                            </div>
                            <p class="text-[10px] text-emerald-400 mt-0.5 flex items-center gap-1">
                                <i data-lucide="check" class="w-3 h-3"></i> Siap sejak 2 mnt lalu
                            </p>
                        </div>
                        <i data-lucide="bell" class="w-4 h-4 text-sky-400"></i>
                    </div>

                    <div class="space-y-1 text-xs text-slate-300 border-t border-b border-[#1e293b] py-2.5">
                        <p class="font-medium text-white">• 1x Matcha Latte Uji Grade</p>
                        <p class="font-medium text-white">• 1x Butter Croissant</p>
                        <p class="text-[10px] text-amber-400 italic mt-1">Notifikasi TV & HP Pengunjung Terkirim</p>
                    </div>

                    <div class="grid grid-cols-2 gap-2">
                        <button class="py-2 bg-[#182030] hover:bg-[#26334d] text-slate-300 font-bold rounded-xl text-xs transition">Panggil (TV)</button>
                        <button class="py-2 bg-sky-500 hover:bg-sky-600 text-slate-950 font-bold rounded-xl text-xs transition">Selesai Saji</button>
                    </div>
                </div>

                <!-- CARD TA-04 -->
                <div class="bg-[#121824] border border-[#1e293b] rounded-2xl p-4 space-y-3">
                    <div class="flex justify-between items-start">
                        <div>
                            <div class="flex items-center gap-2">
                                <h4 class="text-lg font-black text-white">#TA-04</h4>
                                <span class="px-2 py-0.5 rounded bg-amber-500/20 text-amber-400 text-[10px] font-bold">Takeaway</span>
                            </div>
                            <p class="text-[10px] text-slate-400 mt-0.5">Atas Nama: Pak Doni</p>
                        </div>
                        <i data-lucide="shopping-bag" class="w-4 h-4 text-amber-500"></i>
                    </div>

                    <div class="space-y-1 text-xs text-slate-300 border-t border-b border-[#1e293b] py-2.5">
                        <p class="font-medium text-white">• 2x Kopi Susu Aren (Cold)</p>
                        <p class="text-[10px] text-slate-400">Sudah dimasukkan ke kantong takeaway</p>
                    </div>

                    <div class="grid grid-cols-2 gap-2">
                        <button class="py-2 bg-[#182030] hover:bg-[#26334d] text-slate-300 font-bold rounded-xl text-xs transition">Panggil</button>
                        <button class="py-2 bg-emerald-500 hover:bg-emerald-600 text-slate-950 font-bold rounded-xl text-xs transition">✓ Diserahkan</button>
                    </div>
                </div>

            </div>

            <!-- KOLOM 4: SELESAI HARI INI & SHORTCUTS -->
            <div class="space-y-3">
                <div class="flex items-center justify-between p-2.5 rounded-xl bg-[#121824] border border-[#1e293b]">
                    <div class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-slate-500"></span>
                        <h3 class="text-xs font-bold text-white uppercase">Selesai Hari Ini</h3>
                    </div>
                    <span class="text-[10px] text-slate-400">Arsip Cepat</span>
                </div>

                <!-- LIST ITEM SELESAI -->
                <div class="bg-[#121824] border border-[#1e293b] rounded-2xl p-3 space-y-2.5 text-xs">
                    <div class="p-2 rounded-xl bg-[#0e1420] border border-[#1e293b] space-y-1">
                        <div class="flex justify-between items-center">
                            <span class="font-bold text-white">#A-15 (Meja 01)</span>
                            <span class="text-[10px] text-emerald-400">Tuntas 14:38</span>
                        </div>
                        <p class="text-[11px] text-slate-400 truncate">1x Caramel Macchiato, 1x Choco Lava Cake</p>
                        <p class="text-[10px] text-slate-500">Dine-In • Kasir 01 • <strong class="text-slate-300">Rp 68.000</strong></p>
                    </div>

                    <div class="p-2 rounded-xl bg-[#0e1420] border border-[#1e293b] space-y-1">
                        <div class="flex justify-between items-center">
                            <span class="font-bold text-white">#TA-02 (Takeaway)</span>
                            <span class="text-[10px] text-emerald-400">Tuntas 14:34</span>
                        </div>
                        <p class="text-[11px] text-slate-400 truncate">2x Filter V60 Bali Kintamani</p>
                        <p class="text-[10px] text-slate-500">Takeaway (Mba Maya) • <strong class="text-slate-300">Rp 76.000</strong></p>
                    </div>
                </div>

                <!-- PANEL SHORTCUT BARISTA POS -->
                <div class="bg-[#121824] border border-amber-500/20 rounded-2xl p-4 space-y-2">
                    <h4 class="text-xs font-bold text-amber-500 uppercase tracking-wider flex items-center gap-1.5">
                        <i data-lucide="keyboard" class="w-3.5 h-3.5"></i> Shortcut Barista POS
                    </h4>
                    <div class="space-y-1 text-[11px] text-slate-300">
                        <div class="flex justify-between">
                            <span class="font-mono bg-[#182030] px-1.5 py-0.5 rounded border border-[#1e293b] text-slate-400">[Space]</span>
                            <span>Terima pesanan teratas</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="font-mono bg-[#182030] px-1.5 py-0.5 rounded border border-[#1e293b] text-slate-400">[Enter]</span>
                            <span>Tandai siap saji</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="font-mono bg-[#182030] px-1.5 py-0.5 rounded border border-[#1e293b] text-slate-400">[B]</span>
                            <span>Bunyikan Bel Meja</span>
                        </div>
                    </div>
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