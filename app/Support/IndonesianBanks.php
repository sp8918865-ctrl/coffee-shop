<?php

namespace App\Support;

class IndonesianBanks
{
    public const OPTIONS = [
        'BCA',
        'BRI',
        'BNI',
        'Bank Mandiri',
        'BSI',
        'BTN',
        'CIMB Niaga',
        'Danamon',
        'PermataBank',
        'OCBC Indonesia',
        'Maybank Indonesia',
        'Bank Mega',
        'Bank Jago',
        'SeaBank',
        'SMBC Indonesia (Jenius)',
    ];

    public const OTHER = 'other';

    public static function validationOptions(): array
    {
        return [...self::OPTIONS, self::OTHER];
    }
}
