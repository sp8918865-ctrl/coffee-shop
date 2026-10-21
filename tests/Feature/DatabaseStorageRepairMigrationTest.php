<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class DatabaseStorageRepairMigrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_missing_session_and_cache_tables_are_recreated_without_dropping_existing_data(): void
    {
        Schema::drop('sessions');
        Schema::drop('cache_locks');
        Schema::drop('cache');

        $migration = require database_path('migrations/2026_10_10_000004_repair_database_storage_tables_if_missing.php');
        $migration->up();

        $this->assertTrue(Schema::hasColumns('sessions', [
            'id',
            'user_id',
            'ip_address',
            'user_agent',
            'payload',
            'last_activity',
        ]));
        $this->assertTrue(Schema::hasColumns('cache', ['key', 'value', 'expiration']));
        $this->assertTrue(Schema::hasColumns('cache_locks', ['key', 'owner', 'expiration']));

        DB::table('cache')->insert([
            'key' => 'keep-existing',
            'value' => 'preserved',
            'expiration' => now()->timestamp + 3600,
        ]);

        $migration->down();

        $this->assertDatabaseHas('cache', ['key' => 'keep-existing', 'value' => 'preserved']);
    }
}
