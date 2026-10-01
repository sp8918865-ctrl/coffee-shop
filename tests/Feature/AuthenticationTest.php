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

    public function test_user_can_log_in_and_log_out_with_database_credentials(): void
    {
        $user = User::factory()->create([
            'email' => 'barista@example.test',
            'password' => 'secret12345',
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
