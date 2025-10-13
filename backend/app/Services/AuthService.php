<?php

namespace App\Services;

use App\Models\User;
use App\Models\RefreshToken;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthService
{
    const REFRESH_TOKEN_EXPIRY_DAYS = 30;

    public function attemptLogin($email, $password, $ipAddress = null, $userAgent = null)
    {
        $credentials = ['email' => $email, 'password' => $password];

        if (!$token = auth('api')->attempt($credentials)) {
            return null;
        }

        $user = auth('api')->user();

        $refreshToken = $this->generateRefreshToken($user, $ipAddress, $userAgent);

        return [
            'access_token' => $token,
            'refresh_token' => $refreshToken->token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60
        ];
    }

    public function generateRefreshToken(User $user, $ipAddress = null, $userAgent = null)
    {
        $user->refreshTokens()->valid()->update(['is_revoked' => true]);

        $refreshToken = RefreshToken::create([
            'user_id' => $user->id,
            'token' => Str::random(128),
            'expires_at' => Carbon::now()->addDays(self::REFRESH_TOKEN_EXPIRY_DAYS),
            'ip_address' => $ipAddress,
            'user_agent' => $userAgent,
        ]);

        return $refreshToken;
    }

    public function refreshAccessToken($refreshTokenString, $ipAddress = null, $userAgent = null)
    {
        $refreshToken = RefreshToken::where('token', $refreshTokenString)
            ->with('user')
            ->first();

        if (!$refreshToken || !$refreshToken->isValid()) {
            return null;
        }

        $user = $refreshToken->user;

        $newAccessToken = auth('api')->login($user);

        return [
            'access_token' => $newAccessToken,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60
        ];
    }

    public function logout($refreshTokenString = null)
    {
        auth('api')->logout();

        if ($refreshTokenString) {
            $refreshToken = RefreshToken::where('token', $refreshTokenString)->first();
            if ($refreshToken) {
                $refreshToken->revoke();
            }
        }

        return true;
    }

    public function revokeAllRefreshTokens($userId)
    {
        RefreshToken::where('user_id', $userId)
            ->where('is_revoked', false)
            ->update(['is_revoked' => true]);

        return true;
    }

    public function cleanupExpiredTokens()
    {
        return RefreshToken::expired()->delete();
    }

    public function getUserFromToken()
    {
        try {
            return auth('api')->user();
        } catch (\Exception $e) {
            return null;
        }
    }

    public function register(array $data)
    {
        $user = User::create([
            'fullname' => $data['fullname'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'password' => Hash::make($data['password']),
            'status' => true,
            'language' => $data['language'] ?? 'en',
            'birthday' => $data['birthday'] ?? null,
        ]);

        return $user;
    }
}
