<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Product;
use App\Models\Review;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProductModelTest extends TestCase
{
    use RefreshDatabase;

    // ─── Helper ───────────────────────────────────────────────────────────────

    /**
     * Create a product with all required non-nullable fields filled.
     */
    private function makeProduct(array $overrides = []): Product
    {
        return Product::create(array_merge([
            'name'           => 'Test Product',
            'description'    => 'A test product description.',
            'price'          => 10.00,
            'stock_quantity' => 50,
            'status'         => 'active',
            'in_stock'       => true,
        ], $overrides));
    }

    // ─── Slug Generation ──────────────────────────────────────────────────────

    /** @test */
    public function it_auto_generates_slug_from_name_on_create(): void
    {
        $product = $this->makeProduct(['name' => 'Fresh Kampot Pepper']);

        $this->assertEquals('fresh-kampot-pepper', $product->slug);
    }

    /** @test */
    public function it_uses_provided_slug_if_given(): void
    {
        $product = $this->makeProduct(['name' => 'Fresh Pepper', 'slug' => 'my-custom-slug']);

        $this->assertEquals('my-custom-slug', $product->slug);
    }

    /** @test */
    public function it_updates_slug_when_name_changes(): void
    {
        $product = $this->makeProduct(['name' => 'Old Name']);
        $product->update(['name' => 'New Product Name']);

        $this->assertEquals('new-product-name', $product->fresh()->slug);
    }

    // ─── Scopes ───────────────────────────────────────────────────────────────

    /** @test */
    public function scope_active_only_returns_active_products(): void
    {
        $this->makeProduct(['name' => 'Active One',   'status' => 'active']);
        $this->makeProduct(['name' => 'Inactive One', 'status' => 'inactive']);
        $this->makeProduct(['name' => 'Draft One',    'status' => 'draft']);

        $active = Product::active()->get();

        $this->assertCount(1, $active);
        $this->assertEquals('Active One', $active->first()->name);
    }

    /** @test */
    public function scope_featured_only_returns_featured_products(): void
    {
        $this->makeProduct(['name' => 'Featured',     'featured' => true]);
        $this->makeProduct(['name' => 'Not Featured', 'featured' => false]);

        $featured = Product::featured()->get();

        $this->assertCount(1, $featured);
        $this->assertEquals('Featured', $featured->first()->name);
    }

    /** @test */
    public function scope_in_stock_only_returns_stocked_products(): void
    {
        $this->makeProduct(['name' => 'In Stock',  'in_stock' => true]);
        $this->makeProduct(['name' => 'Out Stock', 'in_stock' => false]);

        $inStock = Product::inStock()->get();

        $this->assertCount(1, $inStock);
        $this->assertEquals('In Stock', $inStock->first()->name);
    }

    // ─── Accessors ────────────────────────────────────────────────────────────

    /** @test */
    public function current_price_attribute_returns_regular_price(): void
    {
        $product = $this->makeProduct(['price' => 29.99]);

        $this->assertEquals(29.99, $product->current_price);
    }

    /** @test */
    public function primary_image_returns_main_image_first(): void
    {
        $product = $this->makeProduct([
            'image'  => 'products/main.jpg',
            'images' => ['products/gallery1.jpg', 'products/gallery2.jpg'],
        ]);

        $this->assertEquals('products/main.jpg', $product->primary_image);
    }

    /** @test */
    public function primary_image_falls_back_to_images_array_when_no_main_image(): void
    {
        $product = $this->makeProduct([
            'image'  => null,
            'images' => ['products/gallery1.jpg', 'products/gallery2.jpg'],
        ]);

        $this->assertEquals('products/gallery1.jpg', $product->primary_image);
    }

    /** @test */
    public function primary_image_returns_null_when_no_images(): void
    {
        $product = $this->makeProduct(['image' => null]);

        $this->assertNull($product->primary_image);
    }

    // ─── Rating ───────────────────────────────────────────────────────────────

    /** @test */
    public function update_average_rating_recalculates_correctly(): void
    {
        $user1   = User::factory()->create();
        $user2   = User::factory()->create();
        $product = $this->makeProduct(['name' => 'Rated Product']);

        Review::create(['user_id' => $user1->id, 'product_id' => $product->id, 'rating' => 4, 'title' => 'Good', 'comment' => 'Nice']);
        Review::create(['user_id' => $user2->id, 'product_id' => $product->id, 'rating' => 2, 'title' => 'Bad',  'comment' => 'Meh']);

        $product->updateAverageRating();
        $product->refresh();

        $this->assertEquals(3.00, (float) $product->average_rating);
        $this->assertEquals(2, $product->review_count);
    }

    /** @test */
    public function update_average_rating_resets_to_zero_when_no_reviews(): void
    {
        $product = $this->makeProduct([
            'name'           => 'No Reviews Product',
            'average_rating' => 4.5,
            'review_count'   => 3,
        ]);

        $product->updateAverageRating();
        $product->refresh();

        $this->assertEquals(0, (float) $product->average_rating);
        $this->assertEquals(0, $product->review_count);
    }
}
