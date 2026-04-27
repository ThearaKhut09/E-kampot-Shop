<x-app-layout>
    <x-slot name="title">Notifications - E-Kampot Shop</x-slot>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100 mb-2">Notifications</h1>
            <p class="text-gray-600 dark:text-gray-400">Stay updated on your orders and account activity</p>
        </div>

        <!-- Actions -->
        <div class="flex gap-3 mb-6">
            <a href="{{ route('notifications.index', ['filter' => 'unread']) }}"
               class="px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition text-sm font-medium">
                Unread
            </a>
            <a href="{{ route('notifications.index') }}"
               class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-lg hover:bg-gray-300 transition text-sm font-medium">
                All
            </a>
            @if(request('filter') === 'unread')
                <form method="POST" action="{{ route('notifications.mark-all-read') }}" class="ml-auto">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition text-sm font-medium">
                        Mark all as read
                    </button>
                </form>
            @endif
        </div>

        <!-- Notifications List -->
        <div class="space-y-4">
            @if($notifications->isEmpty())
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-8 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                    </svg>
                    <p class="text-gray-600 dark:text-gray-400 mt-4">No notifications found</p>
                </div>
            @else
                @foreach($notifications as $notification)
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6
                        @if(!$notification->is_read) border-l-4 border-blue-500 @endif">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <div class="flex items-center gap-3 mb-2">
                                    <span class="text-2xl">{{ $notification->icon }}</span>
                                    <div>
                                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                                            {{ $notification->title }}
                                        </h3>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">
                                            {{ $notification->created_at->format('M j, Y g:i A') }}
                                        </p>
                                    </div>
                                </div>
                                <p class="text-gray-700 dark:text-gray-300 mb-4">
                                    {{ $notification->message }}
                                </p>
                                @if($notification->action_url)
                                    <a href="{{ $notification->action_url }}"
                                       class="inline-block px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition text-sm font-medium">
                                        View Details
                                    </a>
                                @endif
                            </div>
                            <div class="ml-4 flex-shrink-0">
                                @if(!$notification->is_read)
                                    <form method="POST" action="{{ route('notifications.mark-read', $notification->id) }}" class="inline">
                                        @csrf
                                        <button type="submit" class="text-blue-600 hover:text-blue-700 font-medium text-sm">
                                            Mark as read
                                        </button>
                                    </form>
                                @else
                                    <span class="text-gray-500 text-sm">✓ Read</span>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach

                <!-- Pagination -->
                @if($notifications instanceof \Illuminate\Pagination\Paginator)
                    <div class="mt-8">
                        {{ $notifications->links() }}
                    </div>
                @endif
            @endif
        </div>
    </div>
</x-app-layout>
