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
use App\Services\BakongService;

class CheckoutController extends Controller
{
    /**
     * Show the checkout / payment page.
     * Generates a KHQR code and displays it for the customer to scan.
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
     * Process payment — creates a pending order and generates KHQR QR code.
     * Returns QR data for the frontend to display and poll.
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
            'payment_method' => 'required|in:bakong_khqr',
            'total' => 'required|numeric|min:0',
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

            $orderNumber = $this->generateOrderNumber();

            // Generate KHQR code
            $bakongService = new BakongService();
            $qrResult = $bakongService->generateQRCode($total, $orderNumber);

            if (!$qrResult['success']) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => $qrResult['message'] ?? 'Failed to generate payment QR code.'
                ]);
            }

            $expirationMinutes = config('bakong.qr_expiration_minutes', 15);

            // Create order with pending payment status
            $order = Order::create([
                'order_number' => $orderNumber,
                'user_id' => Auth::id(),
                'status' => 'pending',
                'subtotal' => $subtotal,
                'tax_amount' => $taxAmount,
                'shipping_amount' => $shippingAmount,
                'total_amount' => $total,
                'currency' => config('bakong.currency', 'USD'),
                'billing_address' => $billingAddress,
                'shipping_address' => $billingAddress,
                'payment_method' => 'bakong_khqr',
                'payment_status' => 'pending',
                'payment_id' => null,
                'qr_string' => $qrResult['qr'],
                'md5_hash' => $qrResult['md5'],
                'payment_expires_at' => now()->addMinutes($expirationMinutes),
                'notes' => 'Bakong KHQR payment — awaiting confirmation',
            ]);

            // Create order items
            foreach ($cartItems as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item->product->id,
                    'product_name' => $item->product->name,
                    'product_sku' => 'PROD-' . $item->product->id,
                    'quantity' => $item->quantity,
                    'price' => $item->product->current_price,
                    'total' => $item->quantity * $item->product->current_price,
                    'product_options' => $item->product_options,
                ]);
            }

            DB::commit();

            // Return QR data for frontend
            return response()->json([
                'success' => true,
                'message' => 'QR code generated. Please scan to pay.',
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'qr_string' => $qrResult['qr'],
                'md5' => $qrResult['md5'],
                'total' => number_format($total, 2),
                'currency' => config('bakong.currency', 'USD'),
                'expires_at' => $order->payment_expires_at->toIso8601String(),
                'expiration_minutes' => $expirationMinutes,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Payment processing error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while processing your payment. Please try again.'
            ]);
        }
    }

    /**
     * Check payment status via Bakong API (polled by frontend every 3s).
     */
    public function checkPaymentStatus(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $request->validate([
            'md5' => 'required|string',
            'order_id' => 'required|integer',
        ]);

        $order = Order::where('id', $request->order_id)
            ->where('user_id', Auth::id())
            ->where('md5_hash', $request->md5)
            ->first();

        if (!$order) {
            return response()->json([
                'success' => false,
                'paid' => false,
                'message' => 'Order not found.',
            ]);
        }

        // Already paid
        if ($order->payment_status === 'paid') {
            return response()->json([
                'success' => true,
                'paid' => true,
                'message' => 'Payment already confirmed.',
                'order_number' => $order->order_number,
            ]);
        }

        // Check if QR has expired
        if ($order->payment_expires_at && now()->greaterThan($order->payment_expires_at)) {
            return response()->json([
                'success' => true,
                'paid' => false,
                'expired' => true,
                'message' => 'Payment QR code has expired.',
            ]);
        }

        // Poll Bakong API
        $bakongService = new BakongService();
        $result = $bakongService->checkPaymentStatus($request->md5);

        if ($result['paid']) {
            DB::beginTransaction();

            try {
                // Refresh order state inside transaction to avoid double processing.
                $order->refresh();

                if ($order->payment_status !== 'paid') {
                    // Update order to paid
                    $order->update([
                        'payment_status' => 'paid',
                        'payment_id' => $result['data']['hash'] ?? ('BAKONG_' . strtoupper(uniqid())),
                        'notes' => 'Payment confirmed via Bakong KHQR',
                    ]);

                    // Deduct stock only after successful payment confirmation.
                    foreach ($order->items()->with('product')->get() as $orderItem) {
                        $product = $orderItem->product;

                        if ($product && $product->manage_stock) {
                            $product->decrement('stock_quantity', $orderItem->quantity);
                        }
                    }

                    // Clear cart only after successful payment confirmation.
                    $this->clearCart();
                }

                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('Failed to finalize paid order: ' . $e->getMessage(), [
                    'order_id' => $order->id,
                    'trace' => $e->getTraceAsString(),
                ]);

                return response()->json([
                    'success' => false,
                    'paid' => false,
                    'message' => 'Payment received, but we could not finalize your order. Please contact support.',
                ], 500);
            }

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

            return response()->json([
                'success' => true,
                'paid' => true,
                'message' => 'Payment confirmed!',
                'order_number' => $order->order_number,
                'total' => number_format($order->total_amount, 2),
            ]);
        }

        return response()->json([
            'success' => true,
            'paid' => false,
            'expired' => false,
            'message' => $result['message'] ?? 'Waiting for payment...',
        ]);
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
        // Free shipping for all orders
        return 0.00;
    }

    /**
     * Calculate tax amount.
     */
    private function calculateTax($subtotal)
    {
        // 0% tax rate
        return 0.00;
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
