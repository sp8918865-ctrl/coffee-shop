<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserCanAccessAdminPanel
{
    public function handle(Request $request, Closure $next): Response
    {
        abort_unless(
            in_array($request->user()?->role, ['admin', 'owner', 'barista'], true),
            403,
            'Halaman tersebut hanya dapat diakses admin, owner, atau barista.'
        );

        return $next($request);
    }
}
