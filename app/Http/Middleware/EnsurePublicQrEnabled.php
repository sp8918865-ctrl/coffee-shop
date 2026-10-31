<?php

namespace App\Http\Middleware;

use App\Models\AppSetting;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsurePublicQrEnabled
{
    public function handle(Request $request, Closure $next): Response
    {
        abort_unless(AppSetting::isPublicQrEnabled(), 403, 'QR menu publik sedang dinonaktifkan.');

        return $next($request);
    }
}
