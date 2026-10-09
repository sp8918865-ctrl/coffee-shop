<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="theme-color" content="#101319">
    @yield('meta')
    <title>@yield('title') - Warung Luwak</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        :root { color-scheme: dark; }
        body { background: #101319; }
        .safe-bottom { padding-bottom: max(1rem, env(safe-area-inset-bottom)); }
        .hide-scrollbar { scrollbar-width: none; }
        .hide-scrollbar::-webkit-scrollbar { display: none; }
    </style>
</head>
<body class="min-h-screen bg-[#101319] text-slate-100 antialiased">
    <div class="mx-auto min-h-screen w-full max-w-[480px] overflow-hidden border-x border-white/5 bg-[#101319] shadow-2xl">
        <header class="sticky top-0 z-30 border-b border-white/[0.06] bg-[#101319]/95 px-4 py-3 backdrop-blur-xl">
            <div class="flex items-center justify-between">
                <a href="{{ route('catalog.index') }}" class="flex min-w-0 items-center gap-3">
                    <span class="grid h-10 w-10 shrink-0 place-items-center overflow-hidden rounded-xl border border-amber-400/25 bg-[#1b1e23]">
                        <img src="{{ asset('logo.png') }}" alt="" class="h-full w-full object-cover" onerror="this.style.display='none'; this.nextElementSibling.classList.remove('hidden')">
                        <i data-lucide="coffee" class="hidden h-5 w-5 text-amber-400"></i>
                    </span>
                    <span class="min-w-0">
                        <span class="block truncate text-[9px] font-bold uppercase tracking-[.22em] text-amber-400">Roastery & Slow Bar</span>
                        <span class="block truncate text-sm font-extrabold text-white">Warung Luwak</span>
                    </span>
                </a>
                <a href="{{ route('checkout') }}" class="relative grid h-10 w-10 shrink-0 place-items-center rounded-xl border border-white/10 bg-[#1b1e23] text-amber-300" aria-label="Buka keranjang">
                    <i data-lucide="shopping-bag" class="h-4 w-4"></i>
                    @if (collect(session('public_order_cart', []))->sum() > 0)
                        <span class="absolute -right-1 -top-1 grid h-4 min-w-4 place-items-center rounded-full bg-amber-400 px-1 text-[9px] font-black text-slate-950">{{ collect(session('public_order_cart', []))->sum() }}</span>
                    @endif
                </a>
            </div>
            @yield('header-extra')
        </header>

        <main class="min-h-[calc(100vh-70px)] px-4 pb-28 pt-5">
            @if (session('success'))
                <div role="status" class="mb-4 rounded-xl border border-emerald-400/20 bg-emerald-400/[0.08] px-4 py-3 text-xs text-emerald-200">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div role="alert" class="mb-4 rounded-xl border border-rose-400/20 bg-rose-400/[0.08] px-4 py-3 text-xs text-rose-200">{{ session('error') }}</div>
            @endif
            @yield('content')
        </main>

        <nav class="safe-bottom fixed inset-x-0 bottom-0 z-40 mx-auto grid w-full max-w-[480px] grid-cols-3 border-t border-white/[0.08] bg-[#15181e]/95 px-3 pt-2 backdrop-blur-xl" aria-label="Navigasi pelanggan">
            <a href="{{ route('catalog.index') }}" class="flex flex-col items-center gap-1 py-1.5 text-[10px] font-semibold {{ request()->routeIs('catalog.index') ? 'text-amber-300' : 'text-slate-500' }}">
                <i data-lucide="layout-grid" class="h-4 w-4"></i>Menu
            </a>
            <a href="{{ route('checkout') }}" class="flex flex-col items-center gap-1 py-1.5 text-[10px] font-semibold {{ request()->routeIs('checkout') ? 'text-amber-300' : 'text-slate-500' }}">
                <i data-lucide="shopping-bag" class="h-4 w-4"></i>Keranjang
            </a>
            <a href="{{ route('orders.index') }}" class="flex flex-col items-center gap-1 py-1.5 text-[10px] font-semibold {{ request()->routeIs('orders.*') ? 'text-amber-300' : 'text-slate-500' }}">
                <i data-lucide="receipt-text" class="h-4 w-4"></i>Pesanan
            </a>
        </nav>
    </div>
    <script>if (window.lucide) lucide.createIcons();</script>
    @stack('scripts')
</body>
</html>
