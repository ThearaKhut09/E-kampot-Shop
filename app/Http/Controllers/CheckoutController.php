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
        // Ensure user is authenticated
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please log in to proceed with checkout.');
        }

        $cartItems = $this->getCartItems();

        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }

        $subtotal = $cartItems->sum(function ($item) {
            return $item->quantity * $item->product->current_price;
        });

        $shipping = $this->calculateShipping($subtotal);
        $tax = $this->calculateTax($subtotal);
        $total = $subtotal + $shipping + $tax;

        return view('checkout.payment', compact('cartItems', 'subtotal', 'shipping', 'tax', 'total'));
    }

    /**
     * Process payment for the new checkout flow.
     */
    public function processPayment(Request $request)
    {
        // Ensure user is authenticated
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Please log in to continue.',
                'redirect_url' => route('login')
            ], 401);
        }

        $request->validate([
            'payment_method' => 'required|in:bakong',
            'total' => 'required|numeric|min:0',
            // Bakong KH fields
            'bakong_phone' => 'required_if:payment_method,bakong|string|max:25',
            'bakong_account_name' => 'nullable|string|max:255',
            'bakong_reference' => 'nullable|string|max:50',
        ]);

        $cartItems = $this->getCartItems();

        if ($cartItems->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Your cart is empty.',
                'redirect_url' => route('cart.index')
            ]);
        }

        // Check stock availability
        foreach ($cartItems as $item) {
            if ($item->product->manage_stock && $item->product->stock_quantity < $item->quantity) {
                return response()->json([
                    'success' => false,
                    'message' => "Sorry, we don't have enough stock for {$item->product->name}."
                ]);
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

            // Verify total matches request
            if (abs($total - $request->total) > 0.01) {
                return response()->json([
                    'success' => false,
                    'message' => 'Order total mismatch. Please refresh and try again.'
                ]);
            }

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

            // Process fake payment
            $paymentResult = $this->processFakePayment($request, $total);

            if (!$paymentResult['success']) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => $paymentResult['message']
                ]);
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
                'shipping_address' => $billingAddress,
                'payment_method' => $request->payment_method,
                'payment_status' => 'paid',
                'payment_id' => $paymentResult['transaction_id'],
                'notes' => 'Payment processed via ' . ucfirst($request->payment_method),
            ]);

            // Create order items
            foreach ($cartItems as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item->product->id,
                    'product_name' => $item->product->name,
                    'product_sku' => 'PROD-' . $item->product->id, // Generate SKU from product ID
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

            // Send order confirmation email (in background)
            try {
                $this->sendOrderConfirmationEmail($order);
            } catch (\Exception $e) {
                Log::error('Failed to send order confirmation email: ' . $e->getMessage());
            }

            // Send admin notification email (in background)
            try {
                $this->sendAdminOrderNotification($order);
            } catch (\Exception $e) {
                Log::error('Failed to send admin order notification: ' . $e->getMessage());
            }

            // Return success response
            $response = [
                'success' => true,
                'message' => 'Payment processed successfully!',
                'order_number' => $order->order_number,
                'total' => number_format($total, 2),
                'payment_method' => $request->payment_method,
            ];

            // Add payment-specific details (Bakong-only flow)
            $response['wallet'] = $paymentResult['wallet'] ?? 'Bakong KHQR';
            $response['payer_phone'] = $paymentResult['payer_phone'] ?? null;

            return response()->json($response);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Payment processing error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while processing your payment. Please try again.'
            ]);
        }
    }

    /**
     * Process fake payment for demo purposes.
     */
    private function processFakePayment($request, $total)
    {
        // Simulate processing delay
        sleep(1);

        if ($request->payment_method === 'bakong') {
            $phone = (string) $request->bakong_phone;

            if (!$this->isValidCambodianPhone($phone)) {
                return [
                    'success' => false,
                    'message' => 'Please enter a valid Cambodian Bakong phone number.',
                ];
            }

            // Demo-only failure trigger for testing payment errors.
            if (strtoupper(trim((string) $request->bakong_reference)) === 'FAIL') {
                return [
                    'success' => false,
                    'message' => 'Bakong payment declined (demo). Please try again.',
                ];
            }

            return [
                'success' => true,
                'transaction_id' => 'BAKONG_' . strtoupper(uniqid()),
                'message' => 'Bakong KHQR payment processed successfully!',
                'wallet' => 'Bakong KHQR',
                'payer_phone' => $this->maskPhone($phone),
            ];
        }

        return ['success' => false, 'message' => 'Invalid payment method'];
    }

    /**
     * Validate Cambodian mobile number format for Bakong demo flow.
     */
    private function isValidCambodianPhone(string $phone): bool
    {
        $normalized = preg_replace('/\D+/', '', $phone);

        if (str_starts_with($normalized, '855')) {
            $normalized = substr($normalized, 3);
        }

        if (str_starts_with($normalized, '0')) {
            $normalized = substr($normalized, 1);
        }

        return preg_match('/^(?:1[0-9]|[6-9][0-9])\d{6,7}$/', $normalized) === 1;
    }

    /**
     * Mask phone number for confirmation payload.
     */
    private function maskPhone(string $phone): string
    {
        $digits = preg_replace('/\D+/', '', $phone);

        if (strlen($digits) <= 4) {
            return $phone;
        }

        return str_repeat('*', strlen($digits) - 4) . substr($digits, -4);
    }

    /**
     * Send admin order notification.
     */
    private function sendAdminOrderNotification($order)
    {
        try {
            // Get admin users
            $admins = User::role('admin')->get();

            foreach ($admins as $admin) {
                Mail::to($admin->email)->send(new \App\Mail\AdminOrderNotification($order));
            }
        } catch (\Exception $e) {
            Log::error('Failed to send admin order notification: ' . $e->getMessage());
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
                    'product_sku' => 'PROD-' . $item->product->id, // Generate SKU from product ID
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
