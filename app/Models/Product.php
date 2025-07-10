<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'short_description',
        'slug',
        'sku',
        'price',
        'stock_quantity',
        'manage_stock',
        'in_stock',
        'is_featured',
        'featured', // Add for admin controller
        'status',
        'image', // Add for single image
        'images',
        'gallery', // Add for gallery images
        'weight',
        'dimensions',
        'attributes',
        'average_rating',
        'review_count',
        'meta_title',
        'meta_description',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'stock_quantity' => 'integer',
        'manage_stock' => 'boolean',
        'in_stock' => 'boolean',
        'is_featured' => 'boolean',
        'images' => 'array',
        'attributes' => 'array',
        'average_rating' => 'decimal:2',
        'review_count' => 'integer',
        'weight' => 'decimal:2',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($product) {
            if (empty($product->slug)) {
                $product->slug = Str::slug($product->name);
            }
            if (empty($product->sku)) {
                $product->sku = 'SKU-' . strtoupper(Str::random(8));
            }
        });

        static::updating(function ($product) {
            if ($product->isDirty('name')) {
                $product->slug = Str::slug($product->name);
            }
        });
    }

    /**
     * Get the categories for this product.
     */
    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'product_categories');
    }

    /**
     * Get the reviews for this product.
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    /**
     * Get the cart items for this product.
     */
    public function cartItems(): HasMany
    {
        return $this->hasMany(Cart::class);
    }

    /**
     * Get the wishlist items for this product.
     */
    public function wishlistItems(): HasMany
    {
        return $this->hasMany(Wishlist::class);
    }

    /**
     * Get the order items for this product.
     */
    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Scope to get only active products.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope to get only featured products.
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope to get only in-stock products.
     */
    public function scopeInStock($query)
    {
        return $query->where('in_stock', true);
    }

    /**
     * Get the current price (just the regular price).
     */
    public function getCurrentPriceAttribute(): float
    {
        return $this->price;
    }

    /**
     * Get the primary image.
     */
    public function getPrimaryImageAttribute(): ?string
    {
        $images = $this->images ?? [];
        return $images[0] ?? null;
    }

    /**
     * Get the full URL for the primary image.
     */
    public function getImageUrlAttribute(): ?string
    {
        if ($this->primary_image) {
            // Check if it's already a full URL
            if (filter_var($this->primary_image, FILTER_VALIDATE_URL)) {
                return $this->primary_image;
            }
            // Otherwise, assume it's stored in the storage directory
            return asset('storage/' . $this->primary_image);
        }

        return null;
    }

    /**
     * Update the product's average rating.
     */
    public function updateAverageRating(): void
    {
        $reviews = $this->reviews()->where('is_approved', true);
        $this->review_count = $reviews->count();
        $this->average_rating = $reviews->count() > 0 ? round($reviews->avg('rating'), 2) : 0;
        $this->save();
    }

    /**
     * Get approved reviews for this product.
     */
    public function approvedReviews(): HasMany
    {
        return $this->hasMany(Review::class)->where('is_approved', true);
    }
}
