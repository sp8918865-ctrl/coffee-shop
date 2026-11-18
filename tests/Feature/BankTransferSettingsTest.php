<?php

namespace Tests\Feature;

use App\Models\AppSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BankTransferSettingsTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_save_the_public_bank_transfer_account(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'owner']))
            ->get(route('admin.bank-transfer'))
            ->assertOk()
            ->assertSee('Rekening Transfer')
            ->assertSee('Rekening belum lengkap');

        $this->put(route('admin.bank-transfer.update'), [
            'bank_name' => 'BCA',
            'account_number' => '1234567890',
            'account_holder' => 'Warung Luwak',
        ])->assertRedirect(route('admin.bank-transfer'))
            ->assertSessionHas('success');

        $this->assertSame([
            'bank_name' => 'BCA',
            'account_number' => '1234567890',
            'account_holder' => 'Warung Luwak',
            'configured' => true,
        ], AppSetting::bankTransferDetails());
    }

    public function test_barista_cannot_manage_the_store_transfer_account(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'barista']), 'barista')
            ->get(route('admin.bank-transfer'))
            ->assertRedirect(route('login.admin'));
    }

    public function test_bank_account_number_must_contain_only_digits_spaces_or_hyphens(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'owner']))
            ->from(route('admin.bank-transfer'))
            ->put(route('admin.bank-transfer.update'), [
                'bank_name' => 'BCA',
                'account_number' => '123ABC',
                'account_holder' => 'Warung Luwak',
            ])
            ->assertSessionHasErrors('account_number');

        $this->assertDatabaseCount('app_settings', 0);
    }
}
