<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dashboard_checklist_items', function (Blueprint $table) {
            $table->id();
            $table->string('label')->unique();
            $table->boolean('is_completed')->default(false);
            $table->timestamps();
        });

        $now = now();
        DB::table('dashboard_checklist_items')->insert(array_map(
            fn (string $label) => [
                'label' => $label,
                'is_completed' => false,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'MVP Core Features Development',
                'Implementasi Core Business Flow',
                'Pembuatan Core API',
                'Frontend/Mobile Integration',
                'Input & Data Validation',
                'Database Integration & Queries',
            ]
        ));
    }

    public function down(): void
    {
        Schema::dropIfExists('dashboard_checklist_items');
    }
};
