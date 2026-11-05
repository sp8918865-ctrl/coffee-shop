<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_home_requires_a_valid_qr_scan(): void
    {
        $this->get('/')->assertRedirect(route('login.admin'));
        $this->get(route('catalog.index'))->assertForbidden();

        $this->get(URL::signedRoute('customer.qr.enter', absolute: false))
            ->assertRedirect(route('catalog.index'));

        $this->get(route('catalog.index'))->assertOk();
    }
}
