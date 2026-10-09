<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('tables')
            ->where('status', 'unavailable')
            ->update(['status' => 'occupied']);
    }

    public function down(): void
    {
        DB::table('tables')
            ->where('status', 'occupied')
            ->update(['status' => 'unavailable']);
    }
};
