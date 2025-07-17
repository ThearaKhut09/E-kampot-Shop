<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class PreventSessionConflict
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // For admin routes, ensure no web guard is active
        if ($request->is('admin*')) {
            if (Auth::guard('web')->check() && !Auth::guard('admin')->check()) {
                // If web user is logged in but trying to access admin, logout web
                Auth::guard('web')->logout();
                $request->session()->flush();
                $request->session()->regenerate();
            }
        } else {
            // For regular routes, if admin is logged in, redirect to admin area
            if (Auth::guard('admin')->check() && $request->isMethod('GET')) {
                // Only redirect for GET requests to prevent CSRF issues
                if (!$request->ajax() && !$request->wantsJson()) {
                    return redirect()->route('admin.dashboard');
                }
            }
        }

        return $next($request);
    }
}
