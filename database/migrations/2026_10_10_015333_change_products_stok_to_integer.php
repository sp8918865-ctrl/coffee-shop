<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('products') || ! Schema::hasColumn('products', 'stok')) {
            return;
        }

        DB::table('products')
            ->select('product_id', 'stok')
            ->whereNotNull('stok')
            ->orderBy('product_id')
            ->chunk(500, function ($products): void {
                foreach ($products as $product) {
                    if (! ctype_digit((string) $product->stok)) {
                        throw new RuntimeException(
                            "Cannot convert products.stok to integer: product {$product->product_id} has a non-integer value."
                        );
                    }
                }
            });

        foreach (Schema::getIndexes('products') as $index) {
            if ($index['unique'] && $index['columns'] === ['stok']) {
                Schema::table('products', function (Blueprint $table) use ($index): void {
                    $table->dropUnique($index['name']);
                });
            }
        }

        Schema::table('products', function (Blueprint $table): void {
            $table->integer('stok')->nullable()->change();
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('products') || ! Schema::hasColumn('products', 'stok')) {
            return;
        }

        Schema::table('products', function (Blueprint $table): void {
            $table->string('stok')->nullable()->change();
        });
    }
};
