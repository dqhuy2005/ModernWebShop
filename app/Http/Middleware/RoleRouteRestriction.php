<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleRouteRestriction
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            return redirect()->route('login')
                ->with('error', 'Vui lòng đăng nhập để tiếp tục.');
        }

        $user = Auth::user();

        if (!$user->role_id || !$user->role) {
            Auth::logout();
            return redirect()->route('login')
                ->with('error', 'Tài khoản của bạn chưa được phân quyền. Vui lòng liên hệ quản trị viên.');
        }

        if (!$user->status) {
            Auth::logout();
            return redirect()->route('login')
                ->with('error', 'Tài khoản của bạn đã bị vô hiệu hóa. Vui lòng liên hệ quản trị viên.');
        }

        if ($request->is('admin/*') && !$user->isAdmin()) {
            Auth::logout();
            return redirect()->route('login')
                ->with('error', 'Bạn không có quyền truy cập khu vực quản trị. Vui lòng đăng nhập bằng tài khoản ');
        }

        return $next($request);
    }
}
