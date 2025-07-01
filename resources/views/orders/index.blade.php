<x-app-layout>
    <x-slot name="title">My Orders - E-Kampot Shop</x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100 mb-2">
                My Orders
            </h1>
            <p class="text-gray-600 dark:text-gray-400">
                Track and manage your orders
            </p>
        </div>

        @if($orders->count() > 0)
            <div class="space-y-6">
                @foreach($orders as $order)
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden">
                        <!-- Order Header -->
                        <div class="px-6 py-4 bg-gray-50 dark:bg-gray-700 border-b border-gray-200 dark:border-gray-600">
                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                                <div class="flex items-center space-x-4">
                                    <div>
                                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                                            Order #{{ $order->order_number }}
                                        </h3>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">
                                            Placed on {{ $order->created_at->format('M j, Y g:i A') }}
                                        </p>
                                    </div>
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                                        @if($order->status === 'delivered') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                        @elseif($order->status === 'shipped') bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200
                                        @elseif($order->status === 'processing') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                                        @elseif($order->status === 'pending') bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200
                                        @elseif($order->status === 'cancelled') bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200
                                        @else bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200 @endif">
                                        {{ ucfirst($order->status) }}
                                    </span>
                                </div>
                                <div class="mt-4 sm:mt-0 flex items-center space-x-3">
                                    <a href="{{ route('orders.show', $order) }}"
                                       class="text-primary-600 hover:text-primary-500 text-sm font-medium">
                                        View Details
                                    </a>
                                    @if(in_array($order->status, ['pending', 'processing']))
                                        <form method="POST" action="{{ route('orders.cancel', $order) }}" class="inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit"
                                                    onclick="return confirm('Are you sure you want to cancel this order?')"
                                                    class="text-red-600 hover:text-red-500 text-sm font-medium">
                                                Cancel Order
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Order Items Preview -->
                        <div class="px-6 py-4">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <!-- Items -->
                                <div class="md:col-span-2">
                                    <h4 class="text-sm font-medium text-gray-900 dark:text-gray-100 mb-3">
                                        Order Items ({{ $order->items->count() }} {{ Str::plural('item', $order->items->count()) }})
                                    </h4>
                                    <div class="space-y-3">
                                        @foreach($order->items->take(3) as $item)
                                            <div class="flex items-center space-x-3">
                                                <div class="flex-shrink-0">
                                                    @if($item->product && $item->product->image)
                                                        <img src="{{ Storage::url($item->product->image) }}"
                                                             alt="{{ $item->product_name }}"
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
                                                    <p class="text-sm font-medium text-gray-900 dark:text-gray-100 truncate">
                                                        {{ $item->product_name }}
                                                    </p>
                                                    <p class="text-sm text-gray-500 dark:text-gray-400">
                                                        Qty: {{ $item->quantity }} × ${{ number_format($item->price, 2) }}
                                                    </p>
                                                </div>
                                                <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                                    ${{ number_format($item->total, 2) }}
                                                </div>
                                            </div>
                                        @endforeach
                                        @if($order->items->count() > 3)
                                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                                ... and {{ $order->items->count() - 3 }} more items
                                            </p>
                                        @endif
                                    </div>
                                </div>

                                <!-- Order Summary -->
                                <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                                    <h4 class="text-sm font-medium text-gray-900 dark:text-gray-100 mb-3">Order Summary</h4>
                                    <div class="space-y-2 text-sm">
                                        <div class="flex justify-between">
                                            <span class="text-gray-600 dark:text-gray-400">Subtotal</span>
                                            <span class="text-gray-900 dark:text-gray-100">${{ number_format($order->subtotal, 2) }}</span>
                                        </div>
                                        @if($order->tax_amount > 0)
                                            <div class="flex justify-between">
                                                <span class="text-gray-600 dark:text-gray-400">Tax</span>
                                                <span class="text-gray-900 dark:text-gray-100">${{ number_format($order->tax_amount, 2) }}</span>
                                            </div>
                                        @endif
                                        @if($order->shipping_amount > 0)
                                            <div class="flex justify-between">
                                                <span class="text-gray-600 dark:text-gray-400">Shipping</span>
                                                <span class="text-gray-900 dark:text-gray-100">${{ number_format($order->shipping_amount, 2) }}</span>
                                            </div>
                                        @endif
                                        <div class="border-t border-gray-200 dark:border-gray-600 pt-2">
                                            <div class="flex justify-between font-medium">
                                                <span class="text-gray-900 dark:text-gray-100">Total</span>
                                                <span class="text-gray-900 dark:text-gray-100">${{ number_format($order->total_amount, 2) }}</span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Payment Info -->
                                    <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-600">
                                        <div class="text-sm">
                                            <p class="text-gray-600 dark:text-gray-400">Payment Method</p>
                                            <p class="font-medium text-gray-900 dark:text-gray-100">
                                                {{ ucfirst(str_replace('_', ' ', $order->payment_method)) }}
                                            </p>
                                            @if($order->payment_status)
                                                <p class="text-gray-600 dark:text-gray-400 mt-1">Status</p>
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                                    @if($order->payment_status === 'paid') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                                    @elseif($order->payment_status === 'pending') bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200
                                                    @else bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200 @endif">
                                                    {{ ucfirst($order->payment_status) }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            @if($orders->hasPages())
                <div class="mt-8">
                    {{ $orders->links() }}
                </div>
            @endif
        @else
            <!-- Empty State -->
            <div class="text-center py-16">
                <div class="mb-8">
                    <svg class="w-24 h-24 text-gray-300 dark:text-gray-600 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-4">
                    No orders yet
                </h2>
                <p class="text-gray-600 dark:text-gray-400 mb-8">
                    You haven't placed any orders yet. Start shopping to see your orders here.
                </p>
                <a href="{{ route('products.index') }}"
                   class="bg-primary-600 hover:bg-primary-700 text-white font-semibold py-3 px-8 rounded-lg inline-flex items-center transition-colors shadow-lg hover:shadow-xl">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l-1 10H6L5 9z"></path>
                    </svg>
                    Start Shopping
                </a>
            </div>
        @endif
    </div>
</x-app-layout>
