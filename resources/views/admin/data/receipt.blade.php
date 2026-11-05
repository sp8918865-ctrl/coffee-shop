<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bon {{ $order->order_number }} - Warung Luwak</title>
    <style>
        * { box-sizing: border-box; }
        body {
            width: 54mm;
            margin: 0 auto;
            padding: 4mm 1mm;
            color: #111;
            font: 11px/1.4 "Courier New", monospace;
        }
        .center { text-align: center; }
        .receipt-logo { display: block; width: 22mm; max-height: 22mm; margin: 0 auto 6px; object-fit: contain; }
        .muted { color: #555; }
        .rule { margin: 8px 0; border: 0; border-top: 1px dashed #555; }
        .row { display: flex; justify-content: space-between; gap: 8px; }
        .row span:last-child { text-align: right; }
        .item { margin: 6px 0; }
        .total { font-size: 13px; font-weight: bold; }
        .tracking-code { margin: 12px 0; text-align: center; }
        .tracking-code canvas, .tracking-code img { display: block; width: 34mm; height: 34mm; margin: 6px auto; }
        .actions { margin: 16px 0; text-align: center; }
        button { padding: 8px 14px; cursor: pointer; }
        @media print {
            @page { size: 58mm auto; margin: 2mm; }
            body { width: auto; margin: 0; padding: 0; }
            .actions { display: none !important; }
        }
    </style>
</head>
<body>
    @php($payment = $order->payments->sortByDesc('payment_id')->first())
    @php($paymentMethod = $payment?->payment_method ?? 'cash')
    <header class="center">
        <img class="receipt-logo" src="{{ asset('logo.png') }}" alt="Logo Warung Luwak">
        <strong>WARUNG LUWAK</strong><br>
        <span class="muted">Bon Pesanan</span>
    </header>

    <hr class="rule">
    <div>No. pesanan: {{ $order->order_number ?? '#'.$order->order_id }}</div>
    <div>Tanggal: {{ $order->created_at?->format('d/m/Y H:i') ?? '-' }}</div>
    <div>Pelanggan: {{ $order->customer_name ?: 'Pelanggan' }}</div>
    <div>{{ $order->order_type === 'dine_in' ? 'Makan di tempat · Meja '.($order->table_number ?: '-') : 'Dibawa pulang' }}</div>

    <hr class="rule">
    @foreach ($order->items as $item)
        <div class="item">
            <div>{{ $item->product_name }}</div>
            <div class="row">
                <span>{{ $item->quantity }} x Rp {{ number_format($item->unit_price, 0, ',', '.') }}</span>
                <span>Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
            </div>
        </div>
    @endforeach

    <hr class="rule">
    <div class="row total">
        <span>TOTAL</span>
        <span>Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
    </div>

    <hr class="rule">
    <strong>PEMBAYARAN</strong>
    @if ($payment)
        @if ($payment->receipt_number)
            <div>No. bon pembayaran: {{ $payment->receipt_number }}</div>
        @endif
        @if ($payment->payment_gateway_ref && $payment->payment_gateway_ref !== 'DEMO-NOT-PROCESSED')
            <div>Referensi: {{ $payment->payment_gateway_ref }}</div>
        @endif
    @endif
    <div class="row">
        <span>Metode</span>
        <span>{{ $paymentMethod === 'qris' ? 'QRIS' : 'Tunai' }}</span>
    </div>
    @if ($payment)
        <div class="row">
            <span>Status</span>
            <span>{{ $payment->payment_status === 'paid' ? 'Lunas' : 'Menunggu' }}</span>
        </div>
        @if ($paymentMethod === 'cash' && $payment->cash_received !== null)
            <div class="row">
                <span>Uang diterima</span>
                <span>Rp {{ number_format($payment->cash_received, 0, ',', '.') }}</span>
            </div>
            <div class="row">
                <span>Kembalian</span>
                <span>Rp {{ number_format($payment->cash_change ?? 0, 0, ',', '.') }}</span>
            </div>
        @endif
    @endif
    @if ($paymentMethod === 'qris' && $payment?->payment_status !== 'paid')
        <p class="muted">QRIS simulasi; pembayaran belum diproses.</p>
    @endif

    <hr class="rule">
    @if ($order->public_tracking_token)
        <div class="tracking-code">
            <strong>SCAN UNTUK CEK STATUS PESANAN</strong>
            <div id="tracking-qr" data-url="{{ $trackingUrl }}" role="img" aria-label="QR untuk melacak pesanan"></div>
            <small class="muted">No. pesanan: {{ $order->order_number }}</small>
            <p id="tracking-qr-error" class="muted" hidden>QR tidak dapat dibuat. Periksa koneksi internet sebelum mencetak.</p>
        </div>
        <hr class="rule">
    @endif
    <p class="center">Terima kasih<br>Selamat menikmati</p>
    <div class="actions">
        <button type="button" onclick="window.print()">Cetak bon</button>
    </div>
    @if ($order->public_tracking_token)
        <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js" referrerpolicy="no-referrer"></script>
        <script>
            const trackingQr = document.getElementById('tracking-qr');
            if (window.QRCode) {
                new QRCode(trackingQr, {
                    text: trackingQr.dataset.url,
                    width: 240,
                    height: 240,
                    colorDark: '#111111',
                    colorLight: '#ffffff',
                    correctLevel: QRCode.CorrectLevel.H,
                });
            } else {
                document.getElementById('tracking-qr-error').hidden = false;
            }
        </script>
    @endif
</body>
</html>
