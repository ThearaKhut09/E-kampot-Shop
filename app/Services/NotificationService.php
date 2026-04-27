<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;

class NotificationService
{
    /**
     * Create a user notification for successful payment.
     */
    public static function notifyPaymentSuccess($userId, $order)
    {
        return Notification::create([
            'user_id' => $userId,
            'type' => 'payment_success',
            'title' => 'Payment Successful! 💳',
            'message' => "Your payment of \${$order->total_amount} for order #{$order->order_number} has been confirmed.",
            'data' => [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'amount' => $order->total_amount,
            ],
            'action_url' => route('orders.show', $order->id),
        ]);
    }

    /**
     * Create a user notification for failed payment.
     */
    public static function notifyPaymentFailed($userId, $order, $reason = 'Unknown')
    {
        return Notification::create([
            'user_id' => $userId,
            'type' => 'payment_failed',
            'title' => 'Payment Failed ❌',
            'message' => "Your payment attempt for order #{$order->order_number} failed. Reason: {$reason}. Please try again.",
            'data' => [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'reason' => $reason,
            ],
            'action_url' => route('checkout.index'),
        ]);
    }

    /**
     * Create notifications when order status changes.
     */
    public static function notifyOrderStatusChange($order, $oldStatus, $newStatus)
    {
        $statusMessages = [
            'pending' => 'Your order is being processed.',
            'processing' => 'Your order is being prepared.',
            'shipped' => 'Your order has been shipped! 📦',
            'delivered' => 'Your order has been delivered! 🎉',
            'cancelled' => 'Your order has been cancelled.',
            'refunded' => 'Your order has been refunded.',
        ];

        $message = $statusMessages[$newStatus] ?? "Your order status has been updated to {$newStatus}.";

        // Notify customer
        Notification::create([
            'user_id' => $order->user_id,
            'type' => 'order_' . $newStatus,
            'title' => 'Order Status Updated 📬',
            'message' => $message,
            'data' => [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'old_status' => $oldStatus,
                'new_status' => $newStatus,
            ],
            'action_url' => route('orders.show', $order->id),
        ]);

        // Notify all admins
        User::whereHas('roles', function ($query) {
            $query->where('name', 'admin');
        })->each(function ($admin) use ($order, $newStatus) {
            Notification::create([
                'admin_id' => $admin->id,
                'type' => 'order_status_changed',
                'title' => 'Order Status Changed 🔔',
                'message' => "Order #{$order->order_number} status changed to {$newStatus}.",
                'data' => [
                    'order_id' => $order->id,
                    'order_number' => $order->order_number,
                    'new_status' => $newStatus,
                    'customer_name' => $order->user->name,
                ],
                'action_url' => route('admin.orders.show', $order->id),
            ]);
        });
    }

    /**
     * Notify admins about a new order.
     */
    public static function notifyNewOrder($order)
    {
        User::whereHas('roles', function ($query) {
            $query->where('name', 'admin');
        })->each(function ($admin) use ($order) {
            Notification::create([
                'admin_id' => $admin->id,
                'type' => 'order_confirmed',
                'title' => 'New Order Received! 🛍️',
                'message' => "New order #{$order->order_number} from {$order->user->name} for \${$order->total_amount}.",
                'data' => [
                    'order_id' => $order->id,
                    'order_number' => $order->order_number,
                    'customer_id' => $order->user_id,
                    'customer_name' => $order->user->name,
                    'total_amount' => $order->total_amount,
                ],
                'action_url' => route('admin.orders.show', $order->id),
            ]);
        });
    }

    /**
     * Notify admins about a new product review.
     */
    public static function notifyNewReview($review)
    {
        User::whereHas('roles', function ($query) {
            $query->where('name', 'admin');
        })->each(function ($admin) use ($review) {
            Notification::create([
                'admin_id' => $admin->id,
                'type' => 'new_review',
                'title' => 'New Product Review 💬',
                'message' => "{$review->user->name} left a {$review->rating}-star review on \"{$review->product->name}\".",
                'data' => [
                    'review_id' => $review->id,
                    'product_id' => $review->product_id,
                    'user_id' => $review->user_id,
                    'rating' => $review->rating,
                ],
                'action_url' => route('admin.reviews.show', $review->id),
            ]);
        });
    }

    /**
     * Notify user about review approval.
     */
    public static function notifyReviewApproved($review)
    {
        Notification::create([
            'user_id' => $review->user_id,
            'type' => 'review_approved',
            'title' => 'Review Approved! ✓',
            'message' => "Your review for \"{$review->product->name}\" has been approved and is now visible.",
            'data' => [
                'review_id' => $review->id,
                'product_id' => $review->product_id,
            ],
            'action_url' => route('products.show', $review->product->slug),
        ]);
    }

    /**
     * Notify admins about low stock product.
     */
    public static function notifyLowStock($product, $threshold = 10)
    {
        if ($product->stock_quantity <= $threshold) {
            User::whereHas('roles', function ($query) {
                $query->where('name', 'admin');
            })->each(function ($admin) use ($product) {
                Notification::create([
                    'admin_id' => $admin->id,
                    'type' => 'product_stock_low',
                    'title' => 'Low Stock Warning ⚠️',
                    'message' => "Product \"{$product->name}\" stock is running low. Current stock: {$product->stock_quantity}.",
                    'data' => [
                        'product_id' => $product->id,
                        'stock_quantity' => $product->stock_quantity,
                    ],
                    'action_url' => route('admin.products.edit', $product->id),
                ]);
            });
        }
    }

    /**
     * Notify admins about new product creation.
     */
    public static function notifyProductCreated($product, $createdBy)
    {
        User::whereHas('roles', function ($query) {
            $query->where('name', 'admin');
        })->each(function ($admin) use ($product, $createdBy) {
            Notification::create([
                'admin_id' => $admin->id,
                'type' => 'product_created',
                'title' => 'New Product Created ✨',
                'message' => "{$createdBy->name} created a new product: \"{$product->name}\".",
                'data' => [
                    'product_id' => $product->id,
                    'created_by' => $createdBy->id,
                ],
                'action_url' => route('admin.products.show', $product->id),
            ]);
        });
    }

    /**
     * Get unread notification count for user.
     */
    public static function getUnreadCountForUser($userId)
    {
        return Notification::forUser($userId)->unread()->count();
    }

    /**
     * Get unread notification count for admin.
     */
    public static function getUnreadCountForAdmin($adminId = null)
    {
        return Notification::forAdmin($adminId)->unread()->count();
    }

    /**
     * Mark all notifications as read for user.
     */
    public static function markAllAsReadForUser($userId)
    {
        Notification::forUser($userId)->unread()->update([
            'is_read' => true,
            'read_at' => now(),
        ]);
    }

    /**
     * Mark all notifications as read for admin.
     */
    public static function markAllAsReadForAdmin($adminId = null)
    {
        Notification::forAdmin($adminId)->unread()->update([
            'is_read' => true,
            'read_at' => now(),
        ]);
    }
}
