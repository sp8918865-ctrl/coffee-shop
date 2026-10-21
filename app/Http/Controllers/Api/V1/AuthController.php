<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request): JsonResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
            'device_name' => ['nullable', 'string', 'max:100'],
        ]);

        $user = User::where('email', $credentials['email'])->first();
        if (! $user || ! Hash::check($credentials['password'], $user->password_hash)
            || ! in_array($user->role, ['admin', 'owner'], true)) {
            return response()->json(['message' => 'Email, kata sandi, atau hak akses tidak valid.'], 422);
        }

        $expiresAt = now()->addDays(30);
        $token = $user->createToken($credentials['device_name'] ?? 'admin-api', ['admin'], $expiresAt);

        return response()->json([
            'data' => [
                'token_type' => 'Bearer',
                'token' => $token->plainTextToken,
                'expires_at' => $expiresAt->toISOString(),
                'user' => $this->userData($user),
            ],
        ])->header('Cache-Control', 'no-store, private');
    }

    public function me(Request $request): JsonResponse
    {
        return response()->json(['data' => $this->userData($request->user())]);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()?->delete();

        return response()->json(['message' => 'Token admin berhasil dicabut.']);
    }

    private function userData(User $user): array
    {
        return [
            'id' => $user->user_id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role,
        ];
    }
}
