<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckAdminAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            abort(404);
        }

        $user = Auth::user();

        if (!$user->isAdmin()) {
            abort(404);
        }

        return $next($request);
    }
}
