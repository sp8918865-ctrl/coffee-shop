<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('cashier');
            $table->string('phone')->nullable();
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->text('description')->nullable();
        });

        Schema::table('products', function (Blueprint $table) {
            $table->text('description')->nullable();
            $table->string('image_url')->nullable();
        });

        Schema::create('tables', function (Blueprint $table) {
            $table->id('table_id');
            $table->string('table_number')->unique();
            $table->string('qr_token')->nullable()->unique();
            $table->unsignedInteger('capacity')->default(1);
            $table->string('status')->default('available');
        });

        Schema::create('payments', function (Blueprint $table) {
            $table->id('payment_id');
            $table->foreignId('order_id')->index()->constrained('orders')->cascadeOnDelete();
            $table->foreignId('processed_by_user_id')->nullable()->index()->constrained('users')->nullOnDelete();
            $table->string('receipt_number')->nullable()->unique();
            $table->string('payment_method')->default('cash');
            $table->string('payment_gateway_ref')->nullable();
            $table->decimal('amount', 12, 2)->default(0);
            $table->decimal('cash_received', 12, 2)->nullable();
            $table->decimal('cash_change', 12, 2)->nullable();
            $table->string('payment_status')->default('pending');
            $table->timestamp('paid_at')->nullable();
        });

        Schema::create('variants', function (Blueprint $table) {
            $table->id('variant_id');
            $table->foreignId('product_id')->index()->constrained('products')->cascadeOnDelete();
            $table->string('variant_group');
            $table->string('variant_name');
            $table->decimal('additional_price', 10, 2)->default(0);
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->string('order_status')->default('pending')->index();
            $table->foreignId('table_id')->nullable()->index()->constrained('tables', 'table_id')->nullOnDelete();
            $table->foreignId('user_id')->nullable()->index()->constrained('users')->nullOnDelete();
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->text('notes')->nullable();
        });

        Schema::create('order_item_variants', function (Blueprint $table) {
            $table->id('order_item_variant_id');
            $table->foreignId('order_item_id')->index()->constrained('order_items')->cascadeOnDelete();
            $table->foreignId('variant_id')->index()->constrained('variants', 'variant_id')->cascadeOnDelete();
        });

        DB::table('orders')->where('subtotal', 0)->update([
            'subtotal' => DB::raw('total_amount'),
        ]);

        foreach ([
            'new' => 'pending',
            'brewing' => 'in_progress',
            'ready' => 'ready',
            'completed' => 'completed',
            'cancelled' => 'cancelled',
        ] as $legacyStatus => $orderStatus) {
            DB::table('orders')->where('status', $legacyStatus)->update([
                'order_status' => $orderStatus,
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('order_item_variants');

        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn('notes');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['table_id']);
            $table->dropForeign(['user_id']);
            $table->dropIndex(['table_id']);
            $table->dropIndex(['user_id']);
            $table->dropIndex(['order_status']);
            $table->dropColumn(['subtotal', 'order_status', 'table_id', 'user_id']);
        });

        Schema::dropIfExists('variants');
        Schema::dropIfExists('payments');
        Schema::dropIfExists('tables');

        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['description', 'image_url']);
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn('description');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'phone']);
        });
    }
};
