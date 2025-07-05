@extends('admin.layout')

@section('title', 'User Details')

@section('content')
<div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
        <div class="flex justify-between items-center">
            <h2 class="text-xl font-semibold text-gray-800 dark:text-white">User Details</h2>
            <div class="flex space-x-2">
                <a href="{{ route('admin.users.edit', $user) }}"
                   class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium transition-colors">
                    Edit User
                </a>
                <a href="{{ route('admin.users.index') }}"
                   class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-md text-sm font-medium transition-colors">
                    Back to Users
                </a>
            </div>
        </div>
    </div>

    <div class="p-6">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- User Information -->
            <div class="lg:col-span-2">
                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Personal Information</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Full Name</label>
                            <p class="text-gray-900 dark:text-white font-medium">{{ $user->name }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Email</label>
                            <p class="text-gray-900 dark:text-white">{{ $user->email }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Phone</label>
                            <p class="text-gray-900 dark:text-white">{{ $user->phone ?: 'Not provided' }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Role</label>
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $user->hasRole('admin') ? 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200' : 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' }}">
                                {{ $user->roles->first()->name ?? 'No Role' }}
                            </span>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Status</label>
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $user->is_active ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' }}">
                                {{ $user->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Email Verification</label>
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $user->email_verified_at ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200' }}">
                                {{ $user->email_verified_at ? 'Verified' : 'Unverified' }}
                            </span>
                        </div>
                    </div>

                    @if($user->address)
                        <div class="mt-4">
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Address</label>
                            <p class="text-gray-900 dark:text-white">{{ $user->address }}</p>
                        </div>
                    @endif

                    <div class="mt-4 grid grid-cols-2 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Joined</label>
                            <p class="text-gray-900 dark:text-white">{{ $user->created_at->format('M d, Y') }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Last Updated</label>
                            <p class="text-gray-900 dark:text-white">{{ $user->updated_at->format('M d, Y') }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Last Login</label>
                            <p class="text-gray-900 dark:text-white">
                                {{ $user->last_login_at ? $user->last_login_at->format('M d, Y H:i') : 'Never' }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Order History -->
                @if($user->orders->count() > 0)
                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-6 mt-6">
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Recent Orders</h3>

                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-600">
                                <thead class="bg-gray-100 dark:bg-gray-600">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Order</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Date</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Items</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Total</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-600">
                                    @foreach($user->orders->take(10) as $order)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                                #{{ $order->id }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                                {{ $order->created_at->format('M d, Y') }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                                {{ $order->orderItems->count() }} items
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                                    @switch($order->status)
                                                        @case('pending')
                                                            bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                                                            @break
                                                        @case('processing')
                                                            bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200
                                                            @break
                                                        @case('shipped')
                                                            bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200
                                                            @break
                                                        @case('delivered')
                                                            bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                                            @break
                                                        @case('cancelled')
                                                            bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200
                                                            @break
                                                        @default
                                                            bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200
                                                    @endswitch
                                                ">
                                                    {{ ucfirst($order->status) }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                                ${{ number_format($order->total_amount, 2) }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                <a href="{{ route('admin.orders.show', $order) }}"
                                                   class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300">
                                                    View
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        @if($user->orders->count() > 10)
                            <div class="mt-4 text-center">
                                <a href="{{ route('admin.orders.index', ['user' => $user->id]) }}"
                                   class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 text-sm font-medium">
                                    View all {{ $user->orders->count() }} orders →
                                </a>
                            </div>
                        @endif
                    </div>
                @endif
            </div>

            <!-- Statistics and Actions -->
            <div class="lg:col-span-1">
                <!-- Statistics -->
                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Account Statistics</h3>

                    <div class="space-y-4">
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600 dark:text-gray-400">Total Orders</span>
                            <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $user->orders->count() }}</span>
                        </div>

                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600 dark:text-gray-400">Completed Orders</span>
                            <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $user->orders->where('status', 'delivered')->count() }}</span>
                        </div>

                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600 dark:text-gray-400">Total Spent</span>
                            <span class="text-sm font-medium text-gray-900 dark:text-white">
                                ${{ number_format($user->orders->where('status', 'delivered')->sum('total_amount'), 2) }}
                            </span>
                        </div>

                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600 dark:text-gray-400">Average Order</span>
                            <span class="text-sm font-medium text-gray-900 dark:text-white">
                                @php
                                    $completedOrders = $user->orders->where('status', 'delivered');
                                    $average = $completedOrders->count() > 0 ? $completedOrders->avg('total_amount') : 0;
                                @endphp
                                ${{ number_format($average, 2) }}
                            </span>
                        </div>

                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600 dark:text-gray-400">Reviews Written</span>
                            <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $user->reviews->count() ?? 0 }}</span>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-6 mt-6">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Quick Actions</h3>

                    <div class="space-y-3">
                        <a href="{{ route('admin.users.edit', $user) }}"
                           class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium transition-colors inline-block text-center">
                            Edit User
                        </a>

                        <a href="{{ route('admin.orders.index', ['user' => $user->id]) }}"
                           class="w-full bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md text-sm font-medium transition-colors inline-block text-center">
                            View Orders ({{ $user->orders->count() }})
                        </a>

                        @if(!$user->email_verified_at)
                            <form action="{{ route('admin.users.verify-email', $user) }}" method="POST" class="w-full">
                                @csrf
                                <button type="submit"
                                        class="w-full bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded-md text-sm font-medium transition-colors">
                                    Verify Email
                                </button>
                            </form>
                        @endif

                        @if($user->is_active)
                            <form action="{{ route('admin.users.deactivate', $user) }}" method="POST" class="w-full" id="deactivate-form-user-{{ $user->id }}">
                                @csrf
                                @method('PATCH')
                                <button type="button"
                                        onclick="confirmDelete(document.getElementById('deactivate-form-user-{{ $user->id }}'), '{{ $user->name }}', 'User Account')"
                                        class="w-full bg-orange-600 hover:bg-orange-700 text-white px-4 py-2 rounded-md text-sm font-medium transition-colors">
                                    Deactivate Account
                                </button>
                            </form>
                        @else
                            <form action="{{ route('admin.users.activate', $user) }}" method="POST" class="w-full">
                                @csrf
                                @method('PATCH')
                                <button type="submit"
                                        class="w-full bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md text-sm font-medium transition-colors">
                                    Activate Account
                                </button>
                            </form>
                        @endif

                        @if(!$user->hasRole('admin') || ($user->hasRole('admin') && \App\Models\User::role('admin')->count() > 1))
                            <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="w-full" id="delete-form-user-show-{{ $user->id }}">
                                @csrf
                                @method('DELETE')
                                <button type="button"
                                        onclick="confirmDelete(document.getElementById('delete-form-user-show-{{ $user->id }}'), '{{ $user->name }}', 'User')"
                                        class="w-full bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-md text-sm font-medium transition-colors">
                                    Delete User
                                </button>
                            </form>
                        @else
                            <div class="text-sm text-gray-500 dark:text-gray-400 p-2 bg-yellow-50 dark:bg-yellow-900/20 rounded-md">
                                Cannot delete: This is the last admin user
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Activity Log -->
                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-6 mt-6">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Recent Activity</h3>

                    <div class="space-y-3">
                        @if($user->orders->count() > 0)
                            @foreach($user->orders->take(5) as $order)
                                <div class="flex items-center space-x-3">
                                    <div class="flex-shrink-0">
                                        <div class="w-2 h-2 bg-blue-500 rounded-full"></div>
                                    </div>
                                    <div class="flex-1">
                                        <p class="text-sm text-gray-900 dark:text-white">
                                            Placed order #{{ $order->id }}
                                        </p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">
                                            {{ $order->created_at->diffForHumans() }}
                                        </p>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <p class="text-sm text-gray-500 dark:text-gray-400">No recent activity</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
