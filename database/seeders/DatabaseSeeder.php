<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $username = env('ADMIN_USERNAME');
        $password = env('ADMIN_PASSWORD');

        if (! $username || ! $password) {
            return;
        }

        User::updateOrCreate(
            ['username' => $username],
            [
                'name' => env('ADMIN_NAME', 'Administrator'),
                'password_hash' => $password,
                'role' => 'owner',
            ]
        );

        $baristaUsername = env('BARISTA_USERNAME');
        $baristaPassword = env('BARISTA_PASSWORD');
        if ($baristaUsername === $username) {
            throw new \InvalidArgumentException('ADMIN_USERNAME dan BARISTA_USERNAME harus berbeda.');
        }

        if ($baristaUsername && $baristaPassword) {
            User::updateOrCreate(
                ['username' => $baristaUsername],
                [
                    'name' => env('BARISTA_NAME', 'Barista'),
                    'password_hash' => $baristaPassword,
                    'role' => 'barista',
                ]
            );
        }
    }
}
