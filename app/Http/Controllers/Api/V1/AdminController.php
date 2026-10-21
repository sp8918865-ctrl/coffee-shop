<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\DiningTable;
use App\Models\Ingredient;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class AdminController extends Controller
{
    public function dashboard(): JsonResponse
    {
        $completedToday = Order::where('order_status', 'completed')->whereDate('created_at', today());

        return response()->json([
            'data' => [
                'revenue_today' => (clone $completedToday)->sum('total_amount'),
                'orders_today' => Order::whereDate('created_at', today())->count(),
                'average_completed_order_today' => (clone $completedToday)->avg('total_amount') ?? 0,
                'products_total' => Product::count(),
                'products_unavailable' => Product::where(function ($query): void {
                    $query->where('is_available', false)->orWhere('stok', '<=', 0)->orWhereNull('stok');
                })->count(),
                'critical_ingredients' => Ingredient::where('status', 'critical')->count(),
                'recent_orders' => Order::with(['items', 'payments'])->latest()->limit(5)->get()
                    ->map(fn (Order $order): array => $this->orderData($order)),
            ],
        ]);
    }

    public function products(): JsonResponse
    {
        return response()->json([
            'data' => Product::with('category')->orderBy('name')->paginate(50),
        ]);
    }

    public function storeProduct(Request $request): JsonResponse
    {
        $product = Product::create($this->productData($request));

        return response()->json(['data' => $product->load('category')], 201);
    }

    public function updateProduct(Request $request, Product $product): JsonResponse
    {
        $product->update($this->productData($request, $product));

        return response()->json(['data' => $product->fresh()->load('category')]);
    }

    public function deleteProduct(Product $product): JsonResponse
    {
        $product->delete();

        return response()->json(['message' => 'Menu berhasil dihapus.']);
    }

    public function categories(): JsonResponse
    {
        return response()->json(['data' => Category::withCount('products')->orderBy('name')->get()]);
    }

    public function storeCategory(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:categories,name'],
            'description' => ['nullable', 'string', 'max:1000'],
        ]);

        return response()->json(['data' => Category::create($data)], 201);
    }

    public function updateCategory(Request $request, Category $category): JsonResponse
    {
        $data = $request->validate([
            'name' => ['sometimes', 'required', 'string', 'max:255', Rule::unique('categories', 'name')->ignore($category->category_id, 'category_id')],
            'description' => ['sometimes', 'nullable', 'string', 'max:1000'],
        ]);
        $category->update($data);

        return response()->json(['data' => $category->fresh()]);
    }

    public function deleteCategory(Category $category): JsonResponse
    {
        $category->delete();

        return response()->json(['message' => 'Kategori berhasil dihapus.']);
    }

    public function ingredients(): JsonResponse
    {
        return response()->json(['data' => Ingredient::orderBy('name')->paginate(50)]);
    }

    public function storeIngredient(Request $request): JsonResponse
    {
        return response()->json(['data' => Ingredient::create($this->ingredientData($request))], 201);
    }

    public function updateIngredient(Request $request, Ingredient $ingredient): JsonResponse
    {
        $ingredient->update($this->ingredientData($request));

        return response()->json(['data' => $ingredient->fresh()]);
    }

    public function deleteIngredient(Ingredient $ingredient): JsonResponse
    {
        $ingredient->delete();

        return response()->json(['message' => 'Bahan berhasil dihapus.']);
    }

    public function tables(): JsonResponse
    {
        return response()->json(['data' => DiningTable::withCount('orders')->orderBy('table_number')->get()]);
    }

    public function storeTable(Request $request): JsonResponse
    {
        return response()->json(['data' => DiningTable::create($this->tableData($request))], 201);
    }

    public function updateTable(Request $request, DiningTable $table): JsonResponse
    {
        $table->update($this->tableData($request, $table));

        return response()->json(['data' => $table->fresh()]);
    }

    public function deleteTable(DiningTable $table): JsonResponse
    {
        $table->delete();

        return response()->json(['message' => 'Meja berhasil dihapus.']);
    }

    public function orders(Request $request): JsonResponse
    {
        $request->validate(['status' => ['nullable', Rule::in(['pending', 'received', 'in_progress', 'ready', 'completed', 'cancelled'])]]);
        $orders = Order::with(['items', 'payments'])
            ->when($request->filled('status'), fn ($query) => $query->where('order_status', $request->input('status')))
            ->latest()
            ->paginate(50);

        return response()->json([
            'data' => $orders->getCollection()->map(fn (Order $order): array => $this->orderData($order)),
            'meta' => [
                'current_page' => $orders->currentPage(),
                'last_page' => $orders->lastPage(),
                'per_page' => $orders->perPage(),
                'total' => $orders->total(),
            ],
        ]);
    }

    public function showOrder(Order $order): JsonResponse
    {
        return response()->json(['data' => $this->orderData($order->load(['items', 'payments']))]);
    }

    public function updateOrderStatus(Request $request, Order $order): JsonResponse
    {
        $data = $request->validate([
            'status' => ['required', Rule::in(['pending', 'received', 'in_progress', 'ready', 'completed', 'cancelled'])],
        ]);
        $order->update(['order_status' => $data['status']]);

        return response()->json(['data' => $this->orderData($order->fresh()->load(['items', 'payments']))]);
    }

    public function deleteOrder(Order $order): JsonResponse
    {
        DB::transaction(fn () => $order->delete());

        return response()->json(['message' => 'Pesanan berhasil dihapus.']);
    }

    private function productData(Request $request, ?Product $product = null): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'category_id' => ['nullable', 'exists:categories,category_id'],
            'stok' => ['required', 'integer', 'min:0'],
            'price' => ['required', 'numeric', 'min:0'],
            'description' => ['nullable', 'string', 'max:1000'],
            'image_url' => ['nullable', 'url', 'max:2048'],
            'is_available' => ['sometimes', 'boolean'],
        ]);
        $data['is_available'] = $request->boolean('is_available', $product?->is_available ?? true);

        return $data;
    }

    private function ingredientData(Request $request): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'quantity' => ['required', 'numeric', 'min:0'],
            'unit' => ['required', 'string', 'max:30'],
            'minimum_quantity' => ['required', 'numeric', 'min:0'],
        ]);
        $data['status'] = $data['quantity'] <= 0
            ? 'critical'
            : ($data['quantity'] <= $data['minimum_quantity'] ? 'low' : 'available');

        return $data;
    }

    private function tableData(Request $request, ?DiningTable $table = null): array
    {
        return $request->validate([
            'table_number' => [
                'required',
                'string',
                'max:30',
                Rule::unique('tables', 'table_number')->ignore($table?->table_id, 'table_id'),
            ],
            'capacity' => ['required', 'integer', 'min:1', 'max:100'],
            'status' => ['required', Rule::in(['available', 'occupied', 'reserved'])],
        ]);
    }

    private function orderData(Order $order): array
    {
        return [
            'id' => $order->order_id,
            'number' => $order->order_number,
            'customer_name' => $order->customer_name,
            'customer_phone' => $order->customer_phone,
            'order_type' => $order->order_type,
            'table_number' => $order->table_number,
            'notes' => $order->notes,
            'status' => $order->order_status,
            'total' => $order->total_amount,
            'items' => $order->items->map(fn (OrderItem $item): array => [
                'product_id' => $item->product_id,
                'name' => $item->product_name,
                'quantity' => $item->quantity,
                'unit_price' => $item->unit_price,
                'subtotal' => $item->subtotal,
            ])->values(),
            'payments' => $order->payments->map(fn (Payment $payment): array => [
                'method' => $payment->payment_method,
                'status' => $payment->payment_status,
                'amount' => $payment->amount,
            ])->values(),
            'created_at' => $order->created_at?->toISOString(),
        ];
    }
}
