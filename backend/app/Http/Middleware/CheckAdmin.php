<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth('api')->check()) {
            if ($request->is('api/*') || $request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthenticated. Please login to access this resource.',
                    'data' => null,
                ], 401);
            }

            return redirect()->route('cms.login')->with('error', 'Vui lòng đăng nhập để tiếp tục.');
        }

        $user = auth('api')->user();

        if (!$user->role) {
            if ($request->is('api/*') || $request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'User does not have any role assigned.',
                    'data' => null,
                ], 403);
            }

            return redirect()->route('home')->with('error', 'Tài khoản của bạn chưa được phân quyền.');
        }

        if (!$user->isAdmin()) {
            if ($request->is('api/*') || $request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Access denied. Admin role required.',
                    'data' => null,
                ], 403);
            }

            return redirect()->route('home')->with('error', 'Bạn không có quyền truy cập trang này.');
        }

        return $next($request);
    }
}
