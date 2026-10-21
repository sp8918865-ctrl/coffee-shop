<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ingredient;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $pesananSelesaiHariIni = Order::where('order_status', 'completed')
            ->whereDate('created_at', today());
        $totalPendapatan = (clone $pesananSelesaiHariIni)->sum('total_amount');
        $totalPesananHariIni = Order::whereDate('created_at', today())->count();
        $rataRataPesanan = (clone $pesananSelesaiHariIni)->avg('total_amount') ?? 0;
        $now = now();
        $pesananSelesai = Order::where('order_status', 'completed');
        $penjualanMinggu = (clone $pesananSelesai)
            ->whereBetween('created_at', [$now->copy()->subDays(6)->startOfDay(), $now])
            ->sum('total_amount');
        $penjualanBulan = (clone $pesananSelesai)
            ->whereBetween('created_at', [$now->copy()->startOfMonth(), $now])
            ->sum('total_amount');
        $penjualanTahun = (clone $pesananSelesai)
            ->whereBetween('created_at', [$now->copy()->startOfYear(), $now])
            ->sum('total_amount');
        $minumanTerlaris = OrderItem::query()
            ->join('orders', 'orders.order_id', '=', 'order_items.order_id')
            ->join('products', 'products.product_id', '=', 'order_items.product_id')
            ->join('categories', 'categories.category_id', '=', 'products.category_id')
            ->where('orders.order_status', 'completed')
            ->where('categories.name', 'Minuman')
            ->whereBetween('orders.created_at', [$now->copy()->subDays(29)->startOfDay(), $now])
            ->select(
                'products.product_id',
                'products.name',
                DB::raw('SUM(order_items.quantity) as total_terjual'),
                DB::raw('SUM(order_items.subtotal) as total_pendapatan')
            )
            ->groupBy('products.product_id', 'products.name')
            ->orderByDesc('total_terjual')
            ->orderBy('products.name')
            ->limit(5)
            ->get();
        $bahanKritis = Ingredient::where('status', 'critical')->get();
        $pesananTerbaru = Order::with('items')->latest()->take(5)->get();
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
            'penjualanMinggu',
            'penjualanBulan',
            'penjualanTahun',
            'minumanTerlaris',
            'bahanKritis',
            'pesananTerbaru',
            'jumlahMenu',
            'jumlahMenuHabis',
            'daftarBahanKritis',
        ));
    }
}
