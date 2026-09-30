<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Warung Luwak - Portal Staf & Pengelola</title>
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
<body class="min-h-screen flex flex-col justify-between antialiased selection:bg-amber-500 selection:text-slate-950">

    <!-- TOP DESKTOP HEADER BAR -->
    <header class="w-full border-b border-[#1e293b] bg-[#0e1420]/80 backdrop-blur-md px-8 py-3.5 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 bg-amber-500/10 border border-amber-500/30 rounded-lg flex items-center justify-center overflow-hidden p-1.5">
                <img src="{{ asset('logo.png') }}" alt="Warung Luwak Logo" class="w-full h-full object-contain">
            </div>
            <div>
                <span class="text-sm font-bold text-white tracking-wide block leading-none">WARUNG LUWAK</span>
                <span class="text-[10px] text-slate-400 font-mono">Specialty & Slow Bar OS</span>
            </div>
        </div>

        <div class="flex items-center gap-6">
            <div class="hidden md:flex items-center gap-2 text-xs text-slate-400">
                <i data-lucide="map-pin" class="w-3.5 h-3.5 text-amber-500"></i>
                <span>Roastery Jakarta Selatan</span>
            </div>
            <div class="h-4 w-[1px] bg-[#1e293b] hidden md:block"></div>
            <div class="flex items-center gap-2 px-3 py-1.5 rounded-lg bg-[#182030] border border-[#26334d] text-xs font-medium text-emerald-400">
                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                <span>Terminal #01 Active</span>
            </div>
        </div>
    </header>

    <!-- MAIN CONTAINER -->
    <main class="flex-1 flex items-center justify-center p-6 my-auto">
        <div class="w-full max-w-[460px] bg-[#121824] border border-[#1e293b] rounded-2xl p-8 shadow-2xl shadow-black/60 relative">
            
            <!-- LOGO & HEADER FORM -->
            <div class="flex flex-col items-center text-center mb-6">
                <div class="w-16 h-16 bg-amber-500/10 border border-amber-500/30 rounded-2xl flex items-center justify-center mb-4 shadow-lg shadow-amber-500/5 overflow-hidden p-2.5">
                    <img src="{{ asset('logo.png') }}" alt="Warung Luwak Logo" class="w-full h-full object-contain">
                </div>
                
                <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-[#182030] border border-[#26334d] text-[11px] font-medium text-slate-300 mb-3">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                    <span>POS & OPERASIONAL VER 3.4</span>
                </div>

                <h1 class="text-xl font-bold text-white tracking-tight">Warung Luwak – Portal Staf & Pengelola</h1>
                <p class="text-xs text-slate-400 mt-1 max-w-xs leading-relaxed">
                    Masuk untuk mengelola pesanan, menu, dan operasional kedai
                </p>
            </div>

            <!-- ROLE SWITCH TABS -->
            <div class="grid grid-cols-2 gap-1 p-1 bg-[#0b0f17] border border-[#1e293b] rounded-xl mb-6">
                <button id="btn-admin" type="button" onclick="switchRole('admin')" class="flex items-center justify-center gap-2 py-2 px-3 text-xs font-semibold rounded-lg bg-amber-500 text-slate-950 transition shadow">
                    <i data-lucide="shield-check" class="w-3.5 h-3.5"></i>
                    <span>Admin / Pengelola</span>
                </button>
                <button id="btn-barista" type="button" onclick="switchRole('barista')" class="flex items-center justify-center gap-2 py-2 px-3 text-xs font-medium rounded-lg text-slate-400 hover:text-white transition">
                    <i data-lucide="cup-soda" class="w-3.5 h-3.5"></i>
                    <span>Barista / Kasir</span>
                </button>
            </div>

            <!-- FORM LOGIN (Mengarahkan ke route admin.dashboard saat submit) -->
            <form action="{{ route('admin.dashboard') }}" method="GET" class="space-y-4">
                @csrf

                <!-- INPUT EMAIL / NIP (Manual & Wajib Diisi) -->
                <div>
                    <label class="block text-xs font-medium text-slate-300 mb-1.5">Email / NIP Pegawai</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-500">
                            <i data-lucide="user" class="w-4 h-4"></i>
                        </div>
                        <input type="text" name="login_id" required 
                            placeholder="admin@warungluwak.com atau KOP-ADM-001" 
                            class="w-full bg-[#182030] border border-[#1e293b] rounded-xl pl-10 pr-4 py-2.5 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-amber-500 transition">
                    </div>
                </div>

                <!-- INPUT PASSWORD (Manual & Wajib Diisi) -->
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label class="block text-xs font-medium text-slate-300">Kata Sandi</label>
                        <a href="#" class="text-[11px] text-amber-500 hover:underline">Lupa Kata Sandi?</a>
                    </div>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-500">
                            <i data-lucide="lock" class="w-4 h-4"></i>
                        </div>
                        <input type="password" id="passwordInput" name="password" required 
                            placeholder="Masukkan kata sandi akun" 
                            class="w-full bg-[#182030] border border-[#1e293b] rounded-xl pl-10 pr-10 py-2.5 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-amber-500 transition">
                        <button type="button" onclick="togglePassword()" class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-500 hover:text-slate-300">
                            <i id="eyeIcon" data-lucide="eye" class="w-4 h-4"></i>
                        </button>
                    </div>
                </div>

                <!-- BUTTON SUBMIT -->
                <button type="submit" class="w-full py-3 bg-amber-500 hover:bg-amber-600 text-slate-950 font-bold rounded-xl text-xs flex items-center justify-center gap-2 transition shadow-lg shadow-amber-500/10 mt-2">
                    <span>Masuk ke Sistem Kedai</span>
                    <i data-lucide="arrow-right" class="w-4 h-4"></i>
                </button>
            </form>

            <!-- FOOTER SECURITY -->
            <div class="mt-6 pt-5 border-t border-[#1e293b] text-center space-y-1">
                <div class="flex items-center justify-center gap-1.5 text-[11px] text-slate-400">
                    <i data-lucide="shield-check" class="w-3.5 h-3.5 text-emerald-400"></i>
                    <span>Sistem Terenkripsi TLS 1.3 • Cabang Utama Roastery Jakarta Selatan</span>
                </div>
                <p class="text-[10px] text-slate-500">Khusus personel resmi PT Warung Luwak Nusantara</p>
            </div>

        </div>
    </main>

    <!-- FOOTER DESKTOP -->
    <footer class="w-full border-t border-[#1e293b] bg-[#0e1420]/60 px-8 py-3 text-center md:flex md:justify-between items-center text-[11px] text-slate-500">
        <p>© 2026 PT Warung Luwak Nusantara. All rights reserved.</p>
        <div class="flex items-center justify-center gap-4 mt-1 md:mt-0">
            <a href="#" class="hover:text-slate-300 transition">Bantuan Operasional</a>
            <span>•</span>
            <a href="#" class="hover:text-slate-300 transition">Status Server</a>
            <span>•</span>
            <a href="#" class="hover:text-slate-300 transition">Panduan POS</a>
        </div>
    </footer>

    <!-- JAVASCRIPT -->
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            lucide.createIcons();
        });

        function togglePassword() {
            const input = document.getElementById('passwordInput');
            const icon = document.getElementById('eyeIcon');
            if (input.type === 'password') {
                input.type = 'text';
                icon.setAttribute('data-lucide', 'eye-off');
            } else {
                input.type = 'password';
                icon.setAttribute('data-lucide', 'eye');
            }
            lucide.createIcons();
        }

        function switchRole(role) {
            const btnAdmin = document.getElementById('btn-admin');
            const btnBarista = document.getElementById('btn-barista');

            if (role === 'admin') {
                btnAdmin.className = "flex items-center justify-center gap-2 py-2 px-3 text-xs font-semibold rounded-lg bg-amber-500 text-slate-950 transition shadow";
                btnBarista.className = "flex items-center justify-center gap-2 py-2 px-3 text-xs font-medium rounded-lg text-slate-400 hover:text-white transition";
            } else {
                btnBarista.className = "flex items-center justify-center gap-2 py-2 px-3 text-xs font-semibold rounded-lg bg-amber-500 text-slate-950 transition shadow";
                btnAdmin.className = "flex items-center justify-center gap-2 py-2 px-3 text-xs font-medium rounded-lg text-slate-400 hover:text-white transition";
            }
        }
    </script>
</body>
</html>