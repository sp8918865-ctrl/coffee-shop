<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class CoreApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_api_root_routes_describe_the_available_versioned_api(): void
    {
        $this->getJson('/api')
            ->assertOk()
            ->assertJsonPath('version', 'v1')
            ->assertJsonPath('endpoints.v1', url('/api/v1'));

        $this->getJson('/api/v1')
            ->assertOk()
            ->assertJsonPath('version', 'v1')
            ->assertJsonPath('endpoints.customer_menu', url('/api/v1/customer/menu'));
    }

    public function test_admin_can_login_use_api_and_revoke_a_bearer_token(): void
    {
        $admin = User::factory()->create([
            'email' => 'owner@example.test',
            'password_hash' => 'secret-password',
            'role' => 'owner',
        ]);

        $login = $this->postJson('/api/v1/auth/login', [
            'email' => $admin->email,
            'password' => 'secret-password',
            'device_name' => 'test-client',
        ])->assertOk()
            ->assertJsonPath('data.token_type', 'Bearer')
            ->assertJsonPath('data.user.role', 'owner')
            ->assertHeader('Cache-Control', 'no-store, private');

        $token = $login->json('data.token');
        $this->withToken($token)
            ->getJson('/api/v1/admin/dashboard')
            ->assertOk()
            ->assertJsonStructure(['data' => ['revenue_today', 'orders_today', 'products_total']]);

        $this->postJson('/api/v1/admin/auth/logout')
            ->assertOk();

        $tokenId = explode('|', $token, 2)[0];
        $this->assertDatabaseMissing('personal_access_tokens', ['id' => $tokenId]);
    }

    public function test_non_admin_cannot_get_an_admin_api_token(): void
    {
        $barista = User::factory()->create([
            'email' => 'barista@example.test',
            'password_hash' => 'secret-password',
            'role' => 'barista',
        ]);

        $this->postJson('/api/v1/auth/login', [
            'email' => $barista->email,
            'password' => 'secret-password',
        ])->assertUnprocessable()
            ->assertJsonPath('message', 'Email, kata sandi, atau hak akses tidak valid.');
    }

    public function test_customer_api_requires_signed_qr_entry_and_hides_unavailable_menu_items(): void
    {
        $category = Category::create(['name' => 'Kopi']);
        Product::create([
            'category_id' => $category->category_id,
            'name' => 'Latte',
            'stok' => 5,
            'price' => 25000,
            'is_available' => true,
        ]);
        Product::create([
            'category_id' => $category->category_id,
            'name' => 'Stok Habis',
            'stok' => 0,
            'price' => 22000,
            'is_available' => true,
        ]);
        Product::create([
            'category_id' => $category->category_id,
            'name' => 'Stok Belum Diatur',
            'price' => 22000,
            'is_available' => true,
        ]);
        Product::create([
            'category_id' => $category->category_id,
            'name' => 'Menu Habis',
            'price' => 22000,
            'is_available' => false,
        ]);

        $this->getJson('/api/v1/customer/menu')->assertUnauthorized();

        $access = $this->getJson(URL::signedRoute('api.v1.customer.qr-access', absolute: false))
            ->assertOk()
            ->assertJsonPath('data.token_type', 'Bearer')
            ->assertHeader('Cache-Control', 'no-store, private');
        $token = $access->json('data.token');

        $this->withToken($token)
            ->getJson('/api/v1/customer/menu')
            ->assertOk()
            ->assertJsonFragment(['name' => 'Latte'])
            ->assertJsonMissing(['name' => 'Menu Habis'])
            ->assertJsonMissing(['name' => 'Stok Habis'])
            ->assertJsonMissing(['name' => 'Stok Belum Diatur']);

        $this->withToken($token)->postJson('/api/v1/customer/auth/logout')->assertOk();
        $this->assertDatabaseMissing('customer_api_tokens', [
            'token_hash' => hash('sha256', $token),
        ]);
    }

    public function test_customer_api_creates_order_using_database_price_and_returns_tracking_token(): void
    {
        $category = Category::create(['name' => 'Kopi']);
        $product = Product::create([
            'category_id' => $category->category_id,
            'name' => 'Latte',
            'stok' => 2,
            'price' => 25000,
            'is_available' => true,
        ]);
        $access = $this->getJson(URL::signedRoute('api.v1.customer.qr-access', absolute: false));
        $token = $access->json('data.token');

        $response = $this->withToken($token)->postJson('/api/v1/customer/orders', [
            'customer_name' => 'Ayu',
            'order_type' => 'take_away',
            'payment_method' => 'qris',
            'items' => [
                ['product_id' => $product->product_id, 'quantity' => 2],
            ],
        ])->assertCreated()
            ->assertJsonPath('data.total', 50000)
            ->assertJsonPath('data.status', 'pending')
            ->assertJsonPath('data.items.0.name', 'Latte');
        $this->assertDatabaseHas('products', ['product_id' => $product->product_id, 'stok' => 0]);

        $trackingToken = $response->json('data.tracking_token');
        $this->assertIsString($trackingToken);
        $this->assertSame(64, strlen($trackingToken));

        $this->withToken($token)
            ->getJson('/api/v1/customer/orders/'.$trackingToken)
            ->assertOk()
            ->assertJsonPath('data.customer_name', 'Ayu');
        $this->withToken($token)
            ->postJson('/api/v1/customer/orders', [
                'customer_name' => 'Ayu',
                'order_type' => 'take_away',
                'payment_method' => 'cash',
                'items' => [['product_id' => $product->product_id, 'quantity' => 1]],
            ])->assertUnprocessable();
        $this->assertDatabaseCount('orders', 1);
    }

    public function test_admin_api_can_manage_products_categories_ingredients_and_tables(): void
    {
        $admin = User::factory()->create([
            'email' => 'api-owner@example.test',
            'password_hash' => 'secret-password',
            'role' => 'owner',
        ]);
        $token = $this->postJson('/api/v1/auth/login', [
            'email' => $admin->email,
            'password' => 'secret-password',
        ])->json('data.token');
        $this->withToken($token);

        $categoryId = $this->postJson('/api/v1/admin/categories', ['name' => 'Kopi API'])
            ->assertCreated()
            ->json('data.category_id');

        $productId = $this->postJson('/api/v1/admin/products', [
            'name' => 'Latte API',
            'category_id' => $categoryId,
            'stok' => '12',
            'price' => 25000,
            'is_available' => true,
        ])->assertCreated()
            ->json('data.product_id');

        $this->getJson('/api/v1/admin/products')
            ->assertOk()
            ->assertJsonFragment(['name' => 'Latte API']);
        $this->putJson('/api/v1/admin/products/'.$productId, [
            'name' => 'Latte API Besar',
            'category_id' => $categoryId,
            'stok' => '12',
            'price' => 30000,
            'is_available' => true,
        ])->assertOk()
            ->assertJsonPath('data.name', 'Latte API Besar');

        $ingredientId = $this->postJson('/api/v1/admin/ingredients', [
            'name' => 'Biji API',
            'quantity' => 5,
            'unit' => 'kg',
            'minimum_quantity' => 1,
        ])->assertCreated()
            ->json('data.id');
        $this->assertDatabaseHas('ingredients', ['id' => $ingredientId, 'status' => 'available']);

        $tableId = $this->postJson('/api/v1/admin/tables', [
            'table_number' => 'API-01',
            'capacity' => 4,
            'status' => 'available',
        ])->assertCreated()
            ->json('data.table_id');
        $this->getJson('/api/v1/admin/tables')
            ->assertOk()
            ->assertJsonFragment(['table_number' => 'API-01']);

        $this->assertDatabaseHas('products', ['product_id' => $productId, 'name' => 'Latte API Besar']);
        $this->assertDatabaseHas('tables', ['table_id' => $tableId, 'status' => 'available']);
    }

    public function test_admin_api_rejects_missing_and_invalid_tokens(): void
    {
        $this->getJson('/api/v1/admin/dashboard')->assertUnauthorized();
        $this->withToken('not-a-valid-token')
            ->getJson('/api/v1/admin/dashboard')
            ->assertUnauthorized();
    }

    public function test_mobile_devices_cannot_log_in_to_or_use_admin_api(): void
    {
        $admin = User::factory()->create([
            'email' => 'desktop-owner@example.test',
            'password_hash' => 'secret-password',
            'role' => 'owner',
        ]);
        $token = $this->postJson('/api/v1/auth/login', [
            'email' => $admin->email,
            'password' => 'secret-password',
        ])->json('data.token');

        $this->withHeader('User-Agent', 'Mozilla/5.0 (iPhone; CPU iPhone OS 17_0 like Mac OS X) Mobile')
            ->postJson('/api/v1/auth/login', [
                'email' => $admin->email,
                'password' => 'secret-password',
            ])
            ->assertForbidden();

        $this->withHeader('User-Agent', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/130.0')
            ->withToken($token)
            ->getJson('/api/v1/admin/dashboard')
            ->assertOk();

        $this->withHeader('User-Agent', 'Mozilla/5.0 (iPhone; CPU iPhone OS 17_0 like Mac OS X) Mobile')
            ->withToken($token)
            ->getJson('/api/v1/admin/dashboard')
            ->assertForbidden();
    }
}
