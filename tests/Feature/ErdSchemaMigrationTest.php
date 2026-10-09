<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class ErdSchemaMigrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_legacy_rows_survive_column_renames_and_foreign_keys_remain_usable(): void
    {
        $migration = require database_path('migrations/2026_10_09_000002_align_legacy_columns_with_erd.php');
        $migration->down();

        $now = now();
        $passwordHash = Hash::make('existing-password');

        DB::table('users')->insert([
            'id' => 41,
            'name' => 'Existing User',
            'email' => 'existing@example.test',
            'password' => $passwordHash,
            'role' => 'cashier',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        DB::table('categories')->insert([
            'id' => 42,
            'name' => 'Existing Category',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        DB::table('products')->insert([
            'id' => 43,
            'category_id' => 42,
            'name' => 'Existing Product',
            'price' => 12000,
            'is_available' => true,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        DB::table('orders')->insert([
            'id' => 44,
            'status' => 'brewing',
            'order_status' => 'in_progress',
            'order_type' => 'takeaway',
            'total_amount' => 12000,
            'subtotal' => 12000,
            'user_id' => 41,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        DB::table('order_items')->insert([
            'id' => 45,
            'order_id' => 44,
            'product_id' => 43,
            'product_name' => 'Existing Product',
            'quantity' => 1,
            'unit_price' => 12000,
            'subtotal' => 12000,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $migration->up();

        $this->assertTrue(Schema::hasColumn('users', 'user_id'));
        $this->assertTrue(Schema::hasColumn('users', 'password_hash'));
        $this->assertTrue(Schema::hasColumn('categories', 'category_id'));
        $this->assertTrue(Schema::hasColumn('products', 'product_id'));
        $this->assertTrue(Schema::hasColumn('orders', 'order_id'));
        $this->assertTrue(Schema::hasColumn('orders', 'order_status'));
        $this->assertTrue(Schema::hasColumn('order_items', 'order_item_id'));
        $this->assertFalse(Schema::hasColumn('users', 'id'));
        $this->assertFalse(Schema::hasColumn('users', 'password'));
        $this->assertFalse(Schema::hasColumn('categories', 'id'));
        $this->assertFalse(Schema::hasColumn('products', 'id'));
        $this->assertFalse(Schema::hasColumn('orders', 'id'));
        $this->assertFalse(Schema::hasColumn('orders', 'status'));
        $this->assertFalse(Schema::hasColumn('order_items', 'id'));

        $this->assertDatabaseHas('users', ['user_id' => 41, 'password_hash' => $passwordHash]);
        $this->assertDatabaseHas('categories', ['category_id' => 42, 'name' => 'Existing Category']);
        $this->assertDatabaseHas('products', ['product_id' => 43, 'category_id' => 42]);
        $this->assertDatabaseHas('orders', [
            'order_id' => 44,
            'user_id' => 41,
            'order_status' => 'in_progress',
            'order_type' => 'take_away',
        ]);
        $this->assertDatabaseHas('order_items', ['order_item_id' => 45, 'order_id' => 44, 'product_id' => 43]);
    }

    public function test_legacy_unavailable_table_status_is_migrated_to_occupied(): void
    {
        $tableId = DB::table('tables')->insertGetId([
            'table_number' => 'LEGACY-01',
            'capacity' => 2,
            'status' => 'unavailable',
        ], 'table_id');

        $migration = require database_path('migrations/2026_10_09_000005_align_table_statuses_with_erd.php');
        $migration->up();

        $this->assertDatabaseHas('tables', [
            'table_id' => $tableId,
            'status' => 'occupied',
        ]);
    }
}
