<x-app-layout>
    <x-slot name="title">Shopping Cart - E-Kampot Shop</x-slot>

<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="text-center mb-8">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100 mb-2">
            Shopping Cart
        </h1>
        <p class="text-gray-600 dark:text-gray-400">
            Review your items before checkout
        </p>
    </div>

    <!-- Success Message -->
    @if(session('success'))
    <div class="mb-8 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-700 rounded-lg p-6">
        <div class="flex items-center">
            <svg class="w-6 h-6 text-green-600 dark:text-green-400 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <div>
                <h3 class="text-lg font-semibold text-green-800 dark:text-green-200 mb-1">
                    🎉 Checkout Successful!
                </h3>
                <p class="text-green-700 dark:text-green-300">
                    {{ session('success') }}
                </p>
            </div>
        </div>
    </div>
    @endif

    @if($cartItems->count() > 0)
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
            <!-- Cart Items -->
            <div class="divide-y divide-gray-200 dark:divide-gray-700">
                @foreach($cartItems as $item)
                    <div class="p-6 cart-item" data-cart-id="{{ $item->id }}">
                        <div class="flex items-center space-x-4">
                            <!-- Product Image -->
                            <div class="flex-shrink-0">
                                @if($item->product->image_url)
                                    <img src="{{ $item->product->image_url }}"
                                         alt="{{ $item->product->name }}"
                                         class="w-20 h-20 object-cover rounded-lg border border-gray-200 dark:border-gray-600">
                                @else
                                    <div class="w-20 h-20 bg-gray-200 dark:bg-gray-700 rounded-lg flex items-center justify-center">
                                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                    </div>
                                @endif
                            </div>

                            <!-- Product Details -->
                            <div class="flex-1 min-w-0">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-1">
                                    <a href="{{ route('products.show', $item->product->slug) }}"
                                       class="hover:text-primary-600 dark:hover:text-primary-400 transition-colors">
                                        {{ $item->product->name }}
                                    </a>
                                </h3>
                                <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">
                                    {{ Str::limit($item->product->description, 100) }}
                                </p>
                                <div class="flex items-center space-x-4">
                                    <span class="text-lg font-bold text-primary-600 dark:text-primary-400">
                                        ${{ number_format($item->product->current_price, 2) }}
                                    </span>
                                    @if($item->product->is_on_sale)
                                        <span class="text-sm text-gray-500 line-through">
                                            ${{ number_format($item->product->price, 2) }}
                                        </span>
                                        <span class="bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200 text-xs font-semibold px-2 py-1 rounded">
                                            {{ $item->product->discount_percentage }}% OFF
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <!-- Quantity Controls -->
                            <div class="flex items-center space-x-3">
                                <button type="button"
                                        onclick="updateQuantity({{ $item->id }}, {{ $item->quantity - 1 }})"
                                        class="w-8 h-8 rounded-full bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400 hover:bg-gray-200 dark:hover:bg-gray-600 flex items-center justify-center transition-colors"
                                        {{ $item->quantity <= 1 ? 'disabled' : '' }}>
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                                    </svg>
                                </button>

                                <span class="quantity-display w-8 text-center font-semibold text-gray-900 dark:text-gray-100">
                                    {{ $item->quantity }}
                                </span>

                                <button type="button"
                                        onclick="updateQuantity({{ $item->id }}, {{ $item->quantity + 1 }})"
                                        class="w-8 h-8 rounded-full bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400 hover:bg-gray-200 dark:hover:bg-gray-600 flex items-center justify-center transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                    </svg>
                                </button>
                            </div>

                            <!-- Item Total -->
                            <div class="text-right">
                                <div class="text-lg font-bold text-gray-900 dark:text-gray-100 item-total">
                                    ${{ number_format($item->total, 2) }}
                                </div>
                                <button type="button"
                                        onclick="removeItem({{ $item->id }})"
                                        class="text-sm text-red-600 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300 mt-1 transition-colors">
                                    Remove
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Cart Summary -->
            <div class="bg-gray-50 dark:bg-gray-700 px-6 py-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <button type="button"
                                onclick="clearCart()"
                                class="text-sm text-red-600 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300 transition-colors">
                            Clear Cart
                        </button>
                    </div>
                    <div class="text-right">
                        <div class="text-sm text-gray-600 dark:text-gray-400 mb-1">
                            Total ({{ $cartItems->sum('quantity') }} items)
                        </div>
                        <div class="text-2xl font-bold text-gray-900 dark:text-gray-100" id="cart-total">
                            ${{ number_format($total, 2) }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Checkout Section -->
        <div class="mt-8 flex flex-col sm:flex-row gap-4">
            <a href="{{ route('products.index') }}"
               class="flex-1 bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-gray-100 font-semibold py-3 px-6 rounded-lg text-center hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
                Continue Shopping
            </a>
            <button type="button"
                    id="quick-checkout-btn"
                    class="flex-1 bg-primary-600 hover:bg-primary-700 text-white font-semibold py-3 px-6 rounded-lg transition-all duration-200 shadow-lg hover:shadow-xl disabled:opacity-50 disabled:cursor-not-allowed transform hover:-translate-y-0.5"
                    onclick="proceedToCheckout()">
                <span id="checkout-btn-text" class="flex items-center justify-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                    </svg>
                    Proceed to Checkout
                </span>
                <span id="checkout-btn-loading" class="hidden flex items-center justify-center">
                    <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Processing Order...
                </span>
            </button>
        </div>
    @else
        <!-- Empty Cart -->
        <div class="text-center py-16">
            <div class="mb-8">
                <svg class="w-24 h-24 text-gray-300 dark:text-gray-600 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l-1 10H6L5 9z"></path>
                </svg>
            </div>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-4">
                Your cart is empty
            </h2>
            <p class="text-gray-600 dark:text-gray-400 mb-8">
                Add some products to your cart to get started
            </p>
            <a href="{{ route('products.index') }}"
               class="bg-primary-600 hover:bg-primary-700 text-white font-semibold py-3 px-8 rounded-lg inline-flex items-center transition-colors shadow-lg hover:shadow-xl">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l-1 10H6L5 9z"></path>
                </svg>
                Start Shopping
            </a>
        </div>
    @endif
</div>

<!-- Loading Overlay -->
<div id="loading-overlay" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden items-center justify-center">
    <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-xl">
        <div class="flex items-center space-x-3">
            <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-primary-600"></div>
            <span class="text-gray-900 dark:text-gray-100">Updating cart...</span>
        </div>
    </div>
</div>

<!-- Clear Cart Confirmation Modal -->
<div id="clear-cart-modal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden items-center justify-center">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-xs w-full mx-4">
        <div class="p-4">
            <div class="flex items-center justify-center w-10 h-10 mx-auto mb-3 bg-red-100 dark:bg-red-900 rounded-full">
                <svg class="w-5 h-5 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                </svg>
            </div>
            <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100 text-center mb-1">
                Clear Cart
            </h3>
            <p class="text-sm text-gray-600 dark:text-gray-400 text-center mb-4">
                Are you sure you want to clear your entire cart? This action cannot be undone.
            </p>
            <div class="flex space-x-2">
                <button type="button"
                        onclick="closeClearCartModal()"
                        class="flex-1 text-sm bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-gray-100 font-medium py-1.5 px-3 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
                    Cancel
                </button>
                <button type="button"
                        onclick="confirmClearCart()"
                        class="flex-1 text-sm bg-red-600 hover:bg-red-700 text-white font-medium py-1.5 px-3 rounded-lg transition-colors">
                    Clear Cart
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Remove Item Confirmation Modal -->
<div id="remove-item-modal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden items-center justify-center">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-xs w-full mx-4">
        <div class="p-4">
            <div class="flex items-center justify-center w-10 h-10 mx-auto mb-3 bg-orange-100 dark:bg-orange-900 rounded-full">
                <svg class="w-5 h-5 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                </svg>
            </div>
            <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100 text-center mb-1">
                Remove Item
            </h3>
            <p class="text-sm text-gray-600 dark:text-gray-400 text-center mb-4">
                Are you sure you want to remove this item from your cart?
            </p>
            <div class="flex space-x-2">
                <button type="button"
                        onclick="closeRemoveItemModal()"
                        class="flex-1 text-sm bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-gray-100 font-medium py-1.5 px-3 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
                    Cancel
                </button>
                <button type="button"
                        onclick="confirmRemoveItem()"
                        class="flex-1 text-sm bg-orange-600 hover:bg-orange-700 text-white font-medium py-1.5 px-3 rounded-lg transition-colors">
                    Remove
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
let currentCartIdToRemove = null;

function showLoading() {
    document.getElementById('loading-overlay').classList.remove('hidden');
    document.getElementById('loading-overlay').classList.add('flex');
}

function hideLoading() {
    document.getElementById('loading-overlay').classList.add('hidden');
    document.getElementById('loading-overlay').classList.remove('flex');
}

function showClearCartModal() {
    document.getElementById('clear-cart-modal').classList.remove('hidden');
    document.getElementById('clear-cart-modal').classList.add('flex');
}

function closeClearCartModal() {
    document.getElementById('clear-cart-modal').classList.add('hidden');
    document.getElementById('clear-cart-modal').classList.remove('flex');
}

function showRemoveItemModal(cartId) {
    currentCartIdToRemove = cartId;
    document.getElementById('remove-item-modal').classList.remove('hidden');
    document.getElementById('remove-item-modal').classList.add('flex');
}

function closeRemoveItemModal() {
    currentCartIdToRemove = null;
    document.getElementById('remove-item-modal').classList.add('hidden');
    document.getElementById('remove-item-modal').classList.remove('flex');
}

function updateQuantity(cartId, newQuantity) {
    if (newQuantity < 1) return;

    showLoading();

    fetch(`/cart/${cartId}`, {
        method: 'PATCH',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            quantity: newQuantity
        })
    })
    .then(response => response.json())
    .then(data => {
        hideLoading();
        if (data.success) {
            // Update quantity display
            const cartItem = document.querySelector(`[data-cart-id="${cartId}"]`);
            cartItem.querySelector('.quantity-display').textContent = newQuantity;

            // Update item total
            cartItem.querySelector('.item-total').textContent = '$' + parseFloat(data.total).toFixed(2);

            // Update cart total
            document.getElementById('cart-total').textContent = '$' + parseFloat(data.cart_total).toFixed(2);

            // Show success message
            showToast('Cart updated successfully', 'success');
        } else {
            showToast(data.message || 'Error updating quantity', 'error');
        }
    })
    .catch(error => {
        hideLoading();
        console.error('Error:', error);
        showToast('Error updating quantity', 'error');
    });
}

