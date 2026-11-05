@extends('admin.data.layout')

@section('title', 'Pesanan Diterima')
@section('heading', 'Pesanan Diterima')

@section('content')
<div class="space-y-5">
    <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
        <div class="rounded-xl border border-amber-500/20 bg-[#121824] p-4"><span class="text-[10px] font-semibold uppercase tracking-wider text-slate-400">PESANAN BARU</span><strong class="mt-2 block text-2xl font-extrabold text-amber-400">{{ $pesananBaru->count() }}</strong><span class="text-[10px] text-slate-500">Menunggu diproses</span></div>
        <div class="rounded-xl border border-emerald-500/20 bg-[#121824] p-4"><span class="text-[10px] font-semibold uppercase tracking-wider text-slate-400">SEDANG DIRACIK</span><strong class="mt-2 block text-2xl font-extrabold text-emerald-400">{{ $pesananBrewing->count() }}</strong><span class="text-[10px] text-slate-500">Di stasiun bar</span></div>
        <div class="rounded-xl border border-sky-500/20 bg-[#121824] p-4"><span class="text-[10px] font-semibold uppercase tracking-wider text-slate-400">SIAP DISAJIKAN</span><strong class="mt-2 block text-2xl font-extrabold text-sky-400">{{ $pesananReady->count() }}</strong><span class="text-[10px] text-slate-500">Menunggu pelanggan</span></div>
        <div class="rounded-xl border border-[#1e293b] bg-[#121824] p-4"><span class="text-[10px] font-semibold uppercase tracking-wider text-slate-400">SELESAI HARI INI</span><strong class="mt-2 block text-2xl font-extrabold text-white">{{ $pesananSelesai->count() }}</strong><span class="text-[10px] text-slate-500">Transaksi tuntas</span></div>
    </div>
    <details class="rounded-xl border border-[#1e293b] bg-[#121824] p-5"><summary class="flex cursor-pointer list-none items-center justify-between text-xs font-bold text-amber-400"><span class="flex items-center gap-2"><i data-lucide="plus" class="h-4 w-4"></i>Tambah pesanan</span><i data-lucide="chevron-down" class="h-4 w-4"></i></summary><form method="POST" action="{{ \App\Support\PanelRoute::url('admin.pesanan.store') }}" class="mt-4">@csrf
        <h2 class="mb-4 text-sm font-semibold text-white">Buat pesanan</h2>
        <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-5">
            <label class="text-xs text-slate-300">Menu<select name="product_id" required class="mt-1 w-full rounded-lg border border-[#26334d] bg-[#0b0f17] px-3 py-2 text-white"><option value="">Pilih menu</option>@foreach ($products as $product)<option value="{{ $product->product_id }}">{{ $product->name }} - Rp {{ number_format($product->price, 0, ',', '.') }}</option>@endforeach</select></label>
            <label class="text-xs text-slate-300">Jumlah<input name="quantity" type="number" min="1" value="1" required class="mt-1 w-full rounded-lg border border-[#26334d] bg-[#0b0f17] px-3 py-2 text-white"></label>
            <label class="text-xs text-slate-300">Jenis<select name="order_type" class="mt-1 w-full rounded-lg border border-[#26334d] bg-[#0b0f17] px-3 py-2 text-white"><option value="takeaway">Takeaway</option><option value="dine_in">Dine-in</option></select></label>
            <label class="text-xs text-slate-300">Meja<select name="table_id" class="mt-1 w-full rounded-lg border border-[#26334d] bg-[#0b0f17] px-3 py-2 text-white"><option value="">Pilih meja</option>@foreach ($tables as $table)<option value="{{ $table->table_id }}">Meja {{ $table->table_number }}</option>@endforeach</select></label>
            <label class="text-xs text-slate-300">Nama pelanggan<input name="customer_name" maxlength="255" class="mt-1 w-full rounded-lg border border-[#26334d] bg-[#0b0f17] px-3 py-2 text-white"></label>
        </div>
        <label class="mt-3 block text-xs text-slate-300">Catatan<input name="notes" maxlength="1000" class="mt-1 w-full rounded-lg border border-[#26334d] bg-[#0b0f17] px-3 py-2 text-white"></label>
        <button @disabled($products->isEmpty()) class="mt-4 rounded-lg bg-amber-500 px-4 py-2 text-xs font-bold text-slate-950 disabled:opacity-50">Simpan pesanan</button>
    </form></details>
    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
        @foreach ([['Pesanan baru', $pesananBaru, 'new'], ['Sedang diracik', $pesananBrewing, 'brewing'], ['Siap disajikan', $pesananReady, 'ready'], ['Selesai hari ini', $pesananSelesai, 'completed']] as [$label, $orders, $status])
        <section class="min-w-0 space-y-3"><h2 class="border-b border-[#1e293b] pb-2 text-sm font-semibold text-white">{{ $label }} <span class="text-xs text-slate-500">{{ $orders->count() }}</span></h2>
            @forelse ($orders as $order)
            <article class="rounded-lg border border-[#1e293b] bg-[#121824] p-4">
                <div class="flex items-start justify-between gap-2"><div><h3 class="font-bold text-amber-400">{{ $order->order_number ?? ('#' . $order->order_id) }}</h3><p class="mt-1 text-[11px] text-slate-400">{{ $order->order_type === 'dine_in' ? 'Meja ' . ($order->table_number ?: '-') : 'Takeaway' }} · {{ $order->customer_name ?: 'Pelanggan' }}</p></div><span class="text-xs text-white">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span></div>
                <ul class="my-3 space-y-2 border-y border-[#1e293b] py-2 text-xs">@foreach ($order->items as $item)<li class="flex justify-between gap-2"><span>{{ $item->quantity }}x {{ $item->product_name }} <span class="text-slate-400">· Rp {{ number_format($item->unit_price, 0, ',', '.') }}/item</span></span><strong class="shrink-0 text-slate-200">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</strong></li>@endforeach</ul>
                @if ($order->notes)<p class="mb-3 text-[11px] text-slate-400">{{ $order->notes }}</p>@endif
                @php($payment = $order->payments->sortByDesc('payment_id')->first())
                @if (!$payment || $payment->payment_method === 'cash')
                    <form method="POST" action="{{ \App\Support\PanelRoute::url('admin.pesanan.cash-payment', $order) }}" data-cash-payment-form data-minimum="{{ ceil($order->total_amount) }}" class="mb-3 rounded-lg border border-emerald-500/20 bg-[#0b0f17] p-3">
                        @csrf
                        <label for="cash_received_{{ $order->order_id }}" class="text-[11px] font-semibold text-slate-300">Uang dari pelanggan (Rp)</label>
                        <div class="mt-2 flex gap-2">
                            <input id="cash_received_{{ $order->order_id }}" name="cash_received" type="number" min="{{ ceil($order->total_amount) }}" max="9999999999" step="1" inputmode="numeric" required value="{{ $payment?->cash_received }}" placeholder="{{ number_format(ceil($order->total_amount), 0, ',', '.') }}" class="min-w-0 flex-1 rounded border border-[#26334d] bg-[#121824] px-2 py-2 text-xs text-white">
                            <button data-save-button class="shrink-0 rounded bg-emerald-500 px-3 py-2 text-[11px] font-bold text-slate-950">Simpan tunai</button>
                        </div>
                        <p data-save-status role="status" aria-live="polite" class="mt-2 text-[10px] text-slate-400">@if ($payment?->cash_received !== null) Tersimpan @endif</p>
                        @if ($payment?->cash_received !== null)
                            <p data-cash-change class="mt-2 text-[10px] text-emerald-300">Kembalian: Rp {{ number_format($payment->cash_change ?? 0, 0, ',', '.') }}</p>
                        @else
                            <p data-cash-change class="mt-2 text-[10px] text-emerald-300"></p>
                        @endif
                    </form>
                @endif
                <a href="{{ \App\Support\PanelRoute::url('admin.pesanan.receipt', $order) }}" target="_blank" rel="noopener" class="mb-2 inline-flex items-center gap-1.5 rounded border border-amber-500/40 px-2.5 py-1.5 text-[11px] font-semibold text-amber-300 hover:border-amber-400 hover:bg-amber-500/10"><i data-lucide="receipt-text" class="h-3.5 w-3.5"></i>Buat bon</a>
                @if ($status !== 'completed')<form method="POST" action="{{ \App\Support\PanelRoute::url('admin.pesanan.status', $order) }}" class="flex gap-2">@csrf @method('PATCH')<select name="status" class="min-w-0 flex-1 rounded bg-[#0b0f17] px-2 py-1 text-xs text-white"><option value="new" @selected($order->status === 'new')>Baru</option><option value="received" @selected($order->status === 'received')>Diterima</option><option value="brewing" @selected($order->status === 'brewing')>Diracik</option><option value="ready" @selected($order->status === 'ready')>Siap</option><option value="completed" @selected($order->status === 'completed')>Selesai</option><option value="cancelled" @selected($order->status === 'cancelled')>Batalkan</option></select><button class="rounded bg-emerald-500 px-2 py-1 text-xs font-bold text-slate-950">Ubah</button></form>@endif
                <form method="POST" action="{{ \App\Support\PanelRoute::url('admin.pesanan.destroy', $order) }}" onsubmit="return confirm('Hapus pesanan ini?')" class="mt-2">@csrf @method('DELETE')<button class="text-[11px] text-rose-400">Hapus pesanan</button></form>
            </article>
            @empty
            <p class="rounded-lg border border-dashed border-[#26334d] p-4 text-xs text-slate-500">Tidak ada pesanan.</p>
            @endforelse
        </section>
        @endforeach
    </div>
