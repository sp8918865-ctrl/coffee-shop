<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Product;
use App\Models\DiningTable;
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
        abort_unless($product->is_available, 404);

        $data = $request->validate(['quantity' => ['nullable', 'integer', 'min:1', 'max:20']]);
        $cart = $request->session()->get(self::CART_KEY, []);
        $cart[$product->product_id] = min(20, ($cart[$product->product_id] ?? 0) + ($data['quantity'] ?? 1));
        $request->session()->put(self::CART_KEY, $cart);

        return back()->with('success', $product->name.' ditambahkan ke keranjang.');
    }

    public function updateCart(Request $request, int $product): RedirectResponse
    {
        $data = $request->validate(['quantity' => ['required', 'integer', 'min:1', 'max:20']]);
        $cart = $request->session()->get(self::CART_KEY, []);
        abort_unless(isset($cart[$product]), 404);
        abort_unless(Product::whereKey($product)->where('is_available', true)->exists(), 404);
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
        ]);
        $items = $this->cartProducts($request);
        $cart = $request->session()->get(self::CART_KEY, []);
        if (! is_array($cart) || $items->count() !== count($cart)) {
            return redirect()->route('catalog.index')->with('error', 'Ada menu yang sudah tidak tersedia. Periksa kembali keranjang Anda.');
        }
        if ($items->isEmpty()) {
            return redirect()->route('catalog.index')->with('error', 'Keranjang Anda kosong atau menu sudah tidak tersedia.');
        }

        $order = DB::transaction(function () use ($data, $items): Order {
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

            $total = $items->sum(fn (Product $product) => $product->price * $product->cart_quantity);
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

        return Product::whereIn('product_id', array_keys($cart))
            ->where('is_available', true)
            ->orderBy('name')
            ->get()
            ->each(function (Product $product) use ($cart): void {
                $product->cart_quantity = max(1, min(20, (int) ($cart[$product->product_id] ?? 1)));
            });
    }
}
