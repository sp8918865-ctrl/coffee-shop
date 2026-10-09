<?php

namespace Tests\Feature;

use App\Models\DiningTable;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Product;
use App\Models\User;
use App\Models\Variant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ErdRelationshipsTest extends TestCase
{
    use RefreshDatabase;

    public function test_erd_entities_can_be_linked_through_eloquent_relationships(): void
    {
        $user = User::factory()->create();
        $table = DiningTable::create([
            'table_number' => 'T-01',
            'qr_token' => 'table-token-01',
            'capacity' => 4,
            'status' => 'available',
        ]);
        $category = Category::create(['name' => 'Coffee']);
        $product = Product::create([
            'category_id' => $category->category_id,
            'name' => 'Latte',
            'price' => 25000,
            'is_available' => true,
        ]);
        $order = Order::create([
            'order_number' => 'ORD-ERD-001',
            'status' => 'new',
            'order_type' => 'dine_in',
            'subtotal' => 25000,
            'total_amount' => 25000,
            'table_id' => $table->table_id,
            'user_id' => $user->user_id,
        ]);
        $item = OrderItem::create([
            'order_id' => $order->order_id,
            'product_id' => $product->product_id,
            'product_name' => $product->name,
            'quantity' => 1,
            'unit_price' => 25000,
            'subtotal' => 25000,
            'notes' => 'Less ice',
        ]);
        $variant = Variant::create([
            'product_id' => $product->product_id,
            'variant_group' => 'Milk',
            'variant_name' => 'Oat',
            'additional_price' => 5000,
        ]);
        $item->variants()->attach($variant->variant_id);
        $payment = Payment::create([
            'order_id' => $order->order_id,
            'processed_by_user_id' => $user->user_id,
            'receipt_number' => 'RCP-ERD-001',
            'payment_method' => 'qris',
            'amount' => 25000,
            'payment_status' => 'paid',
        ]);

        $this->assertSame($table->table_id, $order->table->table_id);
        $this->assertSame($user->user_id, $order->user->user_id);
        $this->assertSame($order->order_id, $table->orders->first()->order_id);
        $this->assertSame($product->product_id, $item->product->product_id);
        $this->assertSame($item->order_item_id, $product->orderItems->first()->order_item_id);
        $this->assertSame($category->category_id, $product->category->category_id);
        $this->assertSame($product->product_id, $category->products->first()->product_id);
        $this->assertSame($variant->variant_id, $item->variants->first()->variant_id);
        $this->assertSame($product->product_id, $variant->product->product_id);
        $this->assertSame($item->order_item_id, $variant->orderItems->first()->order_item_id);
        $this->assertSame($order->order_id, $payment->order->order_id);
        $this->assertSame($user->user_id, $payment->processedBy->user_id);
        $this->assertSame($payment->payment_id, $user->processedPayments->first()->payment_id);
    }
}
