<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ingredient;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index()
    {
        $pesananSelesaiHariIni = Order::where('order_status', 'completed')
            ->whereDate('created_at', today());
        $totalPendapatan = (clone $pesananSelesaiHariIni)->sum('total_amount');
        $totalPesananHariIni = Order::whereDate('created_at', today())->count();
        $rataRataPesanan = (clone $pesananSelesaiHariIni)->avg('total_amount') ?? 0;
        $salesSummary = null;
        $dailySales = collect();
        $dailySalesMax = 0;
        $bestSellingMenus = collect();

        if (in_array(auth()->user()?->role, ['admin', 'owner'], true)) {
            $now = now();
            $completedOrders = Order::where('order_status', 'completed');
            $salesByDate = (clone $completedOrders)
                ->whereBetween('created_at', [$now->copy()->subDays(6)->startOfDay(), $now])
                ->get(['created_at', 'total_amount'])
                ->groupBy(fn (Order $order): string => $order->created_at->toDateString())
                ->map(fn ($orders): float => (float) $orders->sum('total_amount'));
            $dailySales = collect(range(6, 0))
                ->map(function (int $daysAgo) use ($now, $salesByDate): array {
                    $day = $now->copy()->subDays($daysAgo);

                    return [
                        'label' => $day->format('d/m'),
                        'amount' => $salesByDate->get($day->toDateString(), 0),
                    ];
                });
            $dailySalesMax = (float) $dailySales->max('amount');
            $bestSellingMenus = OrderItem::query()
                ->join('orders', 'orders.order_id', '=', 'order_items.order_id')
                ->where('orders.order_status', 'completed')
                ->whereBetween('orders.created_at', [$now->copy()->subDays(29)->startOfDay(), $now])
                ->select('order_items.product_name')
                ->selectRaw('SUM(order_items.quantity) as sold_quantity')
                ->selectRaw('SUM(order_items.subtotal) as sales_amount')
                ->groupBy('order_items.product_name')
                ->orderByDesc('sold_quantity')
                ->orderBy('order_items.product_name')
                ->limit(5)
                ->get();
            $salesSummary = [
                '7 hari terakhir' => (clone $completedOrders)
                    ->whereBetween('created_at', [$now->copy()->subDays(6)->startOfDay(), $now])
                    ->sum('total_amount'),
                'Bulan ini' => (clone $completedOrders)
                    ->whereBetween('created_at', [$now->copy()->subDays(29)->startOfDay(), $now])
                    ->sum('total_amount'),
                'Tahun ini' => (clone $completedOrders)
                    ->whereBetween('created_at', [$now->copy()->startOfYear(), $now])
                    ->sum('total_amount'),
            ];
        }

        $bahanKritis = Ingredient::where('status', 'critical')->get();
        $jumlahMenu = Product::count();
        $jumlahMenuHabis = Product::where(function ($query): void {
            $query->where('is_available', false)
                ->orWhere('stok', '<=', 0)
                ->orWhereNull('stok');
        })->count();
        $daftarBahanKritis = $bahanKritis->pluck('name')->take(3)->join(', ') ?: 'Tidak ada bahan kritis';

        return view('admin.dashboard', compact(
            'totalPendapatan',
            'totalPesananHariIni',
            'rataRataPesanan',
            'salesSummary',
            'dailySales',
            'dailySalesMax',
            'bestSellingMenus',
            'bahanKritis',
            'jumlahMenu',
            'jumlahMenuHabis',
            'daftarBahanKritis',
        ));
    }

    public function salesReport(): View
    {
        $now = now();
        $completedOrders = Order::where('order_status', 'completed');
        $salesSummary = [
            '7 hari terakhir' => (clone $completedOrders)
                ->whereBetween('created_at', [$now->copy()->subDays(6)->startOfDay(), $now])
                ->sum('total_amount'),
            'Bulan ini' => (clone $completedOrders)
                ->whereBetween('created_at', [$now->copy()->subDays(29)->startOfDay(), $now])
                ->sum('total_amount'),
            'Tahun ini' => (clone $completedOrders)
                ->whereBetween('created_at', [$now->copy()->startOfYear(), $now])
                ->sum('total_amount'),
        ];
        $orders = (clone $completedOrders)
            ->whereBetween('created_at', [$now->copy()->startOfYear(), $now])
            ->latest('created_at')
            ->get();

        return view('admin.sales-report', compact('now', 'salesSummary', 'orders'));
    }
}
