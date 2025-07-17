<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
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
        // For admin routes, use separate session configuration
        if ($request->is('admin') || $request->is('admin/*')) {
            // Set separate session configuration for admin
            Config::set([
                'session.cookie' => config('session.admin_cookie'),
                'session.table' => 'admin_sessions', // Use separate table if needed
            ]);
        }

        return $next($request);
    }
}
