<x-app-layout>
    <x-slot name="title">E-Kampot Shop - Your One-Stop Shopping Destination</x-slot>

    <!-- Hero Section -->
    <section class="relative overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-br from-primary-600 via-primary-700 to-primary-900"></div>
        <div class="relative text-white py-20">
            <div class="container-app">
                <div class="text-center">
                    <h1 class="text-4xl md:text-6xl font-bold mb-6 tracking-tight">Welcome to E-Kampot Shop</h1>
                    <p class="text-lg md:text-2xl mb-10 text-white/90">Discover amazing products at unbeatable prices</p>
                    <div class="flex items-center justify-center gap-4 flex-wrap">
                        <a href="{{ route('products.index') }}" class="btn-primary px-6 py-3 text-base">
                            Shop Now
                        </a>
                        <a href="{{ route('about') }}" class="btn-secondary px-6 py-3 text-base border-0 bg-white/10 text-white hover:bg-white/20">
                            Learn More
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Featured Categories -->
    <section class="py-16 bg-white dark:bg-gray-900 transition-colors duration-200">
        <div class="container-app">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-4">Shop by Category</h2>
                <p class="text-lg text-gray-600 dark:text-gray-400">Browse our wide range of product categories</p>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                @foreach($categories as $category)
                    <a href="{{ route('category.show', $category->slug) }}" class="group text-center">
                        <div class="card p-8 hover:shadow-lg transition-shadow duration-200">
                            <div class="text-4xl mb-4">
                                @switch($category->name)
                                    @case('Phones')
                                        📱
                                        @break
                                    @case('Electronics')
                                        💻
                                        @break
                                    @case('Fashion')
                                        👗
                                        @break
                                    @case('Home & Kitchen')
                                        🏠
                                        @break
                                    @case('Beauty & Personal Care')
                                        💄
                                        @break
                                    @case('Sports & Outdoors')
                                        ⚽
                                        @break
                                    @case('Books & Stationery')
                                        📚
                                        @break
                                    @case('Toys & Games')
                                        🎮
                                        @break
                                    @default
                                        🛍️
                                @endswitch
                            </div>
                            <h3 class="font-semibold text-gray-900 dark:text-white group-hover:text-primary-600 dark:group-hover:text-primary-400 transition-colors">
                                {{ $category->name }}
                            </h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">{{ $category->products_count ?? 'Many' }} products</p>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Featured Products -->
    <section class="py-16 bg-gray-50 dark:bg-gray-800 transition-colors duration-200">
        <div class="container-app">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-4">Featured Products</h2>
                <p class="text-lg text-gray-600 dark:text-gray-400">Hand-picked products just for you</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach($featuredProducts as $product)
                    <div class="card hover:shadow-lg transition-shadow duration-200">
                        <div class="aspect-square card-media">
                            @if($product->primary_image)
                                <img src="{{ asset('storage/' . $product->primary_image) }}"
                                     alt="{{ $product->title ?: $product->name }}"
                                     class="">
                            @else
                                <img src="https://via.placeholder.com/400x400/e5e7eb/6b7280?text=No+Image"
                                     alt="{{ $product->title ?: $product->name }}"
                                     class="">
                            @endif
                        </div>
                        <div class="card-body">
                            <div class="flex flex-wrap gap-1 mb-2">
                                @foreach($product->categories->take(2) as $category)
                                    <span class="badge-primary">
                                        {{ $category->name }}
                                    </span>
                                @endforeach
                            </div>
                            <h3 class="font-semibold text-gray-900 dark:text-white mb-2 line-clamp-2">
                                <a href="{{ route('products.show', $product->slug) }}" class="hover:text-primary-600 dark:hover:text-primary-400 transition-colors">
                                    {{ $product->title ?: $product->name }}
                                </a>
                            </h3>
                            @if($product->title && $product->title !== $product->name)
                                <p class="text-gray-500 dark:text-gray-400 text-xs mb-2">
                                    Name: {{ $product->name }}
                                </p>
                            @endif
                            <p class="text-gray-600 dark:text-gray-400 text-sm mb-3 line-clamp-2">{{ $product->description }}</p>
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-2">
                                    <span class="text-lg font-bold text-gray-900 dark:text-white">
                                        ${{ number_format($product->price, 2) }}
                                    </span>
                                </div>
                                <button onclick="addToCart({{ $product->id }})"
                                        class="btn-primary px-3 py-2 text-sm">
                                    Add to Cart
                                </button>
                            </div>
                            @if($product->average_rating > 0)
                                <div class="flex items-center mt-2">
                                    <div class="flex text-yellow-400">
                                        @for($i = 1; $i <= 5; $i++)
                                            @if($i <= $product->average_rating)
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
                                        ({{ $product->review_count }} {{ Str::plural('review', $product->review_count) }})
                                    </span>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="text-center mt-12">
                <a href="{{ route('products.index') }}" class="btn-primary px-8 py-3">
                    View All Products
                </a>
            </div>
        </div>
    </section>

    <!-- New Arrivals -->
    @if($newProducts->count() > 0)
    <section class="py-16 bg-white dark:bg-gray-900 transition-colors duration-200">
        <div class="container-app">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-4">New Arrivals</h2>
                <p class="text-lg text-gray-600 dark:text-gray-400">Fresh products just added to our collection</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach($newProducts as $product)
                    <div class="card hover:shadow-lg transition-shadow duration-200">
                        <div class="aspect-square card-media relative">
                            <span class="absolute top-2 left-2 bg-green-500 text-white px-2 py-1 text-xs rounded z-10">New</span>
                            @if($product->primary_image)
                                <img src="{{ asset('storage/' . $product->primary_image) }}"
                                     alt="{{ $product->title ?: $product->name }}"
                                     class="">
                            @else
                                <img src="https://via.placeholder.com/400x400/e5e7eb/6b7280?text=No+Image"
                                     alt="{{ $product->title ?: $product->name }}"
                                     class="">
                            @endif
                        </div>
                        <div class="card-body">
                            <div class="flex flex-wrap gap-1 mb-2">
                                @foreach($product->categories->take(2) as $category)
                                    <span class="badge-primary">
                                        {{ $category->name }}
                                    </span>
                                @endforeach
                            </div>
                            <h3 class="font-semibold text-gray-900 dark:text-white mb-2 line-clamp-2">
                                <a href="{{ route('products.show', $product->slug) }}" class="hover:text-primary-600 dark:hover:text-primary-400 transition-colors">
                                    {{ $product->title ?: $product->name }}
                                </a>
                            </h3>
                            @if($product->title && $product->title !== $product->name)
                                <p class="text-gray-500 dark:text-gray-400 text-xs mb-2">
                                    Name: {{ $product->name }}
                                </p>
                            @endif
                            <p class="text-gray-600 dark:text-gray-400 text-sm mb-3 line-clamp-2">{{ $product->description }}</p>
                            <div class="flex items-center justify-between">
                                <span class="text-lg font-bold text-gray-900 dark:text-white">
                                    ${{ number_format($product->current_price, 2) }}
                                </span>
                                <button onclick="addToCart({{ $product->id }})"
                                        class="btn-primary px-3 py-2 text-sm">
                                    Add to Cart
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif

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
