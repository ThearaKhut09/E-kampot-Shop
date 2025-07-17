<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data="{ darkMode: localStorage.getItem('darkMode') === 'true' || (!localStorage.getItem('darkMode') && window.matchMedia('(prefers-color-scheme: dark)').matches), mobileMenuOpen: false }" x-bind:class="{ 'dark': darkMode }">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Alpine.js -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>

<body class="font-sans antialiased bg-gray-50 dark:bg-gray-900 transition-colors duration-200">
    <div class="min-h-screen">
        <!-- Navigation -->
        <nav class="bg-white dark:bg-gray-800 shadow-lg sticky top-0 z-50 transition-colors duration-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex items-center">
                        <!-- Logo -->
                        <div class="flex-shrink-0">
                            <a href="{{ route('home') }}"
                                class="flex items-center text-2xl font-bold text-primary-600 dark:text-primary-400">
                                <!-- Logo Icon - Always visible -->
                                <div
                                    class="w-8 h-8 bg-primary-600 dark:bg-primary-400 rounded-lg flex items-center justify-center mr-2 flex-shrink-0">
                                    <span class="text-white dark:text-gray-900 font-bold text-lg">E</span>
                                </div>
                                <!-- Shop Name - Hidden on mobile, shown on desktop (sm and up) -->
                                <span class="hidden sm:inline">E Kampot Shop</span>
                            </a>
                        </div>

                        <!-- Navigation Links -->
                        <div class="hidden sm:ml-8 sm:flex sm:space-x-1">
                            <a href="{{ route('home') }}"
                                class="text-gray-900 dark:text-gray-100 hover:text-primary-600 dark:hover:text-primary-400 hover:bg-gray-100 dark:hover:bg-gray-700 px-4 py-2 rounded-lg text-sm font-semibold transition-all duration-200 {{ request()->routeIs('home') ? 'text-primary-600 dark:text-primary-400 bg-primary-50 dark:bg-primary-900/20' : '' }}">
                                Home
                            </a>
                            <a href="{{ route('products.index') }}"
                                class="text-gray-900 dark:text-gray-100 hover:text-primary-600 dark:hover:text-primary-400 hover:bg-gray-100 dark:hover:bg-gray-700 px-4 py-2 rounded-lg text-sm font-semibold transition-all duration-200 {{ request()->routeIs('products.*') ? 'text-primary-600 dark:text-primary-400 bg-primary-50 dark:bg-primary-900/20' : '' }}">
                                Products
                            </a>
                            <div class="relative" x-data="{ open: false }">
                                <button @click="open = !open"
                                    class="text-gray-900 dark:text-gray-100 hover:text-primary-600 dark:hover:text-primary-400 hover:bg-gray-100 dark:hover:bg-gray-700 px-4 py-2 rounded-lg text-sm font-semibold transition-all duration-200 flex items-center {{ request()->routeIs('category.*') ? 'text-primary-600 dark:text-primary-400 bg-primary-50 dark:bg-primary-900/20' : '' }}">
                                    Categories
                                    <svg class="ml-1 h-4 w-4 transition-transform duration-200"
                                        :class="{ 'rotate-180': open }" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </button>
                                <div x-show="open" @click.away="open = false"
                                    x-transition:enter="transition ease-out duration-200"
                                    x-transition:enter-start="opacity-0 scale-95"
                                    x-transition:enter-end="opacity-100 scale-100"
                                    x-transition:leave="transition ease-in duration-150"
                                    x-transition:leave-start="opacity-100 scale-100"
                                    x-transition:leave-end="opacity-0 scale-95"
                                    class="absolute left-0 mt-2 w-56 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 z-50">
                                    @php
                                        $categories = \App\Models\Category::active()
                                            ->parents()
                                            ->orderBy('sort_order')
                                            ->take(8)
                                            ->get();
                                    @endphp
                                    @foreach ($categories as $category)
                                        <a href="{{ route('category.show', $category->slug) }}"
                                            class="block px-4 py-3 text-sm text-gray-700 dark:text-gray-300 hover:bg-primary-50 dark:hover:bg-primary-900/20 hover:text-primary-600 dark:hover:text-primary-400 transition-colors first:rounded-t-lg last:rounded-b-lg">
                                            {{ $category->name }}
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                            <a href="{{ route('about') }}"
                                class="text-gray-900 dark:text-gray-100 hover:text-primary-600 dark:hover:text-primary-400 hover:bg-gray-100 dark:hover:bg-gray-700 px-4 py-2 rounded-lg text-sm font-semibold transition-all duration-200 {{ request()->routeIs('about') ? 'text-primary-600 dark:text-primary-400 bg-primary-50 dark:bg-primary-900/20' : '' }}">
                                About
                            </a>
                            <a href="{{ route('contact') }}"
                                class="text-gray-900 dark:text-gray-100 hover:text-primary-600 dark:hover:text-primary-400 hover:bg-gray-100 dark:hover:bg-gray-700 px-4 py-2 rounded-lg text-sm font-semibold transition-all duration-200 {{ request()->routeIs('contact') ? 'text-primary-600 dark:text-primary-400 bg-primary-50 dark:bg-primary-900/20' : '' }}">
                                Contact
                            </a>
                        </div>
                    </div>

                    <div class="flex items-center space-x-4">
                        <!-- Mobile menu button -->
                        <div class="sm:hidden">
                            <button @click="mobileMenuOpen = !mobileMenuOpen" type="button"
                                class="p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-primary-500">
                                <span class="sr-only">Open main menu</span>
                                <svg x-show="!mobileMenuOpen" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 6h16M4 12h16M4 18h16" />
                                </svg>
                                <svg x-show="mobileMenuOpen" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                        <!-- Search -->
                        <div class="hidden md:block">
                            <form action="{{ route('products.index') }}" method="GET" class="relative">
                                <input type="text" name="search" placeholder="Search products..."
                                    value="{{ request('search') }}"
                                    class="w-64 px-4 py-2 pl-10 pr-4 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-colors">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                    </svg>
                                </div>
                            </form>
                        </div>

                        <!-- Dark Mode Toggle -->
                        <button @click="darkMode = !darkMode; localStorage.setItem('darkMode', darkMode)"
                            class="p-2 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 transition-colors">
                            <svg x-show="!darkMode" class="h-5 w-5" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z">
                                </path>
                            </svg>
                            <svg x-show="darkMode" class="h-5 w-5" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z">
                                </path>
                            </svg>
                        </button>

                        <!-- Cart (Only for customers and guests) -->
                        @if (Auth::guard('web')->check())
                            <a href="{{ route('cart.index') }}"
                                class="relative p-2 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 transition-colors">
                                <svg width="24px" height="24px" viewBox="0 0 24 24"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <circle cx="16.5" cy="18.5" r="1.5" />
                                    <circle cx="9.5" cy="18.5" r="1.5" />
                                    <path
                                        d="M18 16H8a1 1 0 0 1-.958-.713L4.256 6H3a1 1 0 0 1 0-2h2a1 1 0 0 1 .958.713L6.344 6H21a1 1 0 0 1 .937 1.352l-3 8A1 1 0 0 1 18 16zm-9.256-2h8.563l2.25-6H6.944z" />
                                </svg>

                                <span
                                    class="absolute -top-1 -right-1 bg-primary-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">
                                    <span id="cart-count">0</span>
                                </span>
                            </a>
                        @endif

                        <!-- Authentication -->
                        @if (Auth::guard('web')->check() || Auth::guard('admin')->check())
                            <div class="relative" x-data="{ open: false }">
                                <button @click="open = !open"
                                    class="flex items-center text-sm font-medium text-gray-700 dark:text-gray-300 hover:text-gray-900 dark:hover:text-gray-100 transition-colors">
                                    <!-- User Icon - Always visible -->
                                    <div
                                        class="w-8 h-8 bg-gray-200 dark:bg-gray-700 rounded-full flex items-center justify-center mr-2 sm:mr-1">
                                        <svg class="w-5 h-5 text-gray-600 dark:text-gray-400" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z">
                                            </path>
                                        </svg>
                                    </div>
                                    <!-- User Name - Hidden on small screens -->
                                    <span class="hidden sm:block">
                                        @if (Auth::guard('web')->check())
                                            {{ Auth::guard('web')->user()->name }}
                                        @else
                                            {{ Auth::guard('admin')->user()->name }}
                                        @endif
                                    </span>
                                </button>
                                <div x-show="open" @click.away="open = false" x-transition
                                    class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-md shadow-lg z-50">
                                    @if (Auth::guard('web')->check())
                                        <a href="{{ route('dashboard') }}"
                                            class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                                            Dashboard
                                        </a>
                                        <a href="{{ route('profile.edit') }}"
                                            class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                                            Profile
                                        </a>
                                        <form method="POST" action="{{ route('logout') }}">
                                            @csrf
                                            <button type="submit"
                                                class="block w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                                                Logout
                                            </button>
                                        </form>
                                    @else
                                        <a href="{{ route('admin.dashboard') }}"
                                            class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                                            Admin Dashboard
                                        </a>
                                        <form method="POST" action="{{ route('admin.logout') }}">
                                            @csrf
                                            <button type="submit"
                                                class="block w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                                                Logout
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        @else
                            <div class="flex items-center space-x-2">
                                <!-- Desktop Login & Register Buttons -->
                                <a href="{{ route('login') }}"
                                    class="text-gray-700 dark:text-gray-300 hover:text-gray-900 dark:hover:text-gray-100 px-3 py-2 text-sm font-medium transition-colors hidden lg:block">
                                    Login
                                </a>
                                <a href="{{ route('register') }}"
                                    class="bg-primary-600 hover:bg-primary-700 text-white px-4 py-2 rounded-md text-sm font-medium transition-colors hidden lg:block">
                                    Register
                                </a>
                                <!-- Mobile/Tablet Login Icon -->
                                <a href="{{ route('login') }}"
                                    class="p-2 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 transition-colors lg:hidden"
                                    title="Login">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1">
                                        </path>
                                    </svg>
                                </a>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Mobile menu -->
                <div x-show="mobileMenuOpen" x-transition class="sm:hidden">
                    <div class="pt-2 pb-3 space-y-1">
                        <a href="{{ route('home') }}"
                            class="block pl-3 pr-4 py-2 border-l-4 text-base font-medium {{ request()->routeIs('home') ? 'border-primary-500 text-primary-700 dark:text-primary-300 bg-primary-50 dark:bg-primary-900/10' : 'border-transparent text-gray-600 dark:text-gray-300 hover:text-gray-800 dark:hover:text-gray-100 hover:bg-gray-50 dark:hover:bg-gray-700 hover:border-gray-300' }} transition-colors">
                            Home
                        </a>
                        <a href="{{ route('products.index') }}"
                            class="block pl-3 pr-4 py-2 border-l-4 text-base font-medium {{ request()->routeIs('products.*') ? 'border-primary-500 text-primary-700 dark:text-primary-300 bg-primary-50 dark:bg-primary-900/10' : 'border-transparent text-gray-600 dark:text-gray-300 hover:text-gray-800 dark:hover:text-gray-100 hover:bg-gray-50 dark:hover:bg-gray-700 hover:border-gray-300' }} transition-colors">
                            Products
                        </a>

                        <!-- Mobile Categories -->
                        <div x-data="{ categoriesOpen: false }">
                            <button @click="categoriesOpen = !categoriesOpen"
                                class="w-full flex items-center justify-between pl-3 pr-4 py-2 border-l-4 text-base font-medium {{ request()->routeIs('category.*') ? 'border-primary-500 text-primary-700 dark:text-primary-300 bg-primary-50 dark:bg-primary-900/10' : 'border-transparent text-gray-600 dark:text-gray-300 hover:text-gray-800 dark:hover:text-gray-100 hover:bg-gray-50 dark:hover:bg-gray-700 hover:border-gray-300' }} transition-colors">
                                <span>Categories</span>
                                <svg :class="{ 'rotate-180': categoriesOpen }"
                                    class="h-5 w-5 transform transition-transform" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>
                            <div x-show="categoriesOpen" x-transition class="pl-6 space-y-1">
                                @php
                                    $mobileCategories = \App\Models\Category::active()
                                        ->parents()
                                        ->orderBy('sort_order')
                                        ->take(8)
                                        ->get();
                                @endphp
                                @foreach ($mobileCategories as $category)
                                    <a href="{{ route('category.show', $category->slug) }}"
                                        class="block pl-3 pr-4 py-2 text-sm text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                        {{ $category->name }}
                                    </a>
                                @endforeach
                            </div>
                        </div>

                        <a href="{{ route('about') }}"
                            class="block pl-3 pr-4 py-2 border-l-4 text-base font-medium {{ request()->routeIs('about') ? 'border-primary-500 text-primary-700 dark:text-primary-300 bg-primary-50 dark:bg-primary-900/10' : 'border-transparent text-gray-600 dark:text-gray-300 hover:text-gray-800 dark:hover:text-gray-100 hover:bg-gray-50 dark:hover:bg-gray-700 hover:border-gray-300' }} transition-colors">
                            About
                        </a>
                        <a href="{{ route('contact') }}"
                            class="block pl-3 pr-4 py-2 border-l-4 text-base font-medium {{ request()->routeIs('contact') ? 'border-primary-500 text-primary-700 dark:text-primary-300 bg-primary-50 dark:bg-primary-900/10' : 'border-transparent text-gray-600 dark:text-gray-300 hover:text-gray-800 dark:hover:text-gray-100 hover:bg-gray-50 dark:hover:bg-gray-700 hover:border-gray-300' }} transition-colors">
                            Contact
                        </a>

                        <!-- Mobile Search -->
                        <div class="px-3 py-2">
                            <form action="{{ route('products.index') }}" method="GET" class="relative">
                                <input type="text" name="search" placeholder="Search products..."
                                    value="{{ request('search') }}"
                                    class="w-full px-4 py-2 pl-10 pr-4 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-colors">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                    </svg>
                                </div>
                            </form>
                        </div>

                        <!-- Mobile Auth Links -->
                        @if (!Auth::guard('web')->check() && !Auth::guard('admin')->check())
                            <div class="pt-4 pb-3 border-t border-gray-200 dark:border-gray-600">
                                <div class="space-y-1">
                                    <a href="{{ route('login') }}"
                                        class="block pl-3 pr-4 py-2 border-l-4 border-transparent text-base font-medium text-gray-600 dark:text-gray-300 hover:text-gray-800 dark:hover:text-gray-100 hover:bg-gray-50 dark:hover:bg-gray-700 hover:border-gray-300 transition-colors">
                                        Login
                                    </a>
                                    <a href="{{ route('register') }}"
                                        class="block pl-3 pr-4 py-2 border-l-4 border-transparent text-base font-medium text-gray-600 dark:text-gray-300 hover:text-gray-800 dark:hover:text-gray-100 hover:bg-gray-50 dark:hover:bg-gray-700 hover:border-gray-300 transition-colors">
                                        Register
                                    </a>
                                </div>
                            </div>
                        @else
                            <div class="pt-4 pb-3 border-t border-gray-200 dark:border-gray-600">
                                <div class="flex items-center px-4">
                                    <div
                                        class="w-10 h-10 bg-gray-200 dark:bg-gray-700 rounded-full flex items-center justify-center mr-3">
                                        <svg class="w-6 h-6 text-gray-600 dark:text-gray-400" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z">
                                            </path>
                                        </svg>
                                    </div>
                                    <div class="text-base font-medium text-gray-800 dark:text-gray-200">
                                        @if (Auth::guard('web')->check())
                                            {{ Auth::guard('web')->user()->name }}
                                        @else
                                            {{ Auth::guard('admin')->user()->name }}
                                        @endif
                                    </div>
                                </div>
                                <div class="mt-3 space-y-1">
                                    @if (Auth::guard('web')->check())
                                        <a href="{{ route('dashboard') }}"
                                            class="block pl-3 pr-4 py-2 border-l-4 border-transparent text-base font-medium text-gray-600 dark:text-gray-300 hover:text-gray-800 dark:hover:text-gray-100 hover:bg-gray-50 dark:hover:bg-gray-700 hover:border-gray-300 transition-colors">
                                            Dashboard
                                        </a>
                                        <a href="{{ route('profile.edit') }}"
                                            class="block pl-3 pr-4 py-2 border-l-4 border-transparent text-base font-medium text-gray-600 dark:text-gray-300 hover:text-gray-800 dark:hover:text-gray-100 hover:bg-gray-50 dark:hover:bg-gray-700 hover:border-gray-300 transition-colors">
                                            Profile
                                        </a>
                                        <form method="POST" action="{{ route('logout') }}">
                                            @csrf
                                            <button type="submit"
                                                class="block w-full text-left pl-3 pr-4 py-2 border-l-4 border-transparent text-base font-medium text-gray-600 dark:text-gray-300 hover:text-gray-800 dark:hover:text-gray-100 hover:bg-gray-50 dark:hover:bg-gray-700 hover:border-gray-300 transition-colors">
                                                Logout
                                            </button>
                                        </form>
                                    @else
                                        <a href="{{ route('admin.dashboard') }}"
                                            class="block pl-3 pr-4 py-2 border-l-4 border-transparent text-base font-medium text-gray-600 dark:text-gray-300 hover:text-gray-800 dark:hover:text-gray-100 hover:bg-gray-50 dark:hover:bg-gray-700 hover:border-gray-300 transition-colors">
                                            Admin Dashboard
                                        </a>
                                        <form method="POST" action="{{ route('admin.logout') }}">
                                            @csrf
                                            <button type="submit"
                                                class="block w-full text-left pl-3 pr-4 py-2 border-l-4 border-transparent text-base font-medium text-gray-600 dark:text-gray-300 hover:text-gray-800 dark:hover:text-gray-100 hover:bg-gray-50 dark:hover:bg-gray-700 hover:border-gray-300 transition-colors">
                                                Logout
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </nav>

        <!-- Page Heading -->
        @isset($header)
            <header class="bg-white dark:bg-gray-800 shadow transition-colors duration-200">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header>
        @endisset

        <!-- Page Content -->
        <main>
            {{ $slot }}
        </main>

        <!-- Footer -->
        <footer class="bg-gray-900 dark:bg-gray-950 text-white mt-16">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                    <div>
                        <h3 class="text-lg font-semibold mb-4">E-Kampot Shop</h3>
                        <p class="text-gray-400 text-sm">Your one-stop shop for everything you need. Quality products,
                            great prices, excellent service.</p>
                    </div>
                    <div>
                        <h4 class="font-semibold mb-4">Quick Links</h4>
                        <div class="space-y-2">
                            <a href="{{ route('products.index') }}"
                                class="block text-gray-400 hover:text-white text-sm transition-colors">Products</a>
                            <a href="{{ route('about') }}"
                                class="block text-gray-400 hover:text-white text-sm transition-colors">About Us</a>
                            <a href="{{ route('contact') }}"
                                class="block text-gray-400 hover:text-white text-sm transition-colors">Contact</a>
                        </div>
                    </div>
                    <div>
                        <h4 class="font-semibold mb-4">Categories</h4>
                        <div class="space-y-2">
                            @foreach (\App\Models\Category::active()->parents()->orderBy('sort_order')->take(5)->get() as $category)
                                <a href="{{ route('category.show', $category->slug) }}"
                                    class="block text-gray-400 hover:text-white text-sm transition-colors">{{ $category->name }}</a>
                            @endforeach
                        </div>
                    </div>
                    <div>
                        <h4 class="font-semibold mb-4">Contact Info</h4>
                        <div class="space-y-2 text-sm text-gray-400">
                            <p>{{ \App\Models\Setting::get('site_address', '123 Main Street, Kampot, Cambodia') }}</p>
                            <p>{{ \App\Models\Setting::get('site_phone', '+1 (555) 123-4567') }}</p>
                            <p>{{ \App\Models\Setting::get('site_email', 'info@ekampot.com') }}</p>
                        </div>
                    </div>
                </div>
                <div class="border-t border-gray-800 mt-8 pt-8 text-center">
                    <p class="text-gray-400 text-sm">&copy; {{ date('Y') }} E-Kampot Shop. All rights reserved.
                    </p>
                </div>
            </div>
        </footer>
    </div>

    <!-- Toast Notifications -->
    <div id="toast-container" class="fixed top-4 right-4 z-50 space-y-2"></div>

    <!-- JavaScript -->
    <script>
        // Global cart management
        window.cartManager = {
            isAdmin: {{ Auth::guard('admin')->check() ? 'true' : 'false' }},
            isWebUser: {{ Auth::guard('web')->check() ? 'true' : 'false' }},

            updateCartCount: function() {
                const cartCountElement = document.getElementById('cart-count');

                // Only update cart count if web user is logged in and cart icon exists
                if (this.isWebUser && cartCountElement) {
                    fetch('{{ route('cart.count') }}', {
                        method: 'GET',
                        credentials: 'same-origin',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                        }
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.json();
                    })
                    .then(data => {
                        cartCountElement.textContent = data.count || 0;
                    })
                    .catch(error => {
                        console.log('Cart count fetch error:', error);
                        cartCountElement.textContent = '0';
                    });
                }
            },

            init: function() {
                this.updateCartCount();

                // Update cart count periodically if web user is logged in
                if (this.isWebUser) {
                    setInterval(() => {
                        this.updateCartCount();
                    }, 30000); // Update every 30 seconds
                }
            }
        };

        // Update cart count on page load
        document.addEventListener('DOMContentLoaded', function() {
            window.cartManager.init();
        });

        function updateCartCount() {
            window.cartManager.updateCartCount();
        }

        function showToast(message, type = 'success') {
            const toast = document.createElement('div');
            toast.className =
                `p-4 rounded-md shadow-lg text-white ${type === 'success' ? 'bg-green-500' : 'bg-red-500'} transform transition-all duration-300 translate-x-full`;
            toast.textContent = message;

            document.getElementById('toast-container').appendChild(toast);

            setTimeout(() => {
                toast.classList.remove('translate-x-full');
            }, 100);

            setTimeout(() => {
                toast.classList.add('translate-x-full');
                setTimeout(() => {
                    toast.remove();
                }, 300);
            }, 3000);
        }
    </script>

    @stack('scripts')
</body>

</html>
