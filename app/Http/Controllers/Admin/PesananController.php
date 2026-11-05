<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DiningTable;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Product;
use App\Support\PanelRoute;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class PesananController extends Controller
{
    public function index(): View
    {
        $pesananBaru = Order::with(['items', 'payments'])->whereIn('order_status', ['pending', 'received'])->latest()->get();
        $pesananBrewing = Order::with(['items', 'payments'])->where('order_status', 'in_progress')->latest()->get();
        $pesananReady = Order::with(['items', 'payments'])->where('order_status', 'ready')->latest()->get();
        $pesananSelesai = Order::with(['items', 'payments'])->where('order_status', 'completed')->whereDate('created_at', today())->latest()->get();
        $products = Product::availableForSale()->orderBy('name')->get();
        $tables = DiningTable::where('status', 'available')->orderBy('table_number')->get();

        return view('admin.data.pesanan', compact(
            'pesananBaru', 'pesananBrewing', 'pesananReady', 'pesananSelesai', 'products', 'tables'
        ));
    }

    public function receipt(Order $order): View
    {
        $order->load(['items', 'payments']);
        if (! $order->public_tracking_token) {
            $order->public_tracking_token = Str::random(64);
            $order->save();
        }

        $trackingPath = URL::signedRoute(
            'customer.order.track.enter',
            ['token' => $order->public_tracking_token],
            absolute: false
        );
        $trackingUrl = rtrim(
            config('app.public_menu_url') ?: request()->getSchemeAndHttpHost(),
            '/'
        ).$trackingPath;

        return view('admin.data.receipt', compact('order', 'trackingUrl'));
    }

    public function recordCashPayment(Request $request, Order $order): RedirectResponse|JsonResponse
    {
        $data = $request->validate([
            'cash_received' => ['required', 'integer', 'min:0', 'max:9999999999'],
        ]);

        DB::transaction(function () use ($data, $order, $request): void {
            $lockedOrder = Order::whereKey($order->order_id)->lockForUpdate()->firstOrFail();
            $payment = $lockedOrder->payments()->latest('payment_id')->lockForUpdate()->first();

            if ($payment && $payment->payment_method !== 'cash') {
                throw ValidationException::withMessages([
                    'cash_received' => 'Nominal uang hanya dapat dicatat untuk pembayaran tunai.',
                ]);
            }

            $cashReceived = (int) $data['cash_received'];
            if ($cashReceived < (float) $lockedOrder->total_amount) {
                throw ValidationException::withMessages([
                    'cash_received' => 'Uang diterima harus sama dengan atau lebih besar dari total pesanan.',
                ]);
            }

            $payment ??= new Payment([
                'order_id' => $lockedOrder->order_id,
                'payment_method' => 'cash',
            ]);
            $payment->fill([
                'processed_by_user_id' => $request->user()?->user_id,
                'amount' => $lockedOrder->total_amount,
                'cash_received' => $cashReceived,
                'cash_change' => $cashReceived - (float) $lockedOrder->total_amount,
                'payment_status' => 'paid',
                'paid_at' => now(),
            ]);
            $payment->save();
        });

        $payment = $order->payments()->latest('payment_id')->firstOrFail();
        $change = (float) $payment->cash_change;

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Pembayaran tunai tersimpan.',
                'cash_received' => (float) $payment->cash_received,
                'cash_change' => $change,
            ]);
        }

        return redirect()->route(PanelRoute::name('admin.pesanan'))
            ->with('success', 'Pembayaran tunai dicatat. Kembalian sudah dihitung; bon siap dicetak.');
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

        DB::transaction(function () use ($data): void {
            $product = Product::availableForSale()
                ->whereKey($data['product_id'])
                ->lockForUpdate()
                ->first();
            if (! $product || ! $product->deductStock($data['quantity'])) {
                throw ValidationException::withMessages([
                    'product_id' => 'Stok menu sudah habis atau tidak mencukupi.',
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

        return redirect()->route(PanelRoute::name('admin.pesanan'))->with('success', 'Pesanan berhasil dibuat.');
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

        return redirect()->route(PanelRoute::name('admin.pesanan'))->with('success', 'Status pesanan diperbarui.');
    }

    public function destroy(Order $order): RedirectResponse
    {
        $order->delete();

        return redirect()->route(PanelRoute::name('admin.pesanan'))->with('success', 'Pesanan berhasil dihapus.');
    }
}
