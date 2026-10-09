@extends('catalog.layout')

@section('title', 'Menu')

@section('header-extra')
    <div class="mt-3 flex items-center justify-between gap-3 rounded-xl border border-white/[0.06] bg-[#191c22] px-3 py-2.5">
        <div class="flex min-w-0 items-center gap-2 text-[10px]">
            <span class="relative flex h-2 w-2 shrink-0"><span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-emerald-400 opacity-50"></span><span class="relative inline-flex h-2 w-2 rounded-full bg-emerald-400"></span></span>
            <span class="truncate text-slate-300">Kopi dibuat fresh setiap hari</span>
        </div>
        <span class="shrink-0 text-[9px] font-bold uppercase tracking-wider text-amber-300">Buka hari ini</span>
    </div>
@endsection

@section('content')
    <section class="relative overflow-hidden rounded-2xl border border-amber-400/15 bg-gradient-to-br from-[#332311] via-[#211b15] to-[#191c22] p-5">
        <div class="relative z-10 max-w-[82%]">
            <p class="text-[9px] font-bold uppercase tracking-[.24em] text-amber-300">Dari biji pilihan</p>
            <h1 class="mt-2 text-2xl font-black leading-tight text-white">Temukan racikan<br>favoritmu.</h1>
            <p class="mt-2 text-[11px] leading-relaxed text-slate-300">Diseduh dengan teliti, dinikmati dengan santai.</p>
        </div>
        <span class="absolute -right-6 -top-6 grid h-36 w-36 place-items-center rounded-full border border-amber-200/10 bg-amber-200/[0.04]">
            <i data-lucide="coffee" class="h-16 w-16 text-amber-300/70"></i>
        </span>
    </section>

    @php
        $menuSections = $categories->map(fn ($category) => [
            'id' => 'category-'.$category->category_id,
            'name' => $category->name,
        ])->values();
        if ($uncategorizedProducts->isNotEmpty()) {
            $menuSections->push(['id' => 'category-other', 'name' => 'Lainnya']);
        }
    @endphp
    @if ($menuSections->isNotEmpty())
        <nav class="hide-scrollbar -mx-4 mt-5 flex gap-2 overflow-x-auto px-4 pb-1" aria-label="Kategori menu">
            @foreach ($menuSections as $index => $section)
                <a href="#{{ $section['id'] }}" class="shrink-0 rounded-full border px-4 py-2 text-[10px] font-bold {{ $index === 0 ? 'border-amber-300 bg-amber-300 text-slate-950' : 'border-white/10 bg-[#1a1d23] text-slate-300' }}">{{ $section['name'] }}</a>
            @endforeach
        </nav>
    @endif

    @forelse ($categories as $category)
        <section id="category-{{ $category->category_id }}" class="mt-6 scroll-mt-36">
            <div class="mb-3 flex items-end justify-between">
                <div>
                    <h2 class="text-sm font-extrabold text-white">{{ $category->name }}</h2>
                    @if ($category->description)
                        <p class="mt-1 text-[10px] text-slate-500">{{ $category->description }}</p>
                    @endif
                </div>
                <span class="text-[9px] font-semibold text-slate-500">{{ $category->products->count() }} menu</span>
            </div>
            <div class="space-y-3">
                @foreach ($category->products as $product)
                    <article class="flex gap-3 rounded-2xl border border-white/[0.07] bg-[#191c22] p-3">
                        <div class="h-[86px] w-[86px] shrink-0 overflow-hidden rounded-xl bg-gradient-to-br from-[#49351f] to-[#27231d]">
                            @if ($product->image_url)
                                <img src="{{ $product->image_url }}" alt="{{ $product->name }}" loading="lazy" class="h-full w-full object-cover">
                            @else
                                <div class="grid h-full w-full place-items-center"><i data-lucide="coffee" class="h-8 w-8 text-amber-300/70"></i></div>
                            @endif
                        </div>
                        <div class="flex min-w-0 flex-1 flex-col">
                            <h3 class="text-xs font-bold text-white">{{ $product->name }}</h3>
                            <p class="mt-1 line-clamp-2 text-[10px] leading-relaxed text-slate-400">{{ $product->description ?: 'Racikan pilihan barista Warung Luwak.' }}</p>
                            <div class="mt-auto flex items-end justify-between gap-2 pt-2">
                                <span class="text-xs font-extrabold text-amber-300">Rp {{ number_format($product->price, 0, ',', '.') }}</span>
                                <form method="POST" action="{{ route('cart.add', $product->product_id) }}">
                                    @csrf
                                    <button aria-label="Tambah {{ $product->name }} ke keranjang" class="grid h-8 w-8 place-items-center rounded-lg bg-amber-300 text-slate-950 transition hover:bg-amber-200">
                                        <i data-lucide="plus" class="h-4 w-4"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>
        </section>
    @empty
        <section class="mt-6 rounded-2xl border border-dashed border-white/10 bg-[#191c22] p-8 text-center">
            <i data-lucide="coffee" class="mx-auto h-8 w-8 text-amber-300"></i>
            <p class="mt-3 text-sm font-bold text-white">Menu segera hadir</p>
            <p class="mt-1 text-xs text-slate-400">Belum ada menu yang tersedia saat ini.</p>
        </section>
    @endforelse

    @if ($uncategorizedProducts->isNotEmpty())
        <section id="category-other" class="mt-6 scroll-mt-36">
            <h2 class="mb-3 text-sm font-extrabold text-white">Menu lainnya</h2>
            <div class="space-y-3">
                @foreach ($uncategorizedProducts as $product)
                    <article class="flex gap-3 rounded-2xl border border-white/[0.07] bg-[#191c22] p-3">
                        <div class="grid h-[86px] w-[86px] shrink-0 place-items-center rounded-xl bg-gradient-to-br from-[#49351f] to-[#27231d]"><i data-lucide="coffee" class="h-8 w-8 text-amber-300/70"></i></div>
                        <div class="flex min-w-0 flex-1 flex-col">
                            <h3 class="text-xs font-bold text-white">{{ $product->name }}</h3>
                            <p class="mt-1 line-clamp-2 text-[10px] text-slate-400">{{ $product->description ?: 'Racikan pilihan barista Warung Luwak.' }}</p>
                            <div class="mt-auto flex items-end justify-between pt-2">
                                <span class="text-xs font-extrabold text-amber-300">Rp {{ number_format($product->price, 0, ',', '.') }}</span>
                                <form method="POST" action="{{ route('cart.add', $product->product_id) }}">@csrf<button aria-label="Tambah {{ $product->name }} ke keranjang" class="grid h-8 w-8 place-items-center rounded-lg bg-amber-300 text-slate-950"><i data-lucide="plus" class="h-4 w-4"></i></button></form>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>
        </section>
    @endif

    @if (collect(session('public_order_cart', []))->sum() > 0)
        <a href="{{ route('checkout') }}" class="fixed bottom-[76px] left-1/2 z-30 flex w-[calc(100%-2rem)] max-w-[448px] -translate-x-1/2 items-center justify-between rounded-xl bg-amber-300 px-4 py-3 text-xs font-extrabold text-slate-950 shadow-xl shadow-black/40">
            <span class="flex items-center gap-2"><i data-lucide="shopping-bag" class="h-4 w-4"></i>{{ collect(session('public_order_cart', []))->sum() }} item di keranjang</span>
            <span>Lihat keranjang <i data-lucide="arrow-right" class="inline h-4 w-4"></i></span>
        </a>
    @endif
@endsection
