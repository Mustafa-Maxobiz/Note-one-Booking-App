<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Session\TokenMismatchException;
use Symfony\Component\HttpFoundation\Response;

class HandleTokenMismatch
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            return $next($request);
        } catch (TokenMismatchException $e) {
            // Handle 419 Page Expired error
            if ($request->expectsJson()) {
                return response()->json([
                    'error' => 'Session expired',
                    'message' => 'Your session has expired. Please refresh the page and try again.',
                    'code' => 419,
                    'redirect' => route('login')
                ], 419);
            }
            
            // For web requests, redirect to login with a message
            return redirect()->route('login')
                ->with('error', 'Your session has expired. Please log in again.')
                ->with('expired', true);
        }
    }
}
