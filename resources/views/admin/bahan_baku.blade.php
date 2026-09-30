<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Warung Luwak Console - Belanja Bahan Baku</title>
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
                <a href="{{ route('admin.menu') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-slate-400 hover:text-white hover:bg-[#182030] transition">
                    <i data-lucide="coffee" class="w-4 h-4"></i>
                    <span>Kelola Menu & Stok</span>
                </a>
                <a href="{{ route('admin.bahan') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl bg-amber-500/10 text-amber-500 border border-amber-500/20 font-semibold">
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
    <main class="flex-1 p-6 lg:p-8 space-y-6 overflow-y-auto flex flex-col justify-between">
        
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

            <!-- PROFILE & NOTIFICATION -->
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

        <!-- CONTAINER KOSONG / PLACEHOLDER CARD -->
        <div class="bg-[#121824] border border-[#1e293b] rounded-2xl flex-1 flex flex-col justify-end p-4 shadow-xl min-h-[500px]">
            <!-- FOOTER CONTAINER -->
            <div class="pt-4 border-t border-[#1e293b] flex flex-col md:flex-row md:items-center justify-between gap-3 text-xs text-slate-400">
                <div class="flex items-center gap-2">
                    <i data-lucide="refresh-cw" class="w-3.5 h-3.5 text-emerald-400 animate-spin"></i>
                    <span>Terakhir disinkronkan dengan Kasir & Kiosk Meja: <strong>Baru saja (09:42:10 WIB)</strong></span>
                </div>
                <div class="flex items-center gap-4">
                    <span>Menampilkan 5 dari 31 Menu</span>
                    <div class="flex items-center gap-1">
                        <button class="p-1 rounded-lg bg-[#0b0f17] border border-[#1e293b] hover:text-white disabled:opacity-50" disabled>
                            <i data-lucide="chevron-left" class="w-4 h-4"></i>
                        </button>
                        <button class="p-1 rounded-lg bg-[#0b0f17] border border-[#1e293b] hover:text-white">
                            <i data-lucide="chevron-right" class="w-4 h-4"></i>
                        </button>
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