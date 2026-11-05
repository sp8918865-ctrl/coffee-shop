<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\URL;
use App\Models\AppSetting;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_from_admin_pages(): void
    {
        $this->get(route('admin.dashboard'))->assertRedirect(route('login.admin'));
        $this->get(route('barista.dashboard'))->assertRedirect(route('login.barista'));
        $this->get('/')->assertRedirect(route('login.admin'));
    }

    public function test_application_landing_always_opens_the_login_page(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'owner']))
            ->get('/')
            ->assertRedirect(route('login.admin'));
    }

    public function test_barista_login_opens_the_shared_panel_without_staff_management(): void
    {
        $barista = User::factory()->create([
            'username' => 'barista@example.test',
            'password_hash' => 'secret12345',
            'role' => 'barista',
        ]);

        $this->post(route('login.barista.submit'), [
            'username' => $barista->username,
            'password' => 'secret12345',
        ])->assertRedirect(route('barista.dashboard'));

        $this->assertAuthenticatedAs($barista, 'barista');
        $this->get('/')->assertRedirect(route('login.admin'));
        $this->get(route('barista.dashboard'))
            ->assertOk()
            ->assertSee('Dasbor & Pendapatan')
            ->assertSee('Kelola Menu')
            ->assertSee('Kelola Meja')
            ->assertSee('Stok Bahan')
            ->assertSee('Pesanan')
            ->assertSee('QR Menu Publik')
            ->assertDontSee('Data Barista & Staf')
            ->assertSee('href="'.route('barista.pesanan').'"', false)
            ->assertSee('href="'.route('barista.menu').'"', false)
            ->assertSee('href="'.route('barista.tables').'"', false)
            ->assertSee('href="'.route('barista.bahan').'"', false)
            ->assertSee('href="'.route('barista.qr-menu').'"', false);
        $this->get(route('barista.menu'))->assertOk();
        $this->get(route('barista.pesanan'))->assertOk();
        $this->get(route('admin.staf'))->assertRedirect(route('login.admin'));
        $this->post(route('admin.staf.store'), [
            'name' => 'Staf Baru',
            'username' => 'staff@example.test',
            'password' => 'secret12345',
            'role' => 'barista',
        ])->assertRedirect(route('login.admin'));
        $this->assertDatabaseMissing('users', ['username' => 'staff@example.test']);
    }

    public function test_login_page_has_no_role_information_or_demo_access(): void
    {
        $this->get(route('login'))
            ->assertOk()
            ->assertDontSee('Akses sesuai peran akun')
            ->assertDontSee('Admin:')
            ->assertDontSee('Barista:')
            ->assertDontSee('Masuk Mode Demo')
            ->assertDontSee('Akses admin tanpa kredensial');

        $this->post('/login/demo')->assertNotFound();
        $this->assertDatabaseMissing('users', ['username' => 'demo-admin']);
    }

    public function test_admin_and_barista_can_stay_authenticated_in_separate_panels_in_one_browser_session(): void
    {
        $admin = User::factory()->create([
            'username' => 'owner@example.test',
            'password_hash' => 'admin-secret',
            'role' => 'owner',
        ]);
        $barista = User::factory()->create([
            'username' => 'barista@example.test',
            'password_hash' => 'barista-secret',
            'role' => 'barista',
        ]);

        $this->post(route('login.admin.submit'), [
            'username' => $admin->username,
            'password' => 'admin-secret',
        ])->assertRedirect(route('admin.dashboard'));
        $this->post(route('login.barista.submit'), [
            'username' => $barista->username,
            'password' => 'barista-secret',
        ])->assertRedirect(route('barista.dashboard'));

        $this->assertAuthenticatedAs($admin, 'web');
        $this->assertAuthenticatedAs($barista, 'barista');
        $this->get(route('admin.dashboard'))->assertOk()->assertSee('Owner');
        $this->get(route('barista.dashboard'))->assertOk()->assertSee('Barista');

        $this->post(route('barista.logout'))->assertRedirect(route('login.barista'));
        $this->assertAuthenticatedAs($admin, 'web');
        $this->assertGuest('barista');
        $this->get(route('admin.dashboard'))->assertOk();
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

    public function test_disabling_public_qr_blocks_existing_customer_sessions(): void
    {
        $this->get(URL::signedRoute('customer.qr.enter', absolute: false))
            ->assertRedirect(route('catalog.index'));

        AppSetting::setPublicQrEnabled(false);

        $this->get(route('catalog.index'))->assertForbidden();
        $this->get(URL::signedRoute('customer.qr.enter', absolute: false))->assertForbidden();
    }

    public function test_staff_without_a_supported_panel_is_logged_out_after_sign_in(): void
    {
        $staff = User::factory()->create([
            'username' => 'cashier@example.test',
            'password_hash' => 'secret12345',
            'role' => 'cashier',
        ]);

        $this->post(route('login.submit'), [
            'username' => $staff->username,
            'password' => 'secret12345',
        ])->assertSessionHasErrors('username');
        $this->assertGuest();
        $this->get(route('login'))->assertOk();
        $this->get(route('admin.dashboard'))->assertRedirect(route('login.admin'));
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

    public function test_admin_layout_hides_hardware_location_and_database_status_labels(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'owner']))
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Warung Luwak')
            ->assertSee('Keluar / Logout')
            ->assertDontSee('ROASTERY ENGINE OS')
            ->assertDontSee('TERMINAL READY')
            ->assertDontSee('Mesin Espresso Synesso')
            ->assertDontSee('Terhubung ke database')
            ->assertDontSee('BAR UTAMA')
            ->assertDontSee('TERMINAL 01')
            ->assertDontSee('Database aktif');
    }

    public function test_user_can_log_in_and_log_out_with_database_credentials(): void
    {
        $user = User::factory()->create([
            'username' => 'barista@example.test',
            'password_hash' => 'secret12345',
        ]);

        $this->post(route('login.submit'), [
            'username' => 'barista@example.test',
            'password' => 'wrong-password',
        ])->assertSessionHasErrors('username');
        $this->assertGuest();

        $this->post(route('login.submit'), [
            'username' => 'barista@example.test',
            'password' => 'secret12345',
        ])->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticatedAs($user);

        $this->post(route('logout'))->assertRedirect(route('login.admin'));
        $this->assertGuest();
    }
}
