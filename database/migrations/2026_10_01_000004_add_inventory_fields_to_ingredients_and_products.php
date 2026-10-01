<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ingredients', function (Blueprint $table) {
            $table->string('name')->after('id');
            $table->decimal('quantity', 12, 3)->default(0);
            $table->string('unit')->default('unit');
            $table->decimal('minimum_quantity', 12, 3)->default(0);
        });

        Schema::table('products', function (Blueprint $table) {
            $table->boolean('is_available')->default(true);
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('is_available');
        });

        Schema::table('ingredients', function (Blueprint $table) {
            $table->dropColumn(['name', 'quantity', 'unit', 'minimum_quantity']);
        });
    }
};
