<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\DiningTable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class PesananController extends Controller
{
    public function index(): View
    {
        $pesananBaru = Order::with('items')->whereIn('order_status', ['pending', 'received'])->latest()->get();
        $pesananBrewing = Order::with('items')->where('order_status', 'in_progress')->latest()->get();
        $pesananReady = Order::with('items')->where('order_status', 'ready')->latest()->get();
        $pesananSelesai = Order::with('items')->where('order_status', 'completed')->whereDate('created_at', today())->latest()->get();
        $products = Product::where('is_available', true)->orderBy('name')->get();
        $tables = DiningTable::where('status', 'available')->orderBy('table_number')->get();

        return view('admin.data.pesanan', compact(
            'pesananBaru', 'pesananBrewing', 'pesananReady', 'pesananSelesai', 'products', 'tables'
        ));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'product_id' => ['required', 'exists:products,product_id'],
            'quantity' => ['required', 'integer', 'min:1'],
            'order_type' => ['required', 'in:dine_in,takeaway'],
            'table_id' => [
                'nullable',
                'required_if:order_type,dine_in',
                Rule::exists('tables', 'table_id')->where('status', 'available'),
            ],
            'customer_name' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $product = Product::whereKey($data['product_id'])->where('is_available', true)->firstOrFail();

        DB::transaction(function () use ($data, $product): void {
            $table = null;
            if ($data['order_type'] === 'dine_in') {
                $table = DiningTable::whereKey($data['table_id'])
                    ->where('status', 'available')
                    ->lockForUpdate()
                    ->first();

                if (! $table) {
                    throw ValidationException::withMessages([
                        'table_id' => 'Meja yang dipilih sudah tidak tersedia.',
                    ]);
                }
            }

            $subtotal = $product->price * $data['quantity'];
            $order = Order::create([
                'order_number' => 'ORD-'.now()->format('ymdHis').'-'.random_int(100, 999),
                'order_status' => 'pending',
                'total_amount' => $subtotal,
                'subtotal' => $subtotal,
                'order_type' => $data['order_type'] === 'takeaway' ? 'take_away' : 'dine_in',
                'table_id' => $table?->table_id,
                'table_number' => $table?->table_number,
                'customer_name' => $data['customer_name'] ?? null,
                'notes' => $data['notes'] ?? null,
                'user_id' => auth()->id(),
            ]);

            OrderItem::create([
                'order_id' => $order->order_id,
                'product_id' => $product->product_id,
                'product_name' => $product->name,
                'quantity' => $data['quantity'],
                'unit_price' => $product->price,
                'subtotal' => $subtotal,
            ]);

            if ($table) {
                $table->update(['status' => 'occupied']);
            }
        });

        return redirect()->route('admin.pesanan')->with('success', 'Pesanan berhasil dibuat.');
    }

    public function updateStatus(Request $request, Order $order): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', 'in:new,received,brewing,ready,completed,cancelled'],
        ]);

        $order->update([
            'status' => $data['status'],
            'order_status' => [
                'new' => 'pending',
                'received' => 'received',
                'brewing' => 'in_progress',
                'ready' => 'ready',
                'completed' => 'completed',
                'cancelled' => 'cancelled',
            ][$data['status']],
        ]);

        return redirect()->route('admin.pesanan')->with('success', 'Status pesanan diperbarui.');
    }

    public function destroy(Order $order): RedirectResponse
    {
        $order->delete();

        return redirect()->route('admin.pesanan')->with('success', 'Pesanan berhasil dihapus.');
    }
}
