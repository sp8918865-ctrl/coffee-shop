<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
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
            ->assertSee('7 hari terakhir')
            ->assertSee('Bulan ini')
            ->assertSee('Tahun ini')
            ->assertSee('Rp 20.000')
            ->assertSee('Rp 50.000')
            ->assertSee('Rp 90.000')
            ->assertDontSee('Minuman terlaris')
            ->assertDontSee('Pesanan terbaru');
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
            ->assertDontSee('Tahun ini');
    }

    private function recordOrder(string $number, string $status, \Illuminate\Support\Carbon $createdAt, int $total): void
    {
        Order::create([
            'order_number' => $number,
            'order_type' => 'take_away',
            'order_status' => $status,
            'subtotal' => $total,
            'total_amount' => $total,
            'created_at' => $createdAt,
            'updated_at' => $createdAt,
        ]);
    }
}
