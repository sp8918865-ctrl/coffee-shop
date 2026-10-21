<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class PruneExpiredStorageTest extends TestCase
{
    use RefreshDatabase;

    public function test_command_deletes_only_expired_database_cache_and_session_rows(): void
    {
        config([
            'session.driver' => 'database',
            'session.table' => 'sessions',
            'session.connection' => null,
            'session.lifetime' => 120,
            'cache.default' => 'database',
            'cache.stores.database.driver' => 'database',
            'cache.stores.database.table' => 'cache',
            'cache.stores.database.connection' => null,
            'cache.stores.database.lock_table' => 'cache_locks',
            'cache.stores.database.lock_connection' => null,
        ]);

        $now = now()->timestamp;
        DB::table('sessions')->insert([
            [
                'id' => 'expired-session',
                'payload' => 'expired',
                'last_activity' => $now - (121 * 60),
            ],
            [
                'id' => 'active-session',
                'payload' => 'active',
                'last_activity' => $now - (30 * 60),
            ],
        ]);
        DB::table('cache')->insert([
            ['key' => 'expired-cache', 'value' => 'expired', 'expiration' => $now - 1],
            ['key' => 'active-cache', 'value' => 'active', 'expiration' => $now + 600],
        ]);
        DB::table('cache_locks')->insert([
            ['key' => 'expired-lock', 'owner' => 'owner', 'expiration' => $now - 1],
            ['key' => 'active-lock', 'owner' => 'owner', 'expiration' => $now + 600],
        ]);

        $this->artisan('app:prune-expired-storage', ['--dry-run' => true])
            ->expectsOutputToContain('Sessions: 1 rows expired.')
            ->expectsOutputToContain('Cache: 1 rows expired.')
            ->expectsOutputToContain('Cache locks: 1 rows expired.')
            ->expectsOutputToContain('Dry run only; no rows were deleted.')
            ->assertSuccessful();

        $this->assertDatabaseHas('sessions', ['id' => 'expired-session']);
        $this->assertDatabaseHas('cache', ['key' => 'expired-cache']);
        $this->assertDatabaseHas('cache_locks', ['key' => 'expired-lock']);

        $this->artisan('app:prune-expired-storage')
            ->expectsOutputToContain('Sessions: 1 rows deleted.')
            ->expectsOutputToContain('Cache: 1 rows deleted.')
            ->expectsOutputToContain('Cache locks: 1 rows deleted.')
            ->assertSuccessful();

        $this->assertDatabaseMissing('sessions', ['id' => 'expired-session']);
        $this->assertDatabaseHas('sessions', ['id' => 'active-session']);
        $this->assertDatabaseMissing('cache', ['key' => 'expired-cache']);
        $this->assertDatabaseHas('cache', ['key' => 'active-cache']);
        $this->assertDatabaseMissing('cache_locks', ['key' => 'expired-lock']);
        $this->assertDatabaseHas('cache_locks', ['key' => 'active-lock']);
    }
}
