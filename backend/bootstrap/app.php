<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Prevent redirect to login route for API requests
        $middleware->redirectGuestsTo(function ($request) {
            // For API requests, return null (no redirect)
            if ($request->is('api/*') || $request->expectsJson()) {
                return null;
            }
            // For web requests, redirect to login (if you have a login route)
            return route('login');
        });
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Handle authentication exceptions for API requests
        $exceptions->render(function (\Illuminate\Auth\AuthenticationException $e, $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthenticated. Please login to access this resource.',
                    'data' => null,
                ], 401);
            }
        });

        // Handle JWT Token Expired Exception
        $exceptions->render(function (\Tymon\JWTAuth\Exceptions\TokenExpiredException $e, $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Access token expired. Please refresh your token or login again.',
                    'data' => null,
                ], 401);
            }
        });

        // Handle JWT Token Invalid Exception
        $exceptions->render(function (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e, $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Access token is invalid. Please login again.',
                    'data' => null,
                ], 401);
            }
        });

        // Handle JWT Token Not Provided Exception
        $exceptions->render(function (\Tymon\JWTAuth\Exceptions\JWTException $e, $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Access token not provided. Please include token in request.',
                    'data' => null,
                ], 401);
            }
        });

        // Handle Validation Exception
        $exceptions->render(function (\Illuminate\Validation\ValidationException $e, $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'data' => $e->errors(),
                ], 422);
            }
        });

        // Handle Not Found Exception (404)
        $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\NotFoundHttpException $e, $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Resource not found',
                    'data' => null,
                ], 404);
            }
        });

        // Handle Method Not Allowed Exception (405)
        $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException $e, $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Method not allowed',
                    'data' => null,
                ], 405);
            }
        });

        // Handle Throttle Exception (429 - Too Many Requests)
        $exceptions->render(function (\Illuminate\Http\Exceptions\ThrottleRequestsException $e, $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Too many requests. Please slow down.',
                    'data' => null,
                ], 429);
            }
        });

        // Handle General Exceptions
        $exceptions->render(function (\Throwable $e, $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                // Don't expose detailed error messages in production
                $message = config('app.debug') 
                    ? $e->getMessage() 
                    : 'An error occurred while processing your request';

                return response()->json([
                    'success' => false,
                    'message' => $message,
                    'data' => null,
                ], 500);
            }
        });
    })->create();
