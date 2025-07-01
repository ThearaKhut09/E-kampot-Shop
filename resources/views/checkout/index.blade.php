<x-app-layout>
    <x-slot name="title">Checkout - E-Kampot Shop</x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Header -->
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100 mb-2">
                Secure Checkout
            </h1>
            <p class="text-gray-600 dark:text-gray-400">
                Review your order and enter your payment details
            </p>
        </div>

        <!-- Error Messages -->
        @if($errors->any() || session('error'))
        <div class="mb-8 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-700 rounded-lg p-4">
            <div class="flex items-start">
                <svg class="w-5 h-5 text-red-600 dark:text-red-400 mt-0.5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <div>
                    <h3 class="text-sm font-medium text-red-800 dark:text-red-200 mb-2">
                        There were some problems with your submission
                    </h3>
                    <ul class="text-sm text-red-700 dark:text-red-300 space-y-1">
                        @if(session('error'))
                        <li>{{ session('error') }}</li>
                        @endif
                        @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
        @endif

        <form method="POST" action="{{ route('checkout.store') }}" id="checkout-form">
            @csrf

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Left Column - Forms -->
                <div class="lg:col-span-2 space-y-8">

                    <!-- Billing Information -->
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
                        <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-6">
                            Billing Information
                        </h2>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="first_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    First Name *
                                </label>
                                <input type="text"
                                       id="first_name"
                                       name="first_name"
                                       value="{{ old('first_name', Auth::user()->first_name ?? '') }}"
                                       required
                                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white">
                                @error('first_name')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="last_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Last Name *
                                </label>
                                <input type="text"
                                       id="last_name"
                                       name="last_name"
                                       value="{{ old('last_name', Auth::user()->last_name ?? '') }}"
                                       required
                                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white">
                                @error('last_name')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Email Address *
                                </label>
                                <input type="email"
                                       id="email"
                                       name="email"
                                       value="{{ old('email', Auth::user()->email ?? '') }}"
                                       required
                                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white">
                                @error('email')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Phone Number *
                                </label>
                                <input type="tel"
                                       id="phone"
                                       name="phone"
                                       value="{{ old('phone', Auth::user()->phone ?? '') }}"
                                       required
                                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white">
                                @error('phone')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="md:col-span-2">
                                <label for="address" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Address *
                                </label>
                                <input type="text"
                                       id="address"
                                       name="address"
                                       value="{{ old('address') }}"
                                       required
                                       placeholder="Street address, apartment, suite, etc."
                                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white">
                                @error('address')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="city" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    City *
                                </label>
                                <input type="text"
                                       id="city"
                                       name="city"
                                       value="{{ old('city') }}"
                                       required
                                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white">
                                @error('city')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="postal_code" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Postal Code *
                                </label>
                                <input type="text"
                                       id="postal_code"
                                       name="postal_code"
                                       value="{{ old('postal_code') }}"
                                       required
                                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white">
                                @error('postal_code')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="md:col-span-2">
                                <label for="country" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Country *
                                </label>
                                <select id="country"
                                        name="country"
                                        required
                                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white">
                                    <option value="">Select Country</option>
                                    <option value="Cambodia" {{ old('country') == 'Cambodia' ? 'selected' : '' }}>Cambodia</option>
                                    <option value="Thailand" {{ old('country') == 'Thailand' ? 'selected' : '' }}>Thailand</option>
                                    <option value="Vietnam" {{ old('country') == 'Vietnam' ? 'selected' : '' }}>Vietnam</option>
                                    <option value="United States" {{ old('country') == 'United States' ? 'selected' : '' }}>United States</option>
                                    <option value="Canada" {{ old('country') == 'Canada' ? 'selected' : '' }}>Canada</option>
                                    <option value="United Kingdom" {{ old('country') == 'United Kingdom' ? 'selected' : '' }}>United Kingdom</option>
                                    <option value="Australia" {{ old('country') == 'Australia' ? 'selected' : '' }}>Australia</option>
                                </select>
                                @error('country')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Shipping Information -->
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
                        <div class="flex items-center justify-between mb-6">
                            <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">
                                Shipping Information
                            </h2>
                            <label class="flex items-center">
                                <input type="checkbox"
                                       id="shipping_different"
                                       name="shipping_different"
                                       value="1"
                                       {{ old('shipping_different') ? 'checked' : '' }}
                                       onchange="toggleShippingForm()"
                                       class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded">
                                <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">
                                    Ship to different address
                                </span>
                            </label>
                        </div>

                        <div id="shipping-form" class="hidden">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="shipping_first_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        First Name *
                                    </label>
                                    <input type="text"
                                           id="shipping_first_name"
                                           name="shipping_first_name"
                                           value="{{ old('shipping_first_name') }}"
                                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white">
                                    @error('shipping_first_name')
                                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="shipping_last_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Last Name *
                                    </label>
                                    <input type="text"
                                           id="shipping_last_name"
                                           name="shipping_last_name"
                                           value="{{ old('shipping_last_name') }}"
                                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white">
                                    @error('shipping_last_name')
                                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="md:col-span-2">
                                    <label for="shipping_address" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Address *
                                    </label>
                                    <input type="text"
                                           id="shipping_address"
                                           name="shipping_address"
                                           value="{{ old('shipping_address') }}"
                                           placeholder="Street address, apartment, suite, etc."
                                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white">
                                    @error('shipping_address')
                                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="shipping_city" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        City *
                                    </label>
                                    <input type="text"
                                           id="shipping_city"
                                           name="shipping_city"
                                           value="{{ old('shipping_city') }}"
                                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white">
                                    @error('shipping_city')
                                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="shipping_postal_code" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Postal Code *
                                    </label>
                                    <input type="text"
                                           id="shipping_postal_code"
                                           name="shipping_postal_code"
                                           value="{{ old('shipping_postal_code') }}"
                                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white">
                                    @error('shipping_postal_code')
                                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="md:col-span-2">
                                    <label for="shipping_country" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Country *
                                    </label>
                                    <select id="shipping_country"
                                            name="shipping_country"
                                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white">
                                        <option value="">Select Country</option>
                                        <option value="Cambodia" {{ old('shipping_country') == 'Cambodia' ? 'selected' : '' }}>Cambodia</option>
                                        <option value="Thailand" {{ old('shipping_country') == 'Thailand' ? 'selected' : '' }}>Thailand</option>
                                        <option value="Vietnam" {{ old('shipping_country') == 'Vietnam' ? 'selected' : '' }}>Vietnam</option>
                                        <option value="United States" {{ old('shipping_country') == 'United States' ? 'selected' : '' }}>United States</option>
                                        <option value="Canada" {{ old('shipping_country') == 'Canada' ? 'selected' : '' }}>Canada</option>
                                        <option value="United Kingdom" {{ old('shipping_country') == 'United Kingdom' ? 'selected' : '' }}>United Kingdom</option>
                                        <option value="Australia" {{ old('shipping_country') == 'Australia' ? 'selected' : '' }}>Australia</option>
                                    </select>
                                    @error('shipping_country')
                                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Method -->
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
                        <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-6">
                            Payment Method
                        </h2>

                        <div class="space-y-4">
                            <!-- Credit Card -->
                            <label class="flex items-center p-4 border border-gray-300 dark:border-gray-600 rounded-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700">
                                <input type="radio"
                                       name="payment_method"
                                       value="credit_card"
                                       {{ old('payment_method', 'credit_card') == 'credit_card' ? 'checked' : '' }}
                                       onchange="showPaymentDetails('credit_card')"
                                       class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300">
                                <div class="ml-3">
                                    <div class="text-sm font-medium text-gray-900 dark:text-gray-100">Credit/Debit Card</div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400">Visa, Mastercard, American Express</div>
                                </div>
                                <div class="ml-auto">
                                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                                    </svg>
                                </div>
                            </label>

                            <!-- PayPal -->
                            <label class="flex items-center p-4 border border-gray-300 dark:border-gray-600 rounded-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700">
                                <input type="radio"
                                       name="payment_method"
                                       value="paypal"
                                       {{ old('payment_method') == 'paypal' ? 'checked' : '' }}
                                       onchange="showPaymentDetails('paypal')"
                                       class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300">
                                <div class="ml-3">
                                    <div class="text-sm font-medium text-gray-900 dark:text-gray-100">PayPal</div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400">Pay with your PayPal account</div>
                                </div>
                                <div class="ml-auto">
                                    <svg class="w-8 h-8 text-blue-600" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M7.076 21.337H2.47a.641.641 0 0 1-.633-.74L4.944.901C5.026.382 5.474 0 5.998 0h7.46c2.57 0 4.578.543 5.69 1.81 1.01 1.15 1.304 2.42 1.012 4.287-.023.143-.047.288-.077.437-.983 5.05-4.349 6.797-8.647 6.797h-2.19c-.524 0-.968.382-1.05.9l-1.12 7.106zm14.146-14.42a3.35 3.35 0 0 0-.607-.421c-.315-.168-.652-.31-1.015-.421C18.556 5.731 17.68 5.5 16.68 5.5h-3.18c-.524 0-.968.382-1.05.9l-1.12 7.106h2.19c4.298 0 7.664-1.747 8.647-6.797.03-.15.054-.294.077-.437.29-1.867-.003-3.137-1.012-4.287z"/>
                                    </svg>
                                </div>
                            </label>

                            <!-- Bank Transfer -->
                            <label class="flex items-center p-4 border border-gray-300 dark:border-gray-600 rounded-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700">
                                <input type="radio"
                                       name="payment_method"
                                       value="bank_transfer"
                                       {{ old('payment_method') == 'bank_transfer' ? 'checked' : '' }}
                                       onchange="showPaymentDetails('bank_transfer')"
                                       class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300">
                                <div class="ml-3">
                                    <div class="text-sm font-medium text-gray-900 dark:text-gray-100">Bank Transfer</div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400">Direct bank transfer</div>
                                </div>
                                <div class="ml-auto">
                                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21v-4m0 0V5a2 2 0 012-2h6.5l1 1H21l-3 6 3 6h-8.5l-1-1H5a2 2 0 00-2 2zm9-13.5V9"></path>
                                    </svg>
                                </div>
                            </label>

                            <!-- Cash on Delivery -->
                            <label class="flex items-center p-4 border border-gray-300 dark:border-gray-600 rounded-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700">
                                <input type="radio"
                                       name="payment_method"
                                       value="cash_on_delivery"
                                       {{ old('payment_method') == 'cash_on_delivery' ? 'checked' : '' }}
                                       onchange="showPaymentDetails('cash_on_delivery')"
                                       class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300">
                                <div class="ml-3">
                                    <div class="text-sm font-medium text-gray-900 dark:text-gray-100">Cash on Delivery</div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400">Pay when your order is delivered</div>
                                </div>
                                <div class="ml-auto">
                                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                    </svg>
                                </div>
                            </label>
                        </div>

                        @error('payment_method')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror

                        <!-- Credit Card Details -->
                        <div id="credit_card_details" class="mt-6 {{ old('payment_method', 'credit_card') == 'credit_card' ? '' : 'hidden' }}">
                            <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Card Details</h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div class="md:col-span-2">
                                        <label for="card_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                            Name on Card *
                                        </label>
                                        <input type="text"
                                               id="card_name"
                                               name="card_name"
                                               value="{{ old('card_name') }}"
                                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-600 dark:text-white">
                                        @error('card_name')
                                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div class="md:col-span-2">
                                        <label for="card_number" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                            Card Number *
                                        </label>
                                        <input type="text"
                                               id="card_number"
                                               name="card_number"
                                               value="{{ old('card_number') }}"
                                               placeholder="1234 5678 9012 3456"
                                               maxlength="19"
                                               onchange="formatCardNumber(this)"
                                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-600 dark:text-white">
                                        @error('card_number')
                                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div>
                                        <label for="card_expiry" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                            Expiry Date *
                                        </label>
                                        <input type="text"
                                               id="card_expiry"
                                               name="card_expiry"
                                               value="{{ old('card_expiry') }}"
                                               placeholder="MM/YY"
                                               maxlength="5"
                                               onchange="formatExpiryDate(this)"
                                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-600 dark:text-white">
                                        @error('card_expiry')
                                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div>
                                        <label for="card_cvc" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                            CVC *
                                        </label>
                                        <input type="text"
                                               id="card_cvc"
                                               name="card_cvc"
                                               value="{{ old('card_cvc') }}"
                                               placeholder="123"
                                               maxlength="4"
                                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-600 dark:text-white">
                                        @error('card_cvc')
                                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Bank Transfer Details -->
                        <div id="bank_transfer_details" class="mt-6 hidden">
                            <div class="bg-blue-50 dark:bg-blue-900/20 p-4 rounded-lg">
                                <h3 class="text-lg font-medium text-blue-900 dark:text-blue-200 mb-2">Bank Transfer Instructions</h3>
                                <p class="text-sm text-blue-800 dark:text-blue-300 mb-2">
                                    Please transfer the total amount to the following bank account:
                                </p>
                                <div class="text-sm text-blue-800 dark:text-blue-300">
                                    <p><strong>Bank:</strong> E-Kampot Bank</p>
                                    <p><strong>Account Name:</strong> E-Kampot Shop Ltd.</p>
                                    <p><strong>Account Number:</strong> 1234567890</p>
                                    <p><strong>SWIFT Code:</strong> EKMPKH22</p>
                                </div>
                            </div>
                        </div>

                        <!-- Cash on Delivery Details -->
                        <div id="cash_on_delivery_details" class="mt-6 hidden">
                            <div class="bg-green-50 dark:bg-green-900/20 p-4 rounded-lg">
                                <h3 class="text-lg font-medium text-green-900 dark:text-green-200 mb-2">Cash on Delivery</h3>
                                <p class="text-sm text-green-800 dark:text-green-300">
                                    You will pay for your order when it is delivered to your doorstep. Please have the exact amount ready.
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Order Notes -->
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
                        <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-4">
                            Order Notes (Optional)
                        </h2>
                        <textarea name="notes"
                                  id="notes"
                                  rows="3"
                                  placeholder="Special instructions for your order..."
                                  class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white">{{ old('notes') }}</textarea>
                    </div>

                    <!-- Terms and Conditions -->
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
                        <label class="flex items-start">
                            <input type="checkbox"
                                   name="terms"
                                   value="1"
                                   required
                                   class="mt-1 h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded">
                            <span class="ml-3 text-sm text-gray-700 dark:text-gray-300">
                                I agree to the <a href="#" class="text-primary-600 hover:text-primary-500">Terms and Conditions</a>
                                and <a href="#" class="text-primary-600 hover:text-primary-500">Privacy Policy</a> *
                            </span>
                        </label>
                        @error('terms')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Right Column - Order Summary -->
                <div class="lg:col-span-1">
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 sticky top-6">
                        <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-6">
                            Order Summary
                        </h2>

                        <!-- Order Items -->
                        <div class="space-y-4 mb-6">
                            @foreach($cartItems as $item)
                                <div class="flex items-center space-x-3">
                                    <div class="flex-shrink-0">
                                        @if($item->product->image)
                                            <img src="{{ Storage::url($item->product->image) }}"
                                                 alt="{{ $item->product->name }}"
                                                 class="w-12 h-12 object-cover rounded">
                                        @else
                                            <div class="w-12 h-12 bg-gray-200 dark:bg-gray-600 rounded flex items-center justify-center">
                                                <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                </svg>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <h4 class="text-sm font-medium text-gray-900 dark:text-gray-100 truncate">
                                            {{ $item->product->name }}
                                        </h4>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">
                                            Qty: {{ $item->quantity }}
                                        </p>
                                    </div>
                                    <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                        ${{ number_format($item->quantity * $item->product->current_price, 2) }}
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Totals -->
                        <div class="border-t border-gray-200 dark:border-gray-700 pt-4 space-y-2">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600 dark:text-gray-400">Subtotal</span>
                                <span class="text-gray-900 dark:text-gray-100">${{ number_format($subtotal, 2) }}</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600 dark:text-gray-400">Shipping</span>
                                <span class="text-gray-900 dark:text-gray-100">
                                    @if($shippingAmount > 0)
                                        ${{ number_format($shippingAmount, 2) }}
                                    @else
                                        Free
                                    @endif
                                </span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600 dark:text-gray-400">Tax</span>
                                <span class="text-gray-900 dark:text-gray-100">${{ number_format($taxAmount, 2) }}</span>
                            </div>
                            <div class="border-t border-gray-200 dark:border-gray-700 pt-2">
                                <div class="flex justify-between">
                                    <span class="text-lg font-semibold text-gray-900 dark:text-gray-100">Total</span>
                                    <span class="text-lg font-semibold text-gray-900 dark:text-gray-100">${{ number_format($total, 2) }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Place Order Button -->
                        <button type="submit"
                                id="place-order-btn"
                                class="w-full mt-6 bg-primary-600 hover:bg-primary-700 text-white font-semibold py-3 px-6 rounded-lg transition-all duration-200 shadow-lg hover:shadow-xl disabled:opacity-50 disabled:cursor-not-allowed transform hover:-translate-y-0.5">
                            <span id="btn-text" class="flex items-center justify-center">
                                <svg id="btn-icon" class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                </svg>
                                Place Order
                            </span>
                            <span id="btn-loading" class="hidden flex items-center justify-center">
                                <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Processing Payment...
                            </span>
                        </button>

                        <!-- Payment Processing Overlay -->
                        <div id="payment-overlay" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
                            <div class="bg-white dark:bg-gray-800 rounded-lg p-8 max-w-md mx-4 text-center">
                                <div class="inline-flex items-center justify-center w-16 h-16 bg-blue-100 dark:bg-blue-900 rounded-full mb-4">
                                    <svg class="animate-spin w-8 h-8 text-blue-600 dark:text-blue-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                </div>
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-2">Processing Your Payment</h3>
                                <p class="text-gray-600 dark:text-gray-400">Please wait while we securely process your payment...</p>
                                <div class="mt-4">
                                    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                        <div class="bg-blue-600 h-2 rounded-full animate-pulse" style="width: 45%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4 text-center">
                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                🔒 Your payment information is secure and encrypted
                            </p>
                            <!-- Demo Button for Testing (Development Only) -->
                            @if(config('app.debug'))
                            <button type="button"
                                    onclick="showFakePaymentSuccess()"
                                    class="mt-2 text-xs text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-200 underline">
                                🧪 Demo: Show Success Message
                            </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    @push('scripts')
    <script>
        // Toggle shipping form visibility
        function toggleShippingForm() {
            const checkbox = document.getElementById('shipping_different');
            const form = document.getElementById('shipping-form');

            if (checkbox.checked) {
                form.classList.remove('hidden');
            } else {
                form.classList.add('hidden');
            }
        }

        // Show payment details based on selected method
        function showPaymentDetails(method) {
            // Hide all payment details
            document.getElementById('credit_card_details').classList.add('hidden');
            document.getElementById('bank_transfer_details').classList.add('hidden');
            document.getElementById('cash_on_delivery_details').classList.add('hidden');

            // Show selected payment method details
            if (method === 'credit_card') {
                document.getElementById('credit_card_details').classList.remove('hidden');
            } else if (method === 'bank_transfer') {
                document.getElementById('bank_transfer_details').classList.remove('hidden');
            } else if (method === 'cash_on_delivery') {
                document.getElementById('cash_on_delivery_details').classList.remove('hidden');
            }
        }

        // Format card number with spaces
        function formatCardNumber(input) {
            let value = input.value.replace(/\s/g, '').replace(/[^0-9]/gi, '');
            let formattedValue = value.match(/.{1,4}/g)?.join(' ') || value;
            input.value = formattedValue;
        }

        // Format expiry date
        function formatExpiryDate(input) {
            let value = input.value.replace(/\D/g, '');
            if (value.length >= 2) {
                value = value.substring(0, 2) + '/' + value.substring(2, 4);
            }
            input.value = value;
        }

        // Add fake payment success demo for instant feedback
        function showFakePaymentSuccess() {
            // Create success overlay
            const successOverlay = document.createElement('div');
            successOverlay.className = 'fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center z-50';
            successOverlay.innerHTML = `
                <div class="bg-white dark:bg-gray-800 rounded-lg p-8 max-w-md mx-4 text-center transform scale-0 transition-transform duration-300">
                    <div class="inline-flex items-center justify-center w-16 h-16 bg-green-100 dark:bg-green-900 rounded-full mb-4">
                        <svg class="w-8 h-8 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100 mb-2">Payment Successful!</h3>
                    <p class="text-gray-600 dark:text-gray-400 mb-4">Your payment has been processed successfully.</p>
                    <p class="text-sm text-green-600 dark:text-green-400 font-medium">Transaction ID: TXN_${Date.now()}</p>
                    <button onclick="this.parentElement.parentElement.remove()" class="mt-4 bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg transition-colors">
                        Continue
                    </button>
                </div>
            `;

            document.body.appendChild(successOverlay);

            // Animate in
            setTimeout(() => {
                successOverlay.querySelector('div > div').classList.add('scale-100');
                successOverlay.querySelector('div > div').classList.remove('scale-0');
            }, 100);

            // Auto remove after 5 seconds
            setTimeout(() => {
                if (successOverlay.parentElement) {
                    successOverlay.remove();
                }
            }, 5000);
        }

        // Form submission handling
        document.getElementById('checkout-form').addEventListener('submit', function(e) {
            const button = document.getElementById('place-order-btn');
            const btnText = document.getElementById('btn-text');
            const btnLoading = document.getElementById('btn-loading');
            const overlay = document.getElementById('payment-overlay');

            // Disable button and show loading state
            button.disabled = true;
            btnText.classList.add('hidden');
            btnLoading.classList.remove('hidden');

            // Show payment processing overlay after a short delay
            setTimeout(() => {
                overlay.classList.remove('hidden');
            }, 500);

            // Simulate successful payment for demo (remove in production)
            setTimeout(() => {
                // Hide payment processing overlay
                overlay.classList.add('hidden');

                // Show fake payment success message
                showFakePaymentSuccess();

                // Re-enable button and show success state
                button.disabled = false;
                btnText.classList.remove('hidden');
                btnLoading.classList.add('hidden');
            }, 3000);
        });

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            // Show appropriate payment details if form has errors and old input
            const selectedPaymentMethod = document.querySelector('input[name="payment_method"]:checked');
            if (selectedPaymentMethod) {
                showPaymentDetails(selectedPaymentMethod.value);
            }

            // Show shipping form if checkbox was checked
            if (document.getElementById('shipping_different').checked) {
                toggleShippingForm();
            }
        });
    </script>
    @endpush
</x-app-layout>
