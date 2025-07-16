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
                            <div class="w-12 h-12 bg-blue-600 rounded-lg flex items-center justify-center shadow-lg border border-gray-200">
                                <!-- Custom Visa Icon -->
                                <svg class="w-10 h-7" viewBox="0 -11 70 70" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <rect x="0.5" y="0.5" width="69" height="47" rx="5.5" fill="white" stroke="#D9D9D9"/>
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M21.2505 32.5165H17.0099L13.8299 20.3847C13.679 19.8267 13.3585 19.3333 12.8871 19.1008C11.7106 18.5165 10.4142 18.0514 9 17.8169V17.3498H15.8313C16.7742 17.3498 17.4813 18.0514 17.5991 18.8663L19.2491 27.6173L23.4877 17.3498H27.6104L21.2505 32.5165ZM29.9675 32.5165H25.9626L29.2604 17.3498H33.2653L29.9675 32.5165ZM38.4467 21.5514C38.5646 20.7346 39.2717 20.2675 40.0967 20.2675C41.3931 20.1502 42.8052 20.3848 43.9838 20.9671L44.6909 17.7016C43.5123 17.2345 42.216 17 41.0395 17C37.1524 17 34.3239 19.1008 34.3239 22.0165C34.3239 24.2346 36.3274 25.3992 37.7417 26.1008C39.2717 26.8004 39.861 27.2675 39.7431 27.9671C39.7431 29.0165 38.5646 29.4836 37.3881 29.4836C35.9739 29.4836 34.5596 29.1338 33.2653 28.5494L32.5582 31.8169C33.9724 32.3992 35.5025 32.6338 36.9167 32.6338C41.2752 32.749 43.9838 30.6502 43.9838 27.5C43.9838 23.5329 38.4467 23.3004 38.4467 21.5514ZM58 32.5165L54.82 17.3498H51.4044C50.6972 17.3498 49.9901 17.8169 49.7544 18.5165L43.8659 32.5165H47.9887L48.8116 30.3004H53.8772L54.3486 32.5165H58ZM51.9936 21.4342L53.1701 27.1502H49.8723L51.9936 21.4342Z" fill="#172B85"/>
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
                            <div class="w-12 h-12 bg-white rounded-lg flex items-center justify-center shadow-lg border border-gray-200">
                                <!-- Custom PayPal Icon -->
                                <svg class="w-8 h-8" viewBox="0 0 512 512" xmlns="http://www.w3.org/2000/svg">
                                    <g>
                                        <path style="fill:#385C8E;" d="M474.348,89.297c20.796,38.137,13.933,83.11,5.743,109.688
                                            C435.77,343.757,235.787,335.882,206.856,335.882c-28.897,0-35.627,26.947-35.627,26.947l-21.606,94.263
                                            c-5.89,33.087-35.896,31.439-35.896,31.439s-39.004,0-65.094,0c-1.637,0-3.164-0.113-4.567-0.316
                                            c-0.323,6.059,0.539,23.771,22.2,23.771c26.069,0,65.074,0,65.074,0s30.007,1.683,35.917-31.403l21.583-94.259
                                            c0,0,6.753-26.948,35.651-26.948c28.884,0,228.914,7.873,273.257-136.904C507.64,190.112,515.683,130.653,474.348,89.297z"/>
                                        <path style="fill:#385C8E;" d="M129.523,436.545l21.603-94.266c0,0,6.711-26.901,35.651-26.901
                                            c28.873,0,228.89,7.832,273.214-136.909C476.21,125.55,487.47,0.009,285.513,0.009H139.638c0,0-30.321-1.408-37.803,30.829
                                            L6.521,442.196c0,0-4.093,25.778,21.987,25.778c26.104,0,65.116,0,65.116,0S123.632,469.67,129.523,436.545z M184.5,197.259
                                            l19.371-83.589c0,0,6.17-22.731,26.104-26.097c19.913-3.378,53.834,0.601,62.56,2.248c56.65,10.639,44.6,64.239,44.6,64.239
                                            c-11.216,82.738-140.003,71.257-140.003,71.257C176.939,218.016,184.5,197.259,184.5,197.259z"/>
                                    </g>
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
                                <div class="w-12 h-12 bg-white rounded-lg flex items-center justify-center shadow-lg border border-gray-200">
                                    <!-- Custom PayPal Icon -->
                                    <svg class="w-8 h-8" viewBox="0 0 512 512" xmlns="http://www.w3.org/2000/svg">
                                        <g>
                                            <path style="fill:#385C8E;" d="M474.348,89.297c20.796,38.137,13.933,83.11,5.743,109.688
                                                C435.77,343.757,235.787,335.882,206.856,335.882c-28.897,0-35.627,26.947-35.627,26.947l-21.606,94.263
                                                c-5.89,33.087-35.896,31.439-35.896,31.439s-39.004,0-65.094,0c-1.637,0-3.164-0.113-4.567-0.316
                                                c-0.323,6.059,0.539,23.771,22.2,23.771c26.069,0,65.074,0,65.074,0s30.007,1.683,35.917-31.403l21.583-94.259
                                                c0,0,6.753-26.948,35.651-26.948c28.884,0,228.914,7.873,273.257-136.904C507.64,190.112,515.683,130.653,474.348,89.297z"/>
                                            <path style="fill:#385C8E;" d="M129.523,436.545l21.603-94.266c0,0,6.711-26.901,35.651-26.901
                                                c28.873,0,228.89,7.832,273.214-136.909C476.21,125.55,487.47,0.009,285.513,0.009H139.638c0,0-30.321-1.408-37.803,30.829
                                                L6.521,442.196c0,0-4.093,25.778,21.987,25.778c26.104,0,65.116,0,65.116,0S123.632,469.67,129.523,436.545z M184.5,197.259
                                                l19.371-83.589c0,0,6.17-22.731,26.104-26.097c19.913-3.378,53.834,0.601,62.56,2.248c56.65,10.639,44.6,64.239,44.6,64.239
                                                c-11.216,82.738-140.003,71.257-140.003,71.257C176.939,218.016,184.5,197.259,184.5,197.259z"/>
                                        </g>
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
