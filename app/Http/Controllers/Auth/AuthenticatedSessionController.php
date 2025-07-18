<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        session()->regenerate();

        // Check user role and redirect accordingly
        $user = Auth::user();

        try {
            if ($user && $user->hasRole('admin')) {
                // If user has admin role, redirect to admin dashboard
                return redirect()->intended(route('admin.dashboard', absolute: false));
            }
        } catch (\Exception $e) {
            // If role checking fails, fall back to regular dashboard
            Log::warning('Role check failed during login: ' . $e->getMessage());
        }

        return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        // Logout from web guard only
        Auth::guard('web')->logout();

        // Don't invalidate session - just regenerate token for security
        // This allows admin to remain logged in if they are
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
