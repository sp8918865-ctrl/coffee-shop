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
            $table->renameColumn('id', 'user_id');
            $table->renameColumn('password', 'password_hash');
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->renameColumn('id', 'category_id');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->renameColumn('id', 'product_id');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->renameColumn('id', 'order_id');
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->renameColumn('id', 'order_item_id');
        });

        $legacyStatuses = [
            'new' => 'pending',
            'brewing' => 'in_progress',
            'ready' => 'ready',
            'completed' => 'completed',
            'cancelled' => 'cancelled',
        ];

        foreach ($legacyStatuses as $legacyStatus => $orderStatus) {
            DB::table('orders')->where('status', $legacyStatus)->update([
                'order_status' => $orderStatus,
            ]);
        }

        DB::table('orders')->whereNotIn('status', array_keys($legacyStatuses))->update([
            'order_status' => DB::raw('status'),
        ]);

        DB::table('orders')->where('order_type', 'takeaway')->update([
            'order_type' => 'take_away',
        ]);

        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('status')->default('new');
        });

        $legacyStatuses = [
            'pending' => 'new',
            'in_progress' => 'brewing',
            'ready' => 'ready',
            'completed' => 'completed',
            'cancelled' => 'cancelled',
        ];

        foreach ($legacyStatuses as $orderStatus => $legacyStatus) {
            DB::table('orders')->where('order_status', $orderStatus)->update([
                'status' => $legacyStatus,
            ]);
        }

        DB::table('orders')->whereNotIn('order_status', array_keys($legacyStatuses))->update([
            'status' => DB::raw('order_status'),
        ]);

        DB::table('orders')->where('order_type', 'take_away')->update([
            'order_type' => 'takeaway',
        ]);

        Schema::table('order_items', function (Blueprint $table) {
            $table->renameColumn('order_item_id', 'id');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->renameColumn('order_id', 'id');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->renameColumn('product_id', 'id');
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->renameColumn('category_id', 'id');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->renameColumn('password_hash', 'password');
            $table->renameColumn('user_id', 'id');
        });
    }
};
