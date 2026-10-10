<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\DiningTable;
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
        $this->get(route('admin.tables'))->assertOk();
        $this->get(route('admin.bahan'))->assertOk();
        $this->get(route('admin.staf'))->assertOk();
        $this->get(route('admin.pesanan'))->assertOk();
        $this->get(route('admin.dashboard'))->assertOk();
    }

    public function test_default_food_and_drink_categories_appear_in_menu_form(): void
    {
        $this->actingAs(User::factory()->create())
            ->get(route('admin.menu'))
            ->assertOk()
            ->assertSee('Minuman')
            ->assertSee('Makanan');

        $this->assertDatabaseHas('categories', ['name' => 'Minuman']);
        $this->assertDatabaseHas('categories', ['name' => 'Makanan']);
    }

    public function test_admin_can_manage_tables_and_control_customer_availability(): void
    {
        $this->actingAs(User::factory()->create())
            ->post(route('admin.tables.store'), [
                'table_number' => 'Teras 1',
                'capacity' => 4,
                'status' => 'occupied',
            ])
            ->assertRedirect(route('admin.tables'));

        $table = DiningTable::where('table_number', 'Teras 1')->firstOrFail();
        $this->assertDatabaseHas('tables', [
            'table_id' => $table->table_id,
            'capacity' => 4,
            'status' => 'occupied',
        ]);
        $this->getJson(route('admin.tables.status'))
            ->assertOk()
            ->assertJsonPath('tables.0.id', $table->table_id)
            ->assertJsonPath('tables.0.status', 'occupied');

        $this->put(route('admin.tables.update', $table), [
            'table_number' => 'Teras 2',
            'capacity' => 6,
            'status' => 'reserved',
        ])->assertRedirect(route('admin.tables'));

        $this->assertDatabaseHas('tables', [
            'table_id' => $table->table_id,
            'table_number' => 'Teras 2',
            'capacity' => 6,
            'status' => 'reserved',
        ]);
        $this->getJson(route('admin.tables.status'))
            ->assertOk()
            ->assertJsonPath('tables.0.status', 'reserved');

        $this->delete(route('admin.tables.destroy', $table))->assertRedirect(route('admin.tables'));
        $this->assertDatabaseMissing('tables', ['table_id' => $table->table_id]);
    }

    public function test_authentication_uses_the_erd_password_hash_column(): void
    {
        $user = User::factory()->create();

        $this->post(route('login.submit'), [
            'email' => $user->email,
            'password' => 'password',
        ])->assertRedirect(route('admin.dashboard'));

        $this->assertAuthenticatedAs($user);
        $this->assertSame('password_hash', $user->getAuthPasswordName());
    }

    public function test_menu_and_category_records_can_be_saved_updated_and_deleted(): void
    {
        $this->actingAs(User::factory()->create());

        $this->post(route('admin.categories.store'), ['name' => 'Kopi'])->assertRedirect(route('admin.menu'));
        $category = Category::where('name', 'Kopi')->firstOrFail();

        $this->post(route('admin.menu.store'), [
            'name' => 'Kopi Susu',
            'category_id' => $category->category_id,
            'stok' => '15',
            'price' => 25000,
            'is_available' => '1',
        ])->assertRedirect(route('admin.menu'));
        $product = Product::where('stok', '15')->firstOrFail();

        $this->put(route('admin.menu.update', $product), [
            'name' => 'Kopi Susu Aren',
            'category_id' => $category->category_id,
            'stok' => '15',
            'price' => 27000,
            'is_available' => '1',
        ])->assertRedirect(route('admin.menu'));
        $this->assertDatabaseHas('products', ['product_id' => $product->product_id, 'name' => 'Kopi Susu Aren', 'price' => 27000]);

        $this->get(route('admin.menu', ['search' => 'Aren']))->assertOk()->assertSee('Kopi Susu Aren');
        $this->get(route('admin.menu', ['category_id' => $category->category_id]))->assertOk()->assertSee('Kopi Susu Aren');
        $this->get(route('admin.menu', ['unavailable' => 1]))->assertOk()->assertDontSee('Kopi Susu Aren');

        $this->delete(route('admin.menu.destroy', $product))->assertRedirect(route('admin.menu'));
        $this->delete(route('admin.categories.destroy', $category))->assertRedirect(route('admin.menu'));
        $this->assertDatabaseMissing('products', ['product_id' => $product->product_id]);
        $this->assertDatabaseMissing('categories', ['category_id' => $category->category_id]);
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
            'role' => 'barista',
        ])->assertRedirect(route('admin.staf'));
        $user = User::where('email', 'ayu@example.test')->firstOrFail();
        $this->assertTrue(Hash::check('secret123', $user->password_hash));
        $this->assertSame('barista', $user->role);

        $this->put(route('admin.staf.update', $user), [
            'name' => 'Ayu Senior',
            'email' => 'ayu@example.test',
            'password' => '',
            'role' => 'barista',
        ])->assertRedirect(route('admin.staf'));
        $this->assertDatabaseHas('users', ['user_id' => $user->user_id, 'name' => 'Ayu Senior']);

        $this->post(route('admin.staf.store'), [
            'name' => 'Dewi Admin',
            'email' => 'dewi-admin@example.test',
            'password' => 'secret123',
            'role' => 'admin',
        ])->assertRedirect(route('admin.staf'));
        $admin = User::where('email', 'dewi-admin@example.test')->firstOrFail();
        $this->assertSame('admin', $admin->role);
        $this->actingAs($admin)->get(route('admin.dashboard'))->assertOk();

        $this->delete(route('admin.staf.destroy', $user))->assertRedirect(route('admin.staf'));
        $this->assertDatabaseMissing('users', ['user_id' => $user->user_id]);
    }

    public function test_orders_can_be_created_moved_and_deleted_with_items(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $product = Product::create(['name' => 'Espresso', 'stok' => '8', 'price' => 18000, 'is_available' => true]);
        $table = DiningTable::create(['table_number' => '05', 'capacity' => 2, 'status' => 'available']);

        $this->post(route('admin.pesanan.store'), [
            'product_id' => $product->product_id,
            'quantity' => 2,
            'order_type' => 'dine_in',
            'table_id' => $table->table_id,
            'customer_name' => 'Budi',
            'notes' => 'Tanpa gula',
        ])->assertRedirect(route('admin.pesanan'));
        $order = Order::firstOrFail();
        $this->assertEquals(36000, (float) $order->total_amount);
        $this->assertEquals(36000, (float) $order->subtotal);
        $this->assertSame('pending', $order->order_status);
        $this->assertSame('dine_in', $order->order_type);
        $this->assertSame($table->table_id, $order->table_id);
        $this->assertSame('05', $order->table_number);
        $this->assertDatabaseHas('tables', [
            'table_id' => $table->table_id,
            'status' => 'occupied',
        ]);
        $this->assertSame($user->user_id, $order->user_id);
        $this->assertDatabaseHas('order_items', ['order_id' => $order->order_id, 'product_name' => 'Espresso', 'quantity' => 2]);

        $this->patch(route('admin.pesanan.status', $order), ['status' => 'brewing'])->assertRedirect(route('admin.pesanan'));
        $this->assertDatabaseHas('orders', ['order_id' => $order->order_id, 'order_status' => 'in_progress']);

        $this->patch(route('admin.pesanan.status', $order), ['status' => 'received'])->assertRedirect(route('admin.pesanan'));
        $this->assertDatabaseHas('orders', ['order_id' => $order->order_id, 'order_status' => 'received']);
        $this->get(route('admin.pesanan'))->assertOk()->assertSee('Diterima');

        $this->delete(route('admin.pesanan.destroy', $order))->assertRedirect(route('admin.pesanan'));
        $this->assertDatabaseMissing('orders', ['order_id' => $order->order_id]);
        $this->assertDatabaseMissing('order_items', ['order_id' => $order->order_id]);
    }
}