function removeItem(cartId) {
    showRemoveItemModal(cartId);
}

function confirmRemoveItem() {
    if (!currentCartIdToRemove) return;

    closeRemoveItemModal();
    showLoading();

    fetch(`/cart/${currentCartIdToRemove}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        hideLoading();
        if (data.success) {
            // Remove the item from DOM
            const cartItem = document.querySelector(`[data-cart-id="${currentCartIdToRemove}"]`);
            cartItem.remove();

            // Update cart total
            document.getElementById('cart-total').textContent = '$' + parseFloat(data.cart_total).toFixed(2);

            // Update cart count in navigation
            updateCartCount();

            // Show success message
            showToast('Item removed from cart', 'success');

            // Check if cart is empty and reload page if needed
            if (data.cart_count === 0) {
                setTimeout(() => {
                    window.location.reload();
                }, 1000);
            }
        } else {
            showToast(data.message || 'Error removing item', 'error');
        }
    })
    .catch(error => {
        hideLoading();
        console.error('Error:', error);
        showToast('Error removing item', 'error');
    });
}

function clearCart() {
    showClearCartModal();
}

function confirmClearCart() {
    closeClearCartModal();
    showLoading();

    fetch('/cart', {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        hideLoading();
        if (data.success) {
            showToast('Cart cleared successfully', 'success');
            setTimeout(() => {
                window.location.reload();
            }, 1000);
        } else {
            showToast(data.message || 'Error clearing cart', 'error');
        }
    })
    .catch(error => {
        hideLoading();
        console.error('Error:', error);
        showToast('Error clearing cart', 'error');
    });
}

// Add confetti animation
function createConfetti() {
    const colors = ['#ff6b6b', '#4ecdc4', '#45b7d1', '#96ceb4', '#feca57', '#ff9ff3'];
    const confettiCount = 50;

    for (let i = 0; i < confettiCount; i++) {
        setTimeout(() => {
            const confetti = document.createElement('div');
            confetti.style.cssText = `
                position: fixed;
                width: 10px;
                height: 10px;
                background: ${colors[Math.floor(Math.random() * colors.length)]};
                left: ${Math.random() * 100}vw;
                top: -10px;
                z-index: 1000;
                border-radius: 50%;
                pointer-events: none;
                animation: fall 3s linear forwards;
            `;

            document.body.appendChild(confetti);

            setTimeout(() => confetti.remove(), 3000);
        }, i * 30);
    }
}

// Add CSS for confetti animation
if (!document.getElementById('confetti-styles')) {
    const style = document.createElement('style');
    style.id = 'confetti-styles';
    style.textContent = `
        @keyframes fall {
            to {
                transform: translateY(100vh) rotate(360deg);
            }
        }
    `;
    document.head.appendChild(style);
}

// Close modals when clicking outside
document.addEventListener('click', function(event) {
    const clearCartModal = document.getElementById('clear-cart-modal');
    const removeItemModal = document.getElementById('remove-item-modal');

    if (event.target === clearCartModal) {
        closeClearCartModal();
    }

    if (event.target === removeItemModal) {
        closeRemoveItemModal();
    }
});

// Close modals with Escape key
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        closeClearCartModal();
        closeRemoveItemModal();
    }
});

// Proceed to checkout function
function proceedToCheckout() {
    @auth
        // Quick checkout process
        const button = document.getElementById('quick-checkout-btn');
        const btnText = document.getElementById('checkout-btn-text');
        const btnLoading = document.getElementById('checkout-btn-loading');

        // Show loading state
        button.disabled = true;
        btnText.classList.add('hidden');
        btnLoading.classList.remove('hidden');        // Make AJAX request for quick checkout
        fetch('{{ route("checkout.quick") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({})
        })
        .then(response => {
            console.log('Response status:', response.status);
            console.log('Response headers:', response.headers.get('content-type'));

            // Check if response is JSON
            const contentType = response.headers.get('content-type');

            // Handle different response types
            if (response.status === 401) {
                // Authentication required
                throw new Error('authentication_required');
            }

            if (response.status === 403) {
                // Access denied
                throw new Error('access_denied');
            }

            if (response.status === 419) {
                // CSRF token mismatch
                throw new Error('csrf_error');
            }

            if (!contentType || !contentType.includes('application/json')) {
                // Log the actual response for debugging
                response.text().then(text => {
                    console.log('Non-JSON response received:', text.substring(0, 200));
                });

                if (response.status === 302) {
                    throw new Error('authentication_required');
                }
                throw new Error('invalid_response');
            }

            return response.json();
        })
        .then(data => {
            if (data.success) {
                // Show success message
                showSuccessModal(data);

                // Clear cart display
                setTimeout(() => {
                    location.reload();
                }, 2000);
            } else {
                // Handle authentication error
                if (data.redirect_url && data.redirect_url.includes('login')) {
                    if (confirm('Your session has expired. Please log in again to continue.')) {
                        window.location.href = data.redirect_url + '?redirect=' + encodeURIComponent(window.location.href);
                    }
                } else {
                    // Show error message
                    showErrorToast(data.message);
                }

                // Reset button
                button.disabled = false;
                btnText.classList.remove('hidden');
                btnLoading.classList.add('hidden');
            }
        })
        .catch(error => {
            console.error('Error:', error);

            if (error.message === 'authentication_required') {
                if (confirm('Your session has expired. Please log in again to continue.')) {
                    window.location.href = '{{ route("login") }}?redirect=' + encodeURIComponent(window.location.href);
                }
            } else if (error.message === 'access_denied') {
                showErrorToast('You do not have permission to perform this action.');
            } else if (error.message === 'csrf_error') {
                showErrorToast('Security token expired. Please refresh the page and try again.');
                setTimeout(() => {
                    location.reload();
                }, 2000);
            } else if (error.message === 'invalid_response') {
                showErrorToast('Server returned an unexpected response. Please try again.');
            } else {
                showErrorToast('An error occurred while processing your order. Please try again.');
            }

            // Reset button
            button.disabled = false;
            btnText.classList.remove('hidden');
            btnLoading.classList.add('hidden');
        });
    @else
        if (confirm('You need to log in to proceed with checkout. Would you like to log in now?')) {
            window.location.href = '{{ route("login") }}?redirect=' + encodeURIComponent('{{ route("cart.index") }}');
        }
    @endauth
}

// Show success modal
function showSuccessModal(data) {
    // Trigger confetti animation
    createConfetti();

    const modal = document.createElement('div');
    modal.className = 'fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center z-50';
    modal.innerHTML = `
        <div class="bg-white dark:bg-gray-800 rounded-lg p-8 max-w-md mx-4 text-center transform scale-0 transition-transform duration-500">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-green-100 dark:bg-green-900 rounded-full mb-4 animate-bounce">
                <svg class="w-8 h-8 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>
            <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100 mb-2">🎉 Order Successful!</h3>
            <p class="text-gray-600 dark:text-gray-400 mb-4">${data.message}</p>
            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 mb-4">
                <div class="text-sm space-y-2">
                    <div class="flex justify-between">
                        <span class="text-gray-600 dark:text-gray-400">Order Number:</span>
                        <span class="font-mono font-medium text-gray-900 dark:text-gray-100">${data.order_number}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600 dark:text-gray-400">Total Amount:</span>
                        <span class="font-semibold text-green-600 dark:text-green-400">$${data.total}</span>
                    </div>
                </div>
            </div>
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">
                ✨ Your cart has been cleared and your order is being processed!
            </p>
            <button onclick="this.closest('.fixed').remove()"
                    class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg transition-colors transform hover:scale-105">
                Continue Shopping
            </button>
        </div>
    `;

    document.body.appendChild(modal);

    // Animate in
    setTimeout(() => {
        modal.querySelector('div > div').classList.add('scale-100');
        modal.querySelector('div > div').classList.remove('scale-0');
    }, 100);

    // Auto remove after 8 seconds
    setTimeout(() => {
        if (modal.parentElement) {
            modal.remove();
        }
    }, 8000);
}

// Show error toast
function showErrorToast(message) {
    const toast = document.createElement('div');
    toast.className = 'fixed top-4 right-4 z-50 max-w-sm bg-red-500 text-white p-4 rounded-lg shadow-lg transform transition-all duration-500 translate-x-full';
    toast.innerHTML = `
        <div class="flex items-center">
            <svg class="w-6 h-6 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <span class="font-medium">${message}</span>
        </div>
    `;

    document.body.appendChild(toast);

    // Slide in
    setTimeout(() => {
        toast.classList.remove('translate-x-full');
    }, 100);

    // Slide out after 4 seconds
    setTimeout(() => {
        toast.classList.add('translate-x-full');
        setTimeout(() => {
            toast.remove();
        }, 500);
    }, 4000);
}
</script>
@endpush
</x-app-layout>
