<x-app-layout>
    <x-slot name="title">Payment - E-Kampot Shop</x-slot>

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
          integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />

    <style>
        .delivery-map {
            height: 260px;
            min-height: 260px;
            width: 100%;
        }

        /* Keep Leaflet controls/layers below site header and other UI */
        .leaflet-container,
        .leaflet-pane,
        .leaflet-top,
        .leaflet-bottom {
            z-index: 10 !important;
        }

        @media (max-width: 640px) {
            .delivery-map {
                height: 200px;
                min-height: 200px;
            }
        }
    </style>

    <div class="min-h-screen bg-gradient-to-br from-gray-50 via-emerald-50/30 to-gray-100 dark:from-gray-900 dark:via-emerald-950/20 dark:to-gray-950 pt-24 pb-8 px-4 sm:px-6 lg:px-8">
        <div class="max-w-5xl mx-auto">

            <!-- Header -->
            <div class="text-center mb-8">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-br from-emerald-400 to-teal-600 rounded-2xl shadow-lg mb-4">
                    <svg class="w-8 h-8 text-black dark:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                    </svg>
                </div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100 mb-2">
                    Secure Payment
                </h1>
                <p class="text-gray-500 dark:text-gray-400">
                    Pay with Bakong KHQR — fast, secure, and instant
                </p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-5 gap-8">

                <!-- Left: Order Summary (2 cols) -->
                <div class="lg:col-span-2 space-y-6">
                    <div class="bg-white/80 dark:bg-gray-800/80 backdrop-blur-xl rounded-2xl shadow-xl border border-gray-200/60 dark:border-gray-700/60 p-6">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                            </svg>
                            Order Summary
                        </h2>

                        <div class="space-y-3 max-h-64 overflow-y-auto pr-1">
                            @foreach($cartItems as $item)
                            <div class="flex justify-between items-center py-2 border-b border-gray-100 dark:border-gray-700/50 last:border-b-0">
                                <div class="flex items-center space-x-3 min-w-0">
                                    @if($item->product->image_url)
                                        <img src="{{ $item->product->image_url }}" alt="{{ $item->product->name }}"
                                             class="w-10 h-10 object-cover rounded-lg flex-shrink-0">
                                    @else
                                        <div class="w-10 h-10 bg-gray-200 dark:bg-gray-700 rounded-lg flex items-center justify-center flex-shrink-0">
                                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                            </svg>
                                        </div>
                                    @endif
                                    <div class="min-w-0">
                                        <p class="text-sm font-medium text-gray-900 dark:text-gray-100 truncate">{{ $item->product->name }}</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">× {{ $item->quantity }}</p>
                                    </div>
                                </div>
                                <span class="text-sm font-semibold text-gray-900 dark:text-gray-100 flex-shrink-0 ml-2">
                                    ${{ number_format($item->quantity * $item->product->current_price, 2) }}
                                </span>
                            </div>
                            @endforeach
                        </div>

                        <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700 space-y-2">
                            <div class="flex justify-between text-sm text-gray-600 dark:text-gray-400">
                                <span>Subtotal</span>
                                <span>${{ number_format($subtotal, 2) }}</span>
                            </div>
                            <div class="flex justify-between text-sm text-gray-600 dark:text-gray-400">
                                <span>Shipping</span>
                                <span>{{ $shipping > 0 ? '$' . number_format($shipping, 2) : 'Free' }}</span>
                            </div>
                            <div class="flex justify-between text-sm text-gray-600 dark:text-gray-400">
                                <span>Tax</span>
                                <span>${{ number_format($tax, 2) }}</span>
                            </div>
                            <div class="flex justify-between text-lg font-bold text-gray-900 dark:text-gray-100 pt-2 border-t border-gray-200 dark:border-gray-700">
                                <span>Total</span>
                                <span class="text-emerald-600 dark:text-emerald-400">${{ number_format($total, 2) }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Method Info -->
                    <div class="bg-white/80 dark:bg-gray-800/80 backdrop-blur-xl rounded-2xl shadow-xl border border-gray-200/60 dark:border-gray-700/60 p-6">
                        <div class="flex items-center space-x-3 mb-4">
                            <div class="w-10 h-10 rounded-xl flex items-center justify-center shadow-lg bg-white overflow-hidden">
                                <img src="{{ asset('images/bakong.png') }}" alt="Bakong KHQR" class="w-full h-full object-cover">
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-900 dark:text-gray-100">Bakong KHQR</h3>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Scan with any Bakong-supported banking app</p>
                            </div>
                        </div>
                        <div class="bg-blue-50 dark:bg-blue-900/20 rounded-xl p-4 space-y-2">
                            <div class="flex items-start space-x-2">
                                <svg class="w-4 h-4 text-blue-500 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>
                                <p class="text-xs text-blue-800 dark:text-blue-200">Supported by ABA, ACLEDA, Wing, TrueMoney, and 40+ banks</p>
                            </div>
                            <div class="flex items-start space-x-2">
                                <svg class="w-4 h-4 text-blue-500 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>
                                <p class="text-xs text-blue-800 dark:text-blue-200">Instant confirmation — no waiting for bank transfers</p>
                            </div>
                            <div class="flex items-start space-x-2">
                                <svg class="w-4 h-4 text-blue-500 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>
                                <p class="text-xs text-blue-800 dark:text-blue-200">Secured by the National Bank of Cambodia</p>
                            </div>
                        </div>
                    </div>

                    <!-- Delivery Location -->
                    <div class="bg-white/80 dark:bg-gray-800/80 backdrop-blur-xl rounded-2xl shadow-xl border border-gray-200/60 dark:border-gray-700/60 p-6">
                        <div class="flex items-center justify-between gap-4 mb-4">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 rounded-xl bg-emerald-100 dark:bg-emerald-900/30 flex items-center justify-center text-emerald-600 dark:text-emerald-400">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.828 0l-4.243-4.243a8 8 0 1111.314 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-gray-900 dark:text-gray-100">Delivery Location</h3>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">Use your live location or tap the map to pin your drop-off point</p>
                                </div>
                            </div>
                            <div id="location-status" class="text-xs font-semibold px-3 py-2 rounded-full bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300">
                                Location not selected
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                            <div>
                                <label for="first_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">First Name *</label>
                                <input type="text" id="first_name" value="" placeholder="Example: Theara" class="delivery-field w-full rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 px-4 py-3 text-gray-900 dark:text-gray-100 focus:border-emerald-500 focus:ring-emerald-500">
                            </div>
                            <div>
                                <label for="last_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Last Name *</label>
                                <input type="text" id="last_name" value="" placeholder="Example: Khut" class="delivery-field w-full rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 px-4 py-3 text-gray-900 dark:text-gray-100 focus:border-emerald-500 focus:ring-emerald-500">
                            </div>
                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Email *</label>
                                <input type="email" id="email" value="" placeholder="Example: yourname@gmail.com" class="delivery-field w-full rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 px-4 py-3 text-gray-900 dark:text-gray-100 focus:border-emerald-500 focus:ring-emerald-500">
                            </div>
                            <div>
                                <label for="phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Phone *</label>
                                <input type="tel" id="phone" value="" placeholder="Example: 068337390" class="delivery-field w-full rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 px-4 py-3 text-gray-900 dark:text-gray-100 focus:border-emerald-500 focus:ring-emerald-500">
                            </div>
                        </div>

                        <div id="delivery-map-wrapper" class="relative overflow-hidden rounded-2xl border border-gray-200 dark:border-gray-700 bg-gray-100 dark:bg-gray-900">
                            <div id="delivery-map" class="delivery-map w-full"></div>
                            <div class="pointer-events-none absolute left-4 top-4 rounded-full bg-white/95 dark:bg-gray-900/90 px-3 py-2 text-xs font-bold text-gray-800 dark:text-gray-200 shadow-lg border border-gray-200 dark:border-gray-700">
                                Tap the map to fine-tune the pin
                            </div>
                        </div>

                        <div class="mt-4 grid grid-cols-2 gap-3">
                            <button type="button" onclick="useCurrentLocation()" class="inline-flex items-center justify-center rounded-xl bg-emerald-700 hover:bg-emerald-800 px-4 py-3 text-sm font-bold text-black dark:text-white shadow-lg shadow-emerald-700/30 transition-colors">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.828 0l-4.243-4.243a8 8 0 1111.314 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                                Use Current Location
                            </button>
                            <button type="button" onclick="resetLocation()" class="inline-flex items-center justify-center rounded-xl bg-gray-300 hover:bg-gray-400 dark:bg-gray-700 dark:hover:bg-gray-600 px-4 py-3 text-sm font-bold text-gray-950 dark:text-gray-100 shadow-sm transition-colors">
                                Reset
                            </button>
                        </div>

                        <div class="mt-4 grid grid-cols-1 gap-3 sm:grid-cols-3">
                            <div class="rounded-xl bg-gray-50 dark:bg-gray-700/40 p-4 sm:col-span-2">
                                <p class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Selected location</p>
                                <p id="location-display" class="mt-1 text-sm font-medium text-gray-900 dark:text-gray-100">No location selected yet</p>
                            </div>
                            <div class="rounded-xl bg-gray-50 dark:bg-gray-700/40 p-4">
                                <p class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Coordinates</p>
                                <p id="location-coordinates" class="mt-1 text-sm font-medium text-gray-900 dark:text-gray-100">--</p>
                            </div>
                        </div>

                        <input type="hidden" id="location_name" value="">
                        <input type="hidden" id="latitude" value="">
                        <input type="hidden" id="longitude" value="">
                    </div>

                    <!-- Back to Cart -->
                    <a href="{{ route('cart.index') }}"
                       class="flex items-center justify-center w-full py-3 px-6 bg-gray-200 dark:bg-gray-700/50 text-gray-900 dark:text-gray-300 font-bold rounded-xl hover:bg-gray-300 dark:hover:bg-gray-700 transition-all duration-200 shadow-sm">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Back to Cart
                    </a>
                </div>

                <!-- Right: QR Code Area (3 cols) -->
                <div class="lg:col-span-3">

                    <!-- STEP 1: Generate QR (Initial State) -->
                    <div id="step-generate" class="bg-white/80 dark:bg-gray-800/80 backdrop-blur-xl rounded-2xl shadow-xl border border-gray-200/60 dark:border-gray-700/60 overflow-hidden">
                        <div class="bg-gradient-to-r from-emerald-500 to-teal-600 p-6 text-center">
                            <h2 class="text-xl font-bold text-white mb-1">Pay with Bakong KHQR</h2>
                            <p class="text-emerald-100 text-sm">Click the button below to generate your payment QR code</p>
                        </div>
                        <div class="p-8 text-center">
                            <div class="w-48 h-48 mx-auto mb-6 bg-gray-100 dark:bg-gray-700/50 rounded-2xl flex items-center justify-center border-2 border-dashed border-gray-300 dark:border-gray-600">
                                <svg class="w-16 h-16 text-gray-500 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path>
                                </svg>
                            </div>

                            <div class="mb-6">
                                <p class="text-2xl font-bold text-emerald-600 dark:text-emerald-400 mb-1">
                                    ${{ number_format($total, 2) }} <span class="text-base font-normal text-gray-500">USD</span>
                                </p>
                            </div>

                            <button type="button" id="generate-qr-btn"
                                    onclick="generateQRCode()"
                                    class="w-full max-w-sm mx-auto bg-gradient-to-r from-emerald-700 to-teal-800 hover:from-emerald-800 hover:to-teal-900 text-black dark:text-white font-bold py-4 px-8 rounded-xl transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 flex items-center justify-center">
                                <span id="generate-btn-text" class="flex items-center">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path>
                                    </svg>
                                    Generate QR Code to Pay
                                </span>
                                <span id="generate-btn-loading" class="hidden items-center">
                                    <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    Generating...
                                </span>
                            </button>
                        </div>
                    </div>

                    <!-- STEP 2: QR Code Display + Polling (Hidden initially) -->
                    <div id="step-qr" class="hidden">
                        <div class="bg-white/80 dark:bg-gray-800/80 backdrop-blur-xl rounded-2xl shadow-xl border border-gray-200/60 dark:border-gray-700/60 overflow-hidden">
                            <!-- Header with timer -->
                            <div class="bg-gradient-to-r from-emerald-500 to-teal-600 p-4 flex items-center justify-between">
                                <div class="flex items-center text-white">
                                    <div class="w-3 h-3 bg-white rounded-full animate-pulse mr-2"></div>
                                    <span class="font-medium text-sm">Waiting for payment...</span>
                                </div>
                                <div id="timer-display" class="bg-white/20 backdrop-blur-sm px-3 py-1 rounded-lg text-white font-mono text-sm font-semibold">
                                    15:00
                                </div>
                            </div>

                            <div class="p-6 text-center">
                                <!-- QR Code Container -->
                                <div class="relative inline-block mb-6">
                                    <div class="qr-pulse-ring absolute inset-0 rounded-2xl"></div>
                                    <div id="qr-code-container" class="relative bg-white p-4 rounded-2xl shadow-lg inline-block">
                                        <!-- QR code rendered here by JS -->
                                    </div>
                                </div>

                                <!-- Amount -->
                                <div class="mb-4">
                                    <p class="text-sm text-gray-700 dark:text-gray-400 mb-1 font-medium">Amount to pay</p>
                                    <p class="text-3xl font-bold text-gray-900 dark:text-gray-100">
                                        $<span id="qr-amount">{{ number_format($total, 2) }}</span>
                                        <span class="text-base font-normal text-gray-600 dark:text-gray-400">USD</span>
                                    </p>
                                </div>

                                <!-- Order Number -->
                                <div class="mb-6">
                                    <p class="text-xs text-gray-700 dark:text-gray-400 font-medium">Order: <span id="qr-order-number" class="font-mono font-semibold text-gray-900 dark:text-gray-300">—</span></p>
                                </div>

                                <!-- Instructions -->
                                <div class="bg-gray-50 dark:bg-gray-700/30 rounded-xl p-4 mb-4 text-left">
                                    <p class="text-sm font-semibold text-gray-900 dark:text-gray-100 mb-3">How to pay:</p>
                                    <div class="space-y-2">
                                        <div class="flex items-start space-x-3">
                                            <span class="w-6 h-6 bg-emerald-100 dark:bg-emerald-900/40 text-emerald-700 dark:text-emerald-400 rounded-full flex items-center justify-center text-xs font-bold flex-shrink-0">1</span>
                                            <p class="text-sm text-gray-700 dark:text-gray-400 font-medium">Open your banking app (ABA, ACLEDA, Wing, etc.)</p>
                                        </div>
                                        <div class="flex items-start space-x-3">
                                            <span class="w-6 h-6 bg-emerald-100 dark:bg-emerald-900/40 text-emerald-700 dark:text-emerald-400 rounded-full flex items-center justify-center text-xs font-bold flex-shrink-0">2</span>
                                            <p class="text-sm text-gray-700 dark:text-gray-400 font-medium">Scan this QR code</p>
                                        </div>
                                        <div class="flex items-start space-x-3">
                                            <span class="w-6 h-6 bg-emerald-100 dark:bg-emerald-900/40 text-emerald-700 dark:text-emerald-400 rounded-full flex items-center justify-center text-xs font-bold flex-shrink-0">3</span>
                                            <p class="text-sm text-gray-700 dark:text-gray-400 font-medium">Confirm the payment in your app</p>
                                        </div>
                                        <div class="flex items-start space-x-3">
                                            <span class="w-6 h-6 bg-emerald-100 dark:bg-emerald-900/40 text-emerald-700 dark:text-emerald-400 rounded-full flex items-center justify-center text-xs font-bold flex-shrink-0">4</span>
                                            <p class="text-sm text-gray-700 dark:text-gray-400 font-medium">This page will update automatically</p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Polling Status -->
                                <div id="polling-status" class="flex items-center justify-center text-sm text-gray-700 dark:text-gray-400 font-medium">
                                    <svg class="animate-spin w-4 h-4 mr-2 text-emerald-500" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    Checking payment status...
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- STEP 3: Expired State (Hidden initially) -->
                    <div id="step-expired" class="hidden">
                        <div class="bg-white/80 dark:bg-gray-800/80 backdrop-blur-xl rounded-2xl shadow-xl border border-red-200/60 dark:border-red-700/60 overflow-hidden">
                            <div class="bg-gradient-to-r from-red-500 to-rose-600 p-6 text-center">
                                <div class="w-16 h-16 mx-auto bg-white/20 rounded-full flex items-center justify-center mb-3">
                                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <h2 class="text-xl font-bold text-white mb-1">QR Code Expired</h2>
                                <p class="text-red-100 text-sm">The payment window has closed</p>
                            </div>
                            <div class="p-8 text-center">
                                <p class="text-sm text-gray-700 dark:text-gray-400 mb-6 font-medium">
                                    Your QR code has expired. Please generate a new one to complete your payment. If you did not pay, your items are still in your cart.
                                </p>
                                <button onclick="window.location.reload()"
                                        class="bg-gradient-to-r from-emerald-700 to-teal-800 hover:from-emerald-800 hover:to-teal-900 text-white font-bold py-3 px-8 rounded-xl transition-all duration-300 shadow-lg hover:shadow-xl">
                                    <svg class="w-5 h-5 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                    </svg>
                                    Try Again
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- STEP 4: Success State (Hidden initially) -->
                    <div id="step-success" class="hidden">
                        <div class="bg-white/80 dark:bg-gray-800/80 backdrop-blur-xl rounded-2xl shadow-xl border border-emerald-200/60 dark:border-emerald-700/60 overflow-hidden">
                            <div class="bg-gradient-to-r from-emerald-500 to-green-600 p-8 text-center">
                                <div class="w-20 h-20 mx-auto bg-white/20 rounded-full flex items-center justify-center mb-4 animate-bounce">
                                    <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                </div>
                                <h2 class="text-2xl font-bold text-white mb-1">Payment Successful!</h2>
                                <p class="text-emerald-100">Your order has been confirmed</p>
                            </div>
                            <div class="p-8">
                                <div class="bg-emerald-50 dark:bg-emerald-900/20 rounded-xl p-6 mb-6 space-y-3">
                                    <div class="flex justify-between items-center">
                                        <span class="text-gray-600 dark:text-gray-400 text-sm font-medium">Order Number</span>
                                        <span id="success-order-number" class="font-mono font-bold text-emerald-600 dark:text-emerald-400">—</span>
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <span class="text-gray-600 dark:text-gray-400 text-sm font-medium">Amount Paid</span>
                                        <span id="success-total" class="text-xl font-bold text-gray-900 dark:text-gray-100">—</span>
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <span class="text-gray-600 dark:text-gray-400 text-sm font-medium">Payment Method</span>
                                        <span class="font-medium text-gray-900 dark:text-gray-100">Bakong KHQR</span>
                                    </div>
                                </div>

                                <p class="text-sm text-gray-700 dark:text-gray-400 text-center mb-6 font-medium">
                                    A confirmation email has been sent. You will be redirected shortly...
                                </p>

                                <div class="flex flex-col sm:flex-row gap-3">
                                    <a href="{{ route('orders.index') }}"
                                       class="flex-1 text-center bg-emerald-50 dark:bg-emerald-900/20 text-emerald-600 dark:text-emerald-400 font-semibold py-3 px-6 rounded-xl hover:bg-emerald-100 dark:hover:bg-emerald-900/40 transition-colors">
                                        View Orders
                                    </a>
                                    <a href="{{ route('products.index') }}"
                                       class="flex-1 text-center bg-gradient-to-r from-emerald-700 to-teal-800 hover:from-emerald-800 hover:to-teal-900 text-white font-bold py-3 px-6 rounded-xl transition-all duration-200 shadow-lg">
                                        Continue Shopping
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
            integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <!-- QRCode.js CDN -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>

    <style>
        .qr-pulse-ring {
            animation: qrPulse 2s ease-in-out infinite;
            border: 2px solid rgba(16, 185, 129, 0.3);
        }
        @keyframes qrPulse {
            0%, 100% { transform: scale(1); opacity: 1; border-color: rgba(16, 185, 129, 0.3); }
            50% { transform: scale(1.05); opacity: 0.7; border-color: rgba(16, 185, 129, 0.6); }
        }
        @keyframes confettiFall {
            to {
                transform: translateY(100vh) rotate(720deg);
                opacity: 0;
            }
        }
    </style>

    <script>
        // State
        let paymentState = {
            orderId: null,
            orderNumber: null,
            md5: null,
            expiresAt: null,
            pollingInterval: null,
            timerInterval: null,
            isPolling: false,
        };

        let deliveryMap = null;
        let deliveryMarker = null;
        const fallbackLocation = [10.6071, 104.1810];
        const DELIVERY_DRAFT_KEY = 'checkout_delivery_draft_v1';

        function saveDeliveryDraft() {
            const payload = {
                first_name: document.getElementById('first_name')?.value || '',
                last_name: document.getElementById('last_name')?.value || '',
                email: document.getElementById('email')?.value || '',
                phone: document.getElementById('phone')?.value || '',
                location_name: document.getElementById('location_name')?.value || '',
                latitude: document.getElementById('latitude')?.value || '',
                longitude: document.getElementById('longitude')?.value || '',
            };

            localStorage.setItem(DELIVERY_DRAFT_KEY, JSON.stringify(payload));
        }

        function restoreDeliveryDraft() {
            const raw = localStorage.getItem(DELIVERY_DRAFT_KEY);
            if (!raw) {
                return;
            }

            try {
                const draft = JSON.parse(raw);
                document.getElementById('first_name').value = draft.first_name || '';
                document.getElementById('last_name').value = draft.last_name || '';
                document.getElementById('email').value = draft.email || '';
                document.getElementById('phone').value = draft.phone || '';
                document.getElementById('location_name').value = draft.location_name || '';
                document.getElementById('latitude').value = draft.latitude || '';
                document.getElementById('longitude').value = draft.longitude || '';

                if (draft.location_name && draft.latitude && draft.longitude) {
                    const labelBase = String(draft.location_name).split(' (')[0] || 'Saved location';
                    setLocationState(draft.latitude, draft.longitude, labelBase);
                }
            } catch (error) {
                localStorage.removeItem(DELIVERY_DRAFT_KEY);
            }
        }

        function setupDeliveryDraftAutoSave() {
            ['first_name', 'last_name', 'email', 'phone'].forEach((id) => {
                const field = document.getElementById(id);
                if (!field) {
                    return;
                }

                field.addEventListener('input', saveDeliveryDraft);
                field.addEventListener('change', saveDeliveryDraft);
            });
        }

        function getDeliveryDetails() {
            return {
                first_name: document.getElementById('first_name').value.trim(),
                last_name: document.getElementById('last_name').value.trim(),
                email: document.getElementById('email').value.trim(),
                phone: document.getElementById('phone').value.trim(),
                location_name: document.getElementById('location_name').value.trim(),
                latitude: document.getElementById('latitude').value.trim(),
                longitude: document.getElementById('longitude').value.trim(),
            };
        }

        function clearFieldErrors() {
            document.querySelectorAll('.delivery-field').forEach((field) => {
                field.classList.remove('border-red-500', 'ring-red-500');
            });

            setLocationErrorState(false);
        }

        function markFieldErrors(errors) {
            clearFieldErrors();

            const locationErrorFields = ['location_name', 'latitude', 'longitude'];
            const hasLocationErrors = locationErrorFields.some((name) => (errors || {})[name]);

            if (hasLocationErrors) {
                setLocationErrorState(true);
            }

            Object.keys(errors || {}).forEach((fieldName) => {
                const field = document.getElementById(fieldName);
                if (field) {
                    field.classList.add('border-red-500', 'ring-red-500');
                }
            });
        }

        function resetGenerateButton() {
            const btn = document.getElementById('generate-qr-btn');
            const btnText = document.getElementById('generate-btn-text');
            const btnLoading = document.getElementById('generate-btn-loading');

            btn.disabled = false;
            btnText.classList.remove('hidden');
            btnLoading.classList.add('hidden');
            btnLoading.classList.remove('flex');
        }

        function validateDeliveryDetails(details) {
            const missingFields = [];

            if (!details.first_name) missingFields.push('First name');
            if (!details.last_name) missingFields.push('Last name');
            if (!details.email) missingFields.push('Email');
            if (!details.phone) missingFields.push('Phone');
            if (!details.location_name) missingFields.push('Location');
            if (!details.latitude || !details.longitude) missingFields.push('Map pin');

            if (missingFields.length > 0) {
                if (!details.location_name || !details.latitude || !details.longitude) {
                    setLocationErrorState(true);
                }
                return `Please complete your delivery details: ${missingFields.join(', ')}.`;
            }

            setLocationErrorState(false);

            return null;
        }

        function setLocationErrorState(hasError) {
            const status = document.getElementById('location-status');
            const wrapper = document.getElementById('delivery-map-wrapper');

            if (!status || !wrapper) {
                return;
            }

            if (hasError) {
                status.textContent = 'Location is required';
                status.className = 'text-xs font-semibold px-3 py-2 rounded-full bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300';
                wrapper.classList.remove('border-gray-200', 'dark:border-gray-700');
                wrapper.classList.add('border-red-500', 'dark:border-red-400');
                return;
            }

            const hasSelectedLocation = document.getElementById('location_name')?.value;

            if (hasSelectedLocation) {
                status.textContent = 'Location selected';
                status.className = 'text-xs font-semibold px-3 py-2 rounded-full bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300';
            } else {
                status.textContent = 'Location not selected';
                status.className = 'text-xs font-semibold px-3 py-2 rounded-full bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300';
            }

            wrapper.classList.remove('border-red-500', 'dark:border-red-400');
            wrapper.classList.add('border-gray-200', 'dark:border-gray-700');
        }

        /**
         * Step 1: Generate QR Code via AJAX
         */
        function generateQRCode() {
            const btn = document.getElementById('generate-qr-btn');
            const btnText = document.getElementById('generate-btn-text');
            const btnLoading = document.getElementById('generate-btn-loading');
            const deliveryDetails = getDeliveryDetails();
            const deliveryValidationMessage = validateDeliveryDetails(deliveryDetails);

            if (deliveryValidationMessage) {
                clearFieldErrors();
                showToast(deliveryValidationMessage, 'error');
                return;
            }

            clearFieldErrors();

            // Show loading
            btn.disabled = true;
            btnText.classList.add('hidden');
            btnLoading.classList.remove('hidden');
            btnLoading.classList.add('flex');

            fetch('{{ route("checkout.process") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    payment_method: 'bakong_khqr',
                    total: {{ $total }},
                    ...deliveryDetails
                })
            })
            .then(async response => {
                const data = await response.json();

                if (!response.ok) {
                    if (response.status === 422 && data.errors) {
                        markFieldErrors(data.errors);

                        const firstError = Object.values(data.errors).flat()[0] || 'Please complete your delivery details.';
                        throw new Error(firstError);
                    }

                    throw new Error(data.message || 'Failed to generate QR code.');
                }

                return data;
            })
            .then(data => {
                if (data.success) {
                    // Save state
                    paymentState.orderId = data.order_id;
                    paymentState.orderNumber = data.order_number;
                    paymentState.md5 = data.md5;
                    paymentState.expiresAt = new Date(data.expires_at);

                    // Show QR step
                    document.getElementById('step-generate').classList.add('hidden');
                    document.getElementById('step-qr').classList.remove('hidden');

                    // Render QR code
                    renderQRCode(data.qr_string);

                    // Update display
                    document.getElementById('qr-amount').textContent = data.total;
                    document.getElementById('qr-order-number').textContent = data.order_number;

                    // Start countdown timer
                    startTimer();

                    // Start polling
                    startPolling();
                } else {
                    showToast(data.message || 'Failed to generate QR code.', 'error');
                    resetGenerateButton();
                }
            })
            .catch(err => {
                console.error('Error generating QR:', err);
                showToast(err.message || 'An error occurred. Please try again.', 'error');
                resetGenerateButton();
            });
        }

        function setLocationState(lat, lng, label) {
            const latitude = Number(lat);
            const longitude = Number(lng);
            const locationLabel = `${label} (${latitude.toFixed(5)}, ${longitude.toFixed(5)})`;

            document.getElementById('location_name').value = locationLabel;
            document.getElementById('latitude').value = latitude.toFixed(6);
            document.getElementById('longitude').value = longitude.toFixed(6);
            document.getElementById('location-display').textContent = locationLabel;
            document.getElementById('location-coordinates').textContent = `${latitude.toFixed(6)}, ${longitude.toFixed(6)}`;
            document.getElementById('location-status').textContent = 'Location selected';
            document.getElementById('location-status').className = 'text-xs font-semibold px-3 py-2 rounded-full bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300';
            setLocationErrorState(false);

            if (!deliveryMarker && deliveryMap) {
                deliveryMarker = L.marker([latitude, longitude], { draggable: true }).addTo(deliveryMap);

                deliveryMarker.on('dragend', (event) => {
                    const position = event.target.getLatLng();
                    setLocationState(position.lat, position.lng, 'Pinned location');
                });
            } else if (deliveryMarker) {
                deliveryMarker.setLatLng([latitude, longitude]);
            }

            if (deliveryMap) {
                deliveryMap.setView([latitude, longitude], 15, { animate: true });
            }

            saveDeliveryDraft();
        }

        function initDeliveryMap() {
            if (deliveryMap || typeof L === 'undefined') {
                return;
            }

            deliveryMap = L.map('delivery-map', {
                zoomControl: true,
            }).setView(fallbackLocation, 13);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; OpenStreetMap contributors'
            }).addTo(deliveryMap);

            deliveryMap.on('click', (event) => {
                setLocationState(event.latlng.lat, event.latlng.lng, 'Pinned location');
            });

            deliveryMap.invalidateSize();

            // Try to center map on user's current position if permission is available.
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    (position) => {
                        const lat = position.coords.latitude;
                        const lng = position.coords.longitude;
                        deliveryMap.setView([lat, lng], 15, { animate: true });
                    },
                    () => {
                        // Keep fallback location when permission is denied/unavailable.
                    },
                    {
                        enableHighAccuracy: true,
                        timeout: 7000,
                        maximumAge: 60000,
                    }
                );
            }
        }

        function useCurrentLocation() {
            if (!navigator.geolocation) {
                showToast('Your browser does not support location sharing.', 'error');
                return;
            }

            showToast('Requesting your current location...', 'success');

            navigator.geolocation.getCurrentPosition(
                (position) => {
                    setLocationState(position.coords.latitude, position.coords.longitude, 'Current location');
                },
                () => {
                    showToast('Unable to access your location. Please enable permission or tap the map manually.', 'error');
                },
                {
                    enableHighAccuracy: true,
                    timeout: 10000,
                    maximumAge: 0,
                }
            );
        }

        function resetLocation() {
            document.getElementById('location_name').value = '';
            document.getElementById('latitude').value = '';
            document.getElementById('longitude').value = '';
            document.getElementById('location-display').textContent = 'No location selected yet';
            document.getElementById('location-coordinates').textContent = '--';
            document.getElementById('location-status').textContent = 'Location not selected';
            document.getElementById('location-status').className = 'text-xs font-semibold px-3 py-2 rounded-full bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300';
            setLocationErrorState(false);

            if (deliveryMap) {
                deliveryMap.setView(fallbackLocation, 13, { animate: true });
            }

            if (deliveryMarker) {
                deliveryMap.removeLayer(deliveryMarker);
                deliveryMarker = null;
            }

            saveDeliveryDraft();
        }

        /**
         * Render QR code in the container
         */
        function renderQRCode(qrString) {
            const container = document.getElementById('qr-code-container');
            container.innerHTML = '';

            try {
                new QRCode(container, {
                    text: qrString,
                    width: 240,
                    height: 240,
                    colorDark : "#1a1a2e",
                    colorLight : "#ffffff",
                    correctLevel : QRCode.CorrectLevel.M
                });
            } catch (error) {
                console.error('QR render error', error);
                container.innerHTML = '<p class="text-red-500 p-4">Failed to render QR code</p>';
            }
        }

        /**
         * Start countdown timer
         */
        function startTimer() {
            const display = document.getElementById('timer-display');

            paymentState.timerInterval = setInterval(() => {
                const now = new Date();
                const diff = paymentState.expiresAt - now;

                if (diff <= 0) {
                    clearInterval(paymentState.timerInterval);
                    stopPolling();
                    showExpiredState();
                    return;
                }

                const minutes = Math.floor(diff / 60000);
                const seconds = Math.floor((diff % 60000) / 1000);
                display.textContent = `${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;

                // Change color when less than 2 minutes
                if (diff < 120000) {
                    display.classList.add('!bg-red-500/30');
                }
            }, 1000);
        }

        /**
         * Start polling for payment status
         */
        function startPolling() {
            if (paymentState.isPolling) return;
            paymentState.isPolling = true;

            paymentState.pollingInterval = setInterval(() => {
                checkPaymentStatus();
            }, 3000); // Poll every 3 seconds
        }

        /**
         * Stop polling
         */
        function stopPolling() {
            paymentState.isPolling = false;
            if (paymentState.pollingInterval) {
                clearInterval(paymentState.pollingInterval);
                paymentState.pollingInterval = null;
            }
        }

        /**
         * Check payment status via AJAX
         */
        function checkPaymentStatus() {
            fetch('{{ route("checkout.payment.status") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    md5: paymentState.md5,
                    order_id: paymentState.orderId
                })
            })
            .then(r => r.json())
            .then(data => {
                if (data.paid) {
                    // Payment confirmed!
                    stopPolling();
                    clearInterval(paymentState.timerInterval);
                    showSuccessState(data);
                } else if (data.expired) {
                    stopPolling();
                    clearInterval(paymentState.timerInterval);
                    showExpiredState();
                }
                // Otherwise, keep polling
            })
            .catch(err => {
                console.error('Polling error:', err);
                // Continue polling on error
            });
        }

        /**
         * Show expired state
         */
        function showExpiredState() {
            document.getElementById('step-qr').classList.add('hidden');
            document.getElementById('step-expired').classList.remove('hidden');
        }

        /**
         * Show success state with confetti
         */
        function showSuccessState(data) {
            // Hide QR, show success
            document.getElementById('step-qr').classList.add('hidden');
            document.getElementById('step-success').classList.remove('hidden');

            // Update success details
            document.getElementById('success-order-number').textContent = data.order_number || paymentState.orderNumber;
            document.getElementById('success-total').textContent = '$' + (data.total || '{{ number_format($total, 2) }}');

            // Launch confetti!
            createConfetti();

            // Clear saved delivery draft after successful payment
            localStorage.removeItem(DELIVERY_DRAFT_KEY);

            // Auto redirect after 10 seconds
            setTimeout(() => {
                window.location.href = '{{ route("products.index") }}';
            }, 10000);
        }

        /**
         * Confetti animation
         */
        function createConfetti() {
            const colors = ['#10b981', '#34d399', '#6ee7b7', '#fbbf24', '#f59e0b', '#a78bfa', '#818cf8', '#fb7185'];
            const count = 80;

            for (let i = 0; i < count; i++) {
                setTimeout(() => {
                    const confetti = document.createElement('div');
                    const size = Math.random() * 8 + 4;
                    confetti.style.cssText = `
                        position: fixed;
                        width: ${size}px;
                        height: ${size}px;
                        background: ${colors[Math.floor(Math.random() * colors.length)]};
                        left: ${Math.random() * 100}vw;
                        top: -10px;
                        z-index: 1000;
                        border-radius: ${Math.random() > 0.5 ? '50%' : '2px'};
                        pointer-events: none;
                        animation: confettiFall ${Math.random() * 2 + 3}s linear forwards;
                        transform: rotate(${Math.random() * 360}deg);
                    `;
                    document.body.appendChild(confetti);
                    setTimeout(() => confetti.remove(), 5000);
                }, i * 40);
            }
        }

        /**
         * Toast notification
         */
        function showToast(message, type = 'success') {
            const toast = document.createElement('div');
            const bgColor = type === 'success' ? 'bg-emerald-500' : 'bg-red-500';
            toast.className = `fixed top-4 right-4 z-50 max-w-sm ${bgColor} text-white p-4 rounded-xl shadow-2xl transform transition-all duration-500 translate-x-full`;
            toast.innerHTML = `
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        ${type === 'success'
                            ? '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>'
                            : '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>'
                        }
                    </svg>
                    <span class="font-medium text-sm">${message}</span>
                </div>
            `;
            document.body.appendChild(toast);
            setTimeout(() => toast.classList.remove('translate-x-full'), 100);
            setTimeout(() => {
                toast.classList.add('translate-x-full');
                setTimeout(() => toast.remove(), 500);
            }, 4000);
        }

        // Cleanup on page unload
        window.addEventListener('beforeunload', () => {
            stopPolling();
            if (paymentState.timerInterval) clearInterval(paymentState.timerInterval);
        });

        document.addEventListener('DOMContentLoaded', function() {
            initDeliveryMap();
            restoreDeliveryDraft();
            setupDeliveryDraftAutoSave();
        });
    </script>
    @endpush
</x-app-layout>
