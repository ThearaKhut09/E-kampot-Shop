<x-app-layout>
    <x-slot name="title">Order Details - E-Kampot Shop</x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100 mb-2">
                    Order #{{ $order->order_number }}
                </h1>
                <p class="text-gray-600 dark:text-gray-400">
                    Placed on {{ $order->created_at->format('M j, Y g:i A') }}
                </p>
            </div>
            <div class="mt-4 sm:mt-0 flex items-center space-x-3">
                <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium
                    @if($order->status === 'delivered') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                    @elseif($order->status === 'shipped') bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200
                    @elseif($order->status === 'processing') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                    @elseif($order->status === 'pending') bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200
                    @elseif($order->status === 'cancelled') bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200
                    @else bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200 @endif">
                    {{ ucfirst($order->status) }}
                </span>
                @if(in_array($order->status, ['pending', 'processing']))
                    <form method="POST" action="{{ route('orders.cancel', $order) }}" class="inline">
                        @csrf
                        @method('PATCH')
                        <button type="submit"
                                onclick="return confirm('Are you sure you want to cancel this order?')"
                                class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                            Cancel Order
                        </button>
                    </form>
                @endif
                <a href="{{ route('orders.index') }}"
                   class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                    Back to Orders
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left Column - Order Details -->
            <div class="lg:col-span-2 space-y-8">
                <!-- Order Items -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden">
                    <div class="px-6 py-4 bg-gray-50 dark:bg-gray-700 border-b border-gray-200 dark:border-gray-600">
                        <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">
                            Order Items ({{ $order->items->count() }} {{ Str::plural('item', $order->items->count()) }})
                        </h2>
                    </div>
                    <div class="p-6">
                        <div class="space-y-6">
                            @foreach($order->items as $item)
                                <div class="flex items-center space-x-4 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                    <div class="flex-shrink-0">
                                        @if($item->product && $item->product->image)
                                            <img src="{{ Storage::url($item->product->image) }}"
                                                 alt="{{ $item->product_name }}"
                                                 class="w-20 h-20 object-cover rounded">
                                        @else
                                            <div class="w-20 h-20 bg-gray-200 dark:bg-gray-600 rounded flex items-center justify-center">
                                                <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                </svg>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-1">
                                            {{ $item->product_name }}
                                        </h3>
                                        @if($item->product && $item->product->slug)
                                            <a href="{{ route('products.show', $item->product->slug) }}"
                                               class="text-primary-600 hover:text-primary-500 text-sm">
                                                View Product
                                            </a>
                                        @endif
                                        <div class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                                            <p>SKU: {{ $item->product_sku }}</p>
                                            <p>Price: ${{ number_format($item->price, 2) }}</p>
                                            <p>Quantity: {{ $item->quantity }}</p>
                                            @if($item->product_options)
                                                <p>Options: {{ is_array($item->product_options) ? implode(', ', $item->product_options) : $item->product_options }}</p>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                                            ${{ number_format($item->total, 2) }}
                                        </p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Order Status Timeline -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-6">Order Status</h2>
                    <div class="space-y-4">
                        <!-- Order Placed -->
                        <div class="flex items-center">
                            <div class="flex-shrink-0 w-8 h-8 bg-green-100 dark:bg-green-900 rounded-full flex items-center justify-center">
                                <svg class="w-4 h-4 text-green-600 dark:text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-900 dark:text-gray-100">Order Placed</p>
                                <p class="text-sm text-gray-500 dark:text-gray-400">{{ $order->created_at->format('M j, Y g:i A') }}</p>
                            </div>
                        </div>

                        <!-- Processing -->
                        <div class="flex items-center">
                            <div class="flex-shrink-0 w-8 h-8 {{ in_array($order->status, ['processing', 'shipped', 'delivered']) ? 'bg-green-100 dark:bg-green-900' : 'bg-gray-100 dark:bg-gray-700' }} rounded-full flex items-center justify-center">
                                @if(in_array($order->status, ['processing', 'shipped', 'delivered']))
                                    <svg class="w-4 h-4 text-green-600 dark:text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                @else
                                    <div class="w-3 h-3 bg-gray-300 dark:bg-gray-600 rounded-full"></div>
                                @endif
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-900 dark:text-gray-100">Processing</p>
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    @if(in_array($order->status, ['processing', 'shipped', 'delivered']))
                                        Your order is being prepared
                                    @else
                                        Pending
                                    @endif
                                </p>
                            </div>
                        </div>

                        <!-- Shipped -->
                        <div class="flex items-center">
                            <div class="flex-shrink-0 w-8 h-8 {{ in_array($order->status, ['shipped', 'delivered']) ? 'bg-green-100 dark:bg-green-900' : 'bg-gray-100 dark:bg-gray-700' }} rounded-full flex items-center justify-center">
                                @if(in_array($order->status, ['shipped', 'delivered']))
                                    <svg class="w-4 h-4 text-green-600 dark:text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                @else
                                    <div class="w-3 h-3 bg-gray-300 dark:bg-gray-600 rounded-full"></div>
                                @endif
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-900 dark:text-gray-100">Shipped</p>
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    @if($order->shipped_at)
                                        {{ $order->shipped_at->format('M j, Y g:i A') }}
                                    @elseif(in_array($order->status, ['shipped', 'delivered']))
                                        Your order is on its way
                                    @else
                                        Pending
                                    @endif
                                </p>
                            </div>
                        </div>

                        <!-- Delivered -->
                        <div class="flex items-center">
                            <div class="flex-shrink-0 w-8 h-8 {{ $order->status === 'delivered' ? 'bg-green-100 dark:bg-green-900' : 'bg-gray-100 dark:bg-gray-700' }} rounded-full flex items-center justify-center">
                                @if($order->status === 'delivered')
                                    <svg class="w-4 h-4 text-green-600 dark:text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                @else
                                    <div class="w-3 h-3 bg-gray-300 dark:bg-gray-600 rounded-full"></div>
                                @endif
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-900 dark:text-gray-100">Delivered</p>
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    @if($order->delivered_at)
                                        {{ $order->delivered_at->format('M j, Y g:i A') }}
                                    @elseif($order->status === 'delivered')
                                        Order delivered
                                    @else
                                        Pending
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column - Order Summary & Details -->
            <div class="lg:col-span-1 space-y-6">
                <!-- Order Summary -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-6">Order Summary</h2>
                    <div class="space-y-3">
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
                        <div class="border-t border-gray-200 dark:border-gray-700 pt-3">
                            <div class="flex justify-between">
                                <span class="text-lg font-semibold text-gray-900 dark:text-gray-100">Total</span>
                                <span class="text-lg font-semibold text-gray-900 dark:text-gray-100">${{ number_format($order->total_amount, 2) }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Payment Information -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-4">Payment Information</h2>
                    <div class="space-y-3 text-sm">
                        <div>
                            <p class="text-gray-600 dark:text-gray-400">Payment Method</p>
                            <p class="font-medium text-gray-900 dark:text-gray-100">{{ ucfirst(str_replace('_', ' ', $order->payment_method)) }}</p>
                        </div>
                        <div>
                            <p class="text-gray-600 dark:text-gray-400">Payment Status</p>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                @if($order->payment_status === 'paid') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                @elseif($order->payment_status === 'pending') bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200
                                @else bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200 @endif">
                                {{ ucfirst($order->payment_status ?? 'pending') }}
                            </span>
                        </div>
                        @if($order->payment_id)
                            <div>
                                <p class="text-gray-600 dark:text-gray-400">Transaction ID</p>
                                <p class="font-mono text-gray-900 dark:text-gray-100 text-xs">{{ $order->payment_id }}</p>
                            </div>
                        @endif
                    </div>

                    @if($order->payment_method === 'bank_transfer' && $order->payment_status === 'pending')
                        <div class="mt-4 p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                            <h4 class="text-sm font-medium text-blue-900 dark:text-blue-200 mb-2">Bank Transfer Instructions</h4>
                            <div class="text-xs text-blue-800 dark:text-blue-300">
                                <p><strong>Bank:</strong> E-Kampot Bank</p>
                                <p><strong>Account:</strong> 1234567890</p>
                                <p><strong>Reference:</strong> {{ $order->order_number }}</p>
                                <p><strong>Amount:</strong> ${{ number_format($order->total_amount, 2) }}</p>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Addresses -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-4">Delivery Address</h2>
                    <div class="text-sm text-gray-600 dark:text-gray-400 space-y-1">
                        <p class="font-medium text-gray-900 dark:text-gray-100">
                            {{ $order->shipping_address['first_name'] }} {{ $order->shipping_address['last_name'] }}
                        </p>
                        <p>{{ $order->shipping_address['address'] }}</p>
                        <p>{{ $order->shipping_address['city'] }}, {{ $order->shipping_address['postal_code'] }}</p>
                        <p>{{ $order->shipping_address['country'] }}</p>
                    </div>
                </div>

                <!-- Customer Support -->
                <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-4">
                    <h3 class="text-sm font-medium text-blue-900 dark:text-blue-200 mb-2">Need Help?</h3>
                    <p class="text-sm text-blue-800 dark:text-blue-300 mb-3">
                        Have questions about your order?
                    </p>
                    <a href="{{ route('contact') }}"
                       class="text-blue-600 hover:text-blue-500 text-sm font-medium">
                        Contact Support
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
