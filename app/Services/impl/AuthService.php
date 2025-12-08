<?php

namespace App\Services\impl;

use App\Models\User;
use App\Models\Role;
use App\Models\RefreshToken;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log as logger;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Services\IAuthService;

class AuthService implements IAuthService
{
    const REFRESH_TOKEN_EXPIRY_DAYS = 30;

    public function attemptLogin($email, $password, $ipAddress = null, $userAgent = null): ?array
    {
        $credentials = ['email' => $email, 'password' => $password];

        if (!$token = auth('api')->attempt($credentials)) {
            return null;
        }

        $user = auth('api')->user()->load('role');

        $refreshToken = $this->generateRefreshToken($user, $ipAddress, $userAgent);

        return [
            'access_token' => $token,
            'refresh_token' => $refreshToken->token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60 * 24,
            'user' => [
                'id' => $user->id,
                'fullname' => $user->fullname,
                'email' => $user->email,
                'role' => $user->role ? $user->role->slug : null,
            ]
        ];
    }

    public function generateRefreshToken(User $user, $ipAddress = null, $userAgent = null): RefreshToken
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

    public function refreshAccessToken($refreshTokenString, $ipAddress = null, $userAgent = null): ?array
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

    public function logout($refreshTokenString = null): bool
    {
        try {
            if (auth('api')->check()) {
                auth('api')->logout();
            }
        } catch (\Exception $e) {
            logger::error('JWT logout skipped: ' . $e->getMessage());
        }

        if ($refreshTokenString) {
            $refreshToken = RefreshToken::where('token', $refreshTokenString)->first();

            if (!$refreshToken) {
                return false;
            }

            if ($refreshToken->is_revoked) {
                return false;
            }

            $refreshToken->update(['is_revoked' => true]);
            return true;
        }

        return false;
    }

    public function revokeAllRefreshTokens($userId): bool
    {
        RefreshToken::where('user_id', $userId)
            ->where('is_revoked', false)
            ->update(['is_revoked' => true]);

        return true;
    }

    public function cleanupExpiredTokens(): int
    {
        return RefreshToken::expired()->delete();
    }

    public function getUserFromToken(): ?User
    {
        try {
            $user = auth('api')->user();
            if ($user) {
                $user->load('role');
            }
            return $user;
        } catch (\Exception $e) {
            return null;
        }
    }

    public function register(array $data): User
    {
        $userRole = Role::where('slug', Role::USER)->first();

        $user = User::create([
            'fullname' => $data['fullname'],
            'email' => $data['email'],
            'role_id' => $userRole ? $userRole->id : null,
            'phone' => $data['phone'] ?? null,
            'password' => Hash::make($data['password']),
            'status' => true,
            'language' => $data['language'] ?? 'en',
            'birthday' => $data['birthday'] ?? null,
        ]);

        $user->load('role');

        return $user;
    }
}
