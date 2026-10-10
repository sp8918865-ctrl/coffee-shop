<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class ProductStockColumnMigrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_existing_sku_column_is_renamed_to_stok_without_losing_data(): void
    {
        DB::table('products')->insert([
            'name' => 'Kopi Uji',
            'stok' => '15',
            'price' => 18000,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        Schema::table('products', function ($table) {
            $table->renameColumn('stok', 'sku');
        });

        $migration = require database_path('migrations/2026_10_10_015332_rename_product_sku_to_stok.php');
        $migration->up();

        $this->assertTrue(Schema::hasColumn('products', 'stok'));
        $this->assertFalse(Schema::hasColumn('products', 'sku'));
        $this->assertSame('integer', Schema::getColumnType('products', 'stok'));
        $this->assertDatabaseHas('products', ['name' => 'Kopi Uji', 'stok' => '15']);
    }

    public function test_stok_is_converted_to_integer_and_can_be_shared_by_multiple_products(): void
    {
        Schema::table('products', function ($table): void {
            $table->string('stok')->nullable()->change();
            $table->unique('stok');
        });
        DB::table('products')->insert([
            [
                'name' => 'Kopi Satu',
                'stok' => '15',
                'price' => 18000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Kopi Dua',
                'stok' => '20',
                'price' => 20000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        $migration = require database_path('migrations/2026_10_10_015333_change_products_stok_to_integer.php');
        $migration->up();

        $this->assertSame('integer', Schema::getColumnType('products', 'stok'));
        $this->assertSame(0, collect(Schema::getIndexes('products'))
            ->where('unique', true)
            ->where('columns', ['stok'])
            ->count());
        $this->assertDatabaseHas('products', ['name' => 'Kopi Satu', 'stok' => 15]);
        $this->assertDatabaseHas('products', ['name' => 'Kopi Dua', 'stok' => 20]);

        DB::table('products')->insert([
            'name' => 'Kopi Tiga',
            'stok' => 15,
            'price' => 22000,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $this->assertDatabaseHas('products', ['name' => 'Kopi Tiga', 'stok' => 15]);
    }
}
