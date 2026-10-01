<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ingredient;
use App\Models\Order;
use App\Models\Product;

class DashboardController extends Controller
{
    public function index()
    {
        $pesananSelesaiHariIni = Order::where('status', 'completed')->whereDate('created_at', today());
        $totalPendapatan = (clone $pesananSelesaiHariIni)->sum('total_amount');
        $totalPesananHariIni = Order::whereDate('created_at', today())->count();
        $rataRataPesanan = (clone $pesananSelesaiHariIni)->avg('total_amount') ?? 0;
        $bahanKritis = Ingredient::where('status', 'critical')->get();
        $pesananTerbaru = Order::with('items')->latest()->take(5)->get();
        $jumlahMenu = Product::count();
        $jumlahMenuHabis = Product::where('is_available', false)->count();
        $daftarBahanKritis = $bahanKritis->pluck('name')->take(3)->join(', ') ?: 'Tidak ada bahan kritis';

        return view('admin.dashboard', compact(
            'totalPendapatan', 'totalPesananHariIni', 'rataRataPesanan',
            'bahanKritis', 'pesananTerbaru', 'jumlahMenu', 'jumlahMenuHabis',
            'daftarBahanKritis'
        ));
    }
}
