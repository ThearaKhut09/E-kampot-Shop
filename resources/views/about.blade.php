<x-app-layout>
    <x-slot name="title">About Us - E-Kampot Shop</x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <!-- Hero Section -->
        <div class="text-center mb-16">
            <h1 class="text-4xl font-bold text-gray-900 dark:text-gray-100 mb-4">About E-Kampot Shop</h1>
            <p class="text-xl text-gray-600 dark:text-gray-300 max-w-3xl mx-auto">
                Your trusted online destination for quality products at unbeatable prices
            </p>
        </div>

        <!-- Story Section -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center mb-16">
            <div>
                <h2 class="text-3xl font-bold text-gray-900 dark:text-gray-100 mb-6">Our Story</h2>
                <div class="space-y-4 text-gray-600 dark:text-gray-300">
                    <p>
                        Founded in the heart of Cambodia, E-Kampot Shop began as a small family business with a simple mission:
                        to provide high-quality products to customers across the region at affordable prices.
                    </p>
                    <p>
                        What started as a single store in Kampot has grown into a comprehensive e-commerce platform,
                        serving thousands of satisfied customers with everything from electronics and fashion to home goods and accessories.
                    </p>
                    <p>
                        We believe that everyone deserves access to great products without breaking the bank, and we work
                        tirelessly to source the best items from trusted suppliers around the world.
                    </p>
                </div>
            </div>
            <div class="bg-gray-200 dark:bg-gray-700 rounded-lg h-96 flex items-center justify-center">
                <div class="text-center text-gray-500 dark:text-gray-400">
                    <svg class="w-24 h-24 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                    <p class="text-lg font-medium">Our Store Image</p>
                </div>
            </div>
        </div>

        <!-- Values Section -->
        <div class="mb-16">
            <h2 class="text-3xl font-bold text-gray-900 dark:text-gray-100 text-center mb-12">Our Values</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="text-center">
                    <div class="bg-primary-100 dark:bg-primary-900/20 rounded-full w-20 h-20 flex items-center justify-center mx-auto mb-4">
                        <svg class="w-10 h-10 text-primary-600 dark:text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-2">Quality First</h3>
                    <p class="text-gray-600 dark:text-gray-300">
                        We carefully select every product to ensure it meets our high standards for quality and durability.
                    </p>
                </div>
                <div class="text-center">
                    <div class="bg-primary-100 dark:bg-primary-900/20 rounded-full w-20 h-20 flex items-center justify-center mx-auto mb-4">
                        <svg class="w-10 h-10 text-primary-600 dark:text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-2">Fair Pricing</h3>
                    <p class="text-gray-600 dark:text-gray-300">
                        Our competitive prices ensure you get the best value for your money without compromising on quality.
                    </p>
                </div>
                <div class="text-center">
                    <div class="bg-primary-100 dark:bg-primary-900/20 rounded-full w-20 h-20 flex items-center justify-center mx-auto mb-4">
                        <svg class="w-10 h-10 text-primary-600 dark:text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-2">Customer Care</h3>
                    <p class="text-gray-600 dark:text-gray-300">
                        Your satisfaction is our priority. We're here to help with any questions or concerns you may have.
                    </p>
                </div>
            </div>
        </div>

        <!-- Stats Section -->
        <div class="bg-primary-50 dark:bg-gray-800 rounded-xl p-8 mb-16">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8">
                <div class="text-center">
                    <div class="text-3xl font-bold text-primary-600 dark:text-primary-400 mb-2">10,000+</div>
                    <div class="text-gray-600 dark:text-gray-300">Happy Customers</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-bold text-primary-600 dark:text-primary-400 mb-2">5,000+</div>
                    <div class="text-gray-600 dark:text-gray-300">Products</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-bold text-primary-600 dark:text-primary-400 mb-2">50+</div>
                    <div class="text-gray-600 dark:text-gray-300">Categories</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-bold text-primary-600 dark:text-primary-400 mb-2">24/7</div>
                    <div class="text-gray-600 dark:text-gray-300">Support</div>
                </div>
            </div>
        </div>

        <!-- Team Section -->
        <div class="text-center">
            <h2 class="text-3xl font-bold text-gray-900 dark:text-gray-100 mb-6">Why Choose Us?</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="p-6 bg-white dark:bg-gray-800 rounded-lg shadow-md">
                    <div class="text-primary-600 dark:text-primary-400 mb-4">
                        <svg class="w-12 h-12 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                        </svg>
                    </div>
                    <h3 class="font-semibold text-gray-900 dark:text-gray-100 mb-2">Fast Shipping</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-300">Quick and reliable delivery to your doorstep</p>
                </div>
                <div class="p-6 bg-white dark:bg-gray-800 rounded-lg shadow-md">
                    <div class="text-primary-600 dark:text-primary-400 mb-4">
                        <svg class="w-12 h-12 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                        </svg>
                    </div>
                    <h3 class="font-semibold text-gray-900 dark:text-gray-100 mb-2">Secure Payment</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-300">Your payment information is always protected</p>
                </div>
                <div class="p-6 bg-white dark:bg-gray-800 rounded-lg shadow-md">
                    <div class="text-primary-600 dark:text-primary-400 mb-4">
                        <svg class="w-12 h-12 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                    </div>
                    <h3 class="font-semibold text-gray-900 dark:text-gray-100 mb-2">Easy Returns</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-300">Hassle-free returns within 30 days</p>
                </div>
                <div class="p-6 bg-white dark:bg-gray-800 rounded-lg shadow-md">
                    <div class="text-primary-600 dark:text-primary-400 mb-4">
                        <svg class="w-12 h-12 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192L5.636 18.364M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                    </div>
                    <h3 class="font-semibold text-gray-900 dark:text-gray-100 mb-2">24/7 Support</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-300">Round-the-clock customer assistance</p>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
