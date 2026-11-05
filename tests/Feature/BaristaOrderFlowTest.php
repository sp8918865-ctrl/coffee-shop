<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class BaristaOrderFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_barista_can_use_the_shared_order_panel_and_update_order_status(): void
    {
        $barista = User::factory()->create([
            'username' => 'barista@example.test',
            'password_hash' => 'secret12345',
            'role' => 'barista',
        ]);
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

        $this->post(route('login.barista.submit'), [
            'username' => $barista->username,
            'password' => 'secret12345',
        ])->assertRedirect(route('barista.dashboard'));

        $this->get(route('barista.dashboard'))
            ->assertOk()
            ->assertSee('Barista')
            ->assertDontSee('Data Barista &amp; Staf');
        $this->get(route('barista.pesanan'))
            ->assertOk()
            ->assertSee('ORD-BARISTA-001')
            ->assertSee('Kopi Susu');

        $this->patch(route('barista.pesanan.status', $order), ['status' => 'brewing'])
            ->assertRedirect(route('barista.pesanan'));
        $this->assertDatabaseHas('orders', [
            'order_id' => $order->order_id,
            'order_status' => 'in_progress',
        ]);
    }

    public function test_barista_can_print_a_58mm_receipt_with_recorded_payment_details(): void
    {
        $barista = User::factory()->create(['role' => 'barista']);
        $order = Order::create([
            'order_number' => 'ORD-RECEIPT-001',
            'order_type' => 'dine_in',
            'table_number' => '05',
            'customer_name' => 'Ayu',
            'order_status' => 'pending',
            'subtotal' => 36000,
            'total_amount' => 36000,
        ]);
        OrderItem::create([
            'order_id' => $order->order_id,
            'product_name' => 'Kopi Susu',
            'quantity' => 2,
            'unit_price' => 18000,
            'subtotal' => 36000,
        ]);
        Payment::create([
            'order_id' => $order->order_id,
            'receipt_number' => 'PAY-RECEIPT-001',
            'payment_method' => 'cash',
            'amount' => 36000,
            'cash_received' => 50000,
            'cash_change' => 14000,
            'payment_status' => 'pending',
        ]);

        $this->actingAs($barista, 'barista')
            ->get(route('barista.pesanan'))
            ->assertOk()
            ->assertSee('Buat bon')
            ->assertSee(route('barista.pesanan.receipt', $order), false);

        $this->get(route('barista.pesanan.receipt', $order))
            ->assertOk()
            ->assertSee('58mm', false)
            ->assertSee(asset('logo.png'), false)
            ->assertSee('alt="Logo Warung Luwak"', false)
            ->assertSee('WARUNG LUWAK')
            ->assertSee('ORD-RECEIPT-001')
            ->assertSee('SCAN UNTUK CEK STATUS PESANAN')
            ->assertSee('PAY-RECEIPT-001')
            ->assertSee('Kopi Susu')
            ->assertSee('Rp 36.000')
            ->assertSee('Rp 50.000')
            ->assertSee('Rp 14.000')
            ->assertSee('Menunggu')
            ->assertDontSee('ID TRANSAKSI')
            ->assertDontSee('ADMIN FEE');

        $trackingToken = $order->fresh()->public_tracking_token;
        $this->assertIsString($trackingToken);
        $this->assertSame(64, strlen($trackingToken));

        $trackingEntry = URL::signedRoute(
            'customer.order.track.enter',
            ['token' => $trackingToken],
            absolute: false
        );
        $this->assertStringContainsString('orders/track-entry/'.$trackingToken.'?signature=', $trackingEntry);

        $this->get($trackingEntry)
            ->assertRedirect(route('orders.track', $trackingToken));
        $this->get(route('orders.track', $trackingToken))
            ->assertOk()
            ->assertSee('ORD-RECEIPT-001');
    }

    public function test_only_barista_can_access_the_barista_receipt(): void
    {
        $order = Order::create([
            'order_number' => 'ORD-RECEIPT-PRIVATE',
            'order_type' => 'take_away',
            'order_status' => 'pending',
            'total_amount' => 10000,
        ]);

        $this->get(route('barista.pesanan.receipt', $order))
            ->assertRedirect(route('login.barista'));

        $this->actingAs(User::factory()->create(['role' => 'owner']), 'web')
            ->get(route('barista.pesanan.receipt', $order))
            ->assertRedirect(route('login.barista'));
    }

    public function test_admin_can_create_a_receipt_from_the_order_card(): void
    {
        $admin = User::factory()->create(['role' => 'owner']);
        $order = Order::create([
            'order_number' => 'ORD-ADMIN-RECEIPT-001',
            'order_type' => 'take_away',
            'order_status' => 'pending',
            'total_amount' => 12000,
        ]);

        $this->actingAs($admin, 'web')
            ->get(route('admin.pesanan'))
            ->assertOk()
            ->assertSee('Buat bon')
            ->assertSee(route('admin.pesanan.receipt', $order), false);

        $this->get(route('admin.pesanan.receipt', $order))
            ->assertOk()
            ->assertSee('ORD-ADMIN-RECEIPT-001');
    }
}
