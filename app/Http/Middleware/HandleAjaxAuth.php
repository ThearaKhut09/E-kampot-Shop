<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class HandleAjaxAuth
{
    /**
     * Handle an incoming request.
     * This middleware ensures AJAX requests get JSON responses for authentication failures.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Let the request proceed first
        $response = $next($request);

        // Check if this is a redirect response (likely from auth middleware) and the request expects JSON
        if ($response instanceof \Illuminate\Http\RedirectResponse &&
            ($request->expectsJson() || $request->ajax())) {

            // If redirecting to login, return JSON instead
            if (str_contains($response->getTargetUrl(), 'login')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please log in to continue.',
                    'redirect_url' => route('login')
                ], 401);
            }
        }

        return $response;
    }
}
