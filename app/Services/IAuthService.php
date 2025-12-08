<?php

namespace App\Services;

use App\Models\User;
use App\Models\RefreshToken;

interface IAuthService
{
    public function attemptLogin($email, $password, $ipAddress = null, $userAgent = null): ?array;
    
    public function generateRefreshToken(User $user, $ipAddress = null, $userAgent = null): RefreshToken;
    
    public function refreshAccessToken($refreshTokenString, $ipAddress = null, $userAgent = null): ?array;
    
    public function logout($refreshTokenString = null): bool;
    
    public function revokeAllRefreshTokens($userId): bool;
    
    public function cleanupExpiredTokens(): int;
    
    public function getUserFromToken(): ?User;
    
    public function register(array $data): User;
}
