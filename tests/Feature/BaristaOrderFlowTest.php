<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BaristaOrderFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_barista_can_use_the_shared_order_panel_and_update_order_status(): void
    {
        $barista = User::factory()->create(['role' => 'barista']);
        $order = Order::create([
            'order_number' => 'ORD-BARISTA-001',
            'order_type' => 'take_away',
            'order_status' => 'pending',
            'subtotal' => 18000,
            'total_amount' => 18000,
        ]);
        OrderItem::create([
            'order_id' => $order->order_id,
            'product_name' => 'Kopi Susu',
            'quantity' => 1,
            'unit_price' => 18000,
            'subtotal' => 18000,
        ]);

        $this->actingAs($barista)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Barista')
            ->assertDontSee('Data Barista &amp; Staf');
        $this->get(route('admin.pesanan'))
            ->assertOk()
            ->assertSee('ORD-BARISTA-001')
            ->assertSee('Kopi Susu');

        $this->patch(route('admin.pesanan.status', $order), ['status' => 'brewing'])
            ->assertRedirect(route('admin.pesanan'));
        $this->assertDatabaseHas('orders', [
            'order_id' => $order->order_id,
            'order_status' => 'in_progress',
        ]);
    }
}
