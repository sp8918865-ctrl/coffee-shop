<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Throwable;

class HealthCheckController extends Controller
{
    public function check(): JsonResponse
    {
        try {
            $startTime = microtime(true);
            
            // Ping koneksi PDO
            $pdo = DB::connection()->getPdo();
            $latency = round((microtime(true) - $startTime) * 1000, 2);

            // Ambil versi PostgreSQL
            $versionResult = DB::select('SELECT version()');
            $version = $versionResult[0]->version ?? 'Unknown';

            return response()->json([
                'status' => 'ok',
                'message' => 'Terhubung ke database Supabase dengan sukses.',
                'database' => [
                    'driver' => config('database.default'),
                    'host' => config('database.connections.pgsql.host'),
                    'port' => config('database.connections.pgsql.port'),
                    'database_name' => DB::connection()->getDatabaseName(),
                    'latency_ms' => $latency,
                    'version' => $version,
                ],
                'timestamp' => now()->toIso8601String(),
            ], 200);

        } catch (Throwable $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal terhubung ke database Supabase.',
                'error_detail' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
                'timestamp' => now()->toIso8601String(),
            ], 500);
        }
    }
}