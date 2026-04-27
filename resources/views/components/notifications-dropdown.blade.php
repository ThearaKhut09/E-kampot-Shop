<!-- Notifications Dropdown for User -->
<div class="relative" x-data="{ open: false }" @click.outside="open = false">
    <button @click="open = !open" class="relative p-2 text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-gray-100 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-full transition duration-200">
        <!-- Bell Icon -->
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
        </svg>

        <!-- Notification Badge -->
        @php
            $unreadCount = \App\Services\NotificationService::getUnreadCountForUser(auth()->id());
        @endphp
        @if($unreadCount > 0)
            <span class="absolute top-0 right-0 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white transform translate-x-1/2 -translate-y-1/2 bg-red-500 rounded-full shadow-lg animate-pulse">
                {{ $unreadCount > 99 ? '99+' : $unreadCount }}
            </span>
        @endif
    </button>

    <!-- Dropdown Menu -->
    <div class="absolute right-0 z-50 w-96 mt-2 bg-white dark:bg-gray-800 rounded-xl shadow-2xl border border-gray-100 dark:border-gray-700 overflow-hidden transition-all duration-200"
        :class="{ 'opacity-0 invisible scale-95': !open, 'opacity-100 visible scale-100': open }"
        style="transform-origin: top right;">

        @php
            $notifications = auth()->user()->notifications()->whereNull('admin_id')->latest()->take(8)->get();
        @endphp

        <!-- Header -->
        <div class="px-5 py-4 bg-gradient-to-r from-primary-50 to-emerald-50 dark:from-gray-700 dark:to-gray-700 border-b border-gray-200 dark:border-gray-600">
            <h3 class="text-base font-bold text-gray-900 dark:text-white">
                Notifications
                @if($unreadCount > 0)
                    <span class="inline-flex items-center justify-center w-6 h-6 ml-2 text-xs font-bold text-white bg-red-500 rounded-full">
                        {{ $unreadCount > 9 ? '9+' : $unreadCount }}
                    </span>
                @endif
            </h3>
        </div>

        <!-- Notifications List -->
        <div class="max-h-96 overflow-y-auto divide-y divide-gray-100 dark:divide-gray-700">
            @if($notifications->isEmpty())
                <div class="px-5 py-12 text-center">
                    <div class="mb-3">
                        <svg class="w-16 h-16 mx-auto text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                        </svg>
                    </div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">No notifications yet</p>
                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">You're all caught up!</p>
                </div>
            @else
                @foreach($notifications as $notification)
                    <a href="{{ $notification->action_url }}" class="px-5 py-4 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors duration-150 group block
                        @if(!$notification->is_read) bg-blue-50/50 dark:bg-blue-900/20 @endif">
                        <div class="flex items-start gap-3">
                            <!-- Icon Container -->
                            <div class="flex-shrink-0 mt-1">
                                <div class="flex items-center justify-center w-10 h-10 rounded-lg
                                    @if($notification->type === 'payment_success') bg-green-100 dark:bg-green-900/30
                                    @elseif($notification->type === 'payment_failed') bg-red-100 dark:bg-red-900/30
                                    @elseif($notification->type === 'order_status') bg-blue-100 dark:bg-blue-900/30
                                    @elseif($notification->type === 'review_approved') bg-purple-100 dark:bg-purple-900/30
                                    @else bg-gray-100 dark:bg-gray-700
                                    @endif
                                ">
                                    @if($notification->type === 'payment_success')
                                        <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                        </svg>
                                    @elseif($notification->type === 'payment_failed')
                                        <svg class="w-5 h-5 text-red-600 dark:text-red-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                                        </svg>
                                    @elseif($notification->type === 'order_status')
                                        <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M3 1a1 1 0 000 2h1.22l.305 1.222a.997.997 0 00.01.042l1.358 5.43-.893.892C3.74 11.846 4.632 14 6.414 14H15a1 1 0 000-2H6.414l1-1H14a1 1 0 00.894-.553l3-6A1 1 0 0017 6H6.28l-.31-1.243A1 1 0 005 3H3z"></path>
                                            <path d="M16 16a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM6.5 18a1.5 1.5 0 100-3 1.5 1.5 0 000 3z"></path>
                                        </svg>
                                    @elseif($notification->type === 'review_approved')
                                        <svg class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                        </svg>
                                    @else
                                        <svg class="w-5 h-5 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    @endif
                                </div>
                            </div>

                            <!-- Content -->
                            <div class="flex-1 min-w-0">
                                <div class="flex items-start justify-between gap-2">
                                    <h4 class="text-sm font-semibold text-gray-900 dark:text-white truncate group-hover:text-primary-600 dark:group-hover:text-primary-400">
                                        {{ $notification->title }}
                                    </h4>
                                    @if(!$notification->is_read)
                                        <span class="inline-flex flex-shrink-0 w-2 h-2 bg-blue-500 rounded-full mt-1.5"></span>
                                    @endif
                                </div>
                                <p class="text-xs text-gray-600 dark:text-gray-400 mt-1 line-clamp-2">
                                    {{ $notification->message }}
                                </p>
                                <p class="text-xs text-gray-400 dark:text-gray-500 mt-2 font-medium">
                                    {{ $notification->created_at->diffForHumans() }}
                                </p>
                            </div>

                            <!-- Actions -->
                            <div class="flex-shrink-0 flex gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                <button @click.prevent="
                                    fetch('{{ route('notifications.mark-read', $notification->id) }}', {
                                        method: 'POST',
                                        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                                    }).then(() => location.reload());
                                " class="p-1.5 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 hover:bg-white dark:hover:bg-gray-600 rounded-md transition-colors"
                                    title="Mark as read">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"></path>
                                        <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"></path>
                                    </svg>
                                </button>
                                <button @click.prevent="
                                    fetch('{{ route('notifications.delete', $notification->id) }}', {
                                        method: 'DELETE',
                                        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                                    }).then(() => location.reload());
                                " class="p-1.5 text-gray-400 hover:text-red-600 dark:hover:text-red-400 hover:bg-white dark:hover:bg-gray-600 rounded-md transition-colors"
                                    title="Delete">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </a>
                @endforeach
            @endif
        </div>

        <!-- Footer -->
        @if(!$notifications->isEmpty())
            <div class="px-5 py-3 bg-gray-50 dark:bg-gray-700/50 border-t border-gray-100 dark:border-gray-600">
                <a href="{{ route('notifications.index') }}" class="inline-flex items-center text-sm font-semibold text-primary-600 dark:text-primary-400 hover:text-primary-700 dark:hover:text-primary-300 transition-colors">
                    View all notifications
                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </a>
            </div>
        @endif
    </div>
</div>
