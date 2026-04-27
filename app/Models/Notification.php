<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'admin_id',
        'type',
        'title',
        'message',
        'data',
        'is_read',
        'read_at',
        'action_url',
    ];

    protected $casts = [
        'data' => 'array',
        'is_read' => 'boolean',
        'read_at' => 'datetime',
    ];

    /**
     * Get the user that owns the notification (for user notifications).
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the admin that owns the notification (for admin notifications).
     */
    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    /**
     * Scope to get unread notifications.
     */
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    /**
     * Scope to get user notifications.
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId)->whereNull('admin_id');
    }

    /**
     * Scope to get admin notifications.
     */
    public function scopeForAdmin($query, $adminId)
    {
        return $query->where('admin_id', $adminId)->whereNull('user_id');
    }

    /**
     * Mark notification as read.
     */
    public function markAsRead()
    {
        $this->update([
            'is_read' => true,
            'read_at' => now(),
        ]);
    }

    /**
     * Get notification icon based on type.
     */
    public function getIconAttribute(): string
    {
        return match($this->type) {
            'payment_success' => '✓',
            'payment_failed' => '✕',
            'order_confirmed' => '🛍️',
            'order_shipped' => '📦',
            'order_delivered' => '🎉',
            'order_cancelled' => '❌',
            'review_submitted' => '⭐',
            'review_approved' => '👍',
            'product_stock_low' => '⚠️',
            'product_created' => '✨',
            'new_review' => '💬',
            default => 'ℹ️',
        };
    }

    /**
     * Get notification color badge based on type.
     */
    public function getColorAttribute(): string
    {
        return match($this->type) {
            'payment_success' => 'emerald',
            'payment_failed' => 'red',
            'order_confirmed' => 'blue',
            'order_shipped' => 'indigo',
            'order_delivered' => 'green',
            'order_cancelled' => 'orange',
            'review_submitted' => 'yellow',
            'review_approved' => 'green',
            'product_stock_low' => 'red',
            'product_created' => 'purple',
            'new_review' => 'blue',
            default => 'gray',
        };
    }
}
