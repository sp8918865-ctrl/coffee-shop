<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardSalesTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_summarizes_completed_sales_and_ranks_drinks_sold_in_the_last_30_days(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'owner']));

        $drinks = Category::firstOrCreate(['name' => 'Minuman']);
        $food = Category::firstOrCreate(['name' => 'Makanan']);
        $latte = Product::create([
            'category_id' => $drinks->category_id,
            'name' => 'Latte',
            'stok' => 20,
            'price' => 10000,
            'is_available' => true,
        ]);
        $tea = Product::create([
            'category_id' => $drinks->category_id,
            'name' => 'Teh',
            'stok' => 20,
            'price' => 5000,
            'is_available' => true,
        ]);
        $meal = Product::create([
            'category_id' => $food->category_id,
            'name' => 'Roti',
            'stok' => 20,
            'price' => 7000,
            'is_available' => true,
        ]);

        $this->recordOrder('SLS-TODAY', 'completed', now(), 20000, [
            [$latte, 2, 10000],
            [$tea, 6, 5000],
        ]);
        $this->recordOrder('SLS-MONTH', 'completed', now()->subDays(10), 30000, [
            [$latte, 3, 10000],
        ]);
        $this->recordOrder('SLS-YEAR', 'completed', now()->subDays(40), 40000, [
            [$tea, 8, 5000],
        ]);
        $this->recordOrder('SLS-OLD', 'completed', now()->subDays(400), 50000, [
            [$latte, 50, 10000],
        ]);
        $this->recordOrder('SLS-PENDING', 'pending', now(), 99000, [
            [$latte, 100, 10000],
        ]);
        $this->recordOrder('SLS-FOOD', 'completed', now(), 7000, [
            [$meal, 10, 700],
        ]);

        $this->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('7 hari terakhir')
            ->assertSee('Bulan ini')
            ->assertSee('Tahun ini')
            ->assertSee('Minuman terlaris')
            ->assertSee('30 hari terakhir')
            ->assertSee('Rp 27.000')
            ->assertSee('Rp 57.000')
            ->assertSee('Rp 97.000')
            ->assertSee('Teh')
            ->assertSee('6 gelas')
            ->assertDontSee('100 gelas')
            ->assertDontSee('50 gelas');
    }

    /**
     * @param array<int, array{0: Product, 1: int, 2: int}> $items
     */
    private function recordOrder(string $number, string $status, \Illuminate\Support\Carbon $createdAt, int $total, array $items): void
    {
        $order = Order::create([
            'order_number' => $number,
            'order_type' => 'take_away',
            'order_status' => $status,
            'subtotal' => $total,
            'total_amount' => $total,
            'created_at' => $createdAt,
            'updated_at' => $createdAt,
        ]);

        foreach ($items as [$product, $quantity, $unitPrice]) {
            OrderItem::create([
                'order_id' => $order->order_id,
                'product_id' => $product->product_id,
                'product_name' => $product->name,
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'subtotal' => $quantity * $unitPrice,
            ]);
        }
    }
}
