<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckAdminAccess
{
    /**
     * Handle an incoming request.
     *
     * Middleware này kiểm tra xem user có quyền truy cập admin không.
     * Nếu user thường cố tình truy cập vào admin routes, trả về 404
     * thay vì thông báo lỗi để tăng tính bảo mật.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Kiểm tra user đã đăng nhập chưa
        if (!Auth::check()) {
            abort(404);
        }

        // Kiểm tra user có phải admin không
        $user = Auth::user();

        if (!$user->isAdmin()) {
            // Trả về 404 thay vì thông báo lỗi để tăng bảo mật
            // User thường không cần biết rằng route admin tồn tại
            abort(404);
        }

        return $next($request);
    }
}
