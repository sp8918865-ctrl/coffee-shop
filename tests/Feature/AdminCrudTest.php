<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Ingredient;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_database_backed_admin_pages_render(): void
    {
        $this->actingAs(User::factory()->create());

        $this->get(route('admin.menu'))->assertOk();
        $this->get(route('admin.bahan'))->assertOk();
        $this->get(route('admin.staf'))->assertOk();
        $this->get(route('admin.pesanan'))->assertOk();
        $this->get(route('admin.dashboard'))->assertOk();
    }

    public function test_menu_and_category_records_can_be_saved_updated_and_deleted(): void
    {
        $this->actingAs(User::factory()->create());

        $this->post(route('admin.categories.store'), ['name' => 'Kopi'])->assertRedirect(route('admin.menu'));
        $category = Category::where('name', 'Kopi')->firstOrFail();

        $this->post(route('admin.menu.store'), [
            'name' => 'Kopi Susu',
            'category_id' => $category->id,
            'sku' => 'KOPI-001',
            'price' => 25000,
            'is_available' => '1',
        ])->assertRedirect(route('admin.menu'));
        $product = Product::where('sku', 'KOPI-001')->firstOrFail();

        $this->put(route('admin.menu.update', $product), [
            'name' => 'Kopi Susu Aren',
            'category_id' => $category->id,
            'sku' => 'KOPI-001',
            'price' => 27000,
            'is_available' => '1',
        ])->assertRedirect(route('admin.menu'));
        $this->assertDatabaseHas('products', ['id' => $product->id, 'name' => 'Kopi Susu Aren', 'price' => 27000]);

        $this->get(route('admin.menu', ['search' => 'Aren']))->assertOk()->assertSee('Kopi Susu Aren');
        $this->get(route('admin.menu', ['category_id' => $category->id]))->assertOk()->assertSee('Kopi Susu Aren');
        $this->get(route('admin.menu', ['unavailable' => 1]))->assertOk()->assertDontSee('Kopi Susu Aren');

        $this->delete(route('admin.menu.destroy', $product))->assertRedirect(route('admin.menu'));
        $this->delete(route('admin.categories.destroy', $category))->assertRedirect(route('admin.menu'));
        $this->assertDatabaseMissing('products', ['id' => $product->id]);
        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
    }

    public function test_ingredients_can_be_saved_updated_and_deleted_with_computed_status(): void
    {
        $this->actingAs(User::factory()->create());

        $this->post(route('admin.bahan.store'), [
            'name' => 'Susu',
            'quantity' => 0,
            'unit' => 'ml',
            'minimum_quantity' => 100,
        ])->assertRedirect(route('admin.bahan'));
        $ingredient = Ingredient::where('name', 'Susu')->firstOrFail();
        $this->assertSame('critical', $ingredient->status);

        $this->put(route('admin.bahan.update', $ingredient), [
            'name' => 'Susu segar',
            'quantity' => 500,
            'unit' => 'ml',
            'minimum_quantity' => 100,
        ])->assertRedirect(route('admin.bahan'));
        $this->assertDatabaseHas('ingredients', ['id' => $ingredient->id, 'name' => 'Susu segar', 'status' => 'available']);

        $this->delete(route('admin.bahan.destroy', $ingredient))->assertRedirect(route('admin.bahan'));
        $this->assertDatabaseMissing('ingredients', ['id' => $ingredient->id]);
    }

    public function test_staff_accounts_can_be_saved_updated_and_deleted(): void
    {
        $this->actingAs(User::factory()->create());

        $this->post(route('admin.staf.store'), [
            'name' => 'Ayu Barista',
            'email' => 'ayu@example.test',
            'password' => 'secret123',
        ])->assertRedirect(route('admin.staf'));
        $user = User::where('email', 'ayu@example.test')->firstOrFail();
        $this->assertTrue(Hash::check('secret123', $user->password));

        $this->put(route('admin.staf.update', $user), [
            'name' => 'Ayu Senior',
            'email' => 'ayu@example.test',
            'password' => '',
        ])->assertRedirect(route('admin.staf'));
        $this->assertDatabaseHas('users', ['id' => $user->id, 'name' => 'Ayu Senior']);

        $this->delete(route('admin.staf.destroy', $user))->assertRedirect(route('admin.staf'));
        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }

    public function test_orders_can_be_created_moved_and_deleted_with_items(): void
    {
        $this->actingAs(User::factory()->create());

        $product = Product::create(['name' => 'Espresso', 'sku' => 'ESP-001', 'price' => 18000, 'is_available' => true]);

        $this->post(route('admin.pesanan.store'), [
            'product_id' => $product->id,
            'quantity' => 2,
            'order_type' => 'dine_in',
            'table_number' => '05',
            'customer_name' => 'Budi',
            'notes' => 'Tanpa gula',
        ])->assertRedirect(route('admin.pesanan'));
        $order = Order::firstOrFail();
        $this->assertEquals(36000, (float) $order->total_amount);
        $this->assertDatabaseHas('order_items', ['order_id' => $order->id, 'product_name' => 'Espresso', 'quantity' => 2]);

        $this->patch(route('admin.pesanan.status', $order), ['status' => 'brewing'])->assertRedirect(route('admin.pesanan'));
        $this->assertDatabaseHas('orders', ['id' => $order->id, 'status' => 'brewing']);

        $this->delete(route('admin.pesanan.destroy', $order))->assertRedirect(route('admin.pesanan'));
        $this->assertDatabaseMissing('orders', ['id' => $order->id]);
        $this->assertDatabaseMissing('order_items', ['order_id' => $order->id]);
    }
}
