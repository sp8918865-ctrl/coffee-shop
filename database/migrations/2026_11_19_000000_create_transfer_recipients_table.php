<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transfer_recipients', function (Blueprint $table) {
            $table->id();
            $table->string('bank', 100);
            $table->string('account_name', 100);
            $table->string('account_number', 50);
            $table->timestamps();
        });

        $settings = DB::table('app_settings')
            ->whereIn('key', [
                'transfer_recipient_bank',
                'transfer_recipient_account_name',
                'transfer_recipient_account_number',
            ])
            ->pluck('value', 'key');

        if ($settings->get('transfer_recipient_bank')
            && $settings->get('transfer_recipient_account_name')
            && $settings->get('transfer_recipient_account_number')) {
            DB::table('transfer_recipients')->insert([
                'bank' => $settings->get('transfer_recipient_bank'),
                'account_name' => $settings->get('transfer_recipient_account_name'),
                'account_number' => $settings->get('transfer_recipient_account_number'),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('transfer_recipients');
    }
};
