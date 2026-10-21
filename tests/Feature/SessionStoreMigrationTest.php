<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class SessionStoreMigrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_database_session_store_table_is_created_by_migrations(): void
    {
        $this->assertTrue(Schema::hasTable('sessions'));

        Schema::drop('sessions');

        $migration = require database_path('migrations/2026_10_10_000000_ensure_sessions_table_exists.php');
        $migration->up();

        $this->assertTrue(Schema::hasColumns('sessions', [
            'id',
            'user_id',
            'ip_address',
            'user_agent',
            'payload',
            'last_activity',
        ]));
    }
}
