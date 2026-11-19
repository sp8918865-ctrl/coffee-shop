<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class DashboardSalesTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_dashboard_shows_weekly_monthly_and_yearly_sales_summary(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'owner']));

        $this->recordOrder('SLS-TODAY', 'completed', now(), 20000);
        $this->recordOrder('SLS-MONTH', 'completed', now()->subDays(10), 30000);
        $this->recordOrder('SLS-YEAR', 'completed', now()->subDays(40), 40000);
        $this->recordOrder('SLS-OLD', 'completed', now()->subDays(400), 50000);
        $this->recordOrder('SLS-PENDING', 'pending', now(), 99000);

        $this->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Total pendapatan')
            ->assertSee('Rata-rata nilai pesanan')
            ->assertSee('Ketersediaan menu')
            ->assertSee('Stok gudang')
            ->assertSee('Ringkasan penjualan')
            ->assertSee('Cetak ringkasan penjualan')
            ->assertSee('7 hari terakhir')
            ->assertSee('Bulan ini')
            ->assertSee('Tahun ini')
            ->assertSee('Rp 20.000')
            ->assertSee('Rp 50.000')
            ->assertSee('Rp 90.000');
    }

    public function test_admin_dashboard_shows_daily_sales_and_best_selling_menus(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'owner']));

        $todayOrder = $this->recordOrder('SLS-CHART-TODAY', 'completed', now(), 18000);
        $olderOrder = $this->recordOrder('SLS-CHART-OLDER', 'completed', now()->subDays(2), 10000);
        $pendingOrder = $this->recordOrder('SLS-CHART-PENDING', 'pending', now(), 50000);
        $oldOrder = $this->recordOrder('SLS-CHART-OLD', 'completed', now()->subDays(31), 25000);

        $this->recordOrderItem($todayOrder, 'Kopi Susu', 2, 18000);
        $this->recordOrderItem($olderOrder, 'Espresso', 1, 10000);
        $this->recordOrderItem($pendingOrder, 'Menu Belum Selesai', 5, 50000);
        $this->recordOrderItem($oldOrder, 'Menu Lama', 1, 25000);

        $this->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Penjualan harian (7 hari terakhir)')
            ->assertSee('Menu terlaris (30 hari terakhir)')
            ->assertSee('Kopi Susu')
            ->assertSee('2 terjual')
            ->assertSee('Rp 18.000')
            ->assertSee('Espresso')
            ->assertDontSee('Menu Belum Selesai')
            ->assertDontSee('Menu Lama');
    }

    public function test_barista_dashboard_hides_sales_period_summaries(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'barista']), 'barista')
            ->get(route('barista.dashboard'))
            ->assertOk()
            ->assertSee('Total pendapatan')
            ->assertDontSee('Ringkasan penjualan')
            ->assertDontSee('7 hari terakhir')
            ->assertDontSee('Bulan ini')
            ->assertDontSee('Tahun ini')
            ->assertDontSee('Penjualan harian (7 hari terakhir)')
            ->assertDontSee('Menu terlaris (30 hari terakhir)');
    }

    public function test_admin_can_print_sales_report_with_timestamp_components(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-11-05 12:27:00'));
        $this->actingAs(User::factory()->create(['role' => 'owner']));
        $this->recordOrder('SLS-REPORT', 'completed', Carbon::parse('2026-11-05 09:07:00'), 25000);
        $this->recordOrder('SLS-IGNORED', 'pending', Carbon::parse('2026-11-05 10:30:00'), 99000);

        $this->get(route('admin.sales-report'))
            ->assertOk()
            ->assertSee('Cetak laporan')
            ->assertSee('WARUNG LUWAK — Ringkasan Penjualan')
            ->assertSee('Tahun')
            ->assertSee('Bulan')
            ->assertSee('Tanggal')
            ->assertSee('Hari')
            ->assertSee('Jam')
            ->assertSee('Menit')
            ->assertSee('2026')
            ->assertSee('November')
            ->assertSee('05')
            ->assertSee('Kamis')
            ->assertSee('09')
            ->assertSee('07')
            ->assertSee('Rp 25.000')
            ->assertDontSee('SLS-IGNORED');

        Carbon::setTestNow();
    }

    public function test_admin_can_filter_sales_report_by_payment_method(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-11-05 12:27:00'));
        $this->actingAs(User::factory()->create(['role' => 'owner']));

        $cashOrder = $this->recordOrder('SLS-CASH', 'completed', Carbon::parse('2026-11-05 09:07:00'), 25000);
        $qrisOrder = $this->recordOrder('SLS-QRIS', 'completed', Carbon::parse('2026-11-05 10:07:00'), 40000);
        $this->recordPayment($cashOrder, 'cash', 25000);
        $this->recordPayment($qrisOrder, 'qris', 40000);

        $this->get(route('admin.sales-report', ['payment_method' => 'cash']))
            ->assertOk()
            ->assertSee('Metode pembayaran')
            ->assertSee('Tunai')
            ->assertSee('SLS-CASH')
            ->assertDontSee('SLS-QRIS');

        $this->get(route('admin.sales-report', ['payment_method' => 'qris']))
            ->assertOk()
            ->assertSee('SLS-QRIS')
            ->assertDontSee('SLS-CASH');

        Carbon::setTestNow();
    }

    public function test_barista_cannot_access_sales_report(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'barista']), 'barista')
            ->get(route('admin.sales-report'))
            ->assertRedirect(route('login.admin'));
    }

    private function recordPayment(Order $order, string $method, int $amount): void
    {
        Payment::create([
            'order_id' => $order->order_id,
            'payment_method' => $method,
            'amount' => $amount,
            'payment_status' => 'paid',
        ]);
    }

    private function recordOrder(string $number, string $status, \Illuminate\Support\Carbon $createdAt, int $total): Order
    {
        return Order::create([
            'order_number' => $number,
            'order_type' => 'take_away',
            'order_status' => $status,
            'subtotal' => $total,
            'total_amount' => $total,
            'created_at' => $createdAt,
            'updated_at' => $createdAt,
        ]);
    }

    private function recordOrderItem(Order $order, string $productName, int $quantity, int $subtotal): void
    {
        OrderItem::create([
            'order_id' => $order->order_id,
            'product_name' => $productName,
            'quantity' => $quantity,
            'unit_price' => $subtotal / $quantity,
            'subtotal' => $subtotal,
        ]);
    }
}
