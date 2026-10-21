<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class DatabaseCacheTablesMigrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_missing_database_cache_tables_are_recreated_by_migration(): void
    {
        Schema::drop('cache_locks');
        Schema::drop('cache');

        $migration = require database_path('migrations/2026_10_10_000001_ensure_database_cache_tables_exist.php');
        $migration->up();

        $this->assertTrue(Schema::hasColumns('cache', ['key', 'value', 'expiration']));
        $this->assertTrue(Schema::hasColumns('cache_locks', ['key', 'owner', 'expiration']));
    }
}
