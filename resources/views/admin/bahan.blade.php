<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Warung Luwak Console - Daftar Belanja Bahan Baku</title>
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

        <!-- TITLE & ACTION BUTTONS -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold text-white tracking-tight">Daftar Belanja Bahan Baku</h2>
                <p class="text-xs text-slate-400 mt-1">Kebutuhan belanja otomatis terhitung dari resep menu & sisa stok harian.</p>
            </div>
            <div class="flex items-center gap-2">
                <button class="inline-flex items-center gap-2 px-3.5 py-2.5 bg-[#121824] border border-[#1e293b] text-slate-300 hover:text-white rounded-xl text-xs font-semibold transition">
                    <i data-lucide="printer" class="w-4 h-4"></i>
                    <span>Cetak / Ekspor</span>
                </button>
                <button class="inline-flex items-center gap-2 px-4 py-2.5 bg-amber-500 hover:bg-amber-600 text-slate-950 font-bold rounded-xl text-xs transition shadow-lg shadow-amber-500/10">
                    <i data-lucide="plus" class="w-4 h-4"></i>
                    <span>Tambah Belanja</span>
                </button>
            </div>
        </div>

        <!-- STATS / METRIC CARDS -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-[#121824] border border-[#1e293b] rounded-2xl p-4 relative overflow-hidden">
                <div class="flex items-center justify-between text-xs text-slate-400 mb-2">
                    <span class="font-semibold text-[11px] tracking-wider uppercase">ESTIMASI TOTAL BELANJA</span>
                    <i data-lucide="wallet" class="w-4 h-4 text-amber-500"></i>
                </div>
                <h3 class="text-2xl font-extrabold text-amber-500">Rp 1.450.000</h3>
                <p class="text-[10px] text-slate-500 mt-1">*Berdasarkan estimasi harga supplier terdaftar</p>
            </div>

            <div class="bg-[#121824] border border-[#1e293b] rounded-2xl p-4 relative overflow-hidden">
                <div class="flex items-center justify-between text-xs text-slate-400 mb-2">
                    <span class="font-semibold text-[11px] tracking-wider uppercase">BAHAN KRITIS</span>
                    <i data-lucide="alert-triangle" class="w-4 h-4 text-rose-400"></i>
                </div>
                <h3 class="text-2xl font-extrabold text-white">3 Bahan <span class="text-xs font-normal text-rose-400">Habis (0)</span></h3>
                <p class="text-[10px] text-slate-500 mt-1">Sangat memicu menu dinonaktifkan</p>
            </div>

            <div class="bg-[#121824] border border-[#1e293b] rounded-2xl p-4 relative overflow-hidden">
                <div class="flex items-center justify-between text-xs text-slate-400 mb-2">
                    <span class="font-semibold text-[11px] tracking-wider uppercase">STATUS PENGADAAN</span>
                    <i data-lucide="shopping-cart" class="w-4 h-4 text-emerald-400"></i>
                </div>
                <h3 class="text-2xl font-extrabold text-white">2 Belum Dipesan</h3>
                <p class="text-[10px] text-slate-500 mt-1">Segera kirim ke vendor via WhatsApp</p>
            </div>
        </div>

        <!-- MAIN LAYOUT GRID: LEFT (PRIORITIES) & RIGHT (SUPPLIERS & NOTES) -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <!-- LEFT COLUMN (LIST BELANJA - 2 COLS) -->
            <div class="lg:col-span-2 space-y-6">

                <!-- PRIORITAS 1: DARURAT / KRITIS -->
                <div class="space-y-3">
                    <div class="flex items-center justify-between border-b border-[#1e293b] pb-2">
                        <div class="flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                            <h3 class="text-xs font-bold text-white uppercase tracking-wider">Prioritas 1: Stok Habis / Kritis</h3>
                            <span class="px-2 py-0.5 rounded bg-rose-500/20 text-rose-400 text-[10px] font-bold">Darurat</span>
                        </div>
                        <span class="text-[10px] text-slate-400">Segera Beli Sebelum Restok Shift Sore</span>
                    </div>

                    <!-- ITEM 1 -->
                    <div class="bg-[#121824] border border-[#1e293b] rounded-2xl p-4 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 hover:border-rose-500/40 transition">
                        <div class="flex items-start gap-3">
                            <input type="checkbox" class="mt-1 rounded bg-[#182030] border-[#1e293b] text-amber-500 focus:ring-0">
                            <div>
                                <h4 class="text-xs font-bold text-white">Alpukat Mentega Bagus <span class="text-[10px] text-slate-500 font-mono">TGS-FRT-01</span></h4>
                                <div class="flex items-center gap-2 text-[11px] text-slate-400 mt-1">
                                    <span>Kebutuhan: <strong class="text-white">10 kg</strong></span>
                                    <span>•</span>
                                    <span>Rp <strong class="text-white">35.000/kg</strong></span>
                                    <span>•</span>
                                    <span class="text-amber-400">Total: Rp 350.000</span>
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center gap-2 self-end sm:self-auto">
                            <span class="px-2.5 py-1 rounded-full bg-rose-500/10 text-rose-400 border border-rose-500/20 text-[10px] font-semibold">
                                Sisa Stok: 0g
                            </span>
                            <button class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 hover:bg-emerald-500/20 rounded-xl text-xs font-bold transition">
                                <i data-lucide="message-square" class="w-3.5 h-3.5"></i>
                                <span>Pesan WA</span>
                            </button>
                        </div>
                    </div>

                    <!-- ITEM 2 -->
                    <div class="bg-[#121824] border border-[#1e293b] rounded-2xl p-4 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 hover:border-rose-500/40 transition">
                        <div class="flex items-start gap-3">
                            <input type="checkbox" class="mt-1 rounded bg-[#182030] border-[#1e293b] text-amber-500 focus:ring-0">
                            <div>
                                <h4 class="text-xs font-bold text-white">Bubuk Murni Uji Matcha <span class="text-[10px] text-slate-500 font-mono">TGS-NK-MTC-02</span></h4>
                                <div class="flex items-center gap-2 text-[11px] text-slate-400 mt-1">
                                    <span>Kebutuhan: <strong class="text-white">1 kaleng (200g)</strong></span>
                                    <span>•</span>
                                    <span>Rp <strong class="text-white">245.000</strong></span>
                                    <span>•</span>
                                    <span class="text-amber-400">Total: Rp 245.000</span>
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center gap-2 self-end sm:self-auto">
                            <span class="px-2.5 py-1 rounded-full bg-rose-500/10 text-rose-400 border border-rose-500/20 text-[10px] font-semibold">
                                Sisa Stok: 0g
                            </span>
                            <button class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 hover:bg-emerald-500/20 rounded-xl text-xs font-bold transition">
                                <i data-lucide="message-square" class="w-3.5 h-3.5"></i>
                                <span>Pesan WA</span>
                            </button>
                        </div>
                    </div>

                    <!-- ITEM 3 -->
                    <div class="bg-[#121824] border border-[#1e293b] rounded-2xl p-4 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 hover:border-emerald-500/40 transition">
                        <div class="flex items-start gap-3">
                            <input type="checkbox" checked class="mt-1 rounded bg-[#182030] border-[#1e293b] text-amber-500 focus:ring-0">
                            <div>
                                <h4 class="text-xs font-bold text-white">Fresh Milk Pasteurisasi 1L <span class="text-[10px] text-slate-500 font-mono">TGS-MLK-01</span></h4>
                                <div class="flex items-center gap-2 text-[11px] text-slate-400 mt-1">
                                    <span>Kebutuhan: <strong class="text-white">20 liter</strong></span>
                                    <span>•</span>
                                    <span>Rp <strong class="text-white">22.000/L</strong></span>
                                    <span>•</span>
                                    <span class="text-amber-400">Total: Rp 440.000</span>
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center gap-2 self-end sm:self-auto">
                            <span class="px-2.5 py-1 rounded-full bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 text-[10px] font-semibold">
                                Sudah Dipesan / Dikonfirmasi
                            </span>
                            <button class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-slate-800 text-slate-400 rounded-xl text-xs font-bold" disabled>
                                <i data-lucide="check" class="w-3.5 h-3.5"></i>
                                <span>Selesai</span>
                            </button>
                        </div>
                    </div>

                </div>

                <!-- PRIORITAS 2: PENYESUAIAN STOK MINGGUAN -->
                <div class="space-y-3 pt-4">
                    <div class="flex items-center justify-between border-b border-[#1e293b] pb-2">
                        <div class="flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                            <h3 class="text-xs font-bold text-white uppercase tracking-wider">Prioritas 2: Stok Hampir Menipis</h3>
                            <span class="px-2 py-0.5 rounded bg-amber-500/20 text-amber-400 text-[10px] font-bold">Restok Harian</span>
                        </div>
                        <span class="text-[10px] text-slate-400">Untuk Menjaga Ketersediaan Harian</span>
                    </div>

                    <!-- ITEM 4 -->
                    <div class="bg-[#121824] border border-[#1e293b] rounded-2xl p-4 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 hover:border-amber-500/40 transition">
                        <div class="flex items-start gap-3">
                            <input type="checkbox" class="mt-1 rounded bg-[#182030] border-[#1e293b] text-amber-500 focus:ring-0">
                            <div>
                                <h4 class="text-xs font-bold text-white">Gula Aren Cair Organik <span class="text-[10px] text-slate-500 font-mono">TGS-SGR-01</span></h4>
                                <div class="flex items-center gap-2 text-[11px] text-slate-400 mt-1">
                                    <span>Kebutuhan: <strong class="text-white">5 Liter</strong></span>
                                    <span>•</span>
                                    <span>Rp <strong class="text-white">37.000/L</strong></span>
                                    <span>•</span>
                                    <span class="text-amber-400">Total: Rp 185.000</span>
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center gap-2 self-end sm:self-auto">
                            <span class="px-2.5 py-1 rounded-full bg-amber-500/10 text-amber-400 border border-amber-500/20 text-[10px] font-semibold">
                                Sisa Stok: 800ml
                            </span>
                            <button class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 hover:bg-emerald-500/20 rounded-xl text-xs font-bold transition">
                                <i data-lucide="message-square" class="w-3.5 h-3.5"></i>
                                <span>Pesan WA</span>
                            </button>
                        </div>
                    </div>

                    <!-- ITEM 5 -->
                    <div class="bg-[#121824] border border-[#1e293b] rounded-2xl p-4 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 hover:border-amber-500/40 transition">
                        <div class="flex items-start gap-3">
                            <input type="checkbox" class="mt-1 rounded bg-[#182030] border-[#1e293b] text-amber-500 focus:ring-0">
                            <div>
                                <h4 class="text-xs font-bold text-white">Biji Kopi Luwak Liar Single Origin <span class="text-[10px] text-slate-500 font-mono">TGS-CF-LWK-01</span></h4>
                                <div class="flex items-center gap-2 text-[11px] text-slate-400 mt-1">
                                    <span>Kebutuhan: <strong class="text-white">1 kg</strong></span>
                                    <span>•</span>
                                    <span>Rp <strong class="text-white">210.000/kg</strong></span>
                                    <span>•</span>
                                    <span class="text-amber-400">Total: Rp 210.000</span>
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center gap-2 self-end sm:self-auto">
                            <span class="px-2.5 py-1 rounded-full bg-amber-500/10 text-amber-400 border border-amber-500/20 text-[10px] font-semibold">
                                Sisa Stok: 350g
                            </span>
                            <button class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 hover:bg-emerald-500/20 rounded-xl text-xs font-bold transition">
                                <i data-lucide="message-square" class="w-3.5 h-3.5"></i>
                                <span>Pesan WA</span>
                            </button>
                        </div>
                    </div>

                    <!-- ITEM 6 -->
                    <div class="bg-[#121824] border border-[#1e293b] rounded-2xl p-4 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 hover:border-amber-500/40 transition">
                        <div class="flex items-start gap-3">
                            <input type="checkbox" class="mt-1 rounded bg-[#182030] border-[#1e293b] text-amber-500 focus:ring-0">
                            <div>
                                <h4 class="text-xs font-bold text-white">Paper Cup 12oz & Tutup Sablon <span class="text-[10px] text-slate-500 font-mono">TGS-PKG-01</span></h4>
                                <div class="flex items-center gap-2 text-[11px] text-slate-400 mt-1">
                                    <span>Kebutuhan: <strong class="text-white">2 Dus (1.000pcs)</strong></span>
                                    <span>•</span>
                                    <span>Rp <strong class="text-white">160.000/dus</strong></span>
                                    <span>•</span>
                                    <span class="text-amber-400">Total: Rp 320.000</span>
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center gap-2 self-end sm:self-auto">
                            <span class="px-2.5 py-1 rounded-full bg-amber-500/10 text-amber-400 border border-amber-500/20 text-[10px] font-semibold">
                                Sisa Stok: 120pcs
                            </span>
                            <button class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 hover:bg-emerald-500/20 rounded-xl text-xs font-bold transition">
                                <i data-lucide="message-square" class="w-3.5 h-3.5"></i>
                                <span>Pesan WA</span>
                            </button>
                        </div>
                    </div>

                </div>

            </div>

            <!-- RIGHT COLUMN (SIDEBAR PANEL SUPPLIER & NOTES) -->
            <div class="space-y-6">
                
                <!-- CONTACT SUPPLIER CARD -->
                <div class="bg-[#121824] border border-[#1e293b] rounded-2xl p-5 space-y-4">
                    <div class="flex items-center justify-between border-b border-[#1e293b] pb-3">
                        <h3 class="text-xs font-bold text-white uppercase tracking-wider">Kontak Mitra Supplier</h3>
                        <span class="text-[10px] text-emerald-400 font-bold">4 VENDOR AKTIF</span>
                    </div>

                    <div class="space-y-3 text-xs">
                        <div class="flex items-center justify-between p-2.5 rounded-xl bg-[#0e1420] border border-[#1e293b]">
                            <div>
                                <p class="font-bold text-white">Pasar Induk Buah</p>
                                <p class="text-[10px] text-slate-400 font-mono">Pak Haji • 0812-8877-9900</p>
                            </div>
                            <button class="p-2 rounded-lg bg-emerald-500/10 text-emerald-400 hover:bg-emerald-500/20 transition">
                                <i data-lucide="message-circle" class="w-4 h-4"></i>
                            </button>
                        </div>

                        <div class="flex items-center justify-between p-2.5 rounded-xl bg-[#0e1420] border border-[#1e293b]">
                            <div>
                                <p class="font-bold text-white">Matcha Co Bandung</p>
                                <p class="text-[10px] text-slate-400 font-mono">Ibu Siska • 0815-7766-3300</p>
                            </div>
                            <button class="p-2 rounded-lg bg-emerald-500/10 text-emerald-400 hover:bg-emerald-500/20 transition">
                                <i data-lucide="message-circle" class="w-4 h-4"></i>
                            </button>
                        </div>

                        <div class="flex items-center justify-between p-2.5 rounded-xl bg-[#0e1420] border border-[#1e293b]">
                            <div>
                                <p class="font-bold text-white">Agra Dairy Susu</p>
                                <p class="text-[10px] text-slate-400 font-mono">Pak Budi • 0821-2233-4455</p>
                            </div>
                            <button class="p-2 rounded-lg bg-emerald-500/10 text-emerald-400 hover:bg-emerald-500/20 transition">
                                <i data-lucide="message-circle" class="w-4 h-4"></i>
                            </button>
                        </div>

                        <div class="flex items-center justify-between p-2.5 rounded-xl bg-[#0e1420] border border-[#1e293b]">
                            <div>
                                <p class="font-bold text-white">Kemasan Mandiri Bar</p>
                                <p class="text-[10px] text-slate-400 font-mono">CS • 0811-1233-[4411]</p>
                            </div>
                            <button class="p-2 rounded-lg bg-emerald-500/10 text-emerald-400 hover:bg-emerald-500/20 transition">
                                <i data-lucide="message-circle" class="w-4 h-4"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- LOGISTIC NOTES CARD -->
                <div class="bg-[#121824] border border-[#1e293b] rounded-2xl p-5 space-y-3">
                    <h3 class="text-xs font-bold text-white uppercase tracking-wider border-b border-[#1e293b] pb-2">Catatan Logistik Barista</h3>
                    
                    <div class="space-y-2 text-xs text-slate-300">
                        <div class="p-2.5 rounded-xl bg-[#0e1420] border border-[#1e293b] space-y-1">
                            <p class="font-semibold text-amber-400">Alpukat Mentega:</p>
                            <p class="text-[11px] text-slate-400 leading-relaxed">Cek kematangan buah saat pengiriman. Hindari buah yang terlalu lunak.</p>
                        </div>

                        <div class="p-2.5 rounded-xl bg-[#0e1420] border border-[#1e293b] space-y-1">
                            <p class="font-semibold text-amber-400">Fresh Milk Pasteurisasi:</p>
                            <p class="text-[11px] text-slate-400 leading-relaxed">Langsung simpan di chiller dengan suhu 2°C - 4°C.</p>
                        </div>

                        <div class="p-2.5 rounded-xl bg-[#0e1420] border border-[#1e293b] space-y-1">
                            <p class="font-semibold text-amber-400">Sisa Nota & Bon:</p>
                            <p class="text-[11px] text-slate-400 leading-relaxed">Foto dan upload ke drive/WA Kasir sebelum pergantian shift malam.</p>
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