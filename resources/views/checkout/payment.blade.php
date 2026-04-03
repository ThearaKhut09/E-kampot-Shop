<x-app-layout>
    <x-slot name="title">Payment - E-Kampot Shop</x-slot>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Header -->
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100 mb-2">
                Bakong KHQR Payment
            </h1>
            <p class="text-gray-600 dark:text-gray-400">
                Complete your order with Bakong only
            </p>
        </div>

        <!-- Order Summary -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700 p-6 mb-8">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-4">Order Summary</h2>

            <div class="space-y-3">
                @foreach($cartItems as $item)
                <div class="flex justify-between items-center py-2 border-b border-gray-200 dark:border-gray-700 last:border-b-0">
                    <div class="flex items-center space-x-3">
                        @if($item->product->image_url)
                            <img src="{{ $item->product->image_url }}" alt="{{ $item->product->name }}"
                                 class="w-12 h-12 object-cover rounded-lg">
                        @else
                            <div class="w-12 h-12 bg-gray-200 dark:bg-gray-700 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                        @endif
                        <div>
                            <p class="font-medium text-gray-900 dark:text-gray-100">{{ $item->product->name }}</p>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Qty: {{ $item->quantity }}</p>
                        </div>
                    </div>
                    <span class="font-semibold text-gray-900 dark:text-gray-100">
                        ${{ number_format($item->quantity * $item->product->current_price, 2) }}
                    </span>
                </div>
                @endforeach

                <div class="pt-4 space-y-2">
                    <div class="flex justify-between text-gray-600 dark:text-gray-400">
                        <span>Subtotal:</span>
                        <span>${{ number_format($subtotal, 2) }}</span>
                    </div>
                    <div class="flex justify-between text-gray-600 dark:text-gray-400">
                        <span>Shipping:</span>
                        <span>${{ number_format($shipping, 2) }}</span>
                    </div>
                    <div class="flex justify-between text-gray-600 dark:text-gray-400">
                        <span>Tax:</span>
                        <span>${{ number_format($tax, 2) }}</span>
                    </div>
                    <div class="flex justify-between text-xl font-bold text-gray-900 dark:text-gray-100 pt-2 border-t border-gray-200 dark:border-gray-700">
                        <span>Total:</span>
                        <span>${{ number_format($total, 2) }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Payment Methods -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="p-6">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-6">Payment Method</h2>

                <div class="payment-method-card border-2 border-emerald-500 dark:border-emerald-400 bg-emerald-50 dark:bg-emerald-900/20 rounded-xl p-6 mb-8">
                    <div class="flex items-center space-x-4">
                        <img src="{{ asset('images/bakong.png') }}" alt="Bakong Logo" class="w-12 h-12 rounded-lg shadow-sm bg-white p-1">
                        <div>
                            <h3 class="font-semibold text-gray-900 dark:text-gray-100">Bakong KHQR</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Only Bakong payment is available</p>
                        </div>
                    </div>
                </div>

                <div id="payment-forms">
                    <div id="bakong-form" class="payment-form">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Bakong KHQR (Demo)</h3>
                        <div class="bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-700 rounded-lg p-6">
                            <p class="text-sm text-emerald-900 dark:text-emerald-200 mb-4">
                                This is a fake Bakong payment for testing. No real transaction will be charged.
                            </p>

                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                                <div class="bg-white dark:bg-gray-800 rounded-lg border border-emerald-200 dark:border-emerald-700 p-4">
                                    <div class="aspect-square w-full max-w-[220px] mx-auto rounded-lg border-2 border-gray-200 dark:border-gray-600 flex items-center justify-center bg-white dark:bg-gray-800">
                                        <img src="{{ asset('images/khqr.jpg') }}" alt="KHQR" class="w-full h-full object-cover rounded-md">
                                    </div>
                                    <p class="text-xs text-gray-600 dark:text-gray-400 mt-3 text-center">
                                        Merchant: E-Kampot Shop Demo<br>
                                        Amount: ${{ number_format($total, 2) }}
                                    </p>
                                </div>

                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Bakong Phone Number</label>
                                        <input type="text" id="bakong-phone" name="bakong_phone" placeholder="+855 12 345 678"
                                               class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-emerald-500 dark:focus:ring-emerald-400 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Account Name (Optional)</label>
                                        <input type="text" id="bakong-account-name" name="bakong_account_name" placeholder="Your Bakong account name"
                                               class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-emerald-500 dark:focus:ring-emerald-400 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Reference (Optional)</label>
                                        <input type="text" id="bakong-reference" name="bakong_reference" placeholder="Type FAIL to simulate payment decline"
                                               class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-emerald-500 dark:focus:ring-emerald-400 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="mt-8 flex flex-col sm:flex-row gap-4">
                    <a href="{{ route('cart.index') }}"
                       class="flex-1 bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-gray-100 font-semibold py-3 px-6 rounded-lg text-center hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
                        ← Back to Cart
                    </a>
                    <button type="button" id="process-payment-btn"
                            class="flex-1 bg-primary-600 hover:bg-primary-700 text-white font-semibold py-3 px-6 rounded-lg transition-all duration-200 shadow-lg hover:shadow-xl disabled:opacity-50 disabled:cursor-not-allowed transform hover:-translate-y-0.5"
                            onclick="processPayment()">
                        <span id="payment-btn-text" class="flex items-center justify-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                            </svg>
                            Complete Payment
                        </span>
                        <span id="payment-btn-loading" class="hidden items-center justify-center">
                            <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Processing Payment...
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        const selectedPaymentMethod = 'bakong';

        // Bakong phone formatting
        document.getElementById('bakong-phone')?.addEventListener('input', function(e) {
            e.target.value = e.target.value.replace(/[^0-9+\s]/g, '');
        });

        function processPayment() {
            const button = document.getElementById('process-payment-btn');
            const btnText = document.getElementById('payment-btn-text');
            const btnLoading = document.getElementById('payment-btn-loading');

            const bakongPhone = document.getElementById('bakong-phone')?.value?.trim();
            if (!bakongPhone) {
                showToast('Please enter your Bakong phone number', 'error');
                return;
            }

            // Show loading state
            button.disabled = true;
            btnText.classList.add('hidden');
            btnLoading.classList.remove('hidden');
            btnLoading.classList.add('flex');

            // Prepare payment data
            let paymentData = {
                payment_method: selectedPaymentMethod,
                total: {{ $total }},
                bakong_phone: bakongPhone,
                bakong_account_name: document.getElementById('bakong-account-name')?.value?.trim() || '',
                bakong_reference: document.getElementById('bakong-reference')?.value?.trim() || ''
            };

            // Process payment
            fetch('{{ route("checkout.process") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                },
                body: JSON.stringify(paymentData)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Show success modal with confetti
                    showPaymentSuccessModal(data);
                } else {
                    showToast(data.message || 'Payment failed. Please try again.', 'error');
                    // Reset button
                    button.disabled = false;
                    btnText.classList.remove('hidden');
                    btnLoading.classList.add('hidden');
                    btnLoading.classList.remove('flex');
                }
            })
            .catch(error => {
                console.error('Payment error:', error);
                showToast('An error occurred. Please try again.', 'error');
                // Reset button
                button.disabled = false;
                btnText.classList.remove('hidden');
                btnLoading.classList.add('hidden');
                btnLoading.classList.remove('flex');
            });
        }

        function showPaymentSuccessModal(data) {
            // Create confetti
            createConfetti();

            const modal = document.createElement('div');
            modal.className = 'fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center z-50';
            modal.innerHTML = `
                <div class="bg-white dark:bg-gray-800 rounded-2xl p-8 max-w-lg mx-4 text-center transform scale-0 transition-transform duration-500">
                    <div class="inline-flex items-center justify-center w-20 h-20 bg-green-100 dark:bg-green-900 rounded-full mb-6 animate-bounce">
                        <svg class="w-10 h-10 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>

                    <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-2">🎉 Order Successful!</h2>
                    <p class="text-gray-600 dark:text-gray-400 mb-6">Order placed successfully!</p>

                    <div class="bg-gray-50 dark:bg-gray-700 rounded-xl p-6 mb-6 text-left">
                        <div class="space-y-3">
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600 dark:text-gray-400 font-medium">Order Number:</span>
                                <span class="font-mono font-bold text-primary-600 dark:text-primary-400">${data.order_number}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600 dark:text-gray-400 font-medium">Total Amount:</span>
                                <span class="text-xl font-bold text-green-600 dark:text-green-400">$${data.total}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600 dark:text-gray-400 font-medium">Payment Method:</span>
                                <span class="font-medium text-gray-900 dark:text-gray-100">Bakong KHQR</span>
                            </div>
                            ${data.payer_phone ? `
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600 dark:text-gray-400 font-medium">Bakong Phone:</span>
                                <span class="font-medium text-gray-900 dark:text-gray-100">${data.payer_phone}</span>
                            </div>
                            ` : ''}
                        </div>
                    </div>

                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">
                        ✨ Data sent to admin order management
                    </p>

                    <button onclick="window.location.href='{{ route('products.index') }}'"
                            class="w-full bg-primary-600 hover:bg-primary-700 text-white font-semibold py-3 px-6 rounded-lg transition-colors transform hover:scale-105">
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

            // Auto redirect after 8 seconds
            setTimeout(() => {
                window.location.href = '{{ route('products.index') }}';
            }, 8000);
        }

        // Confetti animation
        function createConfetti() {
            const colors = ['#ff6b6b', '#4ecdc4', '#45b7d1', '#96ceb4', '#feca57', '#ff9ff3', '#a29bfe', '#fd79a8'];
            const confettiCount = 100;

            for (let i = 0; i < confettiCount; i++) {
                setTimeout(() => {
                    const confetti = document.createElement('div');
                    confetti.style.cssText = `
                        position: fixed;
                        width: ${Math.random() * 10 + 5}px;
                        height: ${Math.random() * 10 + 5}px;
                        background: ${colors[Math.floor(Math.random() * colors.length)]};
                        left: ${Math.random() * 100}vw;
                        top: -10px;
                        z-index: 1000;
                        border-radius: 50%;
                        pointer-events: none;
                        animation: confettiFall ${Math.random() * 2 + 3}s linear forwards;
                        transform: rotate(${Math.random() * 360}deg);
                    `;

                    document.body.appendChild(confetti);
                    setTimeout(() => confetti.remove(), 5000);
                }, i * 50);
            }
        }

        // Add confetti CSS
        if (!document.getElementById('confetti-styles')) {
            const style = document.createElement('style');
            style.id = 'confetti-styles';
            style.textContent = `
                @keyframes confettiFall {
                    to {
                        transform: translateY(100vh) rotate(720deg);
                        opacity: 0;
                    }
                }
            `;
            document.head.appendChild(style);
        }

        function showToast(message, type = 'success') {
            const toast = document.createElement('div');
            const bgColor = type === 'success' ? 'bg-green-500' : 'bg-red-500';
            toast.className = `fixed top-4 right-4 z-50 max-w-sm ${bgColor} text-white p-4 rounded-lg shadow-lg transform transition-all duration-500 translate-x-full`;
            toast.innerHTML = `
                <div class="flex items-center">
                    <svg class="w-6 h-6 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        ${type === 'success' ?
                            '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>' :
                            '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>'
                        }
                    </svg>
                    <span class="font-medium">${message}</span>
                </div>
            `;

            document.body.appendChild(toast);

            setTimeout(() => toast.classList.remove('translate-x-full'), 100);
            setTimeout(() => {
                toast.classList.add('translate-x-full');
                setTimeout(() => toast.remove(), 500);
            }, 4000);
        }
    </script>
    @endpush
</x-app-layout>
