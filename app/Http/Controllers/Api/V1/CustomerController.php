<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\DiningTable;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class CustomerController extends Controller
{
    public function menu(): JsonResponse
    {
        $categories = Category::whereHas('products', fn ($query) => $query->availableForSale())
            ->with(['products' => fn ($query) => $query->availableForSale()->orderBy('name')])
            ->orderBy('name')
            ->get()
            ->map(fn (Category $category): array => [
                'id' => $category->category_id,
                'name' => $category->name,
                'description' => $category->description,
                'products' => $category->products->map(fn (Product $product): array => $this->productData($product))->values(),
            ]);

        $uncategorized = Product::availableForSale()
            ->whereNull('category_id')
            ->orderBy('name')
            ->get()
            ->map(fn (Product $product): array => $this->productData($product))
            ->values();

        return response()->json(['data' => ['categories' => $categories, 'uncategorized' => $uncategorized]]);
    }

    public function tables(): JsonResponse
    {
        $tables = DiningTable::where('status', 'available')
            ->orderBy('table_number')
            ->get(['table_id', 'table_number', 'capacity'])
            ->map(fn (DiningTable $table): array => [
                'id' => $table->table_id,
                'number' => $table->table_number,
                'capacity' => $table->capacity,
            ]);

        return response()->json(['data' => $tables]);
    }

    public function placeOrder(Request $request): JsonResponse
    {
        $data = $request->validate([
            'customer_name' => ['required', 'string', 'max:255'],
            'customer_phone' => ['nullable', 'string', 'max:30'],
            'order_type' => ['required', Rule::in(['dine_in', 'take_away'])],
            'table_id' => [
                'nullable',
                'required_if:order_type,dine_in',
                Rule::exists('tables', 'table_id')->where('status', 'available'),
            ],
            'payment_method' => ['required', Rule::in(['qris', 'cash'])],
            'notes' => ['nullable', 'string', 'max:1000'],
            'items' => ['required', 'array', 'min:1', 'max:50'],
            'items.*.product_id' => ['required', 'integer', 'distinct', 'exists:products,product_id'],
            'items.*.quantity' => ['required', 'integer', 'min:1', 'max:20'],
        ]);

        $order = DB::transaction(function () use ($data): Order {
            $productIds = collect($data['items'])->pluck('product_id');
            $products = Product::availableForSale()
                ->whereIn('product_id', $productIds)
                ->lockForUpdate()
                ->get()
                ->keyBy('product_id');

            if ($products->count() !== $productIds->count()) {
                throw ValidationException::withMessages([
                    'items' => 'Satu atau lebih menu sudah tidak tersedia.',
                ]);
            }

            $table = null;
            if ($data['order_type'] === 'dine_in') {
                $table = DiningTable::whereKey($data['table_id'])
                    ->where('status', 'available')
                    ->lockForUpdate()
                    ->first();

                if (! $table) {
                    throw ValidationException::withMessages([
                        'table_id' => 'Meja sudah tidak tersedia. Silakan pilih meja lain.',
                    ]);
                }
            }

            $total = collect($data['items'])->sum(
                fn (array $item): float => (float) $products[$item['product_id']]->price * $item['quantity']
            );

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
                'notes' => $data['notes'] ?? null,
            ]);

            foreach ($data['items'] as $item) {
                $product = $products[$item['product_id']];
                if (! $product->deductStock($item['quantity'])) {
                    throw ValidationException::withMessages([
                        'items' => 'Stok '.$product->name.' sudah habis atau tidak mencukupi.',
                    ]);
                }

                $lineTotal = (float) $product->price * $item['quantity'];

                OrderItem::create([
                    'order_id' => $order->order_id,
                    'product_id' => $product->product_id,
                    'product_name' => $product->name,
                    'quantity' => $item['quantity'],
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

            return $order->load(['items', 'payments']);
        });

        return response()->json([
            'data' => $this->orderData($order, true),
            'message' => 'Pesanan berhasil dibuat.',
        ], 201);
    }

    public function trackOrder(string $token): JsonResponse
    {
        $order = Order::with(['items', 'payments'])
            ->where('public_tracking_token', $token)
            ->firstOrFail();

        return response()->json(['data' => $this->orderData($order)]);
    }

    public function issueQrToken(Request $request): JsonResponse
    {
        $plainTextToken = Str::random(64);
        $expiresAt = now()->addHours(12);

        DB::table('customer_api_tokens')->where('expires_at', '<=', now())->delete();
        DB::table('customer_api_tokens')->insert([
            'token_hash' => hash('sha256', $plainTextToken),
            'expires_at' => $expiresAt,
            'created_at' => now(),
            'last_used_at' => null,
        ]);

        return response()->json([
            'data' => [
                'token_type' => 'Bearer',
                'token' => $plainTextToken,
                'expires_at' => $expiresAt->toISOString(),
            ],
        ])->header('Cache-Control', 'no-store, private');
    }

    public function revokeQrToken(Request $request): JsonResponse
    {
        DB::table('customer_api_tokens')
            ->where('id', $request->attributes->get('customer_api_token_id'))
            ->delete();

        return response()->json(['message' => 'Token QR pelanggan berhasil dicabut.']);
    }

    private function productData(Product $product): array
    {
        return [
            'id' => $product->product_id,
            'category_id' => $product->category_id,
            'name' => $product->name,
            'description' => $product->description,
            'price' => $product->price,
            'stok' => $product->stok,
            'image_url' => $product->image_url,
        ];
    }

    private function orderData(Order $order, bool $includeTrackingToken = false): array
    {
        $data = [
            'id' => $order->order_id,
            'number' => $order->order_number,
            'customer_name' => $order->customer_name,
            'order_type' => $order->order_type,
            'table_number' => $order->table_number,
            'status' => $order->order_status,
            'total' => $order->total_amount,
            'items' => $order->items->map(fn (OrderItem $item): array => [
                'name' => $item->product_name,
                'quantity' => $item->quantity,
                'unit_price' => $item->unit_price,
                'subtotal' => $item->subtotal,
            ])->values(),
            'payment' => $order->payments->map(fn (Payment $payment): array => [
                'method' => $payment->payment_method,
                'status' => $payment->payment_status,
                'amount' => $payment->amount,
            ])->values(),
            'created_at' => $order->created_at?->toISOString(),
        ];

        if ($includeTrackingToken) {
            $data['tracking_token'] = $order->public_tracking_token;
        }

        return $data;
    }
}
