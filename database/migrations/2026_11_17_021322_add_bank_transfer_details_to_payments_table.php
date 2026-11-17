<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->string('nomor_rekening')->nullable();
            $table->string('nama_bank')->nullable();
            $table->string('nama_pemilik_rekening')->nullable();
            $table->string('bukti_pembayaran')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn([
                'nomor_rekening',
                'nama_bank',
                'nama_pemilik_rekening',
                'bukti_pembayaran',
            ]);
        });
    }
};
