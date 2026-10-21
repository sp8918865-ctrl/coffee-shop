<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureCustomerScannedQr
{
    public function handle(Request $request, Closure $next): Response
    {
        abort_unless(
            $request->session()->get('customer_qr_access') === true,
            403,
            'Silakan pindai QR menu Warung Luwak untuk membuka halaman pelanggan.'
        );

        return $next($request);
    }
}
