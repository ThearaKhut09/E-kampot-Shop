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
                <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-4">{{ __('ui.shop_by_category') }}</h2>
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
                <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-4">{{ __('ui.featured_products') }}</h2>
                <p class="text-lg text-gray-600 dark:text-gray-400">Hand-picked products just for you</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach($featuredProducts as $product)
                    <x-product.card :product="$product" mode="home" :show-categories="true" />
                @endforeach
            </div>

            <div class="text-center mt-12">
                <a href="{{ route('products.index') }}" class="btn-primary px-8 py-3">
                    {{ __('ui.view_all_products') }}
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
                    <x-product.card
                        :product="$product"
                        mode="home"
                        :show-categories="true"
                        :show-new-badge="true"
                        price-field="current_price"
                    />
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
