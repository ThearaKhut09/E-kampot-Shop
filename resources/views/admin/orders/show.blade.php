@extends('admin.layout')

@section('title', 'Order Details')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Order #{{ $order->order_number ?? $order->id }}</h2>
        <div class="space-x-2">
            <a href="{{ route('admin.orders.edit', $order) }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium">
                Edit Order
            </a>
            <a href="{{ route('admin.orders.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-md text-sm font-medium">
                Back to Orders
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Order Information -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Order Items -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">Order Items</h3>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        @foreach($order->orderItems as $item)
                        <div class="flex items-center space-x-4 p-4 border border-gray-200 dark:border-gray-700 rounded-lg">
                            <div class="flex-shrink-0">
                                @if($item->product->image)
                                    <img class="h-16 w-16 rounded object-cover" src="{{ Storage::url($item->product->image) }}" alt="{{ $item->product->name }}">
                                @else
                                    <div class="h-16 w-16 bg-gray-300 dark:bg-gray-600 rounded"></div>
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <h4 class="text-sm font-medium text-gray-900 dark:text-white">
                                    {{ $item->product->name }}
                                </h4>
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    SKU: {{ $item->product->sku }}
                                </p>
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    Quantity: {{ $item->quantity }}
                                </p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-medium text-gray-900 dark:text-white">
                                    ${{ number_format($item->price, 2) }}
                                </p>
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    Total: ${{ number_format($item->price * $item->quantity, 2) }}
                                </p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Customer Information -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">Customer Information</h3>
                </div>
                <div class="p-6">
                    <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Name</dt>
                            <dd class="text-sm text-gray-900 dark:text-white">{{ $order->user->name }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Email</dt>
                            <dd class="text-sm text-gray-900 dark:text-white">{{ $order->user->email }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Phone</dt>
                            <dd class="text-sm text-gray-900 dark:text-white">{{ $order->user->phone ?? 'N/A' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Customer Since</dt>
                            <dd class="text-sm text-gray-900 dark:text-white">{{ $order->user->created_at->format('M j, Y') }}</dd>
                        </div>
                    </dl>
                </div>
            </div>

            <!-- Addresses -->
            @if($order->billing_address || $order->shipping_address)
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">Addresses</h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        @if($order->billing_address)
                        <div>
                            <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-2">Billing Address</h4>
                            <div class="text-sm text-gray-600 dark:text-gray-400">
                                @foreach($order->billing_address as $line)
                                    <div>{{ $line }}</div>
                                @endforeach
                            </div>
                        </div>
                        @endif

                        @if($order->shipping_address)
                        <div>
                            <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-2">Shipping Address</h4>
                            <div class="text-sm text-gray-600 dark:text-gray-400">
                                @foreach($order->shipping_address as $line)
                                    <div>{{ $line }}</div>
                                @endforeach
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Order Summary -->
        <div class="space-y-6">
            <!-- Order Status -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">Order Status</h3>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        <div>
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

                        <dl class="space-y-2">
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Order Date</dt>
                                <dd class="text-sm text-gray-900 dark:text-white">{{ $order->created_at->format('M j, Y g:i A') }}</dd>
                            </div>
                            @if($order->shipped_at)
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Shipped Date</dt>
                                <dd class="text-sm text-gray-900 dark:text-white">{{ $order->shipped_at->format('M j, Y g:i A') }}</dd>
                            </div>
                            @endif
                            @if($order->delivered_at)
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Delivered Date</dt>
                                <dd class="text-sm text-gray-900 dark:text-white">{{ $order->delivered_at->format('M j, Y g:i A') }}</dd>
                            </div>
                            @endif
                        </dl>
                    </div>
                </div>
            </div>

            <!-- Order Totals -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">Order Summary</h3>
                </div>
                <div class="p-6">
                    <dl class="space-y-2">
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-500 dark:text-gray-400">Subtotal</dt>
                            <dd class="text-sm text-gray-900 dark:text-white">${{ number_format($order->subtotal, 2) }}</dd>
                        </div>
                        @if($order->tax_amount)
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-500 dark:text-gray-400">Tax</dt>
                            <dd class="text-sm text-gray-900 dark:text-white">${{ number_format($order->tax_amount, 2) }}</dd>
                        </div>
                        @endif
                        @if($order->shipping_amount)
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-500 dark:text-gray-400">Shipping</dt>
                            <dd class="text-sm text-gray-900 dark:text-white">${{ number_format($order->shipping_amount, 2) }}</dd>
                        </div>
                        @endif
                        @if($order->discount_amount)
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-500 dark:text-gray-400">Discount</dt>
                            <dd class="text-sm text-red-600 dark:text-red-400">-${{ number_format($order->discount_amount, 2) }}</dd>
                        </div>
                        @endif
                        <div class="flex justify-between pt-2 border-t border-gray-200 dark:border-gray-700">
                            <dt class="text-base font-medium text-gray-900 dark:text-white">Total</dt>
                            <dd class="text-base font-medium text-gray-900 dark:text-white">${{ number_format($order->total_amount, 2) }}</dd>
                        </div>
                    </dl>
                </div>
            </div>

            <!-- Payment Information -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">Payment</h3>
                </div>
                <div class="p-6">
                    <dl class="space-y-2">
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Payment Method</dt>
                            <dd class="text-sm text-gray-900 dark:text-white">{{ $order->payment_method ?? 'N/A' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Payment Status</dt>
                            <dd class="text-sm text-gray-900 dark:text-white">{{ $order->payment_status ?? 'Pending' }}</dd>
                        </div>
                        @if($order->payment_id)
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Transaction ID</dt>
                            <dd class="text-sm text-gray-900 dark:text-white">{{ $order->payment_id }}</dd>
                        </div>
                        @endif
                    </dl>
                </div>
            </div>

            @if($order->notes)
            <!-- Order Notes -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">Notes</h3>
                </div>
                <div class="p-6">
                    <p class="text-sm text-gray-600 dark:text-gray-400">{{ $order->notes }}</p>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
