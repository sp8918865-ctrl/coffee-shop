<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
}
