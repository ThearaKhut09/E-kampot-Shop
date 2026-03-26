<x-app-layout :title="__('ui.products') . ' - E-Kampot Shop'">
<div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-8">
    <div class="container-app">
        <!-- Page Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">{{ __('ui.products') }}</h1>
            <p class="mt-2 text-gray-600 dark:text-gray-400">Discover our amazing collection of products</p>
        </div>

        <!-- Search and Filters -->
        <div class="card p-6 mb-8">
            <form method="GET" action="{{ route('products.index') }}" class="space-y-4">
                <!-- Search Bar -->
                <div class="flex flex-col sm:flex-row gap-4">
                    <div class="flex-1">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search products..." class="input">
                    </div>
                    <button type="submit" class="btn-primary px-6 py-2">
                        Search
                    </button>
                </div>

                <!-- Filters Row -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <!-- Category Filter -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('ui.category') }}</label>
                        <select name="category" class="input">
                            <option value="">{{ __('ui.all_categories') }}</option>
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
                        <input type="number" name="min_price" value="{{ request('min_price') }}" placeholder="$0" min="0" step="0.01" class="input">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Max Price</label>
                        <input type="number" name="max_price" value="{{ request('max_price') }}" placeholder="$999" min="0" step="0.01" class="input">
                    </div>

                    <!-- Sort Options -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Sort By</label>
                        <select name="sort" onchange="this.form.submit()" class="input">
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
                        <a href="{{ route('products.index') }}" class="btn-ghost">
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
                    <x-product.card :product="$product" mode="shop" />
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
