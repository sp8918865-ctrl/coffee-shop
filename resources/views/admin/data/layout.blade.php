<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - Warung Luwak</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body class="min-h-screen bg-[#0b0f17] text-slate-200 antialiased">
    <div class="min-h-screen md:flex">
        <aside class="flex w-full shrink-0 flex-col justify-between border-b border-[#1e293b] bg-[#0e1420] p-5 md:min-h-screen md:w-64 md:border-b-0 md:border-r">
            <div>
                <a href="{{ route('admin.dashboard') }}" class="mb-8 block">
                    <span class="flex items-center gap-3">
                        <span class="flex h-11 w-11 shrink-0 items-start justify-center overflow-hidden rounded-xl border border-amber-500/30 bg-[#080b10]"><img src="{{ asset('logo.png') }}" alt="Logo Warung Luwak" class="h-[68px] w-[68px] max-w-none object-cover object-top"></span>
                        <span><span class="mb-1 block text-[10px] font-mono tracking-widest text-amber-500">ROASTERY ENGINE OS</span><span class="font-bold tracking-wide text-white">Warung Luwak</span><span class="block text-[11px] text-slate-400">Specialty & Slow Bar</span></span>
                    </span>
                </a>
                <nav class="flex gap-1 overflow-x-auto text-xs font-medium md:flex-col">
                    <a class="flex items-center gap-3 whitespace-nowrap rounded-xl px-3 py-2.5 {{ request()->routeIs('admin.dashboard') ? 'border border-amber-500/20 bg-amber-500/10 font-semibold text-amber-400' : 'text-slate-400 transition hover:bg-[#182030] hover:text-white' }}" href="{{ route('admin.dashboard') }}"><i data-lucide="layout-dashboard" class="h-4 w-4"></i>Dasbor & Pendapatan</a>
                    <a class="flex items-center gap-3 whitespace-nowrap rounded-xl px-3 py-2.5 {{ request()->routeIs('admin.menu') ? 'border border-amber-500/20 bg-amber-500/10 font-semibold text-amber-400' : 'text-slate-400 transition hover:bg-[#182030] hover:text-white' }}" href="{{ route('admin.menu') }}"><i data-lucide="coffee" class="h-4 w-4"></i>Kelola Menu</a>
                    <a class="flex items-center gap-3 whitespace-nowrap rounded-xl px-3 py-2.5 {{ request()->routeIs('admin.bahan') ? 'border border-amber-500/20 bg-amber-500/10 font-semibold text-amber-400' : 'text-slate-400 transition hover:bg-[#182030] hover:text-white' }}" href="{{ route('admin.bahan') }}"><i data-lucide="shopping-bag" class="h-4 w-4"></i>Stok Bahan</a>
                    <a class="flex items-center gap-3 whitespace-nowrap rounded-xl px-3 py-2.5 {{ request()->routeIs('admin.pesanan') ? 'border border-amber-500/20 bg-amber-500/10 font-semibold text-amber-400' : 'text-slate-400 transition hover:bg-[#182030] hover:text-white' }}" href="{{ route('admin.pesanan') }}"><i data-lucide="receipt" class="h-4 w-4"></i>Pesanan</a>
                    <a class="flex items-center gap-3 whitespace-nowrap rounded-xl px-3 py-2.5 {{ request()->routeIs('admin.staf') ? 'border border-amber-500/20 bg-amber-500/10 font-semibold text-amber-400' : 'text-slate-400 transition hover:bg-[#182030] hover:text-white' }}" href="{{ route('admin.staf') }}"><i data-lucide="users" class="h-4 w-4"></i>Data Barista & Staf</a>
                </nav>
                <form method="POST" action="{{ route('logout') }}" class="mt-2 md:hidden">@csrf<button class="flex items-center gap-2 whitespace-nowrap rounded-xl px-3 py-2.5 text-xs font-medium text-rose-400"><i data-lucide="log-out" class="h-4 w-4"></i>Keluar</button></form>
            </div>
            <div class="mt-6 hidden space-y-4 border-t border-[#1e293b] pt-5 md:block">
                <div class="text-[11px]"><span class="block text-[10px] font-semibold tracking-wider text-amber-500">TERMINAL READY</span><span class="font-medium text-slate-300">Mesin Espresso Synesso #01</span><span class="mt-1 flex items-center gap-1.5 text-[10px] text-emerald-400"><span class="h-1.5 w-1.5 rounded-full bg-emerald-400"></span>Terhubung ke database</span></div>
                <form method="POST" action="{{ route('logout') }}">@csrf<button class="flex items-center gap-2 text-xs font-medium text-rose-400 transition hover:text-rose-300"><i data-lucide="log-out" class="h-4 w-4"></i>Keluar / Logout</button></form>
            </div>
        </aside>
        <main class="min-w-0 flex-1 space-y-6 p-5 md:p-8">
            <header class="mb-2 flex flex-col justify-between gap-4 border-b border-[#1e293b] pb-4 sm:flex-row sm:items-center">
                <div>
                    <div class="mb-1 flex items-center gap-2 text-[11px] text-slate-400"><span class="flex items-center gap-1.5 font-bold text-white"><span class="h-2 w-2 rounded-full bg-amber-500"></span>Warung Luwak Console</span><span>• BAR UTAMA</span><span>• TERMINAL 01</span></div>
                    <h1 class="text-xl font-bold text-white">@yield('heading')</h1>
                </div>
                <div class="flex items-center gap-3">
                    <span class="flex items-center gap-1.5 rounded-lg border border-emerald-500/30 bg-emerald-500/10 px-3 py-2 text-[10px] text-emerald-300"><span class="h-1.5 w-1.5 rounded-full bg-emerald-400"></span>Database aktif</span>
                    <div class="flex items-center gap-2 border-l border-[#1e293b] pl-3"><span class="flex h-8 w-8 items-center justify-center rounded-full bg-amber-500/20 text-amber-400"><i data-lucide="user" class="h-4 w-4"></i></span><span class="text-xs"><strong class="block text-white">{{ auth()->user()?->name ?? 'Admin Lokal' }}</strong><span class="text-[10px] text-slate-400">Administrator</span></span></div>
                </div>
            </header>
            @if (session('success'))
                <div role="status" class="mb-5 rounded-lg border border-emerald-500/30 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-200">{{ session('success') }}</div>
            @endif
            @if ($errors->any())
                <div role="alert" class="mb-5 rounded-lg border border-rose-500/30 bg-rose-500/10 px-4 py-3 text-sm text-rose-200">
                    <ul class="list-inside list-disc">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                </div>
            @endif
            @yield('content')
        </main>
    </div>
    <script>lucide.createIcons();</script>
</body>
</html>
