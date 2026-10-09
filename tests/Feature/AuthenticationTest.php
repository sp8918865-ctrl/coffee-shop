<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_from_admin_pages(): void
    {
        $this->get(route('admin.dashboard'))->assertRedirect(route('login'));
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

    public function test_public_catalog_shows_available_products_without_login(): void
    {
        $category = \App\Models\Category::create(['name' => 'Kopi']);
        \App\Models\Product::create([
            'category_id' => $category->category_id,
            'name' => 'Latte Publik',
            'price' => 25000,
            'is_available' => true,
        ]);
        \App\Models\Product::create([
            'category_id' => $category->category_id,
            'name' => 'Menu Habis',
            'price' => 20000,
            'is_available' => false,
        ]);

        $this->get(route('catalog.index'))
            ->assertOk()
            ->assertSee('Latte Publik')
            ->assertDontSee('Menu Habis');
    }

    public function test_non_manager_can_sign_in_but_cannot_open_admin_dashboard(): void
    {
        $barista = User::factory()->create([
            'email' => 'barista@example.test',
            'password_hash' => 'secret12345',
            'role' => 'barista',
        ]);

        $this->post(route('login.submit'), [
            'email' => $barista->email,
            'password' => 'secret12345',
        ])->assertRedirect(route('catalog.index'));
        $this->assertAuthenticatedAs($barista);

        $this->get(route('admin.dashboard'))->assertRedirect(route('catalog.index'));
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
            ->assertSee('http://192.168.1.2:8000/', false)
            ->assertSee('data-url="http://192.168.1.2:8000/"', false);
    }

    public function test_mobile_devices_cannot_access_admin_pages_or_actions(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'owner']))
            ->withHeader('User-Agent', 'Mozilla/5.0 (iPhone; CPU iPhone OS 17_0 like Mac OS X) Mobile')
            ->get(route('admin.dashboard'))
            ->assertForbidden();

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
