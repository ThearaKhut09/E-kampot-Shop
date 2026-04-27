<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data="{ darkMode: localStorage.getItem('darkMode') === 'true' || (! localStorage.getItem('darkMode') && window.matchMedia('(prefers-color-scheme: dark)').matches) }" x-bind:class="{ 'dark': darkMode }">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ __('Log in') }} - E-Kampot Shop</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <!-- Alpine.js -->
        <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    </head>
    <body class="font-sans antialiased bg-gray-50 dark:bg-gray-900 transition-colors duration-200">
        <!-- Dark Mode Toggle -->
        <div class="fixed top-4 right-4 z-50">
            <button @click="darkMode = !darkMode; localStorage.setItem('darkMode', darkMode)"
                    class="p-2 rounded-lg bg-white dark:bg-gray-800 shadow-lg border border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 transition-all duration-200">
                <!-- Sun Icon -->
                <svg x-show="!darkMode" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path>
                </svg>
                <!-- Moon Icon -->
                <svg x-show="darkMode" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path>
                </svg>
            </button>
        </div>

        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 px-6">
            <!-- Logo -->
            <div class="mb-8">
                <a href="/" class="text-3xl font-bold text-primary-600 dark:text-primary-400 hover:text-primary-700 dark:hover:text-primary-300 transition-colors duration-200">
                    E-Kampot Shop
                </a>
            </div>

            <div class="w-full sm:max-w-md mb-4">
                <a href="{{ route('home') }}" class="inline-flex items-center text-sm font-medium text-gray-600 dark:text-gray-300 hover:text-primary-600 dark:hover:text-primary-400 transition-colors duration-200">
                    <span class="mr-1">←</span> Back
                </a>
            </div>

            <!-- Login Card -->
            <div class="w-full sm:max-w-md bg-white dark:bg-gray-800 shadow-xl rounded-2xl border border-gray-200 dark:border-gray-700 overflow-hidden transition-colors duration-200">
                <div class="px-8 py-8">
                    <!-- Header -->
                    <div class="text-center mb-8">
                        <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-2">Welcome Back</h1>
                        <p class="text-gray-600 dark:text-gray-400">Sign in to your account to continue</p>
                    </div>

                    <!-- Session Status -->
                    <x-auth-session-status class="mb-4" :status="session('status')" />

                    <form method="POST" action="{{ route('login') }}" class="space-y-6">
                        @csrf

                        <!-- Email Address -->
                        <div>
                            <label for="email" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                {{ __('Email Address') }}
                            </label>
                            <input id="email"
                                   type="email"
                                   name="email"
                                   value="{{ old('email') }}"
                                   required
                                   autofocus
                                   autocomplete="username"
                                   class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-gray-100 bg-white dark:bg-gray-700 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all duration-200 placeholder-gray-500 dark:placeholder-gray-400"
                                   placeholder="Enter your email">
                            @error('email')
                                <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Password -->
                        <div>
                            <label for="password" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                {{ __('Password') }}
                            </label>
                            <input id="password"
                                   type="password"
                                   name="password"
                                   required
                                   autocomplete="current-password"
                                   class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-gray-100 bg-white dark:bg-gray-700 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all duration-200 placeholder-gray-500 dark:placeholder-gray-400"
                                   placeholder="Enter your password">
                            @error('password')
                                <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Remember Me -->
                        <div class="flex items-center justify-between">
                            <label for="remember_me" class="flex items-center">
                                <input id="remember_me"
                                       type="checkbox"
                                       name="remember"
                                       class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-700 transition-colors duration-200">
                                <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">{{ __('Remember me') }}</span>
                            </label>

                            @if (Route::has('password.request'))
                                <a href="{{ route('password.request') }}"
                                   class="text-sm text-primary-600 dark:text-primary-400 hover:text-primary-700 dark:hover:text-primary-300 font-medium transition-colors duration-200">
                                    {{ __('Forgot password?') }}
                                </a>
                            @endif
                        </div>

                        <!-- Login Button -->
                        <button type="submit"
                                class="w-full bg-primary-600 hover:bg-primary-700 text-white font-semibold py-3 px-4 rounded-lg shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800">
                            {{ __('Sign In') }}
                        </button>

                        <div class="relative">
                            <div class="absolute inset-0 flex items-center">
                                <div class="w-full border-t border-gray-300 dark:border-gray-600"></div>
                            </div>
                            <div class="relative flex justify-center text-sm">
                                <span class="bg-white dark:bg-gray-800 px-3 text-gray-500 dark:text-gray-400">{{ __('ui.or') }}</span>
                            </div>
                        </div>

                        @if(config('services.google.client_id') && config('services.google.client_secret'))
                            <a href="{{ route('google.redirect') }}"
                               class="w-full inline-flex items-center justify-center gap-3 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-200 font-semibold py-3 px-4 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-600 transition-all duration-200">
                                <svg class="w-5 h-5" viewBox="0 0 24 24" aria-hidden="true">
                                    <path fill="#4285F4" d="M21.805 10.023h-9.18v3.955h5.263c-.227 1.27-.909 2.347-1.94 3.066v2.546h3.138c1.838-1.692 2.719-4.186 2.719-7.117 0-.58-.052-1.14-.146-1.676z"/>
                                    <path fill="#34A853" d="M12.625 22c2.625 0 4.826-.87 6.435-2.41l-3.138-2.546c-.87.583-1.984.93-3.297.93-2.534 0-4.68-1.71-5.447-4.01H3.93v2.62A9.724 9.724 0 0012.625 22z"/>
                                    <path fill="#FBBC05" d="M7.178 13.964a5.84 5.84 0 010-3.728v-2.62H3.93a9.724 9.724 0 000 8.968l3.248-2.62z"/>
                                    <path fill="#EA4335" d="M12.625 6.026c1.427 0 2.709.49 3.718 1.454l2.787-2.787C17.446 3.137 15.25 2 12.625 2 8.828 2 5.548 4.16 3.93 7.616l3.248 2.62c.767-2.3 2.913-4.21 5.447-4.21z"/>
                                </svg>
                                <span>{{ __('ui.continue_with_google') }}</span>
                            </a>
                        @endif
                    </form>
                </div>

                <!-- Register Link -->
                <div class="px-8 py-6 bg-gray-50 dark:bg-gray-700 border-t border-gray-200 dark:border-gray-600">
                    <p class="text-center text-sm text-gray-600 dark:text-gray-400">
                        {{ __("Don't have an account?") }}
                        <a href="{{ route('register') }}"
                           class="font-semibold text-primary-600 dark:text-primary-400 hover:text-primary-700 dark:hover:text-primary-300 transition-colors duration-200">
                            {{ __('Sign up here') }}
                        </a>
                    </p>
                </div>

                <!-- Demo Credentials -->
                {{-- <div class="px-8 py-4 bg-green-50 dark:bg-green-900/20 border-t border-green-200 dark:border-green-800">
                    <div class="text-center">
                        <h3 class="text-sm font-semibold text-green-800 dark:text-green-200 mb-3">Demo Customer Credentials</h3>
                        <div class="bg-white dark:bg-gray-800 rounded-lg p-4 border border-green-200 dark:border-green-700">
                            <div class="space-y-2 text-sm">
                                <p class="text-gray-700 dark:text-gray-300">
                                    <strong class="text-green-700 dark:text-green-300">Email:</strong> customer@example.com
                                </p>
                                <p class="text-gray-700 dark:text-gray-300">
                                    <strong class="text-green-700 dark:text-green-300">Password:</strong> password
                                </p>
                            </div>
                        </div>
                    </div>
                </div> --}}
            </div>

            <!-- Footer -->
            <div class="mt-8 text-center">
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    &copy; {{ date('Y') }} E-Kampot Shop. All rights reserved.
                </p>
            </div>
        </div>

        <x-chatbot-widget />
    </body>
</html>
