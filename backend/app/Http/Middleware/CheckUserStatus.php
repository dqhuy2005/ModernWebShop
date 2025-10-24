<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckUserStatus
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $user = Auth::user();

            $user->refresh();

            if ($user->trashed()) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Tài khoản của bạn đã bị xóa. Vui lòng liên hệ với bộ phận hỗ trợ nếu bạn cho rằng đây là lỗi.'
                    ], 403);
                }

                return redirect()->route('login')
                    ->with('error', 'Tài khoản của bạn đã bị xóa. Vui lòng liên hệ với bộ phận hỗ trợ nếu bạn cho rằng đây là lỗi.');
            }

            if (!$user->status) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Tài khoản của bạn đã bị cấm. Vui lòng liên hệ với bộ phận hỗ trợ để biết thêm thông tin.'
                    ], 403);
                }

                return redirect()->route('login')
                    ->with('error', 'Tài khoản của bạn đã bị cấm. Vui lòng liên hệ với bộ phận hỗ trợ để biết thêm thông tin.');
            }
        }

        return $next($request);
    }
}
