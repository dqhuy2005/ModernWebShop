<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\AppBaseController;
use App\Services\AuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log as logger;
use Illuminate\Http\JsonResponse;

class AuthController extends AppBaseController
{
    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'fullname' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'nullable|string|max:20',
            'password' => 'required|string|min:6',
            'language' => 'nullable|string|max:10',
            'birthday' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return $this->sendValidationErrorWithDetails($validator->errors()->toArray());
        }

        try {
            $this->authService->register($request->all());

            return $this->sendResponse(null, 'User registered successfully');
        } catch (\Exception $e) {
            return $this->sendError('Registration failed: ' . $e->getMessage(), 500);
        }
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return $this->sendValidationErrorWithDetails($validator->errors()->toArray());
        }

        try {
            $result = $this->authService->attemptLogin(
                $request->email,
                $request->password,
                $request->ip(),
                $request->userAgent()
            );

            if (!$result) {
                return $this->sendError('Invalid credentials', 401);
            }

            return $this->sendResponse($result, 'Login successful');
        } catch (\Exception $e) {
            return $this->sendError('Login failed: ' . $e->getMessage(), 500);
        }
    }

    public function refresh(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'refresh_token' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->sendValidationErrorWithDetails($validator->errors()->toArray());
        }

        try {
            $result = $this->authService->refreshAccessToken(
                $request->refresh_token,
                $request->ip(),
                $request->userAgent()
            );

            if (!$result) {
                return $this->sendError('Invalid or expired refresh token', 401);
            }

            return $this->sendResponse($result, 'Token refreshed successfully');
        } catch (\Exception $e) {
            return $this->sendError('Token refresh failed: ' . $e->getMessage(), 500);
        }
    }

    public function logout(Request $request)
    {
        try {
            $refreshToken = $request->input('refresh_token');
            $result = $this->authService->logout($refreshToken);

            if (!$result) {
                return $this->sendError('Logout failed: Refresh token not found or already revoked', 400);
            }

            return $this->sendResponse(null, 'Logged out successfully');
        } catch (\Exception $e) {
            return $this->sendError('Logout failed: ' . $e->getMessage(), 500);
        }
    }

    public function me(Request $request)
    {
        try {
            if (!auth('api')->check()) {
                return $this->sendError('Access token expired or invalid. Please login again.', 403);
            }

            $user = auth('api')->user();

            if (!$user) {
                return $this->sendError('User not found', 404);
            }

            if ($user->status === 0) {
                return $this->sendError('User account is inactive', 403);
            }

            return $this->sendResponse($user, 'User profile retrieved successfully');
        } catch (\Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
            return $this->sendError('Access token expired or invalid. Please login again.', 403);
        } catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
            return $this->sendError('Access token expired or invalid. Please login again.', 403);
        } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {
            return $this->sendError('Access token expired or invalid. Please login again.', 403);
        } catch (\Exception $e) {
            return $this->sendError('Failed to get user profile: ' . $e->getMessage(), 500);
        }
    }

    public function revokeAllTokens(Request $request)
    {
        try {
            $user = $this->authService->getUserFromToken();

            if (!$user) {
                return $this->sendError('User not found', 404);
            }

            $this->authService->revokeAllRefreshTokens($user->id);

            return $this->sendResponse(null, 'All refresh tokens revoked successfully');
        } catch (\Exception $e) {
            return $this->sendError('Failed to revoke tokens: ' . $e->getMessage(), 500);
        }
    }
}
