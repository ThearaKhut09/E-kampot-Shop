<x-app-layout title="Products - E-Kampot Shop">
<div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Page Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Products</h1>
            <p class="mt-2 text-gray-600 dark:text-gray-400">Discover our amazing collection of products</p>
        </div>

        <!-- Search and Filters -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6 mb-8">
            <form method="GET" action="{{ route('products.index') }}" class="space-y-4">
                <!-- Search Bar -->
                <div class="flex flex-col sm:flex-row gap-4">
                    <div class="flex-1">
                        <input type="text" name="search" value="{{ request('search') }}"
                               placeholder="Search products..."
                               class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                    </div>
                    <button type="submit" class="px-6 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg font-medium transition-colors duration-200">
                        Search
                    </button>
                </div>

                <!-- Filters Row -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <!-- Category Filter -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Category</label>
                        <select name="category" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                            <option value="">All Categories</option>
                            @if($categories)
                                @foreach($categories as $category)
                                    <option value="{{ $category->slug }}" {{ request('category') === $category->slug ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            @endif
                        </select>
                    </div>

                    <!-- Price Range -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Min Price</label>
                        <input type="number" name="min_price" value="{{ request('min_price') }}"
                               placeholder="$0" min="0" step="0.01"
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Max Price</label>
                        <input type="number" name="max_price" value="{{ request('max_price') }}"
                               placeholder="$999" min="0" step="0.01"
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                    </div>

                    <!-- Sort Options -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Sort By</label>
                        <select name="sort" onchange="this.form.submit()" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                            <option value="latest" {{ request('sort') === 'latest' ? 'selected' : '' }}>Latest</option>
                            <option value="price_low" {{ request('sort') === 'price_low' ? 'selected' : '' }}>Price: Low to High</option>
                            <option value="price_high" {{ request('sort') === 'price_high' ? 'selected' : '' }}>Price: High to Low</option>
                            <option value="rating" {{ request('sort') === 'rating' ? 'selected' : '' }}>Highest Rated</option>
                            <option value="name" {{ request('sort') === 'name' ? 'selected' : '' }}>Name A-Z</option>
                        </select>
                    </div>
                </div>

                <!-- Clear Filters Button -->
                @if(request()->hasAny(['search', 'category', 'min_price', 'max_price', 'min_rating']))
                    <div class="flex justify-end">
                        <a href="{{ route('products.index') }}"
                           class="px-4 py-2 text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-white border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200">
                            Clear Filters
                        </a>
                    </div>
                @endif
            </form>
        </div>

        <!-- Products Grid -->
        @if($products && $products->count() > 0)
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 mb-8">
                @foreach($products as $product)
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden hover:shadow-lg transition-shadow duration-200">
                        <!-- Product Image -->
                        <div class="aspect-w-1 aspect-h-1 bg-gray-200 dark:bg-gray-700">
                            @if($product->image)
                                <img src="{{ asset('storage/' . $product->image) }}"
                                     alt="{{ $product->title ?: $product->name }}"
                                     class="w-full h-48 object-cover">
                            @else
                                <div class="w-full h-48 flex items-center justify-center bg-gray-100 dark:bg-gray-700">
                                    <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                            @endif
                        </div>

                        <!-- Product Info -->
                        <div class="p-4">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2 line-clamp-2">
                                @if($product->slug)
                                    <a href="{{ route('products.show', $product->slug) }}" class="hover:text-primary-600 dark:hover:text-primary-400">
                                        {{ $product->title ?: $product->name }}
                                    </a>
                                @else
                                    {{ $product->title ?: $product->name }}
                                @endif
                            </h3>

                            @if($product->title && $product->title !== $product->name)
                                <p class="text-gray-500 dark:text-gray-400 text-xs mb-2">
                                    Name: {{ $product->name }}
                                </p>
                            @endif

                            @if($product->description)
                                <p class="text-gray-600 dark:text-gray-400 text-sm mb-3 line-clamp-2">
                                    {{ $product->description }}
                                </p>
                            @endif

                            <!-- Rating -->
                            @if(isset($product->average_rating) && $product->average_rating > 0)
                                <div class="flex items-center mb-3">
                                    <div class="flex text-yellow-400">
                                        @for($i = 1; $i <= 5; $i++)
                                            @if($i <= $product->average_rating)
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
                                    <span class="text-sm text-gray-600 dark:text-gray-400 ml-2">
                                        ({{ $product->reviews_count ?? 0 }})
                                    </span>
                                </div>
                            @endif

                            <!-- Price and Stock -->
                            <div class="flex items-center justify-between mb-4">
                                <div class="text-2xl font-bold text-primary-600 dark:text-primary-400">
                                    ${{ number_format($product->price, 2) }}
                                </div>
                                <div class="text-sm">
                                    @if(isset($product->stock_quantity) && $product->stock_quantity > 0)
                                        <span class="text-green-600 dark:text-green-400">In Stock ({{ $product->stock_quantity }})</span>
                                    @elseif(isset($product->in_stock) && $product->in_stock)
                                        <span class="text-green-600 dark:text-green-400">In Stock</span>
                                    @else
                                        <span class="text-red-600 dark:text-red-400">Out of Stock</span>
                                    @endif
                                </div>
                            </div>

                            <!-- Add to Cart Button (Customer Only) -->
                            @auth
                                @if(auth()->user()->hasRole('customer'))
                                    @if((isset($product->stock_quantity) && $product->stock_quantity > 0) || (isset($product->in_stock) && $product->in_stock))
                                        <button onclick="addToCart({{ $product->id }})"
                                                class="w-full bg-primary-600 hover:bg-primary-700 text-white font-medium py-2 px-4 rounded-lg transition-colors duration-200 flex items-center justify-center space-x-2">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-2.5 2.5M7 13l2.5 2.5m6-6.5h.01M17 13v.01"></path>
                                            </svg>
                                            <span>Add to Cart</span>
                                        </button>
                                    @else
                                        <button disabled
                                                class="w-full bg-gray-300 dark:bg-gray-600 text-gray-500 dark:text-gray-400 font-medium py-2 px-4 rounded-lg cursor-not-allowed">
                                            Out of Stock
                                        </button>
                                    @endif
                                @else
                                    <div class="w-full bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400 font-medium py-2 px-4 rounded-lg text-center border border-dashed border-gray-300 dark:border-gray-600">
                                        Admin View Only
                                    </div>
                                @endif
                            @else
                                <a href="{{ route('login') }}" class="w-full bg-primary-600 hover:bg-primary-700 text-white font-medium py-2 px-4 rounded-lg transition-colors duration-200 flex items-center justify-center space-x-2">
                                    <span>Login to Purchase</span>
                                </a>
                            @endauth
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            @if(method_exists($products, 'links'))
                <div class="flex justify-center">
                    {{ $products->appends(request()->query())->links() }}
                </div>
            @endif
        @else
            <!-- No Products Found -->
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2 2v-5m16 0h-2M4 13h2m-2-4v2m0 0h2m14-2v2m0 0h-2"></path>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No products found</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    @if(request()->hasAny(['search', 'category', 'min_price', 'max_price', 'min_rating']))
                        Try adjusting your search or filter criteria.
                    @else
                        Products will appear here once they are added to the store.
                    @endif
                </p>
                @if(request()->hasAny(['search', 'category', 'min_price', 'max_price', 'min_rating']))
                    <div class="mt-6">
                        <a href="{{ route('products.index') }}"
                           class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                            Clear All Filters
                        </a>
                    </div>
                @endif
            </div>
        @endif
    </div>
</div>
@push('scripts')
<script>
    function addToCart(productId) {
        @auth
            @if(auth()->user()->hasRole('customer'))
                fetch('{{ route("cart.add") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        product_id: productId,
                        quantity: 1
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showToast(data.message, 'success');
                        updateCartCount();
                    } else {
                        showToast(data.message, 'error');
                    }
                })
                .catch(error => {
                    showToast('Error adding product to cart', 'error');
                });
            @else
                showToast('Only customers can add items to cart', 'error');
            @endif
        @else
            showToast('Please login to add items to cart', 'error');
            setTimeout(() => {
                window.location.href = '{{ route("login") }}';
            }, 1500);
        @endauth
    }
</script>
@endpush
</x-app-layout>
