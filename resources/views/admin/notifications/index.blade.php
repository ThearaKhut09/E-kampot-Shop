<x-admin-layout>
    <x-slot name="title">Notifications - Admin Panel</x-slot>

    <div class="space-y-6">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">Notifications</h1>
            @php
                $unreadCount = \App\Services\NotificationService::getUnreadCountForAdmin();
            @endphp
            @if($unreadCount > 0)
                <span class="inline-flex items-center justify-center px-3 py-1 text-sm font-medium text-white bg-red-600 rounded-full">
                    {{ $unreadCount }} unread
                </span>
            @endif
        </div>

        <!-- Filters & Actions -->
        <div class="flex flex-wrap gap-4 items-center">
            <a href="{{ route('admin.notifications.index') }}"
               class="px-4 py-2 @if(!request('filter')) bg-emerald-600 text-white @else bg-gray-200 dark:bg-gray-700 text-gray-900 dark:text-gray-100 @endif rounded-lg hover:opacity-90 transition text-sm font-medium">
                All
            </a>
            <a href="{{ route('admin.notifications.index', ['filter' => 'unread']) }}"
               class="px-4 py-2 @if(request('filter') === 'unread') bg-emerald-600 text-white @else bg-gray-200 dark:bg-gray-700 text-gray-900 dark:text-gray-100 @endif rounded-lg hover:opacity-90 transition text-sm font-medium">
                Unread
            </a>

            @if($types->count() > 0)
                <select onchange="window.location.href='{{ route('admin.notifications.index') }}?type='+this.value"
                        class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-lg text-sm font-medium">
                    <option value="">Filter by type...</option>
                    @foreach($types as $type)
                        <option value="{{ $type }}" @if(request('type') === $type) selected @endif>
                            {{ ucfirst(str_replace('_', ' ', $type)) }}
                        </option>
                    @endforeach
                </select>
            @endif

            @if($unreadCount > 0)
                <form method="POST" action="{{ route('admin.notifications.mark-all-read') }}" class="ml-auto">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition text-sm font-medium">
                        Mark all as read
                    </button>
                </form>
            @endif
        </div>

        <!-- Notifications List -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden">
            @if($notifications->isEmpty())
                <div class="p-8 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                    </svg>
                    <p class="text-gray-600 dark:text-gray-400 mt-4">No notifications found</p>
                </div>
            @else
                <div class="divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach($notifications as $notification)
                        <div class="p-6 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition
                            @if(!$notification->is_read) bg-blue-50/50 dark:bg-blue-900/10 @endif">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <div class="flex items-center gap-3 mb-2">
                                        <span class="text-2xl">{{ $notification->icon }}</span>
                                        <div>
                                            <a href="{{ $notification->action_url }}" class="text-lg font-semibold text-gray-900 dark:text-gray-100 hover:text-emerald-600">
                                                {{ $notification->title }}
                                            </a>
                                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                                {{ $notification->created_at->format('M j, Y g:i A') }}
                                            </p>
                                        </div>
                                    </div>
                                    <p class="text-gray-700 dark:text-gray-300 mb-4">
                                        {{ $notification->message }}
                                    </p>
                                    <div class="inline-block px-3 py-1 bg-gray-100 dark:bg-gray-700 rounded-full text-xs font-medium text-gray-700 dark:text-gray-300">
                                        {{ ucfirst(str_replace('_', ' ', $notification->type)) }}
                                    </div>
                                </div>
                                <div class="ml-4 flex items-center gap-3 flex-shrink-0">
                                    <a href="{{ $notification->action_url }}" class="px-3 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition text-sm font-medium">
                                        View
                                    </a>
                                    @if(!$notification->is_read)
                                        <form method="POST" action="{{ route('admin.notifications.mark-read', $notification->id) }}" class="inline">
                                            @csrf
                                            <button type="submit" class="px-3 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition text-sm font-medium">
                                                Mark read
                                            </button>
                                        </form>
                                    @endif
                                    <form method="POST" action="{{ route('admin.notifications.delete', $notification->id) }}" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="px-3 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition text-sm font-medium" onclick="return confirm('Delete this notification?')">
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="px-6 py-4 bg-gray-50 dark:bg-gray-700/50 border-t border-gray-200 dark:border-gray-700">
                    {{ $notifications->links() }}
                </div>
            @endif
        </div>
    </div>
</x-admin-layout>
