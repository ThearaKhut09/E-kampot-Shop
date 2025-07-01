<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use App\Mail\OrderConfirmation;

class CheckoutController extends Controller
{
    /**
     * Show the checkout form.
     */
    public function index()
    {
        $cartItems = $this->getCartItems();

        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }

        $subtotal = $cartItems->sum(function ($item) {
            return $item->quantity * $item->product->current_price;
        });

        $shippingAmount = $this->calculateShipping($subtotal);
        $taxAmount = $this->calculateTax($subtotal);
        $total = $subtotal + $shippingAmount + $taxAmount;

        return view('checkout.index', compact('cartItems', 'subtotal', 'shippingAmount', 'taxAmount', 'total'));
    }

    /**
     * Process the checkout and create order.
     */
    public function store(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:500',
            'city' => 'required|string|max:255',
            'postal_code' => 'required|string|max:20',
            'country' => 'required|string|max:255',
            'payment_method' => 'required|in:credit_card,paypal,bank_transfer,cash_on_delivery',
            'terms' => 'accepted',
            'shipping_different' => 'boolean',
            // Shipping address fields (optional)
            'shipping_first_name' => 'required_if:shipping_different,1|string|max:255',
            'shipping_last_name' => 'required_if:shipping_different,1|string|max:255',
            'shipping_address' => 'required_if:shipping_different,1|string|max:500',
            'shipping_city' => 'required_if:shipping_different,1|string|max:255',
            'shipping_postal_code' => 'required_if:shipping_different,1|string|max:20',
            'shipping_country' => 'required_if:shipping_different,1|string|max:255',
            // Credit card fields (required if payment method is credit card)
            'card_number' => 'required_if:payment_method,credit_card|string',
            'card_expiry' => 'required_if:payment_method,credit_card|string',
            'card_cvc' => 'required_if:payment_method,credit_card|string',
            'card_name' => 'required_if:payment_method,credit_card|string|max:255',
        ]);

        $cartItems = $this->getCartItems();

        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }

        // Check stock availability
        foreach ($cartItems as $item) {
            if ($item->product->manage_stock && $item->product->stock_quantity < $item->quantity) {
                return back()->withErrors(['stock' => "Sorry, we don't have enough stock for {$item->product->name}."]);
            }
        }

        DB::beginTransaction();

        try {
            // Calculate totals
            $subtotal = $cartItems->sum(function ($item) {
                return $item->quantity * $item->product->current_price;
            });
            $shippingAmount = $this->calculateShipping($subtotal);
            $taxAmount = $this->calculateTax($subtotal);
            $total = $subtotal + $shippingAmount + $taxAmount;

            // Create billing address
            $billingAddress = [
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'phone' => $request->phone,
                'address' => $request->address,
                'city' => $request->city,
                'postal_code' => $request->postal_code,
                'country' => $request->country,
            ];

            // Create shipping address
            $shippingAddress = $billingAddress;
            if ($request->shipping_different) {
                $shippingAddress = [
                    'first_name' => $request->shipping_first_name,
                    'last_name' => $request->shipping_last_name,
                    'address' => $request->shipping_address,
                    'city' => $request->shipping_city,
                    'postal_code' => $request->shipping_postal_code,
                    'country' => $request->shipping_country,
                ];
            }

            // Process payment
            $paymentResult = $this->processPayment($request, $total);

            if (!$paymentResult['success']) {
                DB::rollBack();
                return back()->withErrors(['payment' => $paymentResult['message']])
                            ->with('error', 'Payment failed: ' . $paymentResult['message']);
            }

            // Create order
            $order = Order::create([
                'order_number' => $this->generateOrderNumber(),
                'user_id' => Auth::id(),
                'status' => 'pending',
                'subtotal' => $subtotal,
                'tax_amount' => $taxAmount,
                'shipping_amount' => $shippingAmount,
                'total_amount' => $total,
                'currency' => 'USD',
                'billing_address' => $billingAddress,
                'shipping_address' => $shippingAddress,
                'payment_method' => $request->payment_method,
                'payment_status' => $paymentResult['status'],
                'payment_id' => $paymentResult['transaction_id'] ?? null,
                'notes' => $request->notes,
            ]);

            // Store payment success message in session for success page
            session()->flash('payment_message', $paymentResult['message']);
            session()->flash('payment_details', [
                'transaction_id' => $paymentResult['transaction_id'] ?? null,
                'last_four' => $paymentResult['last_four'] ?? null,
                'card_type' => $paymentResult['card_type'] ?? null,
            ]);

            // Create order items
            foreach ($cartItems as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item->product->id,
                    'product_name' => $item->product->name,
                    'product_sku' => $item->product->sku,
                    'quantity' => $item->quantity,
                    'price' => $item->product->current_price,
                    'total' => $item->quantity * $item->product->current_price,
                    'product_options' => $item->product_options,
                ]);

                // Update product stock
                if ($item->product->manage_stock) {
                    $item->product->decrement('stock_quantity', $item->quantity);
                }
            }

            // Clear cart
            $this->clearCart();

            DB::commit();

            // Send confirmation email
            $this->sendOrderConfirmationEmail($order);

            return redirect()->route('checkout.success', $order->id)->with('success', 'Order placed successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'An error occurred while processing your order. Please try again.']);
        }
    }

    /**
     * Show order success page.
     */
    public function success($orderId)
    {
        $order = Order::with(['items.product', 'user'])->findOrFail($orderId);

        // Ensure user can only view their own orders
        if (Auth::id() !== $order->user_id) {
            abort(403);
        }

        return view('checkout.success', compact('order'));
    }

    /**
     * Get cart items for current user/session.
     */
    private function getCartItems()
    {
        $query = Cart::with('product');

        if (Auth::check()) {
            $query->where('user_id', Auth::id());
        } else {
            $query->where('session_id', session()->getId());
        }

        return $query->get();
    }

    /**
     * Calculate shipping amount.
     */
    private function calculateShipping($subtotal)
    {
        // Free shipping over $100
        if ($subtotal >= 100) {
            return 0;
        }

        // Standard shipping rate
        return 10.00;
    }

    /**
     * Calculate tax amount.
     */
    private function calculateTax($subtotal)
    {
        // 10% tax rate
        return $subtotal * 0.10;
    }

    /**
     * Generate unique order number.
     */
    private function generateOrderNumber()
    {
        do {
            $orderNumber = 'ORD-' . date('Y') . date('m') . '-' . strtoupper(substr(uniqid(), -6));
        } while (Order::where('order_number', $orderNumber)->exists());

        return $orderNumber;
    }

    /**
     * Process payment based on selected method.
     */
    private function processPayment($request, $total)
    {
        switch ($request->payment_method) {
            case 'credit_card':
                return $this->processCreditCardPayment($request, $total);
            case 'paypal':
                return $this->processPayPalPayment($request, $total);
            case 'bank_transfer':
                return [
                    'success' => true,
                    'status' => 'pending',
                    'transaction_id' => 'BT_' . strtoupper(uniqid()),
                    'message' => 'Bank transfer initiated successfully! Please complete the transfer within 24 hours.'
                ];
            case 'cash_on_delivery':
                return [
                    'success' => true,
                    'status' => 'pending',
                    'transaction_id' => 'COD_' . strtoupper(uniqid()),
                    'message' => 'Cash on delivery order placed successfully! Pay when your order arrives.'
                ];
            default:
                return ['success' => false, 'message' => 'Invalid payment method'];
        }
    }

    /**
     * Process credit card payment (Demo implementation).
     */
    private function processCreditCardPayment($request, $total)
    {
        // This is a demo implementation
        // In production, integrate with actual payment gateways like Stripe, Square, etc.

        $cardNumber = str_replace(' ', '', $request->card_number);

        // Basic validation
        if (strlen($cardNumber) < 13 || strlen($cardNumber) > 19) {
            return ['success' => false, 'message' => 'Invalid card number'];
        }

        // Demo: Simulate payment processing with a slight delay for realism
        sleep(1);

        $transactionId = 'TXN_' . strtoupper(uniqid());

        // Simulate some payment failures for testing (cards ending in 0000)
        if (substr($cardNumber, -4) === '0000') {
            return ['success' => false, 'message' => 'Payment declined. Please try a different card.'];
        }

        // Simulate successful payment
        return [
            'success' => true,
            'status' => 'paid',
            'transaction_id' => $transactionId,
            'message' => 'Payment processed successfully! Your card has been charged $' . number_format($total, 2),
            'last_four' => substr($cardNumber, -4),
            'card_type' => $this->detectCardType($cardNumber)
        ];
    }

    /**
     * Process PayPal payment (Demo implementation).
     */
    private function processPayPalPayment($request, $total)
    {
        // This is a demo implementation
        // In production, integrate with PayPal API

        // Simulate processing delay
        sleep(1);

        return [
            'success' => true,
            'status' => 'paid',
            'transaction_id' => 'PP_' . strtoupper(uniqid()),
            'message' => 'PayPal payment processed successfully! Amount charged: $' . number_format($total, 2)
        ];
    }

    /**
     * Detect card type from card number.
     */
    private function detectCardType($cardNumber)
    {
        $cardNumber = preg_replace('/\D/', '', $cardNumber);

        if (preg_match('/^4/', $cardNumber)) {
            return 'Visa';
        } elseif (preg_match('/^5[1-5]/', $cardNumber)) {
            return 'Mastercard';
        } elseif (preg_match('/^3[47]/', $cardNumber)) {
            return 'American Express';
        } elseif (preg_match('/^6(?:011|5)/', $cardNumber)) {
            return 'Discover';
        } else {
            return 'Unknown';
        }
    }

    /**
     * Clear cart after successful order.
     */
    private function clearCart()
    {
        $query = Cart::query();

        if (Auth::check()) {
            $query->where('user_id', Auth::id());
        } else {
            $query->where('session_id', session()->getId());
        }

        $query->delete();
    }

    /**
     * Send order confirmation email.
     */
    private function sendOrderConfirmationEmail($order)
    {
        try {
            Mail::to($order->billing_address['email'])->send(new OrderConfirmation($order));
        } catch (\Exception $e) {
            // Log the error but don't fail the order
            Log::error('Failed to send order confirmation email: ' . $e->getMessage());
        }
    }

    /**
     * Quick checkout - simplified process that clears cart and shows success message.
     */
    public function quickCheckout(Request $request)
    {
        // Ensure user is authenticated
        if (!Auth::check()) {
            if ($request->ajax() || $request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please log in to continue.',
                    'redirect_url' => route('login')
                ], 401);
            }
            return redirect()->route('login');
        }

        $cartItems = $this->getCartItems();

        if ($cartItems->isEmpty()) {
            if ($request->ajax() || $request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Your cart is empty.',
                    'redirect_url' => route('cart.index')
                ]);
            }
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }

        DB::beginTransaction();

        try {
            // Calculate totals
            $subtotal = $cartItems->sum(function ($item) {
                return $item->quantity * $item->product->current_price;
            });
            $shippingAmount = $this->calculateShipping($subtotal);
            $taxAmount = $this->calculateTax($subtotal);
            $total = $subtotal + $shippingAmount + $taxAmount;

            // Use current user's information for billing
            $user = Auth::user();
            $billingAddress = [
                'first_name' => $user->first_name ?? 'Customer',
                'last_name' => $user->last_name ?? 'User',
                'email' => $user->email,
                'phone' => $user->phone ?? 'N/A',
                'address' => 'Default Address',
                'city' => 'Kampot',
                'postal_code' => '07000',
                'country' => 'Cambodia',
            ];

            // Simulate successful payment
            $paymentResult = [
                'success' => true,
                'status' => 'paid',
                'transaction_id' => 'QUICK_' . strtoupper(uniqid()),
                'message' => 'Quick checkout completed successfully! Your order has been processed.',
            ];

            // Create order
            $order = Order::create([
                'order_number' => $this->generateOrderNumber(),
                'user_id' => Auth::id(),
                'status' => 'pending',
                'subtotal' => $subtotal,
                'tax_amount' => $taxAmount,
                'shipping_amount' => $shippingAmount,
                'total_amount' => $total,
                'currency' => 'USD',
                'billing_address' => $billingAddress,
                'shipping_address' => $billingAddress,
                'payment_method' => 'quick_checkout',
                'payment_status' => 'paid',
                'payment_id' => $paymentResult['transaction_id'],
                'notes' => 'Quick checkout order',
            ]);

            // Create order items
            foreach ($cartItems as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item->product->id,
                    'product_name' => $item->product->name,
                    'product_sku' => $item->product->sku,
                    'quantity' => $item->quantity,
                    'price' => $item->product->current_price,
                    'total' => $item->quantity * $item->product->current_price,
                    'product_options' => $item->product_options,
                ]);

                // Update product stock
                if ($item->product->manage_stock) {
                    $item->product->decrement('stock_quantity', $item->quantity);
                }
            }

            // Clear cart
            $this->clearCart();

            DB::commit();

            // Store success message in session
            session()->flash('payment_message', $paymentResult['message']);
            session()->flash('payment_details', [
                'transaction_id' => $paymentResult['transaction_id'],
            ]);

            // Return JSON response for AJAX
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Order placed successfully!',
                    'order_number' => $order->order_number,
                    'total' => number_format($total, 2),
                    'redirect_url' => route('cart.index')
                ]);
            }

            return redirect()->route('cart.index')->with('success', 'Order placed successfully! Your cart has been cleared.');

        } catch (\Exception $e) {
            DB::rollBack();

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'An error occurred while processing your order. Please try again.'
                ]);
            }

            return back()->withErrors(['error' => 'An error occurred while processing your order. Please try again.']);
        }
    }
}
