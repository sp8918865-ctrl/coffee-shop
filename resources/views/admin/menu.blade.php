<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Warung Luwak Console - Katalog Menu & Stok</title>
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
<body class="min-h-screen flex flex-col md:flex-row antialiased relative">

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
                <a href="{{ route('admin.menu') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl bg-amber-500/10 text-amber-500 border border-amber-500/20 font-semibold">
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

        <!-- TITLE & BUTTON TAMBAH -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold text-white tracking-tight">Katalog Menu & Ketersediaan Stok</h2>
                <p class="text-xs text-slate-400 mt-1">Kelola status menu, harga, dan ketersediaan stok bar secara langsung.</p>
            </div>
            <button onclick="toggleModal('modal-tambah')" class="inline-flex items-center gap-2 px-4 py-2.5 bg-amber-500 hover:bg-amber-600 text-slate-950 font-bold rounded-xl text-xs transition shadow-lg shadow-amber-500/10">
                <i data-lucide="plus" class="w-4 h-4"></i>
                <span>Tambah Menu Baru</span>
            </button>
        </div>

        <!-- BANNER PERINGATAN STOK HABIS -->
        <div id="alert-banner" class="bg-rose-500/10 border border-rose-500/30 rounded-2xl p-4 flex items-center justify-between text-xs">
            <div class="flex items-center gap-3 text-rose-300">
                <i data-lucide="alert-triangle" class="w-5 h-5 text-rose-400 shrink-0"></i>
                <span><strong>3 Menu dinonaktifkan otomatis:</strong> Avocado Float, Matcha Latte, Croissant (Bahan Habis)</span>
            </div>
            <div class="flex items-center gap-3">
                <button class="px-3 py-1.5 bg-rose-500/20 hover:bg-rose-500/30 border border-rose-500/40 text-rose-200 rounded-lg text-[11px] font-semibold transition">
                    Lihat Bahan
                </button>
                <button onclick="document.getElementById('alert-banner').remove()" class="text-slate-400 hover:text-white">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>
        </div>

        <!-- FILTER TAB & PENCARIAN -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="flex items-center gap-1.5 overflow-x-auto pb-2 md:pb-0 text-xs">
                <button class="px-3.5 py-1.5 rounded-xl bg-amber-500/20 text-amber-400 border border-amber-500/30 font-semibold whitespace-nowrap">Semua (31)</button>
                <button class="px-3.5 py-1.5 rounded-xl bg-[#121824] border border-[#1e293b] text-slate-400 hover:text-white font-medium whitespace-nowrap">Kopi (16)</button>
                <button class="px-3.5 py-1.5 rounded-xl bg-[#121824] border border-[#1e293b] text-slate-400 hover:text-white font-medium whitespace-nowrap">Non-Kopi (8)</button>
                <button class="px-3.5 py-1.5 rounded-xl bg-[#121824] border border-[#1e293b] text-slate-400 hover:text-white font-medium whitespace-nowrap">Pastry (7)</button>
            </div>

            <div class="flex items-center gap-3">
                <div class="relative flex-1 md:w-60">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-500">
                        <i data-lucide="search" class="w-3.5 h-3.5"></i>
                    </div>
                    <input type="text" placeholder="Cari menu..." class="w-full bg-[#121824] border border-[#1e293b] rounded-xl pl-9 pr-4 py-2 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-amber-500">
                </div>
                <button class="inline-flex items-center gap-1.5 px-3 py-2 bg-[#121824] border border-[#1e293b] text-slate-400 hover:text-white rounded-xl text-xs whitespace-nowrap">
                    <i data-lucide="filter" class="w-3.5 h-3.5"></i>
                    <span>Hanya Menu Habis</span>
                </button>
            </div>
        </div>

        <!-- TABEL KATALOG MENU -->
        <div class="bg-[#121824] border border-[#1e293b] rounded-2xl overflow-hidden shadow-xl">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-[#1e293b] text-[11px] font-semibold text-slate-400 uppercase tracking-wider bg-[#0e1420]">
                            <th class="py-3.5 px-4">Menu</th>
                            <th class="py-3.5 px-4">Kategori</th>
                            <th class="py-3.5 px-4">Harga</th>
                            <th class="py-3.5 px-4">Status Bahan</th>
                            <th class="py-3.5 px-4 text-center">Status Tayang</th>
                            <th class="py-3.5 px-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#1e293b] text-xs">
                        
                        <!-- ITEM 1 -->
                        <tr class="hover:bg-[#182030]/50 transition">
                            <td class="py-3.5 px-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl bg-slate-800 border border-[#26334d] flex items-center justify-center overflow-hidden shrink-0">
                                        <i data-lucide="coffee" class="w-5 h-5 text-amber-500"></i>
                                    </div>
                                    <div>
                                        <p class="font-bold text-white">Avocado Coffee Float</p>
                                        <p class="text-[10px] text-slate-500 font-mono">SKU-CF-AVO-09</p>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3.5 px-4 text-slate-300 font-medium">Kopi Spesialis</td>
                            <td class="py-3.5 px-4 font-bold text-white">Rp 38.000</td>
                            <td class="py-3.5 px-4">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-rose-500/10 text-rose-400 border border-rose-500/20 text-[10px] font-semibold">
                                    <span class="w-1.5 h-1.5 rounded-full bg-rose-400"></span> Habis (Alpukat: 0g)
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <label class="inline-flex items-center cursor-pointer">
                                    <input type="checkbox" class="sr-only peer">
                                    <div class="w-9 h-5 bg-slate-700 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-amber-500"></div>
                                </label>
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <div class="flex items-center justify-center gap-2 text-slate-400">
                                    <button class="hover:text-amber-500 transition"><i data-lucide="pencil" class="w-4 h-4"></i></button>
                                    <button class="hover:text-rose-400 transition"><i data-lucide="trash-2" class="w-4 h-4"></i></button>
                                </div>
                            </td>
                        </tr>

                        <!-- ITEM 2 -->
                        <tr class="hover:bg-[#182030]/50 transition">
                            <td class="py-3.5 px-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl bg-slate-800 border border-[#26334d] flex items-center justify-center overflow-hidden shrink-0">
                                        <i data-lucide="coffee" class="w-5 h-5 text-amber-500"></i>
                                    </div>
                                    <div>
                                        <p class="font-bold text-white">Kopi Susu Aren Klasik</p>
                                        <p class="text-[10px] text-slate-500 font-mono">SKU-CF-AREN-01</p>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3.5 px-4 text-slate-300 font-medium">Kopi Ekspres</td>
                            <td class="py-3.5 px-4 font-bold text-white">Rp 24.000</td>
                            <td class="py-3.5 px-4">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 text-[10px] font-semibold">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span> Tersedia (92 porsi)
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <label class="inline-flex items-center cursor-pointer">
                                    <input type="checkbox" checked class="sr-only peer">
                                    <div class="w-9 h-5 bg-slate-700 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-amber-500"></div>
                                </label>
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <div class="flex items-center justify-center gap-2 text-slate-400">
                                    <button class="hover:text-amber-500 transition"><i data-lucide="pencil" class="w-4 h-4"></i></button>
                                    <button class="hover:text-rose-400 transition"><i data-lucide="trash-2" class="w-4 h-4"></i></button>
                                </div>
                            </td>
                        </tr>

                        <!-- ITEM 3 -->
                        <tr class="hover:bg-[#182030]/50 transition">
                            <td class="py-3.5 px-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl bg-slate-800 border border-[#26334d] flex items-center justify-center overflow-hidden shrink-0">
                                        <i data-lucide="cup-soda" class="w-5 h-5 text-emerald-400"></i>
                                    </div>
                                    <div>
                                        <p class="font-bold text-white">Matcha Latte Uji Grade</p>
                                        <p class="text-[10px] text-slate-500 font-mono">SKU-NK-MTC-04</p>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3.5 px-4 text-slate-300 font-medium">Non-Kopi</td>
                            <td class="py-3.5 px-4 font-bold text-white">Rp 35.000</td>
                            <td class="py-3.5 px-4">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-rose-500/10 text-rose-400 border border-rose-500/20 text-[10px] font-semibold">
                                    <span class="w-1.5 h-1.5 rounded-full bg-rose-400"></span> Habis (Bubuk: 0g)
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <label class="inline-flex items-center cursor-pointer">
                                    <input type="checkbox" class="sr-only peer">
                                    <div class="w-9 h-5 bg-slate-700 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-amber-500"></div>
                                </label>
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <div class="flex items-center justify-center gap-2 text-slate-400">
                                    <button class="hover:text-amber-500 transition"><i data-lucide="pencil" class="w-4 h-4"></i></button>
                                    <button class="hover:text-rose-400 transition"><i data-lucide="trash-2" class="w-4 h-4"></i></button>
                                </div>
                            </td>
                        </tr>

                        <!-- ITEM 4 -->
                        <tr class="hover:bg-[#182030]/50 transition">
                            <td class="py-3.5 px-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl bg-slate-800 border border-[#26334d] flex items-center justify-center overflow-hidden shrink-0">
                                        <i data-lucide="coffee" class="w-5 h-5 text-amber-500"></i>
                                    </div>
                                    <div>
                                        <p class="font-bold text-white">V60 Luwak Liar Aceh</p>
                                        <p class="text-[10px] text-slate-500 font-mono">SKU-CF-V60-03</p>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3.5 px-4 text-slate-300 font-medium">Manual Slow Bar</td>
                            <td class="py-3.5 px-4 font-bold text-white">Rp 65.000</td>
                            <td class="py-3.5 px-4">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 text-[10px] font-semibold">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span> Tersedia (14 porsi)
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <label class="inline-flex items-center cursor-pointer">
                                    <input type="checkbox" checked class="sr-only peer">
                                    <div class="w-9 h-5 bg-slate-700 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-amber-500"></div>
                                </label>
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <div class="flex items-center justify-center gap-2 text-slate-400">
                                    <button class="hover:text-amber-500 transition"><i data-lucide="pencil" class="w-4 h-4"></i></button>
                                    <button class="hover:text-rose-400 transition"><i data-lucide="trash-2" class="w-4 h-4"></i></button>
                                </div>
                            </td>
                        </tr>

                        <!-- ITEM 5 -->
                        <tr class="hover:bg-[#182030]/50 transition">
                            <td class="py-3.5 px-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl bg-slate-800 border border-[#26334d] flex items-center justify-center overflow-hidden shrink-0">
                                        <i data-lucide="cookie" class="w-5 h-5 text-amber-400"></i>
                                    </div>
                                    <div>
                                        <p class="font-bold text-white">Croissant Mentega Perancis</p>
                                        <p class="text-[10px] text-slate-500 font-mono">SKU-PA-CRS-02</p>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3.5 px-4 text-slate-300 font-medium">Pastry & Bakery</td>
                            <td class="py-3.5 px-4 font-bold text-white">Rp 28.000</td>
                            <td class="py-3.5 px-4">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-rose-500/10 text-rose-400 border border-rose-500/20 text-[10px] font-semibold">
                                    <span class="w-1.5 h-1.5 rounded-full bg-rose-400"></span> Habis (Showcase: 0)
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <label class="inline-flex items-center cursor-pointer">
                                    <input type="checkbox" class="sr-only peer">
                                    <div class="w-9 h-5 bg-slate-700 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-amber-500"></div>
                                </label>
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <div class="flex items-center justify-center gap-2 text-slate-400">
                                    <button class="hover:text-amber-500 transition"><i data-lucide="pencil" class="w-4 h-4"></i></button>
                                    <button class="hover:text-rose-400 transition"><i data-lucide="trash-2" class="w-4 h-4"></i></button>
                                </div>
                            </td>
                        </tr>

                    </tbody>
                </table>
            </div>

            <!-- FOOTER TABEL -->
            <div class="p-4 border-t border-[#1e293b] flex flex-col md:flex-row md:items-center justify-between gap-3 text-xs text-slate-400 bg-[#0e1420]">
                <div class="flex items-center gap-2">
                    <i data-lucide="refresh-cw" class="w-3.5 h-3.5 text-emerald-400 animate-spin"></i>
                    <span>Terakhir disinkronkan dengan Kasir & Kiosk Meja: <strong>Baru saja (09:42:10 WIB)</strong></span>
                </div>
                <div class="flex items-center gap-4">
                    <span>Menampilkan 5 dari 31 Menu</span>
                    <div class="flex items-center gap-1">
                        <button class="p-1 rounded-lg bg-[#121824] border border-[#1e293b] hover:text-white disabled:opacity-50" disabled>
                            <i data-lucide="chevron-left" class="w-4 h-4"></i>
                        </button>
                        <button class="p-1 rounded-lg bg-[#121824] border border-[#1e293b] hover:text-white">
                            <i data-lucide="chevron-right" class="w-4 h-4"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

    </main>

    <!-- MODAL TAMBAH MENU BARU -->
    <div id="modal-tambah" class="fixed inset-0 bg-black/70 backdrop-blur-sm flex items-center justify-center p-4 z-50 hidden">
        <div class="bg-[#121824] border border-[#1e293b] rounded-2xl w-full max-w-md p-6 space-y-5 shadow-2xl">
            <div class="flex justify-between items-center border-b border-[#1e293b] pb-3">
                <h3 class="text-base font-bold text-white flex items-center gap-2">
                    <i data-lucide="coffee" class="w-4 h-4 text-amber-500"></i>
                    Tambah Menu Baru
                </h3>
                <button onclick="toggleModal('modal-tambah')" class="text-slate-400 hover:text-white">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>

            <form action="#" method="POST" class="space-y-4 text-xs">
                <div>
                    <label class="block font-medium text-slate-300 mb-1">Nama Menu</label>
                    <input type="text" placeholder="Contoh: Cold Brew Citrus Nitro" class="w-full bg-[#182030] border border-[#1e293b] rounded-xl px-3 py-2 text-white placeholder-slate-500 focus:outline-none focus:border-amber-500">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-medium text-slate-300 mb-1">Kategori</label>
                        <select class="w-full bg-[#182030] border border-[#1e293b] rounded-xl px-3 py-2 text-white focus:outline-none focus:border-amber-500">
                            <option>Kopi Spesialis</option>
                            <option>Kopi Ekspres</option>
                            <option>Manual Slow Bar</option>
                            <option>Non-Kopi</option>
                            <option>Pastry & Bakery</option>
                        </select>
                    </div>
                    <div>
                        <label class="block font-medium text-slate-300 mb-1">Harga (Rp)</label>
                        <input type="number" placeholder="35000" class="w-full bg-[#182030] border border-[#1e293b] rounded-xl px-3 py-2 text-white placeholder-slate-500 focus:outline-none focus:border-amber-500">
                    </div>
                </div>

                <div>
                    <label class="block font-medium text-slate-300 mb-1">Kode SKU</label>
                    <input type="text" placeholder="SKU-CF-NITRO-01" class="w-full bg-[#182030] border border-[#1e293b] rounded-xl px-3 py-2 text-white placeholder-slate-500 focus:outline-none focus:border-amber-500">
                </div>

                <div class="flex items-center justify-end gap-3 pt-3 border-t border-[#1e293b]">
                    <button type="button" onclick="toggleModal('modal-tambah')" class="px-4 py-2 rounded-xl border border-[#1e293b] text-slate-300 hover:bg-[#182030] font-medium transition">
                        Batal
                    </button>
                    <button type="button" onclick="toggleModal('modal-tambah')" class="px-4 py-2 rounded-xl bg-amber-500 hover:bg-amber-600 text-slate-950 font-bold transition">
                        Simpan Menu
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            lucide.createIcons();
        });

        function toggleModal(id) {
            const modal = document.getElementById(id);
            modal.classList.toggle('hidden');
        }
    </script>
</body>
</html>