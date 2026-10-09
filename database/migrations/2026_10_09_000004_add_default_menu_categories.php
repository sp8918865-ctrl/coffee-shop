<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $now = now();

        DB::table('categories')->insertOrIgnore([
            ['name' => 'Minuman', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Makanan', 'created_at' => $now, 'updated_at' => $now],
        ]);
    }

    public function down(): void
    {
        // Keep these categories and any menu items assigned to them when rolling back.
    }
};
