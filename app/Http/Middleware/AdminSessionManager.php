<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class AdminSessionManager
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // For admin routes, configure session to use admin-specific settings
        if ($request->is('admin') || $request->is('admin/*')) {
            // Use admin-specific session cookie
            Config::set('session.cookie', config('session.admin_cookie'));

            // Ensure session is started with admin configuration
            if (!Session::isStarted()) {
                Session::start();
            }
        }

        return $next($request);
    }
}
