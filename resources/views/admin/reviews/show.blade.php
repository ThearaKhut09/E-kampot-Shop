@extends('admin.layout')

@section('title', 'Review Details')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Review Details</h2>
        <a href="{{ route('admin.reviews.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-md">
            Back to Reviews
        </a>
    </div>

    <!-- Review Details -->
    <div class="bg-white dark:bg-gray-800 shadow overflow-hidden rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Product Information -->
                <div>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Product Information</h3>
                    <div class="space-y-3">
                        <div class="flex items-center space-x-4">
                            @if($review->product->image)
                                <img class="h-16 w-16 rounded object-cover" src="{{ asset('storage/' . $review->product->image) }}" alt="{{ $review->product->name }}">
                            @else
                                <div class="h-16 w-16 bg-gray-300 dark:bg-gray-600 rounded"></div>
                            @endif
                            <div>
                                <p class="text-sm font-medium text-gray-900 dark:text-white">
                                    {{ $review->product->name }}
                                </p>
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    SKU: {{ $review->product->sku }}
                                </p>
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    Price: ${{ number_format($review->product->price, 2) }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Customer Information -->
                <div>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Customer Information</h3>
                    <div class="space-y-3">
                        <div>
                            <p class="text-sm font-medium text-gray-900 dark:text-white">
                                {{ $review->user->name }}
                            </p>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                {{ $review->user->email }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Review Content -->
            <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Review Content</h3>

                <!-- Rating -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Rating</label>
                    <div class="flex items-center space-x-2">
                        <div class="flex text-yellow-400">
                            @for($i = 1; $i <= 5; $i++)
                                <svg class="w-5 h-5 {{ $i <= $review->rating ? 'text-yellow-400' : 'text-gray-300 dark:text-gray-600' }}" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                </svg>
                            @endfor
                        </div>
                        <span class="text-sm text-gray-600 dark:text-gray-400">{{ $review->rating }}/5</span>
                    </div>
                </div>

                <!-- Title -->
                @if($review->title)
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Title</label>
                        <p class="text-gray-900 dark:text-white">{{ $review->title }}</p>
                    </div>
                @endif

                <!-- Comment -->
                @if($review->comment)
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Comment</label>
                        <p class="text-gray-900 dark:text-white">{{ $review->comment }}</p>
                    </div>
                @endif

                <!-- Status and Date -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Status</label>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                            @if($review->is_approved) bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                            @else bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200 @endif">
                            {{ $review->is_approved ? 'Approved' : 'Pending' }}
                        </span>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Date</label>
                        <p class="text-gray-900 dark:text-white">{{ $review->created_at->format('M j, Y g:i A') }}</p>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex space-x-4">
                    @if(!$review->is_approved)
                        <form method="POST" action="{{ route('admin.reviews.approve', $review) }}" class="inline">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md">
                                Approve Review
                            </button>
                        </form>
                    @else
                        <form method="POST" action="{{ route('admin.reviews.reject', $review) }}" class="inline">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded-md">
                                Unapprove Review
                            </button>
                        </form>
                    @endif

                    <form method="POST" action="{{ route('admin.reviews.destroy', $review) }}" class="inline" id="delete-form-review-{{ $review->id }}">
                        @csrf
                        @method('DELETE')
                        <button type="button" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-md" onclick="confirmDelete(document.getElementById('delete-form-review-{{ $review->id }}'), 'Review for {{ $review->product->name }}', 'Review')">
                            Delete Review
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
