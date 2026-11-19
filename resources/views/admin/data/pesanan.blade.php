@extends('admin.data.layout')

@section('title', 'Pesanan Diterima')
@section('heading', 'Pesanan Diterima')

@section('content')
<div class="space-y-5">
    <div
        data-order-notifications
        data-endpoint="{{ \App\Support\PanelRoute::url('admin.pesanan.notifications') }}"
        data-cursor="{{ $latestOrderId }}"
        data-order-url="{{ \App\Support\PanelRoute::url('admin.pesanan') }}"
        class="flex flex-wrap items-center justify-between gap-3 rounded-xl border border-amber-500/20 bg-amber-500/5 p-3"
    >
        <p class="text-xs text-slate-300">Notifikasi pesanan baru aktif selama halaman ini terbuka.</p>
        <button type="button" data-enable-order-notifications class="rounded-lg border border-amber-500/40 px-3 py-2 text-xs font-semibold text-amber-300 hover:bg-amber-500/10">
            Aktifkan notifikasi browser
        </button>
    </div>
    <div data-order-notification-toast role="status" aria-live="polite" hidden class="fixed right-5 top-5 z-50 max-w-sm rounded-xl border border-amber-500/40 bg-[#121824] p-4 shadow-xl">
        <p data-order-notification-title class="font-bold text-amber-300"></p>
        <p data-order-notification-message class="mt-1 text-xs text-slate-300"></p>
        <a href="{{ \App\Support\PanelRoute::url('admin.pesanan') }}" class="mt-3 inline-block text-xs font-semibold text-white underline">Buka daftar pesanan</a>
    </div>
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
    <div class="flex flex-col gap-3 rounded-xl border border-[#1e293b] bg-[#121824] p-3 sm:flex-row sm:items-center">
        <label class="relative min-w-0 flex-1">
            <i data-lucide="search" class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-500"></i>
            <input
                id="order-filter-search"
                type="search"
                placeholder="Cari nomor, pelanggan, menu, atau catatan..."
                autocomplete="off"
                class="w-full rounded-lg border border-[#26334d] bg-[#0b0f17] py-2 pl-9 pr-3 text-xs text-white placeholder-slate-500"
            >
        </label>
        <select id="order-filter-status" class="rounded-lg border border-[#26334d] bg-[#0b0f17] px-3 py-2 text-xs text-white">
            <option value="">Semua status</option>
            <option value="new">Pesanan baru</option>
            <option value="brewing">Sedang diracik</option>
            <option value="ready">Siap disajikan</option>
            <option value="completed">Selesai hari ini</option>
        </select>
        <button id="order-filter-reset" type="button" class="rounded-lg border border-slate-600 px-3 py-2 text-xs font-semibold text-slate-300 hover:border-slate-400 hover:text-white">
            Reset
        </button>
    </div>
    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
        @foreach ([['Pesanan baru', $pesananBaru, 'new'], ['Sedang diracik', $pesananBrewing, 'brewing'], ['Siap disajikan', $pesananReady, 'ready'], ['Selesai hari ini', $pesananSelesai, 'completed']] as [$label, $orders, $status])
        <section class="min-w-0 space-y-3" data-order-column="{{ $status }}"><h2 class="border-b border-[#1e293b] pb-2 text-sm font-semibold text-white">{{ $label }} <span data-order-count class="text-xs text-slate-500">{{ $orders->count() }}</span></h2>
            @forelse ($orders as $order)
            <article class="rounded-lg border border-[#1e293b] bg-[#121824] p-4" data-order-status="{{ $status }}" data-order-search="{{ strtolower(($order->order_number ?? '#' . $order->order_id).' '.($order->customer_name ?: 'Pelanggan').' '.($order->notes ?: '').' '.$order->items->pluck('product_name')->implode(' ').' '.($order->table_number ? 'meja '.$order->table_number : 'takeaway')) }}">
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
                @if ($payment?->payment_method === 'transfer')
                    <div class="mb-3 rounded-lg border border-sky-500/20 bg-[#0b0f17] p-3">
                        <p class="text-[11px] font-semibold text-slate-300">Transfer bank · {{ $payment->payment_status === 'paid' ? 'Lunas' : 'Menunggu konfirmasi' }}</p>
                        @if ($payment->bank_pengirim || $payment->nama_pemilik_rekening || $payment->nomor_rekening)
                            <div class="mt-2 space-y-1 border-t border-sky-500/10 pt-2 text-[10px] text-slate-400">
                                @if ($payment->bank_pengirim)<p>Bank pengirim: <strong class="text-sky-200">{{ $payment->bank_pengirim }}</strong></p>@endif
                                @if ($payment->nama_pemilik_rekening)<p>Pemilik rekening: <strong class="text-sky-200">{{ $payment->nama_pemilik_rekening }}</strong></p>@endif
                                @if ($payment->nomor_rekening)<p>Nomor rekening: <strong class="text-sky-200">{{ $payment->nomor_rekening }}</strong></p>@endif
                            </div>
                        @endif
                        @if ($payment->payment_status !== 'paid')
                            <form method="POST" action="{{ \App\Support\PanelRoute::url('admin.pesanan.transfer-payment', $order) }}" class="mt-2" onsubmit="return confirm('Konfirmasi bahwa transfer untuk pesanan ini sudah diterima?')">
                                @csrf
                                <button class="rounded bg-sky-400 px-3 py-2 text-[11px] font-bold text-slate-950">Konfirmasi transfer masuk</button>
                            </form>
                        @endif
                    </div>
                @endif
                @if ($payment?->payment_method === 'qris')
                    @php($qrisIsAutomatic = $payment->payment_gateway_ref && $payment->payment_gateway_ref !== 'DEMO-NOT-PROCESSED')
                    <div class="mb-3 rounded-lg border border-amber-500/20 bg-[#0b0f17] p-3">
                        <p class="text-[11px] font-semibold text-slate-300">QRIS · {{ $payment->payment_status === 'paid' ? ($qrisIsAutomatic ? 'Lunas otomatis' : 'Lunas') : ($payment->payment_status === 'failed' ? 'Gagal' : ($qrisIsAutomatic ? 'Menunggu pembayaran' : 'Menunggu konfirmasi staf')) }}</p>
                    </div>
                @endif
                <a href="{{ \App\Support\PanelRoute::url('admin.pesanan.receipt', $order) }}" target="_blank" rel="noopener" class="mb-2 inline-flex items-center gap-1.5 rounded border border-amber-500/40 px-2.5 py-1.5 text-[11px] font-semibold text-amber-300 hover:border-amber-400 hover:bg-amber-500/10"><i data-lucide="receipt-text" class="h-3.5 w-3.5"></i>Buat bon</a>
                @if ($status !== 'completed')<form method="POST" action="{{ \App\Support\PanelRoute::url('admin.pesanan.status', $order) }}" class="flex gap-2">@csrf @method('PATCH')<select name="status" class="min-w-0 flex-1 rounded bg-[#0b0f17] px-2 py-1 text-xs text-white"><option value="new" @selected($order->status === 'new')>Baru</option><option value="received" @selected($order->status === 'received')>Diterima</option><option value="brewing" @selected($order->status === 'brewing')>Diracik</option><option value="ready" @selected($order->status === 'ready')>Siap</option><option value="completed" @selected($order->status === 'completed')>Selesai</option><option value="cancelled" @selected($order->status === 'cancelled')>Batalkan</option></select><button class="rounded bg-emerald-500 px-2 py-1 text-xs font-bold text-slate-950">Ubah</button></form>@endif
                <form method="POST" action="{{ \App\Support\PanelRoute::url('admin.pesanan.destroy', $order) }}" onsubmit="return confirm('Hapus pesanan ini?')" class="mt-2">@csrf @method('DELETE')<button class="text-[11px] text-rose-400">Hapus pesanan</button></form>
            </article>
            @empty
            <p data-empty-state class="rounded-lg border border-dashed border-[#26334d] p-4 text-xs text-slate-500">Tidak ada pesanan.</p>
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

    const orderSearch = document.querySelector('#order-filter-search');
    const orderStatus = document.querySelector('#order-filter-status');
    const orderReset = document.querySelector('#order-filter-reset');

    function normalizeOrderText(value) {
        return (value || '')
            .toString()
            .toLowerCase()
            .normalize('NFD')
            .replace(/[\u0300-\u036f]/g, '');
    }

    function filterOrders() {
        const search = normalizeOrderText(orderSearch.value).trim();
        const status = orderStatus.value;

        document.querySelectorAll('[data-order-column]').forEach(column => {
            const columnStatus = column.dataset.orderColumn;
            const matchesStatus = status === '' || status === columnStatus;
            column.classList.toggle('hidden', !matchesStatus);

            if (!matchesStatus) {
                return;
            }

            let visibleCount = 0;
            column.querySelectorAll('[data-order-status]').forEach(order => {
                const matchesSearch = search === '' || normalizeOrderText(order.dataset.orderSearch).includes(search);
                order.classList.toggle('hidden', !matchesSearch);
                if (matchesSearch) {
                    visibleCount++;
                }
            });

            const count = column.querySelector('[data-order-count]');
            if (count) {
                count.textContent = visibleCount;
            }

            const emptyState = column.querySelector('[data-empty-state]');
            if (emptyState) {
                const hasFilteredOrders = column.querySelectorAll('[data-order-status]:not(.hidden)').length > 0;
                emptyState.classList.toggle('hidden', hasFilteredOrders);
                emptyState.textContent = search === '' ? 'Tidak ada pesanan.' : 'Pesanan tidak ditemukan.';
            }
        });
    }

    orderSearch.addEventListener('input', filterOrders);
    orderStatus.addEventListener('change', filterOrders);
    orderReset.addEventListener('click', () => {
        orderSearch.value = '';
        orderStatus.value = '';
        filterOrders();
        orderSearch.focus();
    });

    const notificationPanel = document.querySelector('[data-order-notifications]');
    if (notificationPanel) {
        const permissionButton = notificationPanel.querySelector('[data-enable-order-notifications]');
        const toast = document.querySelector('[data-order-notification-toast]');
        const toastTitle = toast.querySelector('[data-order-notification-title]');
        const toastMessage = toast.querySelector('[data-order-notification-message]');
        let notificationCursor = Number(notificationPanel.dataset.cursor);
        let toastTimer;

        if (!('Notification' in window)) {
            permissionButton.hidden = true;
        } else if (Notification.permission === 'granted') {
            permissionButton.textContent = 'Notifikasi browser aktif';
            permissionButton.disabled = true;
        } else if (Notification.permission === 'denied') {
            permissionButton.textContent = 'Notifikasi browser diblokir';
            permissionButton.disabled = true;
        }

        permissionButton.addEventListener('click', async () => {
            const permission = await Notification.requestPermission();
            permissionButton.textContent = permission === 'granted'
                ? 'Notifikasi browser aktif'
                : 'Izin notifikasi tidak diberikan';
            permissionButton.disabled = true;
        });

        function showOrderNotification(orders) {
            const latestOrder = orders[orders.length - 1];
            const orderLabel = latestOrder.order_number || `#${latestOrder.order_id}`;
            const customer = latestOrder.customer_name || 'Pelanggan';
            const quantityLabel = orders.length > 1 ? `${orders.length} pesanan baru masuk.` : 'Pesanan baru masuk.';

            toastTitle.textContent = quantityLabel;
            toastMessage.textContent = `${orderLabel} dari ${customer}`;
            toast.hidden = false;
            window.clearTimeout(toastTimer);
            toastTimer = window.setTimeout(() => {
                toast.hidden = true;
            }, 10000);

            if ('Notification' in window && Notification.permission === 'granted') {
                const notification = new Notification(quantityLabel, {
                    body: `${orderLabel} dari ${customer}`,
                    icon: '/logo.png',
                });
                notification.onclick = () => {
                    window.focus();
                    window.location.href = notificationPanel.dataset.orderUrl;
                };
            }
        }

        async function checkForNewOrders() {
            try {
                const response = await fetch(`${notificationPanel.dataset.endpoint}?after=${notificationCursor}`, {
                    headers: { Accept: 'application/json' },
                    credentials: 'same-origin',
                });
                if (!response.ok) {
                    throw new Error('Gagal memeriksa pesanan baru.');
                }

                const result = await response.json();
                notificationCursor = Number(result.cursor);
                if (result.orders.length > 0) {
                    showOrderNotification(result.orders);
                }
            } catch (error) {
                console.error(error);
            } finally {
                window.setTimeout(checkForNewOrders, 5000);
            }
        }

        window.setTimeout(checkForNewOrders, 5000);
    }
</script>
@endsection
