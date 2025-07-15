<x-app-layout>
    <x-slot name="title">Payment - E-Kampot Shop</x-slot>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Header -->
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100 mb-2">
                🛒 Secure Payment
            </h1>
            <p class="text-gray-600 dark:text-gray-400">
                Choose your payment method and complete your order
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

                <!-- Payment Method Selection -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-8">
                    <!-- Visa Payment -->
                    <div class="payment-method-card border-2 border-gray-200 dark:border-gray-700 rounded-xl p-6 cursor-pointer hover:border-blue-500 dark:hover:border-blue-400 transition-colors"
                         data-method="visa" onclick="selectPaymentMethod('visa')">
                        <div class="flex items-center space-x-4">
                            <div class="w-12 h-12 bg-gradient-to-br from-blue-600 to-blue-800 rounded-lg flex items-center justify-center shadow-lg">
                                <!-- Your Custom Visa SVG -->
                                <svg class="w-9 h-6" viewBox="0 -6 36 36" fill="white">
                                    <path d="m33.6 24h-31.2c-1.325 0-2.4-1.075-2.4-2.4v-19.2c0-1.325 1.075-2.4 2.4-2.4h31.2c1.325 0 2.4 1.075 2.4 2.4v19.2c0 1.325-1.075 2.4-2.4 2.4zm-15.76-9.238-.359 2.25c.79.338 1.709.535 2.674.535.077 0 .153-.001.229-.004h-.011c.088.005.19.008.294.008 1.109 0 2.137-.348 2.981-.941l-.017.011c.766-.568 1.258-1.469 1.258-2.485 0-.005 0-.01 0-.015v.001c0-1.1-.736-2.014-2.187-2.72-.426-.208-.79-.426-1.132-.672l.023.016c-.198-.13-.33-.345-.343-.592v-.002c.016-.26.165-.482.379-.6l.004-.002c.282-.164.62-.261.982-.261.042 0 .084.001.126.004h-.006.08c.023 0 .05-.001.077-.001.644 0 1.255.139 1.806.388l-.028-.011.234.125.359-2.171c-.675-.267-1.458-.422-2.277-.422-.016 0-.033 0-.049 0h.003c-.064-.003-.139-.005-.214-.005-1.096 0-2.112.347-2.943.937l.016-.011c-.752.536-1.237 1.404-1.237 2.386v.005c-.01 1.058.752 1.972 2.266 2.72.4.175.745.389 1.054.646l-.007-.006c.175.148.288.365.297.608v.002.002c0 .319-.19.593-.464.716l-.005.002c-.3.158-.656.25-1.034.25-.015 0-.031 0-.046 0h.002c-.022 0-.049 0-.075 0-.857 0-1.669-.19-2.397-.53l.035.015-.343-.172zm10.125 1.141h3.315q.08.343.313 1.5h2.407l-2.094-10.031h-2c-.035-.003-.076-.005-.118-.005-.562 0-1.043.348-1.239.84l-.003.009-3.84 9.187h2.72l.546-1.499zm-13.074-8.531-1.626 10.031h2.594l1.625-10.031zm-9.969 2.047 2.11 7.968h2.734l4.075-10.015h-2.746l-2.534 6.844-.266-1.391-.904-4.609c-.091-.489-.514-.855-1.023-.855-.052 0-.104.004-.154.011l.006-.001h-4.187l-.031.203c3.224.819 5.342 2.586 6.296 5.25-.309-.792-.76-1.467-1.326-2.024l-.001-.001c-.567-.582-1.248-1.049-2.007-1.368l-.04-.015zm25.937 4.421h-2.16q.219-.578 1.032-2.8l.046-.141c.042-.104.094-.24.16-.406s.11-.302.14-.406l.188.859.593 2.89z"/>
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-900 dark:text-gray-100">Credit/Debit Card</h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Visa, MasterCard, American Express</p>
                            </div>
                        </div>
                    </div>

                    <!-- PayPal Payment -->
                    <div class="payment-method-card border-2 border-gray-200 dark:border-gray-700 rounded-xl p-6 cursor-pointer hover:border-yellow-500 dark:hover:border-yellow-400 transition-colors"
                         data-method="paypal" onclick="selectPaymentMethod('paypal')">
                        <div class="flex items-center space-x-4">
                            <div class="w-12 h-12 bg-gradient-to-br from-blue-600 to-blue-800 rounded-lg flex items-center justify-center shadow-lg">
                                <!-- Your Custom PayPal SVG -->
                                <svg class="w-9 h-6" viewBox="0 0 76 76" fill="white">
                                    <path d="M 60.1667,29.2917C 60.1667,36.7245 53.7867,45.9167 45.9167,45.9167L 33.8894,45.9167L 30.875,60.1667L 19.7917,60.1667L 20.3778,57.3958L 28.8958,57.3958L 31.9103,43.1458L 43.9375,43.1458C 51.8076,43.1458 58.1875,33.9536 58.1875,26.5208C 58.1875,24.9629 57.9072,23.606 57.3917,22.4342C 59.1358,24.0552 60.1667,26.3039 60.1667,29.2917 Z M 23.75,14.25L 41.1667,14.25L 42.75,14.25L 42.75,14.2902C 49.8749,14.66 55.4167,19 55.4167,24.5417C 55.4167,33.25 49.0367,41.1667 41.1667,41.1667L 29.5352,41.1667L 26.5208,55.4167L 15.0417,55.4167L 23.75,14.25 Z M 44.7292,26.9167C 44.5447,24.7031 43.3967,23.5626 41.5809,22.9583L 33.387,22.9584L 31.0425,34.0417L 37.2032,34.0417C 41.8973,32.6131 44.7292,29.5115 44.7292,26.9167 Z"/>
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-900 dark:text-gray-100">PayPal</h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Pay with your PayPal account</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Payment Forms -->
                <div id="payment-forms">
                    <!-- Visa Payment Form -->
                    <div id="visa-form" class="payment-form hidden">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Card Information</h3>
                        <form id="visa-payment-form">
                            @csrf
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Card Number</label>
                                    <input type="text" id="card-number" name="card_number" placeholder="1234 5678 9012 3456"
                                           class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100"
                                           maxlength="19" required>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Expiry Date</label>
                                    <input type="text" id="card-expiry" name="card_expiry" placeholder="MM/YY"
                                           class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100"
                                           maxlength="5" required>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">CVV</label>
                                    <input type="text" id="card-cvv" name="card_cvv" placeholder="123"
                                           class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100"
                                           maxlength="4" required>
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Cardholder Name</label>
                                    <input type="text" id="card-name" name="card_name" placeholder="John Doe"
                                           class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100"
                                           required>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- PayPal Form -->
                    <div id="paypal-form" class="payment-form hidden">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">PayPal Payment</h3>
                        <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-700 rounded-lg p-6">
                            <div class="flex items-center space-x-3">
                                <div class="w-12 h-12 bg-gradient-to-br from-blue-600 to-blue-800 rounded-lg flex items-center justify-center shadow-lg">
                                    <!-- Your Custom PayPal SVG -->
                                    <svg class="w-9 h-6" viewBox="0 0 76 76" fill="white">
                                        <path d="M 60.1667,29.2917C 60.1667,36.7245 53.7867,45.9167 45.9167,45.9167L 33.8894,45.9167L 30.875,60.1667L 19.7917,60.1667L 20.3778,57.3958L 28.8958,57.3958L 31.9103,43.1458L 43.9375,43.1458C 51.8076,43.1458 58.1875,33.9536 58.1875,26.5208C 58.1875,24.9629 57.9072,23.606 57.3917,22.4342C 59.1358,24.0552 60.1667,26.3039 60.1667,29.2917 Z M 23.75,14.25L 41.1667,14.25L 42.75,14.25L 42.75,14.2902C 49.8749,14.66 55.4167,19 55.4167,24.5417C 55.4167,33.25 49.0367,41.1667 41.1667,41.1667L 29.5352,41.1667L 26.5208,55.4167L 15.0417,55.4167L 23.75,14.25 Z M 44.7292,26.9167C 44.5447,24.7031 43.3967,23.5626 41.5809,22.9583L 33.387,22.9584L 31.0425,34.0417L 37.2032,34.0417C 41.8973,32.6131 44.7292,29.5115 44.7292,26.9167 Z"/>
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="font-semibold text-gray-900 dark:text-gray-100">Secure PayPal Payment</h4>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">You'll be redirected to PayPal to complete your payment securely.</p>
                                </div>
                            </div>
                            <div class="mt-4 p-4 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-600">
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-700 dark:text-gray-300">Total Amount:</span>
                                    <span class="text-xl font-bold text-gray-900 dark:text-gray-100">${{ number_format($total, 2) }}</span>
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
                            onclick="processPayment()" disabled>
                        <span id="payment-btn-text" class="flex items-center justify-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                            </svg>
                            Complete Payment
                        </span>
                        <span id="payment-btn-loading" class="hidden flex items-center justify-center">
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
        let selectedPaymentMethod = null;

        function selectPaymentMethod(method) {
            selectedPaymentMethod = method;

            // Update card appearances
            document.querySelectorAll('.payment-method-card').forEach(card => {
                card.classList.remove('border-blue-500', 'border-yellow-500', 'dark:border-blue-400', 'dark:border-yellow-400', 'bg-blue-50', 'bg-yellow-50', 'dark:bg-blue-900/20', 'dark:bg-yellow-900/20');
                card.classList.add('border-gray-200', 'dark:border-gray-700');
            });

            // Hide all forms
            document.querySelectorAll('.payment-form').forEach(form => {
                form.classList.add('hidden');
            });

            // Show selected form and update card appearance
            const selectedCard = document.querySelector(`[data-method="${method}"]`);
            const selectedForm = document.getElementById(`${method}-form`);

            if (method === 'visa') {
                selectedCard.classList.add('border-blue-500', 'dark:border-blue-400', 'bg-blue-50', 'dark:bg-blue-900/20');
                selectedCard.classList.remove('border-gray-200', 'dark:border-gray-700');
            } else if (method === 'paypal') {
                selectedCard.classList.add('border-yellow-500', 'dark:border-yellow-400', 'bg-yellow-50', 'dark:bg-yellow-900/20');
                selectedCard.classList.remove('border-gray-200', 'dark:border-gray-700');
            }

            selectedForm.classList.remove('hidden');

            // Enable payment button
            document.getElementById('process-payment-btn').disabled = false;
        }

        // Card number formatting
        document.getElementById('card-number')?.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\s/g, '').replace(/[^0-9]/gi, '');
            let formattedValue = value.match(/.{1,4}/g)?.join(' ') || value;
            if (formattedValue.length <= 19) {
                e.target.value = formattedValue;
            }
        });

        // Expiry date formatting
        document.getElementById('card-expiry')?.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length >= 2) {
                value = value.substring(0, 2) + '/' + value.substring(2, 4);
            }
            e.target.value = value;
        });

        // CVV formatting
        document.getElementById('card-cvv')?.addEventListener('input', function(e) {
            e.target.value = e.target.value.replace(/[^0-9]/gi, '');
        });

        function processPayment() {
            if (!selectedPaymentMethod) {
                showToast('Please select a payment method', 'error');
                return;
            }

            const button = document.getElementById('process-payment-btn');
            const btnText = document.getElementById('payment-btn-text');
            const btnLoading = document.getElementById('payment-btn-loading');

            // Validate form if visa is selected
            if (selectedPaymentMethod === 'visa') {
                const form = document.getElementById('visa-payment-form');
                const formData = new FormData(form);

                if (!formData.get('card_number') || !formData.get('card_expiry') || !formData.get('card_cvv') || !formData.get('card_name')) {
                    showToast('Please fill in all card details', 'error');
                    return;
                }
            }

            // Show loading state
            button.disabled = true;
            btnText.classList.add('hidden');
            btnLoading.classList.remove('hidden');

            // Prepare payment data
            let paymentData = {
                payment_method: selectedPaymentMethod,
                total: {{ $total }}
            };

            if (selectedPaymentMethod === 'visa') {
                const form = document.getElementById('visa-payment-form');
                const formData = new FormData(form);
                paymentData.card_number = formData.get('card_number');
                paymentData.card_expiry = formData.get('card_expiry');
                paymentData.card_cvv = formData.get('card_cvv');
                paymentData.card_name = formData.get('card_name');
            }

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
                }
            })
            .catch(error => {
                console.error('Payment error:', error);
                showToast('An error occurred. Please try again.', 'error');
                // Reset button
                button.disabled = false;
                btnText.classList.remove('hidden');
                btnLoading.classList.add('hidden');
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
                            ${data.payment_method === 'visa' ? `
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600 dark:text-gray-400 font-medium">Payment Method:</span>
                                <span class="font-medium text-gray-900 dark:text-gray-100">${data.card_type} ****${data.last_four}</span>
                            </div>
                            ` : ''}
                            ${data.payment_method === 'paypal' ? `
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600 dark:text-gray-400 font-medium">Payment Method:</span>
                                <span class="font-medium text-gray-900 dark:text-gray-100">PayPal</span>
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
