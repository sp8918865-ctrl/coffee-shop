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

    public static function visualOptions(): array
    {
        $logos = [
            'BCA' => ['mark' => 'BCA', 'background' => '#0060a8'],
            'BRI' => ['mark' => 'BRI', 'background' => '#00529c'],
            'BNI' => ['mark' => 'BNI', 'background' => '#f36f21'],
            'Bank Mandiri' => ['mark' => 'BM', 'background' => '#003d79'],
            'BSI' => ['mark' => 'BSI', 'background' => '#00a39b'],
            'BTN' => ['mark' => 'BTN', 'background' => '#005baa'],
            'CIMB Niaga' => ['mark' => 'CIMB', 'background' => '#e31e24'],
            'Danamon' => ['mark' => 'DAN', 'background' => '#ed1c24'],
            'PermataBank' => ['mark' => 'PB', 'background' => '#d71920'],
            'OCBC Indonesia' => ['mark' => 'OCBC', 'background' => '#cc0000'],
            'Maybank Indonesia' => ['mark' => 'MAY', 'background' => '#f5c400', 'foreground' => '#172033'],
            'Bank Mega' => ['mark' => 'MEGA', 'background' => '#00529b'],
            'Bank Jago' => ['mark' => 'JAGO', 'background' => '#7138c8'],
            'SeaBank' => ['mark' => 'SEA', 'background' => '#f36f21'],
            'SMBC Indonesia (Jenius)' => ['mark' => 'JENIUS', 'background' => '#e60028'],
        ];

        return array_map(
            fn (string $name): array => [
                'name' => $name,
                'mark' => $logos[$name]['mark'],
                'background' => $logos[$name]['background'],
                'foreground' => $logos[$name]['foreground'] ?? '#ffffff',
            ],
            self::OPTIONS
        );
    }
}
