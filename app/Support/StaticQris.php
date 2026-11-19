<?php

namespace App\Support;

use Illuminate\Support\Facades\Storage;

class StaticQris
{
    public static function imageUrl(): ?string
    {
        $path = config('payments.qris.static_image');
        if (! is_string($path) || trim($path) === '') {
            return null;
        }

        $disk = Storage::disk('public');

        return $disk->exists($path) ? $disk->url($path) : null;
    }
}
