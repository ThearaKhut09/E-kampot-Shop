<x-app-layout :title="$category->name . ' - E-Kampot Shop'">
<div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Breadcrumb -->
        <nav class="mb-8" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li class="inline-flex items-center">
                    <a href="{{ route('home') }}" class="text-gray-700 hover:text-primary-600 dark:text-gray-300 dark:hover:text-primary-400">
                        Home
                    </a>
                </li>
                <li>
                    <div class="flex items-center">
                        <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        <a href="{{ route('products.index') }}" class="ml-1 text-gray-700 hover:text-primary-600 dark:text-gray-300 dark:hover:text-primary-400 md:ml-2">
                            Products
                        </a>
                    </div>
                </li>
                <li aria-current="page">
                    <div class="flex items-center">
                        <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="ml-1 text-gray-500 dark:text-gray-400 md:ml-2">{{ $category->name }}</span>
                    </div>
                </li>
            </ol>
        </nav>

        <!-- Category Header -->
        <div class="mb-8">
            <div class="flex items-center space-x-4 mb-6">
                @if($category->image)
                    <img src="{{ Storage::url($category->image) }}"
                         alt="{{ $category->name }}"
                         class="w-16 h-16 object-cover rounded-lg">
                @endif
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">{{ $category->name }}</h1>
                    @if($category->description)
                        <p class="mt-2 text-gray-600 dark:text-gray-400">{{ $category->description }}</p>
                    @endif
                </div>
            </div>

            <!-- Subcategories -->
            @if($category->children && $category->children->count() > 0)
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6 mb-8">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Subcategories</h2>
                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
                        @foreach($category->children as $subcategory)
                            <a href="{{ route('category.show', $subcategory->slug) }}"
                               class="flex flex-col items-center p-4 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200">
                                @if($subcategory->image)
                                    <img src="{{ Storage::url($subcategory->image) }}"
                                         alt="{{ $subcategory->name }}"
                                         class="w-12 h-12 object-cover rounded-lg mb-2">
                                @else
                                    <div class="w-12 h-12 bg-gray-100 dark:bg-gray-600 rounded-lg mb-2 flex items-center justify-center">
                                        <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                        </svg>
                                    </div>
                                @endif
                                <span class="text-sm font-medium text-gray-900 dark:text-white text-center">{{ $subcategory->name }}</span>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        <!-- Search and Filters -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6 mb-8">
            <form method="GET" action="{{ route('category.show', $category->slug) }}" class="space-y-4">
                <!-- Search Bar -->
                <div class="flex flex-col sm:flex-row gap-4">
                    <div class="flex-1">
                        <input type="text" name="search" value="{{ request('search') }}"
                               placeholder="Search products in {{ $category->name }}..."
                               class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                    </div>
                    <button type="submit" class="px-6 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg font-medium transition-colors duration-200">
                        Search
                    </button>
                </div>

                <!-- Filters Row -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
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
                @if(request()->hasAny(['search', 'min_price', 'max_price', 'min_rating']))
                    <div class="flex justify-end">
                        <a href="{{ route('category.show', $category->slug) }}"
                           class="px-4 py-2 text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-white border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200">
                            Clear Filters
                        </a>
                    </div>
                @endif
            </form>
        </div>

        <!-- Products Grid -->
        @if($products->count() > 0)
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
                                <a href="{{ route('products.show', $product->slug) }}" class="hover:text-primary-600 dark:hover:text-primary-400">
                                    {{ $product->title ?: $product->name }}
                                </a>
                            </h3>

                            @if($product->title && $product->title !== $product->name)
                                <p class="text-gray-500 dark:text-gray-400 text-xs mb-2">
                                    Name: {{ $product->name }}
                                </p>
                            @endif

                            <p class="text-gray-600 dark:text-gray-400 text-sm mb-3 line-clamp-2">
                                {{ $product->short_description }}
                            </p>

                            <!-- Rating -->
                            @if($product->average_rating > 0)
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
                                    @if($product->stock_quantity > 0)
                                        <span class="text-green-600 dark:text-green-400">In Stock ({{ $product->stock_quantity }})</span>
                                    @else
                                        <span class="text-red-600 dark:text-red-400">Out of Stock</span>
                                    @endif
                                </div>
                            </div>

                            <!-- Add to Cart Button (Customer Only) -->
                            @auth
                                @if(auth()->user()->hasRole('customer'))
                                    @if($product->stock_quantity > 0)
                                        <button onclick="addToCart({{ $product->id }})"
                                                class="w-full bg-primary-600 hover:bg-primary-700 text-white font-medium py-2 px-4 rounded-lg transition-colors duration-200">
                                            Add to Cart
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
                                <a href="{{ route('login') }}" class="w-full bg-primary-600 hover:bg-primary-700 text-white font-medium py-2 px-4 rounded-lg transition-colors duration-200 block text-center">
                                    Login to Purchase
                                </a>
                            @endauth
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="flex justify-center">
                {{ $products->appends(request()->query())->links() }}
            </div>
        @else
            <!-- No Products Found -->
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2 2v-5m16 0h-2M4 13h2m-2-4v2m0 0h2m14-2v2m0 0h-2"></path>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No products found in {{ $category->name }}</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    @if(request()->hasAny(['search', 'min_price', 'max_price', 'min_rating']))
                        Try adjusting your search or filter criteria.
                    @else
                        Products will appear here once they are added to this category.
                    @endif
                </p>
                @if(request()->hasAny(['search', 'min_price', 'max_price', 'min_rating']))
                    <div class="mt-6">
                        <a href="{{ route('category.show', $category->slug) }}"
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
