<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Warung Luwak Console - Data Barista & Staf</title>
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
                <a href="{{ route('admin.pesanan') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-slate-400 hover:text-white hover:bg-[#182030] transition">
                    <i data-lucide="receipt" class="w-4 h-4"></i>
                    <span>Pesanan Diterima</span>
                </a>
                <a href="{{ route('admin.staf') }}" class="flex items-center justify-between px-3 py-2.5 rounded-xl bg-amber-500/10 text-amber-500 border border-amber-500/20 font-semibold">
                    <div class="flex items-center gap-3">
                        <i data-lucide="users" class="w-4 h-4"></i>
                        <span>Data Barista & Staf</span>
                    </div>
                    <span class="px-1.5 py-0.5 rounded-full bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 text-[9px] font-bold">4 On Shift</span>
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

        <!-- SUB HEADER LOG -->
        <div class="flex items-center gap-2 text-[11px] text-slate-400">
            <span>HUMAN CAPITAL & STATION LOG</span>
            <span>•</span>
            <span class="text-emerald-400 font-semibold">Sync Real-Time</span>
        </div>

        <!-- TITLE & ACTION BUTTONS -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold text-white tracking-tight">Data Barista & Staf Operasional</h2>
                <p class="text-xs text-slate-400 mt-1">Kelola profil kru, jadwal shift aktif, penugasan stasiun bar, dan hak akses terminal Warung Luwak.</p>
            </div>
            <div class="flex items-center gap-2">
                <button class="inline-flex items-center gap-2 px-3.5 py-2.5 bg-[#121824] border border-[#1e293b] text-slate-300 hover:text-white rounded-xl text-xs font-semibold transition">
                    <i data-lucide="download" class="w-4 h-4"></i>
                    <span>Unduh Presensi</span>
                </button>
                <button class="inline-flex items-center gap-2 px-3.5 py-2.5 bg-[#121824] border border-[#1e293b] text-slate-300 hover:text-white rounded-xl text-xs font-semibold transition">
                    <i data-lucide="clock" class="w-4 h-4"></i>
                    <span>Atur Shift</span>
                </button>
                <button class="inline-flex items-center gap-2 px-4 py-2.5 bg-amber-500 hover:bg-amber-600 text-slate-950 font-bold rounded-xl text-xs transition shadow-lg shadow-amber-500/10">
                    <i data-lucide="user-plus" class="w-4 h-4"></i>
                    <span>+ Tambah Barista</span>
                </button>
            </div>
        </div>

        <!-- METRIC CARDS SUMMARY -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-[#121824] border border-[#1e293b] rounded-2xl p-4">
                <span class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider">TOTAL PERSONEL</span>
                <h3 class="text-2xl font-extrabold text-white mt-1">8 <span class="text-xs font-normal text-slate-400">Orang</span></h3>
                <p class="text-[10px] text-slate-500 mt-1">4 Barista, 2 Kasir, 2 Pastry</p>
            </div>

            <div class="bg-[#121824] border border-[#1e293b] rounded-2xl p-4">
                <span class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider">SEDANG BERTUGAS</span>
                <h3 class="text-2xl font-extrabold text-emerald-400 mt-1">4 <span class="text-xs font-normal text-slate-400">Personel</span></h3>
                <p class="text-[10px] text-slate-500 mt-1">Shift Pagi: 07:00 - 15:30</p>
            </div>

            <div class="bg-[#121824] border border-[#1e293b] rounded-2xl p-4">
                <span class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider">COVERAGE STASIUN</span>
                <h3 class="text-2xl font-extrabold text-white mt-1">4/4 <span class="text-xs font-normal text-emerald-400">Penuh</span></h3>
                <p class="text-[10px] text-slate-500 mt-1">Espresso, Slow Bar, Dining, Kasir</p>
            </div>

            <div class="bg-[#121824] border border-[#1e293b] rounded-2xl p-4">
                <span class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider">RATIO KECEPATAN SAJI</span>
                <h3 class="text-2xl font-extrabold text-white mt-1">3.8 <span class="text-xs font-normal text-slate-400">Mnt/Cup</span></h3>
                <p class="text-[10px] text-emerald-400 mt-1">Optimal (< 5.0 min target)</p>
            </div>
        </div>

        <!-- MAIN LAYOUT GRID: STAF LIST (LEFT) & SHIFT/PERMISSIONS (RIGHT) -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <!-- LEFT COLUMN: STAF LIST (2 COLS) -->
            <div class="lg:col-span-2 space-y-4">
                
                <!-- TAB FILTER & SEARCH -->
                <div class="flex flex-col sm:flex-row items-center justify-between gap-3 bg-[#121824] border border-[#1e293b] p-2.5 rounded-2xl text-xs">
                    <div class="flex items-center gap-1 overflow-x-auto w-full sm:w-auto">
                        <button class="p-2 bg-[#182030] text-slate-400 hover:text-white rounded-xl">
                            <i data-lucide="search" class="w-4 h-4"></i>
                        </button>
                        <button class="px-3 py-1.5 rounded-xl bg-amber-500/20 text-amber-400 font-semibold border border-amber-500/30 whitespace-nowrap">Semua Staf (8)</button>
                        <button class="px-3 py-1.5 rounded-xl text-slate-400 hover:text-white whitespace-nowrap">Sedang Bertugas (4)</button>
                        <button class="px-3 py-1.5 rounded-xl text-slate-400 hover:text-white whitespace-nowrap">Shift Pagi</button>
                        <button class="px-3 py-1.5 rounded-xl text-slate-400 hover:text-white whitespace-nowrap">Shift Sore</button>
                        <button class="px-3 py-1.5 rounded-xl text-slate-400 hover:text-white whitespace-nowrap">Libur</button>
                    </div>
                </div>

                <!-- STAF LIST CARDS -->
                <div class="space-y-3">
                    
                    <!-- STAF 1 -->
                    <div class="bg-[#121824] border border-[#1e293b] rounded-2xl p-4 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 hover:border-emerald-500/30 transition">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-xl bg-amber-500/10 border border-amber-500/20 flex items-center justify-center text-amber-500 font-bold overflow-hidden">
                                <i data-lucide="user" class="w-6 h-6"></i>
                            </div>
                            <div>
                                <h4 class="text-sm font-bold text-white">Dimas Kurnia</h4>
                                <p class="text-[11px] text-amber-400 font-medium">Head Barista & Senior Roaster</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-6 text-xs text-slate-400 w-full sm:w-auto">
                            <div>
                                <span class="text-[10px] text-slate-500 uppercase block">STASIUN</span>
                                <span class="font-bold text-white">Bar Espresso Synesso</span>
                            </div>
                            <div>
                                <span class="text-[10px] text-slate-500 uppercase block">SHIFT</span>
                                <span class="font-medium text-slate-200">07:00 - 15:30</span>
                            </div>
                            <div class="col-span-2 sm:col-span-1 flex items-center justify-end">
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 text-[10px] font-bold">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span> Bertugas
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- STAF 2 -->
                    <div class="bg-[#121824] border border-[#1e293b] rounded-2xl p-4 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 hover:border-emerald-500/30 transition">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-xl bg-amber-500/10 border border-amber-500/20 flex items-center justify-center text-amber-500 font-bold overflow-hidden">
                                <i data-lucide="user" class="w-6 h-6"></i>
                            </div>
                            <div>
                                <h4 class="text-sm font-bold text-white">Rangga Wibisono</h4>
                                <p class="text-[11px] text-amber-400 font-medium">Senior Barista</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-6 text-xs text-slate-400 w-full sm:w-auto">
                            <div>
                                <span class="text-[10px] text-slate-500 uppercase block">STASIUN</span>
                                <span class="font-bold text-white">Slow Bar (Manual Brew)</span>
                            </div>
                            <div>
                                <span class="text-[10px] text-slate-500 uppercase block">SHIFT</span>
                                <span class="font-medium text-slate-200">07:00 - 15:30</span>
                            </div>
                            <div class="col-span-2 sm:col-span-1 flex items-center justify-end">
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 text-[10px] font-bold">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span> Bertugas
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- STAF 3 -->
                    <div class="bg-[#121824] border border-[#1e293b] rounded-2xl p-4 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 hover:border-emerald-500/30 transition">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-xl bg-slate-800 border border-[#26334d] flex items-center justify-center text-slate-400 font-bold">
                                SP
                            </div>
                            <div>
                                <h4 class="text-sm font-bold text-white">Siti Fauziah</h4>
                                <p class="text-[11px] text-slate-400 font-medium">Kasir & Order Dispatcher</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-6 text-xs text-slate-400 w-full sm:w-auto">
                            <div>
                                <span class="text-[10px] text-slate-500 uppercase block">STASIUN</span>
                                <span class="font-bold text-white">Kasir Terminal 01</span>
                            </div>
                            <div>
                                <span class="text-[10px] text-slate-500 uppercase block">SHIFT</span>
                                <span class="font-medium text-slate-200">07:00 - 15:30</span>
                            </div>
                            <div class="col-span-2 sm:col-span-1 flex items-center justify-end">
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 text-[10px] font-bold">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span> Bertugas
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- STAF 4 -->
                    <div class="bg-[#121824] border border-[#1e293b] rounded-2xl p-4 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 hover:border-amber-500/30 transition">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-xl bg-slate-800 border border-[#26334d] flex items-center justify-center text-slate-400 font-bold">
                                BS
                            </div>
                            <div>
                                <h4 class="text-sm font-bold text-white">Budi Santoso</h4>
                                <p class="text-[11px] text-slate-400 font-medium">Junior Barista</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-6 text-xs text-slate-400 w-full sm:w-auto">
                            <div>
                                <span class="text-[10px] text-slate-500 uppercase block">STASIUN</span>
                                <span class="font-bold text-white">Persiapan Bahan Bar</span>
                            </div>
                            <div>
                                <span class="text-[10px] text-slate-500 uppercase block">SHIFT</span>
                                <span class="font-medium text-slate-200">15:00 - 23:00</span>
                            </div>
                            <div class="col-span-2 sm:col-span-1 flex items-center justify-end">
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-amber-500/10 text-amber-400 border border-amber-500/20 text-[10px] font-bold">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-400"></span> Standby
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- STAF 5 -->
                    <div class="bg-[#121824] border border-[#1e293b] rounded-2xl p-4 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 hover:border-amber-500/30 transition">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-xl bg-slate-800 border border-[#26334d] flex items-center justify-center text-slate-400 font-bold">
                                RA
                            </div>
                            <div>
                                <h4 class="text-sm font-bold text-white">Rian Ardiansyah</h4>
                                <p class="text-[11px] text-slate-400 font-medium">Senior Barista</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-6 text-xs text-slate-400 w-full sm:w-auto">
                            <div>
                                <span class="text-[10px] text-slate-500 uppercase block">STASIUN</span>
                                <span class="font-bold text-white">Bar Espresso (Afternoon)</span>
                            </div>
                            <div>
                                <span class="text-[10px] text-slate-500 uppercase block">SHIFT</span>
                                <span class="font-medium text-slate-200">15:00 - 23:00</span>
                            </div>
                            <div class="col-span-2 sm:col-span-1 flex items-center justify-end">
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-amber-500/10 text-amber-400 border border-amber-500/20 text-[10px] font-bold">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-400"></span> Standby
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- STAF 6 -->
                    <div class="bg-[#121824] border border-[#1e293b] rounded-2xl p-4 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 hover:border-slate-700 transition">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-xl bg-slate-800 border border-[#26334d] flex items-center justify-center text-slate-400 font-bold">
                                MP
                            </div>
                            <div>
                                <h4 class="text-sm font-bold text-white">Maya Puspita</h4>
                                <p class="text-[11px] text-slate-400 font-medium">Barista & Pastry Crew</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-6 text-xs text-slate-400 w-full sm:w-auto">
                            <div>
                                <span class="text-[10px] text-slate-500 uppercase block">STASIUN</span>
                                <span class="font-bold text-white">Libur Mingguan</span>
                            </div>
                            <div>
                                <span class="text-[10px] text-slate-500 uppercase block">SHIFT</span>
                                <span class="font-medium text-slate-400">Off</span>
                            </div>
                            <div class="col-span-2 sm:col-span-1 flex items-center justify-end">
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-slate-800 text-slate-400 border border-slate-700 text-[10px] font-bold">
                                    Libur
                                </span>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- FOOTER LIST -->
                <div class="flex items-center justify-between text-xs text-slate-400 pt-2">
                    <span>Menampilkan 6 dari 8 personel terdaftar</span>
                    <div class="flex items-center gap-4 text-[10px]">
                        <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-emerald-400"></span> Bertugas</span>
                        <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-amber-400"></span> Standby</span>
                        <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-slate-500"></span> Libur</span>
                    </div>
                </div>

            </div>

            <!-- RIGHT COLUMN: JADWAL SHIFT & OTORISASI (1 COL) -->
            <div class="space-y-6">
                
                <!-- PANEL JADWAL SHIFT -->
                <div class="bg-[#121824] border border-[#1e293b] rounded-2xl p-5 space-y-4">
                    <div class="flex items-center justify-between border-b border-[#1e293b] pb-3">
                        <h3 class="text-xs font-bold text-white uppercase tracking-wider">Jadwal Shift</h3>
                        <span class="text-[10px] text-emerald-400 font-bold">• Hari Ini</span>
                    </div>

                    <div class="space-y-3 text-xs">
                        <div class="p-3 rounded-xl bg-[#0e1420] border border-[#1e293b] space-y-1">
                            <div class="flex justify-between items-center">
                                <span class="font-bold text-white">Shift Pagi</span>
                                <span class="text-[10px] text-emerald-400 font-mono">07:00 - 15:30</span>
                            </div>
                            <p class="text-[11px] text-slate-400">4 Personel Bertugas</p>
                            <p class="text-[10px] text-slate-500 truncate">Dimas K., Rangga W., Siti F., +1 Kru</p>
                        </div>

                        <div class="p-3 rounded-xl bg-[#0e1420] border border-[#1e293b] space-y-1">
                            <div class="flex justify-between items-center">
                                <span class="font-bold text-white">Shift Sore</span>
                                <span class="text-[10px] text-amber-400 font-mono">15:00 - 23:00</span>
                            </div>
                            <p class="text-[11px] text-slate-400">3 Personel Bertugas</p>
                            <p class="text-[10px] text-slate-500 truncate">Budi S., Rian A., +1 Kasir</p>
                        </div>
                    </div>

                    <div class="p-3 rounded-xl bg-amber-500/10 border border-amber-500/20 text-xs text-slate-300 space-y-1">
                        <div class="flex items-center gap-1.5 text-amber-400 font-bold text-[11px]">
                            <i data-lucide="info" class="w-3.5 h-3.5"></i>
                            <span>Briefing kalibrasi grinder espresso dilakukan jam 06:45 WIB oleh Head Barista.</span>
                        </div>
                    </div>
                </div>

                <!-- PANEL OTORISASI TERMINAL -->
                <div class="bg-[#121824] border border-[#1e293b] rounded-2xl p-5 space-y-4">
                    <div class="flex items-center justify-between border-b border-[#1e293b] pb-3">
                        <h3 class="text-xs font-bold text-white uppercase tracking-wider">Otorisasi Terminal</h3>
                        <span class="text-[10px] text-emerald-400 font-bold">Aktif</span>
                    </div>

                    <div class="space-y-2.5 text-xs">
                        <div class="flex justify-between items-center">
                            <span class="text-slate-400">Override Barista</span>
                            <span class="text-emerald-400 font-bold">Aktif</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-slate-400">Void Transaksi Kasir</span>
                            <span class="text-amber-400 font-bold">Terbatas</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-slate-400">Akses Penyesuaian Stok</span>
                            <span class="text-slate-200 font-medium">Dimas & Siti</span>
                        </div>
                    </div>

                    <button class="w-full py-2 bg-[#182030] hover:bg-[#26334d] text-slate-300 font-bold rounded-xl text-xs transition border border-[#1e293b] mt-2">
                        Kelola Hak Akses
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