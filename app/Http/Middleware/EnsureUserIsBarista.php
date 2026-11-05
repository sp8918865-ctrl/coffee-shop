<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsBarista
{
    public function handle(Request $request, Closure $next): Response
    {
        abort_unless($request->user()?->role === 'barista', 403, 'Halaman tersebut hanya dapat diakses barista.');

        return $next($request);
    }
}
