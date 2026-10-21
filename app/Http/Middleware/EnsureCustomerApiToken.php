<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class EnsureCustomerApiToken
{
    public function handle(Request $request, Closure $next): Response
    {
        $plainTextToken = $request->bearerToken();
        if (! is_string($plainTextToken) || $plainTextToken === '') {
            return response()->json(['message' => 'Token QR pelanggan diperlukan.'], 401);
        }

        $token = DB::table('customer_api_tokens')
            ->where('token_hash', hash('sha256', $plainTextToken))
            ->where('expires_at', '>', now())
            ->first();

        if (! $token) {
            return response()->json(['message' => 'Token QR tidak valid atau sudah kedaluwarsa.'], 401);
        }

        $request->attributes->set('customer_api_token_id', $token->id);

        DB::table('customer_api_tokens')
            ->where('id', $token->id)
            ->update(['last_used_at' => now()]);

        return $next($request);
    }
}
