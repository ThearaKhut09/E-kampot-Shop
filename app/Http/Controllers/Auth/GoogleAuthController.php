<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class GoogleAuthController extends Controller
{
    /**
     * Redirect user to Google consent screen.
     */
    public function redirect()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Handle Google callback and authenticate user.
     */
    public function callback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (\Throwable $e) {
            $errorMessage = $e->getMessage();
            $errorCode = $e->getCode();
            Log::warning("Google OAuth callback failed: [{$errorCode}] {$errorMessage}");
            Log::debug('Full exception:', ['exception' => (string)$e]);

            return redirect()->route('login')->withErrors([
                'email' => 'Unable to sign in with Google. Error: ' . $errorMessage,
            ]);
        }

        if (!$googleUser->getEmail()) {
            return redirect()->route('login')->withErrors([
                'email' => 'Google account does not provide an email address.',
            ]);
        }

        $fullName = trim((string) ($googleUser->getName() ?: $googleUser->getNickname() ?: 'Google User'));
        [$firstName, $lastName] = $this->splitName($fullName);

        $user = User::where('google_id', $googleUser->getId())
            ->orWhere('email', $googleUser->getEmail())
            ->first();

        if ($user) {
            $isGoogleLinkedUser = $user->google_id === $googleUser->getId() || $user->google_id === null;

            $user->fill([
                'name' => ($user->name && !$isGoogleLinkedUser) ? $user->name : $fullName,
                'first_name' => ($user->first_name && !$isGoogleLinkedUser) ? $user->first_name : $firstName,
                'last_name' => ($user->last_name && !$isGoogleLinkedUser) ? $user->last_name : $lastName,
                'google_id' => $googleUser->getId(),
                'avatar' => $googleUser->getAvatar() ?: $user->avatar,
                'email_verified_at' => $user->email_verified_at ?: now(),
                'last_login_at' => now(),
            ]);

            $user->save();
        } else {
            $user = User::create([
                'name' => $fullName,
                'first_name' => $firstName,
                'last_name' => $lastName,
                'email' => $googleUser->getEmail(),
                'google_id' => $googleUser->getId(),
                'avatar' => $googleUser->getAvatar(),
                'email_verified_at' => now(),
                'password' => Hash::make(Str::random(64)),
                'is_active' => true,
                'dark_mode' => false,
                'last_login_at' => now(),
            ]);

            // Default newly created social users to customer role.
            try {
                \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'customer']);
                $user->assignRole('customer');
            } catch (\Throwable $e) {
                Log::warning('Failed to assign customer role for Google sign-in: '.$e->getMessage());
            }
        }

        Auth::guard('web')->login($user, true);

        // Preserve locale before session regeneration
        $locale = session('locale');

        session()->regenerate();

        // Restore locale after session regeneration
        if ($locale) {
            session(['locale' => $locale]);
        }

        if (method_exists($user, 'hasRole') && $user->hasRole('admin')) {
            return redirect()->intended(route('admin.dashboard', absolute: false));
        }

        return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Split full name into first/last name.
     *
     * @return array{0:string|null,1:string|null}
     */
    private function splitName(string $fullName): array
    {
        if ($fullName === '') {
            return [null, null];
        }

        $parts = preg_split('/\s+/', $fullName, 2);

        return [
            $parts[0] ?? null,
            $parts[1] ?? null,
        ];
    }
}
