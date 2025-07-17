<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class SessionController extends Controller
{
    /**
     * Clear all sessions and logout all guards
     */
    public function clearAll(Request $request)
    {
        // Logout from all guards
        Auth::guard('web')->logout();
        Auth::guard('admin')->logout();

        // Clear session data
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Clear all session cookies
        $response = redirect('/');

        // Clear both regular and admin session cookies
        $response->withCookie(cookie()->forget(config('session.cookie')));
        $response->withCookie(cookie()->forget(config('session.admin_cookie')));

        return $response->with('message', 'All sessions cleared successfully.');
    }
}
