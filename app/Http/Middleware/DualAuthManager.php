<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class DualAuthManager
{
    /**
     * Handle an incoming request.
     * This middleware ensures both web and admin guards can be active simultaneously
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Start session if not already started
        if (!Session::isStarted()) {
            Session::start();
        }

        // For admin routes, ensure admin authentication is prioritized
        if ($request->is('admin') || $request->is('admin/*')) {
            // Check if admin is authenticated and set appropriate session data
            if (Auth::guard('admin')->check()) {
                // Store admin auth info in session with different key
                session(['auth.admin_id' => Auth::guard('admin')->user()->id]);
            }
        } else {
            // For regular routes, ensure web authentication works
            if (Auth::guard('web')->check()) {
                // Store web auth info in session with different key
                session(['auth.web_id' => Auth::guard('web')->user()->id]);
            }
        }

        return $next($request);
    }
}
