<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\DiningTable;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicOrderingTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_browse_menu_add_items_and_view_checkout(): void
    {
        $product = $this->product();

        $this->get(route('catalog.index'))
            ->assertOk()
            ->assertSee('Kopi Susu')
            ->assertSee(route('cart.add', $product->product_id))
            ->assertSee(route('orders.index'))
            ->assertDontSee('href="'.route('login').'"');

        $this->post(route('cart.add', $product->product_id))
            ->assertRedirect()
            ->assertSessionHas('public_order_cart', [$product->product_id => 1]);

        $this->get(route('checkout'))
            ->assertOk()
            ->assertSee('Kopi Susu')
            ->assertSee('QRIS simulasi')
            ->assertSee('Bayar tunai di kedai')
            ->assertSee('Rp 18.000')
            ->assertSee('Meja belum tersedia');

        $this->assertGuest();
    }

    public function test_guest_checkout_persists_order_items_and_unpaid_demo_qris_with_secret_tracking_url(): void
    {
        $product = $this->product();
        $table = DiningTable::create(['table_number' => '07', 'capacity' => 2, 'status' => 'available']);
        $this->withSession(['public_order_cart' => [$product->product_id => 2]]);

        $response = $this->post(route('checkout.place'), [
            'customer_name' => 'Ayu',
            'customer_phone' => '081234567890',
            'order_type' => 'dine_in',
            'table_id' => $table->table_id,
            'payment_method' => 'qris',
        ]);

        $order = Order::with('payments')->firstOrFail();
        $this->assertSame(36000.0, (float) $order->total_amount);
        $this->assertSame('pending', $order->order_status);
        $this->assertSame('dine_in', $order->order_type);
        $this->assertSame($table->table_id, $order->table_id);
        $this->assertSame('07', $order->table_number);
        $this->assertDatabaseHas('tables', [
            'table_id' => $table->table_id,
            'status' => 'occupied',
        ]);
        $this->assertSame(64, strlen($order->public_tracking_token));
        $this->assertNotSame((string) $order->order_id, $order->public_tracking_token);
        $this->assertSame($order->public_tracking_token, session('latest_public_order_token'));
        $this->assertDatabaseHas('order_items', [
            'order_id' => $order->order_id,
            'product_id' => $product->product_id,
            'quantity' => 2,
            'subtotal' => 36000,
        ]);
        $this->assertDatabaseHas('payments', [
            'order_id' => $order->order_id,
            'payment_method' => 'qris',
            'payment_status' => 'pending',
            'payment_gateway_ref' => 'DEMO-NOT-PROCESSED',
            'amount' => 36000,
            'paid_at' => null,
        ]);
        $response->assertRedirect(route('orders.track', $order->public_tracking_token));
        $this->assertNull(session('public_order_cart'));

        $this->get(route('orders.track', $order->public_tracking_token))
            ->assertOk()
            ->assertHeader('Cache-Control', 'no-store, private')
            ->assertSee('name="referrer" content="no-referrer"', false)
            ->assertSee('QRIS SIMULASI')
            ->assertSee('bukan kode QR pembayaran')
            ->assertSee('Belum dibayar')
            ->assertSee('Pesanan diterima');

        $this->get(route('orders.index'))
            ->assertOk()
            ->assertHeader('Cache-Control', 'no-store, private')
            ->assertSee($order->order_number)
            ->assertSee(route('orders.track', $order->public_tracking_token));

        $order->update(['order_status' => 'ready']);
        $this->get(route('orders.track', $order->public_tracking_token))
            ->assertOk()
            ->assertSee('Siap diambil');
    }

    public function test_customer_orders_page_is_public_and_prompts_guests_to_browse_menu(): void
    {
        $this->get(route('orders.index'))
            ->assertOk()
            ->assertSee('Belum ada pesanan')
            ->assertSee(route('catalog.index'));

        $this->assertGuest();
    }

    public function test_cash_demo_records_unpaid_takeaway_order_and_validates_checkout(): void
    {
        $product = $this->product();
        $this->withSession(['public_order_cart' => [$product->product_id => 1]]);

        $this->post(route('checkout.place'), [
            'customer_name' => 'Budi',
            'order_type' => 'take_away',
            'payment_method' => 'cash',
        ])->assertRedirect();

        $order = Order::with('payments')->firstOrFail();
        $this->assertNull($order->table_number);
        $this->assertSame('cash', $order->payments->first()->payment_method);
        $this->assertSame('pending', $order->payments->first()->payment_status);
        $this->assertNull($order->payments->first()->paid_at);

        $this->withSession(['public_order_cart' => [$product->product_id => 1]])
            ->from(route('checkout'))
            ->post(route('checkout.place'), [
                'customer_name' => '',
                'order_type' => 'dine_in',
                'payment_method' => 'card',
            ])
            ->assertSessionHasErrors(['customer_name', 'table_id', 'payment_method']);

        $this->assertDatabaseCount('orders', 1);
    }

    public function test_order_is_not_retrievable_by_sequential_id_or_another_token(): void
    {
        $order = Order::create([
            'order_number' => 'ORD-TEST-001',
            'public_tracking_token' => str_repeat('a', 64),
            'customer_name' => 'Test',
            'order_type' => 'take_away',
            'order_status' => 'pending',
            'subtotal' => 0,
            'total_amount' => 0,
        ]);

        $this->get('/orders/track/'.$order->order_id)->assertNotFound();
        $this->get(route('orders.track', str_repeat('b', 64)))->assertNotFound();
        $this->get(route('orders.track', $order->public_tracking_token))->assertOk();
        $this->assertGuest();
    }

    public function test_unavailable_cart_items_cannot_be_silently_dropped_at_checkout(): void
    {
        $product = $this->product();
        $this->withSession(['public_order_cart' => [$product->product_id => 1]]);
        $product->update(['is_available' => false]);

        $this->get(route('checkout'))
            ->assertRedirect(route('catalog.index'))
            ->assertSessionHas('error');
        $this->post(route('checkout.place'), [
            'customer_name' => 'Ayu',
            'order_type' => 'take_away',
            'payment_method' => 'cash',
        ])->assertRedirect(route('catalog.index'));

        $this->assertDatabaseCount('orders', 0);
    }

    public function test_customer_can_only_select_an_available_admin_configured_table(): void
    {
        $product = $this->product();
        $availableTable = DiningTable::create([
            'table_number' => '01',
            'capacity' => 2,
            'status' => 'available',
        ]);
        $reservedTable = DiningTable::create([
            'table_number' => '02',
            'capacity' => 4,
            'status' => 'reserved',
        ]);

        $this->withSession(['public_order_cart' => [$product->product_id => 1]])
            ->get(route('checkout'))
            ->assertOk()
            ->assertSee('Meja 01')
            ->assertDontSee('Meja 02');

        $this->withSession(['public_order_cart' => [$product->product_id => 1]])
            ->from(route('checkout'))
            ->post(route('checkout.place'), [
                'customer_name' => 'Ayu',
                'order_type' => 'dine_in',
                'table_id' => $reservedTable->table_id,
                'payment_method' => 'cash',
            ])
            ->assertSessionHasErrors('table_id');

        $this->assertDatabaseCount('orders', 0);

        $this->withSession(['public_order_cart' => [$product->product_id => 1]])
            ->post(route('checkout.place'), [
                'customer_name' => 'Ayu',
                'order_type' => 'dine_in',
                'table_id' => $availableTable->table_id,
                'payment_method' => 'cash',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('orders', [
            'table_id' => $availableTable->table_id,
            'table_number' => '01',
        ]);
        $this->assertDatabaseHas('tables', [
            'table_id' => $availableTable->table_id,
            'status' => 'occupied',
        ]);

        $this->withSession(['public_order_cart' => [$product->product_id => 1]])
            ->get(route('checkout'))
            ->assertOk()
            ->assertDontSee('Meja 01');
    }

    private function product(): Product
    {
        $category = Category::create(['name' => 'Kopi']);

        return Product::create([
            'category_id' => $category->category_id,
            'name' => 'Kopi Susu',
            'sku' => 'KOPI-TEST',
            'price' => 18000,
            'is_available' => true,
        ]);
    }
}
