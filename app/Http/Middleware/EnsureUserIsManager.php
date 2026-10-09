<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsManager
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! in_array($request->user()?->role, ['admin', 'owner'], true)) {
            return redirect()->route('catalog.index')
                ->with('error', 'Halaman tersebut hanya dapat diakses admin atau owner.');
        }

        return $next($request);
    }
}
