<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureDesktopDevice
{
    public function handle(Request $request, Closure $next): Response
    {
        $userAgent = $request->userAgent() ?? '';
        $isMobileClient = $request->header('Sec-CH-UA-Mobile') === '?1'
            || preg_match('/android.*mobile|iphone|ipod|windows phone|iemobile|blackberry|bb\d+|opera mini|mobile/i', $userAgent);

        abort_if($isMobileClient, 403, 'Halaman admin hanya dapat diakses melalui komputer.');

        return $next($request);
    }
}
