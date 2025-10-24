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
                        'message' => 'Your account has been deleted. Please contact support if you believe this is an error.'
                    ], 403);
                }

                return redirect()->route('login')
                    ->with('error', 'Your account has been deleted. Please contact support if you believe this is an error.');
            }

            if (!$user->status) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Your account has been banned. Please contact support for more information.'
                    ], 403);
                }

                return redirect()->route('login')
                    ->with('error', 'Your account has been banned. Please contact support for more information.');
            }
        }

        return $next($request);
    }
}
