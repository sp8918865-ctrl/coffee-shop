<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\DiningTable;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
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

    public function checkout(Request $request): View|RedirectResponse
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
        ]);
    }

    public function placeOrder(Request $request): RedirectResponse
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
            'payment_method' => ['required', 'in:qris,cash'],
            'cash_received' => ['required_if:payment_method,cash', 'nullable', 'integer', 'min:0', 'max:9999999999'],
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
        $cashReceived = isset($data['cash_received']) ? (int) $data['cash_received'] : null;
        if ($data['payment_method'] === 'cash' && ($cashReceived === null || $cashReceived < $total)) {
            throw ValidationException::withMessages([
                'cash_received' => 'Uang yang disiapkan harus cukup untuk membayar total pesanan.',
            ]);
        }

        $order = DB::transaction(function () use ($data, $items, $total, $cashReceived): Order {
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
                'payment_gateway_ref' => $data['payment_method'] === 'qris' ? 'DEMO-NOT-PROCESSED' : null,
                'amount' => $total,
                'cash_received' => $data['payment_method'] === 'cash' ? $cashReceived : null,
                'cash_change' => $data['payment_method'] === 'cash' ? $cashReceived - $total : null,
                'payment_status' => 'pending',
            ]);

            if ($table) {
                $table->update(['status' => 'occupied']);
            }

            return $order;
        });

        $request->session()->forget(self::CART_KEY);
        $request->session()->put('latest_public_order_token', $order->public_tracking_token);

        return redirect()->route('orders.track', $order->public_tracking_token);
    }

    public function track(string $token): Response
    {
        $order = Order::with(['items', 'payments'])
            ->where('public_tracking_token', $token)
            ->firstOrFail();

        return response()->view('catalog.track', compact('order'))
            ->header('Cache-Control', 'private, no-store');
    }

    public function orders(Request $request): Response
    {
        $token = $request->session()->get('latest_public_order_token');
        $order = is_string($token)
            ? Order::where('public_tracking_token', $token)->first()
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
