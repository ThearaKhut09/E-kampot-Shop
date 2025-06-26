@extends('admin.layout')

@section('title', 'Edit Order')

@section('content')
<div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
        <div class="flex justify-between items-center">
            <h2 class="text-xl font-semibold text-gray-800 dark:text-white">Edit Order #{{ $order->id }}</h2>
            <div class="flex space-x-2">
                <a href="{{ route('admin.orders.show', $order) }}"
                   class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium transition-colors">
                    View Order
                </a>
                <a href="{{ route('admin.orders.index') }}"
                   class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-md text-sm font-medium transition-colors">
                    Back to Orders
                </a>
            </div>
        </div>
    </div>

    <div class="p-6">
        @if ($errors->any())
            <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-md p-4 mb-6">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-red-800 dark:text-red-200">
                            There were errors with your submission
                        </h3>
                        <div class="mt-2 text-sm text-red-700 dark:text-red-300">
                            <ul class="list-disc pl-5 space-y-1">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Order Status and Notes -->
            <div class="lg:col-span-2">
                <form action="{{ route('admin.orders.update', $order) }}" method="POST" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Order Status</h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Current Status <span class="text-red-500">*</span>
                                </label>
                                <select id="status"
                                        name="status"
                                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                                        required>
                                    <option value="pending" {{ old('status', $order->status) == 'pending' ? 'selected' : '' }}>
                                        Pending
                                    </option>
                                    <option value="processing" {{ old('status', $order->status) == 'processing' ? 'selected' : '' }}>
                                        Processing
                                    </option>
                                    <option value="shipped" {{ old('status', $order->status) == 'shipped' ? 'selected' : '' }}>
                                        Shipped
                                    </option>
                                    <option value="delivered" {{ old('status', $order->status) == 'delivered' ? 'selected' : '' }}>
                                        Delivered
                                    </option>
                                    <option value="cancelled" {{ old('status', $order->status) == 'cancelled' ? 'selected' : '' }}>
                                        Cancelled
                                    </option>
                                    <option value="refunded" {{ old('status', $order->status) == 'refunded' ? 'selected' : '' }}>
                                        Refunded
                                    </option>
                                </select>
                                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                    Current: <span class="font-medium">{{ ucfirst($order->status) }}</span>
                                </p>
                            </div>

                            <div>
                                <label for="payment_status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Payment Status
                                </label>
                                <select id="payment_status"
                                        name="payment_status"
                                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                                    <option value="pending" {{ old('payment_status', $order->payment_status) == 'pending' ? 'selected' : '' }}>
                                        Pending
                                    </option>
                                    <option value="paid" {{ old('payment_status', $order->payment_status) == 'paid' ? 'selected' : '' }}>
                                        Paid
                                    </option>
                                    <option value="failed" {{ old('payment_status', $order->payment_status) == 'failed' ? 'selected' : '' }}>
                                        Failed
                                    </option>
                                    <option value="refunded" {{ old('payment_status', $order->payment_status) == 'refunded' ? 'selected' : '' }}>
                                        Refunded
                                    </option>
                                </select>
                            </div>
                        </div>

                        <div class="mt-4">
                            <label for="notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Admin Notes
                            </label>
                            <textarea id="notes"
                                      name="notes"
                                      rows="4"
                                      class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                                      placeholder="Add any notes about this order...">{{ old('notes', $order->notes) }}</textarea>
                        </div>

                        <div class="mt-6 flex justify-end space-x-3">
                            <a href="{{ route('admin.orders.show', $order) }}"
                               class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-6 py-2 rounded-md text-sm font-medium transition-colors">
                                Cancel
                            </a>
                            <button type="submit"
                                    class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-md text-sm font-medium transition-colors">
                                Update Order
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Order Summary -->
            <div class="lg:col-span-1">
                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Order Summary</h3>

                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600 dark:text-gray-400">Order ID:</span>
                            <span class="text-sm font-medium text-gray-900 dark:text-white">#{{ $order->id }}</span>
                        </div>

                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600 dark:text-gray-400">Customer:</span>
                            <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $order->user->name }}</span>
                        </div>

                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600 dark:text-gray-400">Email:</span>
                            <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $order->user->email }}</span>
                        </div>

                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600 dark:text-gray-400">Order Date:</span>
                            <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $order->created_at->format('M d, Y') }}</span>
                        </div>

                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600 dark:text-gray-400">Items:</span>
                            <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $order->orderItems->count() }}</span>
                        </div>

                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600 dark:text-gray-400">Payment Method:</span>
                            <span class="text-sm font-medium text-gray-900 dark:text-white">{{ ucfirst($order->payment_method) }}</span>
                        </div>

                        <hr class="border-gray-200 dark:border-gray-600">

                        <div class="flex justify-between">
                            <span class="text-sm font-medium text-gray-900 dark:text-white">Total Amount:</span>
                            <span class="text-sm font-bold text-gray-900 dark:text-white">${{ number_format($order->total_amount, 2) }}</span>
                        </div>
                    </div>
                </div>

                <!-- Shipping Information -->
                @if($order->shipping_address)
                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-6 mt-6">
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Shipping Address</h3>
                        <div class="text-sm text-gray-600 dark:text-gray-400">
                            {!! nl2br(e($order->shipping_address)) !!}
                        </div>
                    </div>
                @endif

                <!-- Billing Information -->
                @if($order->billing_address)
                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-6 mt-6">
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Billing Address</h3>
                        <div class="text-sm text-gray-600 dark:text-gray-400">
                            {!! nl2br(e($order->billing_address)) !!}
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Order Items -->
        <div class="mt-8">
            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Order Items</h3>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-600">
                        <thead class="bg-gray-100 dark:bg-gray-600">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Product</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">SKU</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Price</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Quantity</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Total</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-600">
                            @foreach($order->orderItems as $item)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            @if($item->product && $item->product->image)
                                                <img src="{{ Storage::url($item->product->image) }}"
                                                     alt="{{ $item->product_name }}"
                                                     class="w-10 h-10 object-cover rounded mr-3">
                                            @endif
                                            <div>
                                                <div class="text-sm font-medium text-gray-900 dark:text-white">
                                                    {{ $item->product_name }}
                                                </div>
                                                @if($item->product_options)
                                                    <div class="text-sm text-gray-500 dark:text-gray-400">
                                                        {{ $item->product_options }}
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                        {{ $item->product_sku }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                        ${{ number_format($item->price, 2) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                        {{ $item->quantity }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                        ${{ number_format($item->total, 2) }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
