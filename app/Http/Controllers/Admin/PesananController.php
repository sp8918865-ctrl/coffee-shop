<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PesananController extends Controller
{
    public function index(): View
    {
        $pesananBaru = Order::with('items')->where('status', 'new')->latest()->get();
        $pesananBrewing = Order::with('items')->where('status', 'brewing')->latest()->get();
        $pesananReady = Order::with('items')->where('status', 'ready')->latest()->get();
        $pesananSelesai = Order::with('items')->where('status', 'completed')->whereDate('created_at', today())->latest()->get();
        $products = Product::where('is_available', true)->orderBy('name')->get();

        return view('admin.data.pesanan', compact(
            'pesananBaru', 'pesananBrewing', 'pesananReady', 'pesananSelesai', 'products'
        ));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'quantity' => ['required', 'integer', 'min:1'],
            'order_type' => ['required', 'in:dine_in,takeaway'],
            'table_number' => ['nullable', 'required_if:order_type,dine_in', 'string', 'max:30'],
            'customer_name' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $product = Product::whereKey($data['product_id'])->where('is_available', true)->firstOrFail();

        DB::transaction(function () use ($data, $product): void {
            $subtotal = $product->price * $data['quantity'];
            $order = Order::create([
                'order_number' => 'ORD-'.now()->format('ymdHis').'-'.random_int(100, 999),
                'status' => 'new',
                'total_amount' => $subtotal,
                'order_type' => $data['order_type'],
                'table_number' => $data['table_number'] ?? null,
                'customer_name' => $data['customer_name'] ?? null,
                'notes' => $data['notes'] ?? null,
            ]);

            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $product->id,
                'product_name' => $product->name,
                'quantity' => $data['quantity'],
                'unit_price' => $product->price,
                'subtotal' => $subtotal,
            ]);
        });

        return redirect()->route('admin.pesanan')->with('success', 'Pesanan berhasil dibuat.');
    }

    public function updateStatus(Request $request, Order $order): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', 'in:new,brewing,ready,completed,cancelled'],
        ]);

        $order->update($data);

        return redirect()->route('admin.pesanan')->with('success', 'Status pesanan diperbarui.');
    }

    public function destroy(Order $order): RedirectResponse
    {
        $order->delete();

        return redirect()->route('admin.pesanan')->with('success', 'Pesanan berhasil dihapus.');
    }
}
