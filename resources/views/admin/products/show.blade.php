@extends('admin.layout')

@section('title', 'Product Details')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $product->name }}</h2>
        <div class="space-x-2">
            <a href="{{ route('admin.products.edit', $product) }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium">
                Edit Product
            </a>
            <a href="{{ route('admin.products.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-md text-sm font-medium">
                Back to Products
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Product Information -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Basic Information -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">Product Information</h3>
                </div>
                <div class="p-6">
                    <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Name</dt>
                            <dd class="text-sm text-gray-900 dark:text-white">{{ $product->name }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">SKU</dt>
                            <dd class="text-sm text-gray-900 dark:text-white">{{ $product->sku }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Price</dt>
                            <dd class="text-sm text-gray-900 dark:text-white">${{ number_format($product->price, 2) }}</dd>
                        </div>
                        @if($product->sale_price)
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Sale Price</dt>
                            <dd class="text-sm text-gray-900 dark:text-white">${{ number_format($product->sale_price, 2) }}</dd>
                        </div>
                        @endif
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Stock Quantity</dt>
                            <dd class="text-sm text-gray-900 dark:text-white {{ $product->stock_quantity < 10 ? 'text-red-600 dark:text-red-400' : '' }}">
                                {{ $product->stock_quantity }}
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Status</dt>
                            <dd class="text-sm">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    @if($product->status === 'active') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                    @else bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200 @endif">
                                    {{ ucfirst($product->status) }}
                                </span>
                                @if($product->featured)
                                    <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
                                        Featured
                                    </span>
                                @endif
                            </dd>
                        </div>
                        @if($product->weight)
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Weight</dt>
                            <dd class="text-sm text-gray-900 dark:text-white">{{ $product->weight }} kg</dd>
                        </div>
                        @endif
                        @if($product->dimensions)
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Dimensions</dt>
                            <dd class="text-sm text-gray-900 dark:text-white">{{ $product->dimensions }}</dd>
                        </div>
                        @endif
                    </dl>

                    @if($product->description)
                    <div class="mt-6">
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Description</dt>
                        <dd class="text-sm text-gray-900 dark:text-white">{{ $product->description }}</dd>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Categories -->
            @if($product->categories->count() > 0)
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">Categories</h3>
                </div>
                <div class="p-6">
                    <div class="flex flex-wrap gap-2">
                        @foreach($product->categories as $category)
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                {{ $category->name }}
                            </span>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

            <!-- Product Images -->
            @if($product->image || $product->gallery)
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">Images</h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        @if($product->image)
                            <div>
                                <img src="{{ Storage::url($product->image) }}" alt="Main Image" class="w-full h-32 object-cover rounded-lg">
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 text-center">Main Image</p>
                            </div>
                        @endif
                        @if($product->gallery)
                            @foreach(json_decode($product->gallery, true) as $index => $galleryImage)
                                <div>
                                    <img src="{{ Storage::url($galleryImage) }}" alt="Gallery Image {{ $index + 1 }}" class="w-full h-32 object-cover rounded-lg">
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 text-center">Gallery {{ $index + 1 }}</p>
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>
            @endif

            <!-- Reviews -->
            @if($product->reviews->count() > 0)
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">Recent Reviews</h3>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        @foreach($product->reviews->take(5) as $review)
                        <div class="border-b border-gray-200 dark:border-gray-700 pb-4 last:border-b-0">
                            <div class="flex items-center justify-between mb-2">
                                <div class="flex items-center">
                                    <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $review->user->name }}</span>
                                    <div class="flex items-center ml-2">
                                        @for($i = 1; $i <= 5; $i++)
                                            <svg class="w-4 h-4 {{ $i <= $review->rating ? 'text-yellow-400' : 'text-gray-300 dark:text-gray-600' }}" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                            </svg>
                                        @endfor
                                    </div>
                                </div>
                                <span class="text-sm text-gray-500 dark:text-gray-400">{{ $review->created_at->format('M j, Y') }}</span>
                            </div>
                            <p class="text-sm text-gray-600 dark:text-gray-400">{{ $review->comment }}</p>
                        </div>
                        @endforeach
                    </div>
                    @if($product->reviews->count() > 5)
                        <div class="mt-4">
                            <a href="{{ route('admin.reviews.index', ['product' => $product->id]) }}" class="text-sm text-blue-600 dark:text-blue-400 hover:text-blue-500">
                                View all {{ $product->reviews->count() }} reviews →
                            </a>
                        </div>
                    @endif
                </div>
            </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Statistics -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">Statistics</h3>
                </div>
                <div class="p-6">
                    <dl class="space-y-4">
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Average Rating</dt>
                            <dd class="text-sm text-gray-900 dark:text-white">
                                @if($product->reviews->count() > 0)
                                    <div class="flex items-center">
                                        <span class="text-yellow-400">★</span>
                                        <span class="ml-1">{{ round($product->reviews->avg('rating'), 1) }}</span>
                                        <span class="ml-1 text-gray-500 dark:text-gray-400">({{ $product->reviews->count() }} reviews)</span>
                                    </div>
                                @else
                                    <span class="text-gray-500 dark:text-gray-400">No reviews yet</span>
                                @endif
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Sales</dt>
                            <dd class="text-sm text-gray-900 dark:text-white">{{ $product->orderItems->sum('quantity') }} units</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Revenue</dt>
                            <dd class="text-sm text-gray-900 dark:text-white">
                                ${{ number_format($product->orderItems->sum(function($item) { return $item->price * $item->quantity; }), 2) }}
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Created</dt>
                            <dd class="text-sm text-gray-900 dark:text-white">{{ $product->created_at->format('M j, Y') }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Last Updated</dt>
                            <dd class="text-sm text-gray-900 dark:text-white">{{ $product->updated_at->format('M j, Y') }}</dd>
                        </div>
                    </dl>
                </div>
            </div>

            <!-- SEO Information -->
            @if($product->meta_title || $product->meta_description)
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">SEO Information</h3>
                </div>
                <div class="p-6">
                    <dl class="space-y-4">
                        @if($product->meta_title)
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Meta Title</dt>
                            <dd class="text-sm text-gray-900 dark:text-white">{{ $product->meta_title }}</dd>
                        </div>
                        @endif
                        @if($product->meta_description)
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Meta Description</dt>
                            <dd class="text-sm text-gray-900 dark:text-white">{{ $product->meta_description }}</dd>
                        </div>
                        @endif
                    </dl>
                </div>
            </div>
            @endif

            <!-- Quick Actions -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">Quick Actions</h3>
                </div>
                <div class="p-6 space-y-3">
                    <a href="{{ route('products.show', $product->slug) }}" target="_blank" class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium text-center block">
                        View on Store
                    </a>
                    <a href="{{ route('admin.products.edit', $product) }}" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-md text-sm font-medium text-center block">
                        Edit Product
                    </a>
                    <form method="POST" action="{{ route('admin.products.destroy', $product) }}" class="w-full">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-md text-sm font-medium" onclick="return confirm('Are you sure you want to delete this product?')">
                            Delete Product
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
