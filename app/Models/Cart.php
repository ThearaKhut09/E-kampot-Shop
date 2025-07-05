<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Cart extends Model
{
    use HasFactory;

    protected $table = 'cart';

    protected $fillable = [
        'session_id',
        'user_id',
        'product_id',
        'quantity',
        'product_options',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'product_options' => 'array',
    ];

    /**
     * Get the user that owns the cart item.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the product for this cart item.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the total price for this cart item.
     */
    public function getTotalAttribute(): float
    {
        return $this->quantity * $this->product->current_price;
    }

    /**
     * Get the formatted product options for display.
     */
    public function getFormattedProductOptionsAttribute(): ?string
    {
        if (!$this->product_options) {
            return null;
        }

        if (is_array($this->product_options)) {
            return implode(', ', $this->product_options);
        }

        return $this->product_options;
    }

    /**
     * Scope to get cart items for a specific user.
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope to get cart items for a specific session.
     */
    public function scopeForSession($query, $sessionId)
    {
        return $query->where('session_id', $sessionId);
    }
}
