<?php

namespace App\Http\Controllers;

use App\Models\DiningTable;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Product;
use App\Models\TransferRecipient;
use App\Services\Payments\MidtransGateway;
use App\Services\Payments\MidtransGatewayException;
use App\Support\IndonesianBanks;
use App\Support\StaticQris;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class PublicOrderController extends Controller
{
    private const CART_KEY = 'public_order_cart';

    public function addToCart(Request $request, Product $product): RedirectResponse
    {
        $data = $request->validate(['quantity' => ['nullable', 'integer', 'min:1', 'max:20']]);
        $product = Product::availableForSale()->whereKey($product->product_id)->first();
        if (! $product) {
            return back()->with('error', 'Menu sedang habis atau tidak tersedia.');
        }

        $cart = $request->session()->get(self::CART_KEY, []);
        $quantity = ($cart[$product->product_id] ?? 0) + ($data['quantity'] ?? 1);
        if ($quantity > min(20, $product->stok)) {
            return back()->with('error', 'Stok '.$product->name.' hanya tersisa '.$product->stok.'.');
        }

        $cart[$product->product_id] = $quantity;
        $request->session()->put(self::CART_KEY, $cart);

        return back()->with('success', $product->name.' ditambahkan ke keranjang.');
    }

    public function updateCart(Request $request, int $product): RedirectResponse
    {
        $data = $request->validate(['quantity' => ['required', 'integer', 'min:1', 'max:20']]);
        $cart = $request->session()->get(self::CART_KEY, []);
        abort_unless(isset($cart[$product]), 404);
        $availableProduct = Product::availableForSale()->whereKey($product)->first();
        if (! $availableProduct) {
            return back()->with('error', 'Menu sedang habis atau tidak tersedia.');
        }
        if ($data['quantity'] > min(20, $availableProduct->stok)) {
            return back()->with('error', 'Stok '.$availableProduct->name.' hanya tersisa '.$availableProduct->stok.'.');
        }

        $cart[$product] = $data['quantity'];
        $request->session()->put(self::CART_KEY, $cart);

        return back()->with('success', 'Keranjang diperbarui.');
    }

    public function removeFromCart(Request $request, int $product): RedirectResponse
    {
        $cart = $request->session()->get(self::CART_KEY, []);
        unset($cart[$product]);
        $request->session()->put(self::CART_KEY, $cart);

        return back()->with('success', 'Menu dihapus dari keranjang.');
    }

    public function checkout(Request $request, MidtransGateway $midtrans): View|RedirectResponse
    {
        $items = $this->cartProducts($request);
        $cart = $request->session()->get(self::CART_KEY, []);
        if (! is_array($cart) || $items->count() !== count($cart)) {
            return redirect()->route('catalog.index')->with('error', 'Ada menu yang sudah tidak tersedia. Periksa kembali keranjang Anda.');
        }
        if ($items->isEmpty()) {
            return redirect()->route('catalog.index')->with('error', 'Keranjang Anda masih kosong.');
        }

        return view('catalog.checkout', [
            'items' => $items,
            'total' => $items->sum(fn (Product $product) => $product->price * $product->cart_quantity),
            'tables' => DiningTable::where('status', 'available')->orderBy('table_number')->get(),
            'transferRecipients' => TransferRecipient::orderBy('bank')->orderBy('account_name')->get(),
            'midtransQrisEnabled' => $midtrans->isConfigured(),
        ]);
    }

    public function placeOrder(Request $request, MidtransGateway $midtrans): RedirectResponse
    {
        $data = $request->validate([
            'customer_name' => ['required', 'string', 'max:255'],
            'customer_phone' => ['nullable', 'string', 'max:30'],
            'order_type' => ['required', 'in:dine_in,take_away'],
            'table_id' => [
                'nullable',
                'required_if:order_type,dine_in',
                Rule::exists('tables', 'table_id')->where('status', 'available'),
            ],
            'payment_method' => ['required', 'in:qris,cash,transfer'],
            'bank_pengirim' => [
                'required_if:payment_method,transfer',
                'nullable',
                'string',
                'max:100',
                Rule::in(IndonesianBanks::validationOptions()),
            ],
            'bank_pengirim_lainnya' => ['required_if:bank_pengirim,other', 'nullable', 'string', 'max:100'],
            'nama_pemilik_rekening' => ['required_if:payment_method,transfer', 'nullable', 'string', 'max:100'],
            'nomor_rekening' => ['required_if:payment_method,transfer', 'nullable', 'string', 'max:50'],
        ]);
        $items = $this->cartProducts($request);
        $cart = $request->session()->get(self::CART_KEY, []);

        if (! is_array($cart) || $items->count() !== count($cart)) {
            return redirect()->route('catalog.index')->with('error', 'Ada menu yang sudah tidak tersedia. Periksa kembali keranjang Anda.');
        }
        if ($items->isEmpty()) {
            return redirect()->route('catalog.index')->with('error', 'Keranjang Anda kosong atau menu sudah tidak tersedia.');
        }

        $total = $items->sum(fn (Product $product) => $product->price * $product->cart_quantity);
        $senderBank = $data['payment_method'] === 'transfer'
            ? ($data['bank_pengirim'] === IndonesianBanks::OTHER
                ? trim($data['bank_pengirim_lainnya'])
                : $data['bank_pengirim'])
            : null;

        $order = DB::transaction(function () use ($data, $items, $total, $senderBank): Order {
            $table = null;
            if ($data['order_type'] === 'dine_in') {
                $table = DiningTable::whereKey($data['table_id'])
                    ->where('status', 'available')
                    ->lockForUpdate()
                    ->first();

                if (! $table) {
                    throw ValidationException::withMessages([
                        'table_id' => 'Meja yang dipilih sudah tidak tersedia. Silakan pilih meja lain.',
                    ]);
                }
            }

            $order = Order::create([
                'order_number' => 'ORD-'.now()->format('ymdHis').'-'.Str::upper(Str::random(8)),
                'public_tracking_token' => Str::random(64),
                'customer_name' => $data['customer_name'],
                'customer_phone' => $data['customer_phone'] ?? null,
                'order_type' => $data['order_type'],
                'table_id' => $table?->table_id,
                'table_number' => $table?->table_number,
                'order_status' => 'pending',
                'subtotal' => $total,
                'total_amount' => $total,
            ]);

            foreach ($items as $product) {
                if (! $product->deductStock($product->cart_quantity)) {
                    throw ValidationException::withMessages([
                        'items' => 'Stok '.$product->name.' sudah habis atau tidak mencukupi. Silakan perbarui keranjang Anda.',
                    ]);
                }

                $lineTotal = $product->price * $product->cart_quantity;
                OrderItem::create([
                    'order_id' => $order->order_id,
                    'product_id' => $product->product_id,
                    'product_name' => $product->name,
                    'quantity' => $product->cart_quantity,
                    'unit_price' => $product->price,
                    'subtotal' => $lineTotal,
                ]);
            }

            Payment::create([
                'order_id' => $order->order_id,
                'payment_method' => $data['payment_method'],
                'payment_gateway_ref' => $data['payment_method'] === 'qris' && StaticQris::imageUrl() === null
                    ? 'DEMO-NOT-PROCESSED'
                    : null,
                'amount' => $total,
                'payment_status' => 'pending',
                'bank_pengirim' => $senderBank,
                'nama_bank' => $senderBank,
                'nama_pemilik_rekening' => $data['payment_method'] === 'transfer'
                    ? trim($data['nama_pemilik_rekening'])
                    : null,
                'nomor_rekening' => $data['payment_method'] === 'transfer'
                    ? trim($data['nomor_rekening'])
                    : null,
            ]);

            if ($table) {
                $table->update(['status' => 'occupied']);
            }

            return $order;
        });

        $request->session()->forget(self::CART_KEY);
        $request->session()->put('latest_public_order_token', $order->public_tracking_token);

        if ($data['payment_method'] === 'qris' && $midtrans->isConfigured()) {
            $payment = $order->payments()->firstOrFail();
            try {
                $midtrans->createQrisPayment($order, $payment);
            } catch (MidtransGatewayException $exception) {
                Log::error('Midtrans QRIS payment could not be created.', [
                    'order_id' => $order->order_id,
                    'reason' => $exception->getMessage(),
                ]);
                $payment->update(['payment_status' => 'failed']);

                return redirect()->route('orders.track', $order->public_tracking_token)
                    ->with('error', 'QRIS belum dapat dibuat. Silakan coba lagi dari halaman status pesanan.');
            }

            return redirect()->away($payment->fresh()->payment_redirect_url);
        }

        return redirect()->route('orders.track', $order->public_tracking_token);
    }

    public function track(string $token, MidtransGateway $midtrans): Response
    {
        $order = Order::with(['items', 'payments'])
            ->where('public_tracking_token', $token)
            ->firstOrFail();
        $qrisImageUrl = StaticQris::imageUrl();
        $transferRecipients = TransferRecipient::orderBy('bank')->orderBy('account_name')->get();

        return response()->view('catalog.track', [
            'order' => $order,
            'qrisImageUrl' => $qrisImageUrl,
            'transferRecipients' => $transferRecipients,
            'midtransQrisEnabled' => $midtrans->isConfigured(),
        ])
            ->header('Cache-Control', 'private, no-store');
    }

    public function changePaymentMethod(Request $request, string $token, MidtransGateway $midtrans): RedirectResponse
    {
        $data = $request->validate([
            'payment_method' => ['required', Rule::in(['qris', 'cash', 'transfer'])],
            'bank_pengirim' => [
                'required_if:payment_method,transfer',
                'nullable',
                'string',
                'max:100',
                Rule::in(IndonesianBanks::validationOptions()),
            ],
            'bank_pengirim_lainnya' => ['required_if:bank_pengirim,other', 'nullable', 'string', 'max:100'],
            'nama_pemilik_rekening' => ['required_if:payment_method,transfer', 'nullable', 'string', 'max:100'],
            'nomor_rekening' => ['required_if:payment_method,transfer', 'nullable', 'string', 'max:50'],
        ]);
        $order = Order::where('public_tracking_token', $token)->firstOrFail();
        $senderBank = $data['payment_method'] === 'transfer'
            ? ($data['bank_pengirim'] === IndonesianBanks::OTHER
                ? trim($data['bank_pengirim_lainnya'])
                : $data['bank_pengirim'])
            : null;

        DB::transaction(function () use ($data, $order, $senderBank, $midtrans): void {
            $payment = Payment::where('order_id', $order->order_id)
                ->lockForUpdate()
                ->firstOrFail();

            if (! in_array($payment->payment_status, ['pending', 'failed'], true)) {
                throw ValidationException::withMessages([
                    'payment_method' => 'Metode pembayaran tidak dapat diubah setelah pembayaran dikonfirmasi.',
                ]);
            }

            $payment->update([
                'payment_method' => $data['payment_method'],
                'payment_status' => 'pending',
                'paid_at' => null,
                'payment_gateway_ref' => $data['payment_method'] === 'qris'
                    ? ($midtrans->isConfigured() ? null : (StaticQris::imageUrl() === null ? 'DEMO-NOT-PROCESSED' : null))
                    : null,
                'payment_redirect_url' => null,
                'bank_pengirim' => $senderBank,
                'nama_bank' => $senderBank,
                'nama_pemilik_rekening' => $data['payment_method'] === 'transfer'
                    ? trim($data['nama_pemilik_rekening'])
                    : null,
                'nomor_rekening' => $data['payment_method'] === 'transfer'
                    ? trim($data['nomor_rekening'])
                    : null,
            ]);
        });

        if ($data['payment_method'] === 'qris' && $midtrans->isConfigured()) {
            $payment = $order->payments()->latest('payment_id')->firstOrFail();
            try {
                $midtrans->createQrisPayment($order, $payment);
            } catch (MidtransGatewayException $exception) {
                Log::error('Midtrans QRIS payment could not be created.', [
                    'order_id' => $order->order_id,
                    'reason' => $exception->getMessage(),
                ]);
                $payment->update(['payment_status' => 'failed']);

                return redirect()->route('orders.track', $token)
                    ->with('error', 'QRIS belum dapat dibuat. Silakan coba lagi dari halaman status pesanan.');
            }
        }

        return redirect()->route('orders.track', $token)->with('success', 'Metode pembayaran berhasil diubah.');
    }

    public function retryQrisPayment(string $token, MidtransGateway $midtrans): RedirectResponse
    {
        abort_unless($midtrans->isConfigured(), 404);
        $order = Order::where('public_tracking_token', $token)->firstOrFail();
        $payment = $order->payments()->latest('payment_id')->firstOrFail();

        abort_unless(
            $payment->payment_method === 'qris' && in_array($payment->payment_status, ['pending', 'failed'], true),
            422,
            'Only pending QRIS payments can be retried.'
        );

        $payment->update(['payment_status' => 'pending']);
        try {
            $midtrans->createQrisPayment($order, $payment);
        } catch (MidtransGatewayException $exception) {
            Log::error('Midtrans QRIS payment retry failed.', [
                'order_id' => $order->order_id,
                'reason' => $exception->getMessage(),
            ]);
            $payment->update(['payment_status' => 'failed']);

            return redirect()->route('orders.track', $token)
                ->with('error', 'QRIS belum dapat dibuat. Silakan coba beberapa saat lagi.');
        }

        return redirect()->route('orders.track', $token)->with('success', 'QRIS pembayaran berhasil dibuat.');
    }

    public function orders(Request $request): Response
    {
        $token = $request->session()->get('latest_public_order_token');
        $order = is_string($token)
            ? Order::with(['items', 'payments'])
                ->where('public_tracking_token', $token)
                ->first()
            : null;

        return response()->view('catalog.orders', compact('order'))
            ->header('Cache-Control', 'private, no-store');
    }

    private function cartProducts(Request $request)
    {
        $cart = $request->session()->get(self::CART_KEY, []);
        if (! is_array($cart) || $cart === []) {
            return collect();
        }

        return Product::availableForSale()
            ->whereIn('product_id', array_keys($cart))
            ->orderBy('name')
            ->get()
            ->each(function (Product $product) use ($cart): void {
                $product->cart_quantity = max(1, min(20, (int) ($cart[$product->product_id] ?? 1)));
            });
    }
}
