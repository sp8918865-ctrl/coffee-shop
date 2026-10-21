<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class PruneExpiredStorage extends Command
{
    protected $signature = 'app:prune-expired-storage {--dry-run : Report expired rows without deleting them}';

    protected $description = 'Remove expired database cache entries, locks, and sessions';

    public function handle(): int
    {
        $dryRun = $this->option('dry-run');
        $deleted = [];

        if (config('session.driver') === 'database') {
            $table = config('session.table', 'sessions');
            $connection = config('session.connection');

            if (! Schema::connection($connection)->hasTable($table)) {
                $this->error("Session table [{$table}] does not exist. Run php artisan migrate first.");

                return self::FAILURE;
            }

            $cutoff = now()->subMinutes(config('session.lifetime'))->timestamp;
            $deleted['sessions'] = $this->prune($table, 'last_activity', $cutoff, $dryRun, $connection);
        }

        $cacheStoreName = config('cache.default');
        $cacheStore = config("cache.stores.{$cacheStoreName}", []);
        if (($cacheStore['driver'] ?? null) === 'database') {
            $table = $cacheStore['table'] ?? 'cache';
            $connection = $cacheStore['connection'] ?? null;

            if (! Schema::connection($connection)->hasTable($table)) {
                $this->error("Cache table [{$table}] does not exist. Run php artisan migrate first.");

                return self::FAILURE;
            }

            $deleted['cache'] = $this->prune($table, 'expiration', now()->timestamp, $dryRun, $connection);

            $lockTable = $cacheStore['lock_table'] ?? $table.'_locks';
            $lockConnection = $cacheStore['lock_connection'] ?? $connection;
            if (! Schema::connection($lockConnection)->hasTable($lockTable)) {
                $this->error("Cache lock table [{$lockTable}] does not exist. Run php artisan migrate first.");

                return self::FAILURE;
            }

            $deleted['cache locks'] = $this->prune(
                $lockTable,
                'expiration',
                now()->timestamp,
                $dryRun,
                $lockConnection
            );
        }

        foreach ($deleted as $type => $count) {
            $action = $dryRun ? 'expired' : 'deleted';
            $this->line(ucfirst($type).": {$count} rows {$action}.");
        }

        if ($dryRun) {
            $this->info('Dry run only; no rows were deleted.');
        }

        return self::SUCCESS;
    }

    private function prune(string $table, string $column, int $cutoff, bool $dryRun, ?string $connection): int
    {
        $query = DB::connection($connection)->table($table)->where($column, '<=', $cutoff);
        $count = $query->count();

        if (! $dryRun && $count > 0) {
            $query->delete();
        }

        return $count;
    }
}