</div>
<script>
    document.querySelectorAll('[data-cash-payment-form]').forEach(form => {
        const input = form.querySelector('[name="cash_received"]');
        const saveButton = form.querySelector('[data-save-button]');
        const status = form.querySelector('[data-save-status]');
        const changeDisplay = form.querySelector('[data-cash-change]');
        let debounceTimer;
        let savedValue = input.value;
        let isSaving = false;

        function formatRupiah(amount) {
            return 'Rp ' + new Intl.NumberFormat('id-ID', { maximumFractionDigits: 0 }).format(amount);
        }

        async function saveCashPayment() {
            const value = input.value.trim();
            if (isSaving || value === '' || Number(value) < Number(form.dataset.minimum) || value === savedValue) {
                return;
            }

            const submittedValue = value;
            const formData = new FormData(form);
            isSaving = true;
            saveButton.disabled = true;
            status.textContent = 'Menyimpan...';
            status.className = 'mt-2 text-[10px] text-amber-300';

            try {
                const response = await fetch(form.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        Accept: 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    credentials: 'same-origin',
                });
                const result = await response.json();

                if (!response.ok) {
                    throw new Error(result.errors?.cash_received?.[0] || 'Pembayaran tunai gagal disimpan.');
                }

                savedValue = submittedValue;
                if (input.value.trim() === submittedValue) {
                    input.value = savedValue;
                }
                changeDisplay.textContent = 'Kembalian: ' + formatRupiah(result.cash_change);
                status.textContent = result.message;
                status.className = 'mt-2 text-[10px] text-emerald-300';
            } catch (error) {
                status.textContent = error.message || 'Pembayaran tunai gagal disimpan.';
                status.className = 'mt-2 text-[10px] text-rose-300';
            } finally {
                isSaving = false;
                saveButton.disabled = false;
                if (input.value.trim() !== savedValue && Number(input.value) >= Number(form.dataset.minimum)) {
                    debounceTimer = window.setTimeout(saveCashPayment, 0);
                }
            }
        }

        input.addEventListener('input', () => {
            window.clearTimeout(debounceTimer);
            status.textContent = '';
            if (Number(input.value) >= Number(form.dataset.minimum) && input.value !== savedValue) {
                debounceTimer = window.setTimeout(saveCashPayment, 800);
            }
        });
        input.addEventListener('blur', () => {
            window.clearTimeout(debounceTimer);
            saveCashPayment();
        });
        form.addEventListener('submit', event => {
            event.preventDefault();
            window.clearTimeout(debounceTimer);
            saveCashPayment();
        });
    });
</script>
@endsection
