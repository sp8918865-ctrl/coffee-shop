<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('products') || ! Schema::hasColumn('products', 'sku') || Schema::hasColumn('products', 'stok')) {
            return;
        }

        Schema::table('products', function (Blueprint $table) {
            $table->renameColumn('sku', 'stok');
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('products') || ! Schema::hasColumn('products', 'stok') || Schema::hasColumn('products', 'sku')) {
            return;
        }

        Schema::table('products', function (Blueprint $table) {
            $table->renameColumn('stok', 'sku');
        });
    }
};
