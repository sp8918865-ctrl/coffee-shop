<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_from_admin_pages(): void
    {
        $this->get(route('admin.dashboard'))->assertRedirect(route('login'));
        $this->get('/')->assertRedirect(route('login'));
    }

    public function test_admin_landing_redirects_signed_in_managers_to_the_dashboard(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'owner']))
            ->get('/')
            ->assertRedirect(route('admin.dashboard'));
    }

    public function test_barista_login_opens_the_shared_panel_without_staff_management(): void
    {
        $barista = User::factory()->create([
            'email' => 'barista@example.test',
            'password_hash' => 'secret12345',
            'role' => 'barista',
        ]);

        $this->post(route('login.submit'), [
            'email' => $barista->email,
            'password' => 'secret12345',
        ])->assertRedirect(route('admin.dashboard'));

        $this->assertAuthenticatedAs($barista);
        $this->get('/')->assertRedirect(route('admin.dashboard'));
        $this->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Dasbor & Pendapatan')
            ->assertSee('Pesanan')
            ->assertDontSee('Data Barista & Staf');
        $this->get(route('admin.menu'))->assertOk();
        $this->get(route('admin.pesanan'))->assertOk();
        $this->get(route('admin.staf'))->assertForbidden();
        $this->post(route('admin.staf.store'), [
            'name' => 'Staf Baru',
            'email' => 'staff@example.test',
            'password' => 'secret12345',
            'role' => 'barista',
        ])->assertForbidden();
        $this->assertDatabaseMissing('users', ['email' => 'staff@example.test']);
    }

    public function test_demo_login_is_only_available_in_local_environment(): void
    {
        $this->post(route('login.demo'))->assertNotFound();

        $this->app->detectEnvironment(fn () => 'local');

        $this->withSession(['_token' => 'demo-test-token'])
            ->withHeader('X-CSRF-TOKEN', 'demo-test-token')
            ->post(route('login.demo'))
            ->assertRedirect(route('admin.dashboard'));

        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', [
            'email' => 'demo-admin@localhost',
            'role' => 'owner',
        ]);
    }

    public function test_public_catalog_shows_available_products_after_qr_scan_without_login(): void
    {
        $category = \App\Models\Category::create(['name' => 'Kopi']);
        \App\Models\Product::create([
            'category_id' => $category->category_id,
            'name' => 'Latte Publik',
            'stok' => 5,
            'price' => 25000,
            'is_available' => true,
        ]);
        \App\Models\Product::create([
            'category_id' => $category->category_id,
            'name' => 'Menu Habis',
            'price' => 20000,
            'is_available' => false,
        ]);

        $this->get(route('catalog.index'))->assertForbidden();

        $this->get(URL::signedRoute('customer.qr.enter', absolute: false))
            ->assertRedirect(route('catalog.index'));

        $this->get(route('catalog.index'))
            ->assertOk()
            ->assertSee('Latte Publik')
            ->assertDontSee('Menu Habis');
    }

    public function test_customer_pages_reject_direct_and_invalid_qr_access_but_accept_the_signed_qr(): void
    {
        $category = \App\Models\Category::create(['name' => 'Kopi']);
        $product = \App\Models\Product::create([
            'category_id' => $category->category_id,
            'name' => 'Latte',
            'stok' => 5,
            'price' => 25000,
            'is_available' => true,
        ]);

        $this->get(route('catalog.index'))->assertForbidden();
        $this->get(route('checkout'))->assertForbidden();
        $this->get(route('orders.index'))->assertForbidden();
        $this->get(route('orders.track', str_repeat('a', 64)))->assertForbidden();
        $this->get('/menu/qr-entry?signature=invalid')->assertForbidden();
        $this->post(route('cart.add', $product->product_id))->assertForbidden();
        $this->post(route('checkout.place'), [
            'customer_name' => 'Ayu',
            'order_type' => 'take_away',
            'payment_method' => 'cash',
        ])->assertForbidden();
        $this->assertDatabaseCount('orders', 0);

        $this->get(URL::signedRoute('customer.qr.enter', absolute: false))
            ->assertRedirect(route('catalog.index'));

        $this->get(route('catalog.index'))->assertOk();
        $this->get(route('checkout'))->assertRedirect(route('catalog.index'));
        $this->get(route('orders.index'))->assertOk();
        $this->assertGuest();
    }

    public function test_staff_without_a_supported_panel_is_logged_out_after_sign_in(): void
    {
        $staff = User::factory()->create([
            'email' => 'cashier@example.test',
            'password_hash' => 'secret12345',
            'role' => 'cashier',
        ]);

        $this->post(route('login.submit'), [
            'email' => $staff->email,
            'password' => 'secret12345',
        ])->assertRedirect(route('login'))
            ->assertSessionHas('status');
        $this->assertGuest();
        $this->get(route('login'))->assertSee('Akun ini belum memiliki akses ke panel admin atau barista.');
        $this->get(route('admin.dashboard'))->assertRedirect(route('login'));
    }

    public function test_manager_can_view_public_catalog_qr_using_the_current_host(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'owner']));

        config(['app.public_menu_url' => 'http://192.168.1.2:8000']);

        $this->withServerVariables([
            'HTTP_HOST' => 'localhost:8000',
            'HTTPS' => 'off',
        ])->get(route('admin.qr-menu'))
            ->assertOk()
            ->assertSee('QR Menu Publik')
            ->assertSee('http://192.168.1.2:8000/menu/qr-entry?signature=', false)
            ->assertSee('data-url="http://192.168.1.2:8000/menu/qr-entry?signature=', false)
            ->assertSee('http://192.168.1.2:8000/api/v1/customer/qr-access?signature=', false)
            ->assertSee('data-url="http://192.168.1.2:8000/api/v1/customer/qr-access?signature=', false);
    }

    public function test_mobile_devices_cannot_access_admin_pages_or_actions(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'owner']))
            ->withHeader('User-Agent', 'Mozilla/5.0 (iPhone; CPU iPhone OS 17_0 like Mac OS X) Mobile')
            ->get(route('admin.dashboard'))
            ->assertForbidden();

        $this->get(URL::signedRoute('customer.qr.enter', absolute: false))
            ->assertRedirect(route('catalog.index'));
        $this->get(route('catalog.index'))
            ->assertOk()
            ->assertSee('Menu Pelanggan · QR')
            ->assertDontSee('Warung Luwak Console')
            ->assertDontSee('Dasbor & Pendapatan');

        $this->get(route('admin.qr-menu'))->assertForbidden();

        $this->post(route('admin.categories.store'), ['name' => 'Kategori Ponsel'])
            ->assertForbidden();

        $this->assertDatabaseMissing('categories', ['name' => 'Kategori Ponsel']);
    }

    public function test_desktop_devices_can_access_admin_dashboard(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'owner']))
            ->withHeader('User-Agent', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/130.0')
            ->get(route('admin.dashboard'))
            ->assertOk();
    }

    public function test_user_can_log_in_and_log_out_with_database_credentials(): void
    {
        $user = User::factory()->create([
            'email' => 'barista@example.test',
            'password_hash' => 'secret12345',
        ]);

        $this->post(route('login.submit'), [
            'email' => 'barista@example.test',
            'password' => 'wrong-password',
        ])->assertSessionHasErrors('email');
        $this->assertGuest();

        $this->post(route('login.submit'), [
            'email' => 'barista@example.test',
            'password' => 'secret12345',
        ])->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticatedAs($user);

        $this->post(route('logout'))->assertRedirect(route('login'));
        $this->assertGuest();
    }
}
