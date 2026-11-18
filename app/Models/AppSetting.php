<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class AppSetting extends Model
{
    protected $fillable = [
        'key',
        'value',
    ];

    public static function isPublicQrEnabled(): bool
    {
        return static::where('key', 'public_qr_enabled')->value('value') !== '0';
    }

    public static function setPublicQrEnabled(bool $enabled): void
    {
        static::updateOrCreate(
            ['key' => 'public_qr_enabled'],
            ['value' => $enabled ? '1' : '0']
        );
    }

    public static function bankTransferDetails(): array
    {
        $settings = static::whereIn('key', [
            'transfer_bank_name',
            'transfer_account_number',
            'transfer_account_holder',
        ])->pluck('value', 'key');

        $details = [
            'bank_name' => $settings->get('transfer_bank_name', ''),
            'account_number' => $settings->get('transfer_account_number', ''),
            'account_holder' => $settings->get('transfer_account_holder', ''),
        ];

        return $details + [
            'configured' => $details['bank_name'] !== ''
                && $details['account_number'] !== ''
                && $details['account_holder'] !== '',
        ];
    }

    public static function setBankTransferDetails(array $details): void
    {
        DB::transaction(function () use ($details): void {
            foreach ([
                'bank_name' => 'transfer_bank_name',
                'account_number' => 'transfer_account_number',
                'account_holder' => 'transfer_account_holder',
            ] as $field => $key) {
                static::updateOrCreate(
                    ['key' => $key],
                    ['value' => $details[$field]]
                );
            }
        });
    }
}
