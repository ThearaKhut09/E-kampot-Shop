@props([
    'product',
    'mode' => 'shop',
    'showCategories' => false,
    'categoriesLimit' => 2,
    'showNewBadge' => false,
    'newBadgeText' => 'New',
    'priceField' => null,
])

@php
    $displayTitle = $product->title ?: $product->name;
    $productUrl = $product->slug ? route('products.show', $product->slug) : null;

    $primaryImage = data_get($product, 'primary_image');
    $fallbackImage = data_get($product, 'image');
    $imagePath = $primaryImage ?: $fallbackImage;

    $resolvedPrice = null;
    if ($priceField && !is_null(data_get($product, $priceField))) {
        $resolvedPrice = (float) data_get($product, $priceField);
    } elseif (!is_null(data_get($product, 'price'))) {
        $resolvedPrice = (float) data_get($product, 'price');
    } elseif (!is_null(data_get($product, 'current_price'))) {
        $resolvedPrice = (float) data_get($product, 'current_price');
    }

    $averageRating = (float) data_get($product, 'average_rating', data_get($product, 'reviews_avg_rating', 0));
    $reviewCount = (int) data_get($product, 'review_count', data_get($product, 'reviews_count', 0));

    $stockQuantity = data_get($product, 'stock_quantity');
    $inStockFlag = data_get($product, 'in_stock');
    $inStock = !is_null($stockQuantity) ? (int) $stockQuantity > 0 : (bool) $inStockFlag;
@endphp

<div class="card hover:shadow-lg transition-shadow duration-200 h-full">
    <div class="aspect-square card-media relative bg-gray-200 dark:bg-gray-700">
        @if($showNewBadge)
            <span class="absolute top-2 left-2 bg-green-500 text-white px-2 py-1 text-xs rounded z-10">{{ $newBadgeText }}</span>
        @endif

        @if($imagePath)
            <img src="{{ asset('storage/' . $imagePath) }}"
                 alt="{{ $displayTitle }}"
                 class="w-full h-full object-cover">
        @else
            <div class="w-full h-full flex items-center justify-center bg-gray-100 dark:bg-gray-700">
                <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
            </div>
        @endif
    </div>

    <div class="card-body">
        @if($showCategories && isset($product->categories) && $product->categories->count() > 0)
            <div class="flex flex-wrap gap-1 mb-2">
                @foreach($product->categories->take($categoriesLimit) as $category)
                    <span class="badge-primary">{{ $category->name }}</span>
                @endforeach
            </div>
        @endif

        <h3 class="font-semibold text-gray-900 dark:text-white mb-2 line-clamp-2">
            @if($productUrl)
                <a href="{{ $productUrl }}" class="hover:text-primary-600 dark:hover:text-primary-400 transition-colors">
                    {{ $displayTitle }}
                </a>
            @else
                {{ $displayTitle }}
            @endif
        </h3>

        @if($mode === 'home')
            <div class="mt-auto">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-lg font-bold text-gray-900 dark:text-white">
                        ${{ number_format($resolvedPrice ?? 0, 2) }}
                    </span>
                    <button onclick="addToCart({{ $product->id }})" class="btn-primary px-3 py-2 text-sm">
                        Add to Cart
                    </button>
                </div>

                @if($averageRating > 0)
                    <div class="flex items-center">
                        <div class="flex text-yellow-400">
                            @for($i = 1; $i <= 5; $i++)
                                @if($i <= $averageRating)
                                    <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20">
                                        <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
                                    </svg>
                                @else
                                    <svg class="w-4 h-4 text-gray-300 fill-current" viewBox="0 0 20 20">
                                        <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
                                    </svg>
                                @endif
                            @endfor
                        </div>
                        <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">
                            ({{ $reviewCount }} {{ Str::plural('review', $reviewCount) }})
                        </span>
                    </div>
                @endif
            </div>
        @elseif($mode === 'shop')
            @if($averageRating > 0)
                <div class="flex items-center mb-3">
                    <div class="flex text-yellow-400">
                        @for($i = 1; $i <= 5; $i++)
                            @if($i <= $averageRating)
                                <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24">
                                    <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                </svg>
                            @else
                                <svg class="w-4 h-4 text-gray-300 dark:text-gray-600" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                </svg>
                            @endif
                        @endfor
                    </div>
                    <span class="text-sm text-gray-600 dark:text-gray-400 ml-2">({{ $reviewCount }})</span>
                </div>
            @endif

            <div class="mt-auto">
                <div class="flex items-center justify-between mb-4">
                    <span class="text-lg font-bold text-gray-900 dark:text-white">
                        ${{ number_format($resolvedPrice ?? 0, 2) }}
                    </span>
                    <div class="text-sm">
                        @if($inStock)
                            @if(!is_null($stockQuantity))
                                <span class="text-green-600 dark:text-green-400">In Stock ({{ $stockQuantity }})</span>
                            @else
                                <span class="text-green-600 dark:text-green-400">In Stock</span>
                            @endif
                        @else
                            <span class="text-red-600 dark:text-red-400">Out of Stock</span>
                        @endif
                    </div>
                </div>

                @auth
                    @if(auth()->user()->hasRole('customer'))
                        @if($inStock)
                            <button onclick="addToCart({{ $product->id }})" class="w-full btn-primary flex items-center justify-center space-x-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-2.5 2.5M7 13l2.5 2.5m6-6.5h.01M17 13v.01"></path>
                                </svg>
                                <span>Add to Cart</span>
                            </button>
                        @else
                            <button disabled class="w-full btn-ghost cursor-not-allowed">Out of Stock</button>
                        @endif
                    @else
                        <div class="w-full bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400 font-medium py-2 px-4 rounded-lg text-center border border-dashed border-gray-300 dark:border-gray-600">
                            Admin View Only
                        </div>
                    @endif
                @else
                    <a href="{{ route('login') }}" class="w-full btn-primary flex items-center justify-center space-x-2">
                        <span>Login to Purchase</span>
                    </a>
                @endauth
            </div>
        @else
            <div class="text-lg font-bold text-gray-900 dark:text-white mt-auto">
                ${{ number_format($resolvedPrice ?? 0, 2) }}
            </div>
        @endif
    </div>
</div>
