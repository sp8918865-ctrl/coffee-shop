<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class IngredientsTableMigrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_missing_ingredients_table_is_recreated_by_migration(): void
    {
        Schema::drop('ingredients');

        $migration = require database_path('migrations/2026_10_10_000002_ensure_ingredients_table_exists.php');
        $migration->up();

        $this->assertTrue(Schema::hasColumns('ingredients', [
            'id',
            'name',
            'quantity',
            'unit',
            'minimum_quantity',
            'status',
            'created_at',
            'updated_at',
        ]));
    }
}
