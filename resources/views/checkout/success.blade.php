<x-app-layout>
    <x-slot name="title">Order Confirmation - E-Kampot Shop</x-slot>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Success Header -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-green-100 dark:bg-green-900 rounded-full mb-4">
                <svg class="w-8 h-8 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100 mb-2">
                Order Confirmed!
            </h1>
            <p class="text-gray-600 dark:text-gray-400">
                Thank you for your order. We'll send you a confirmation email shortly.
            </p>
        </div>

        <!-- Order Details -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden mb-8">
            <div class="px-6 py-4 bg-gray-50 dark:bg-gray-700 border-b border-gray-200 dark:border-gray-600">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">
                    Order Details
                </h2>
            </div>

            <div class="px-6 py-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Order Number</h3>
                        <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $order->order_number }}</p>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Order Date</h3>
                        <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $order->created_at->format('M j, Y g:i A') }}</p>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Payment Method</h3>
                        <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ ucfirst(str_replace('_', ' ', $order->payment_method)) }}</p>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Order Status</h3>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                            @if($order->status === 'pending') bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200
                            @elseif($order->status === 'processing') bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200
                            @else bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200 @endif">
                            {{ ucfirst($order->status) }}
                        </span>
                    </div>
                </div>

                <!-- Order Items -->
                <div class="border-t border-gray-200 dark:border-gray-700 pt-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Order Items</h3>
                    <div class="space-y-4">
                        @foreach($order->items as $item)
                            <div class="flex items-center space-x-4 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                <div class="flex-shrink-0">
                                    @if($item->product && $item->product->image)
                                        <img src="{{ Storage::url($item->product->image) }}"
                                             alt="{{ $item->product_name }}"
                                             class="w-16 h-16 object-cover rounded">
                                    @else
                                        <div class="w-16 h-16 bg-gray-200 dark:bg-gray-600 rounded flex items-center justify-center">
                                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                            </svg>
                                        </div>
                                    @endif
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h4 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                                        {{ $item->product_name }}
                                    </h4>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">
                                        SKU: {{ $item->product_sku }}
                                    </p>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">
                                        Quantity: {{ $item->quantity }}
                                    </p>
                                    @if($item->product_options)
                                        <p class="text-sm text-gray-500 dark:text-gray-400">
                                            Options: {{ $item->product_options }}
                                        </p>
                                    @endif
                                </div>
                                <div class="text-right">
                                    <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                                        ${{ number_format($item->price, 2) }}
                                    </p>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">
                                        Total: ${{ number_format($item->total, 2) }}
                                    </p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Order Totals -->
                <div class="border-t border-gray-200 dark:border-gray-700 pt-6 mt-6">
                    <div class="max-w-md ml-auto">
                        <div class="space-y-2">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600 dark:text-gray-400">Subtotal</span>
                                <span class="text-gray-900 dark:text-gray-100">${{ number_format($order->subtotal, 2) }}</span>
                            </div>
                            @if($order->tax_amount > 0)
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600 dark:text-gray-400">Tax</span>
                                    <span class="text-gray-900 dark:text-gray-100">${{ number_format($order->tax_amount, 2) }}</span>
                                </div>
                            @endif
                            @if($order->shipping_amount > 0)
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600 dark:text-gray-400">Shipping</span>
                                    <span class="text-gray-900 dark:text-gray-100">${{ number_format($order->shipping_amount, 2) }}</span>
                                </div>
                            @else
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600 dark:text-gray-400">Shipping</span>
                                    <span class="text-green-600 dark:text-green-400 font-medium">Free</span>
                                </div>
                            @endif
                            @if($order->discount_amount > 0)
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600 dark:text-gray-400">Discount</span>
                                    <span class="text-red-600 dark:text-red-400">-${{ number_format($order->discount_amount, 2) }}</span>
                                </div>
                            @endif
                            <div class="border-t border-gray-200 dark:border-gray-700 pt-2">
                                <div class="flex justify-between">
                                    <span class="text-lg font-semibold text-gray-900 dark:text-gray-100">Total</span>
                                    <span class="text-lg font-semibold text-gray-900 dark:text-gray-100">${{ number_format($order->total_amount, 2) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Payment Confirmation -->
        @if(session('payment_message'))
        <div class="bg-green-50 dark:bg-green-900 border border-green-200 dark:border-green-700 rounded-lg p-6 mb-8">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-3 flex-1">
                    <h3 class="text-lg font-semibold text-green-800 dark:text-green-200 mb-2">
                        Payment Successful!
                    </h3>
                    <p class="text-green-700 dark:text-green-300 mb-4">
                        {{ session('payment_message') }}
                    </p>
                    @if(session('payment_details.transaction_id'))
                    <div class="bg-white dark:bg-gray-800 rounded-lg p-4 border border-green-200 dark:border-green-600">
                        <h4 class="font-medium text-gray-900 dark:text-gray-100 mb-2">Payment Details</h4>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-600 dark:text-gray-400">Transaction ID:</span>
                                <span class="font-mono text-gray-900 dark:text-gray-100">{{ session('payment_details.transaction_id') }}</span>
                            </div>
                            @if(session('payment_details.card_type') && session('payment_details.last_four'))
                            <div class="flex justify-between">
                                <span class="text-gray-600 dark:text-gray-400">Payment Method:</span>
                                <span class="text-gray-900 dark:text-gray-100">{{ session('payment_details.card_type') }} •••• •••• •••• {{ session('payment_details.last_four') }}</span>
                            </div>
                            @endif
                            <div class="flex justify-between">
                                <span class="text-gray-600 dark:text-gray-400">Amount:</span>
                                <span class="font-semibold text-green-600 dark:text-green-400">${{ number_format($order->total_amount, 2) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600 dark:text-gray-400">Status:</span>
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-200">
                                    {{ ucfirst($order->payment_status) }}
                                </span>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        @endif

        <!-- Addresses -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
            <!-- Billing Address -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Billing Address</h3>
                <div class="text-sm text-gray-600 dark:text-gray-400 space-y-1">
                    <p class="font-medium text-gray-900 dark:text-gray-100">
                        {{ $order->billing_address['first_name'] }} {{ $order->billing_address['last_name'] }}
                    </p>
                    <p>{{ $order->billing_address['address'] }}</p>
                    <p>{{ $order->billing_address['city'] }}, {{ $order->billing_address['postal_code'] }}</p>
                    <p>{{ $order->billing_address['country'] }}</p>
                    <p class="pt-2">
                        <span class="font-medium">Email:</span> {{ $order->billing_address['email'] }}
                    </p>
                    <p>
                        <span class="font-medium">Phone:</span> {{ $order->billing_address['phone'] }}
                    </p>
                </div>
            </div>

            <!-- Shipping Address -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Shipping Address</h3>
                <div class="text-sm text-gray-600 dark:text-gray-400 space-y-1">
                    <p class="font-medium text-gray-900 dark:text-gray-100">
                        {{ $order->shipping_address['first_name'] }} {{ $order->shipping_address['last_name'] }}
                    </p>
                    <p>{{ $order->shipping_address['address'] }}</p>
                    <p>{{ $order->shipping_address['city'] }}, {{ $order->shipping_address['postal_code'] }}</p>
                    <p>{{ $order->shipping_address['country'] }}</p>
                </div>
            </div>
        </div>

        <!-- Payment Information -->
        @if($order->payment_method !== 'cash_on_delivery')
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 mb-8">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Payment Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Payment Method</p>
                        <p class="font-medium text-gray-900 dark:text-gray-100">{{ ucfirst(str_replace('_', ' ', $order->payment_method)) }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Payment Status</p>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                            @if($order->payment_status === 'paid') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                            @elseif($order->payment_status === 'pending') bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200
                            @else bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200 @endif">
                            {{ ucfirst($order->payment_status) }}
                        </span>
                    </div>
                    @if($order->payment_id)
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Transaction ID</p>
                            <p class="font-medium text-gray-900 dark:text-gray-100">{{ $order->payment_id }}</p>
                        </div>
                    @endif
                </div>

                @if($order->payment_method === 'bank_transfer' && $order->payment_status === 'pending')
                    <div class="mt-4 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                        <h4 class="text-sm font-medium text-blue-900 dark:text-blue-200 mb-2">Bank Transfer Instructions</h4>
                        <p class="text-sm text-blue-800 dark:text-blue-300 mb-2">
                            Please transfer ${{ number_format($order->total_amount, 2) }} to the following bank account:
                        </p>
                        <div class="text-sm text-blue-800 dark:text-blue-300">
                            <p><strong>Bank:</strong> E-Kampot Bank</p>
                            <p><strong>Account Name:</strong> E-Kampot Shop Ltd.</p>
                            <p><strong>Account Number:</strong> 1234567890</p>
                            <p><strong>SWIFT Code:</strong> EKMPKH22</p>
                            <p><strong>Reference:</strong> {{ $order->order_number }}</p>
                        </div>
                    </div>
                @endif
            </div>
        @endif

        <!-- What's Next -->
        <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-6 mb-8">
            <h3 class="text-lg font-semibold text-blue-900 dark:text-blue-200 mb-4">What happens next?</h3>
            <div class="space-y-3 text-sm text-blue-800 dark:text-blue-300">
                <div class="flex items-start space-x-3">
                    <div class="flex-shrink-0 w-6 h-6 bg-blue-600 text-white rounded-full flex items-center justify-center text-xs font-bold">1</div>
                    <p>We'll send you an order confirmation email with all the details.</p>
                </div>
                <div class="flex items-start space-x-3">
                    <div class="flex-shrink-0 w-6 h-6 bg-blue-600 text-white rounded-full flex items-center justify-center text-xs font-bold">2</div>
                    <p>We'll prepare your order and notify you when it's ready to ship.</p>
                </div>
                <div class="flex items-start space-x-3">
                    <div class="flex-shrink-0 w-6 h-6 bg-blue-600 text-white rounded-full flex items-center justify-center text-xs font-bold">3</div>
                    <p>Once shipped, we'll send you tracking information so you can follow your package.</p>
                </div>
                @if($order->payment_method === 'cash_on_delivery')
                    <div class="flex items-start space-x-3">
                        <div class="flex-shrink-0 w-6 h-6 bg-blue-600 text-white rounded-full flex items-center justify-center text-xs font-bold">4</div>
                        <p>Please have ${{ number_format($order->total_amount, 2) }} ready when your order is delivered.</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="{{ route('products.index') }}"
               class="bg-primary-600 hover:bg-primary-700 text-white font-semibold py-3 px-8 rounded-lg text-center transition-colors shadow-lg hover:shadow-xl">
                Continue Shopping
            </a>
            <a href="{{ route('dashboard') }}"
               class="bg-gray-600 hover:bg-gray-700 text-white font-semibold py-3 px-8 rounded-lg text-center transition-colors shadow-lg hover:shadow-xl">
                View My Orders
            </a>
        </div>

        <!-- Customer Support -->
        <div class="text-center mt-8 p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
            <p class="text-gray-600 dark:text-gray-400 mb-2">
                Need help with your order?
            </p>
            <a href="{{ route('contact') }}"
               class="text-primary-600 hover:text-primary-500 font-medium">
                Contact our customer support team
            </a>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Show success animation
            const successHeader = document.querySelector('.text-center');
            if (successHeader) {
                successHeader.style.opacity = '0';
                successHeader.style.transform = 'translateY(-20px)';

                setTimeout(() => {
                    successHeader.style.transition = 'all 0.6s ease-out';
                    successHeader.style.opacity = '1';
                    successHeader.style.transform = 'translateY(0)';
                }, 100);
            }

            // Animate payment confirmation if present
            const paymentConfirmation = document.querySelector('.bg-green-50');
            if (paymentConfirmation) {
                paymentConfirmation.style.opacity = '0';
                paymentConfirmation.style.transform = 'scale(0.95)';

                setTimeout(() => {
                    paymentConfirmation.style.transition = 'all 0.5s ease-out';
                    paymentConfirmation.style.opacity = '1';
                    paymentConfirmation.style.transform = 'scale(1)';
                }, 300);
            }

            // Show success toast notification
            @if(session('payment_message'))
            showSuccessToast('🎉 {{ session('payment_message') }}');
            @endif
        });

        function showSuccessToast(message) {
            const toast = document.createElement('div');
            toast.className = 'fixed top-4 right-4 z-50 max-w-sm bg-green-500 text-white p-4 rounded-lg shadow-lg transform transition-all duration-500 translate-x-full';
            toast.innerHTML = `
                <div class="flex items-center">
                    <svg class="w-6 h-6 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span class="font-medium">${message}</span>
                </div>
            `;

            document.body.appendChild(toast);

            // Slide in
            setTimeout(() => {
                toast.classList.remove('translate-x-full');
            }, 100);

            // Slide out after 5 seconds
            setTimeout(() => {
                toast.classList.add('translate-x-full');
                setTimeout(() => {
                    toast.remove();
                }, 500);
            }, 5000);
        }
    </script>
    @endpush
</x-app-layout>
