<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\Models\User;

class AdminAuthController extends Controller
{
    /**
     * Show the admin login form
     */
    public function showLoginForm()
    {
        return view('admin.auth.login');
    }

    /**
     * Handle admin login request
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $credentials = $request->only('email', 'password');

        // First check if user exists and has admin role
        $user = User::where('email', $credentials['email'])->first();

        if (!$user || !$user->hasRole('admin')) {
            throw ValidationException::withMessages([
                'email' => ['These credentials do not match our admin records.'],
            ]);
        }

        // Attempt to login using admin guard
        if (Auth::guard('admin')->attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();

            return redirect()->intended(route('admin.dashboard'));
        }

        throw ValidationException::withMessages([
            'email' => ['These credentials do not match our admin records.'],
        ]);
    }

    /**
     * Handle admin logout
     */
    public function logout(Request $request)
    {
        // Logout from admin guard only
        Auth::guard('admin')->logout();

        // Don't invalidate session - just regenerate token for security
        // This allows web user to remain logged in if they are
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }
}
