<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Product;
use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProductControllerTest extends TestCase
{
    use RefreshDatabase;

    // ─── Helpers ─────────────────────────────────────────────────────────────

    private function createProduct(array $overrides = []): Product
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

    // ─── Product Listing (index) ──────────────────────────────────────────────

    /** @test */
    public function products_index_page_loads_successfully(): void
    {
        $this->createProduct(['name' => 'Kampot Pepper']);

        $response = $this->get(route('products.index'));

        $response->assertStatus(200);
        $response->assertViewIs('products.index');
        $response->assertViewHas('products');
    }

    /** @test */
    public function products_index_only_shows_active_in_stock_products(): void
    {
        $this->createProduct(['name' => 'Visible Product', 'status' => 'active', 'in_stock' => true]);
        $this->createProduct(['name' => 'Hidden Inactive', 'status' => 'inactive', 'in_stock' => true]);

        $response = $this->get(route('products.index'));

        $response->assertStatus(200);
        $response->assertSee('Visible Product');
        $response->assertDontSee('Hidden Inactive');
    }

    /** @test */
    public function products_index_can_search_by_name(): void
    {
        $this->createProduct(['name' => 'Black Pepper']);
        $this->createProduct(['name' => 'Sea Salt']);

        $response = $this->get(route('products.index', ['search' => 'Pepper']));

        $response->assertStatus(200);
        $response->assertSee('Black Pepper');
        $response->assertDontSee('Sea Salt');
    }

    /** @test */
    public function products_index_can_filter_by_min_price(): void
    {
        $this->createProduct(['name' => 'Cheap', 'price' => 5.00]);
        $this->createProduct(['name' => 'Expensive', 'price' => 50.00]);

        $response = $this->get(route('products.index', ['min_price' => 20]));

        $response->assertStatus(200);
        $response->assertSee('Expensive');
        $response->assertDontSee('Cheap');
    }

    /** @test */
    public function products_index_can_filter_by_max_price(): void
    {
        $this->createProduct(['name' => 'Cheap', 'price' => 5.00]);
        $this->createProduct(['name' => 'Expensive', 'price' => 50.00]);

        $response = $this->get(route('products.index', ['max_price' => 10]));

        $response->assertStatus(200);
        $response->assertSee('Cheap');
        $response->assertDontSee('Expensive');
    }

    /** @test */
    public function products_index_can_sort_by_price_low(): void
    {
        $this->createProduct(['name' => 'Mid', 'price' => 15.00]);
        $this->createProduct(['name' => 'High', 'price' => 30.00]);
        $this->createProduct(['name' => 'Low', 'price' => 5.00]);

        $response = $this->get(route('products.index', ['sort' => 'price_low']));

        $response->assertStatus(200);
        $response->assertSeeInOrder(['Low', 'Mid', 'High']);
    }

    /** @test */
    public function products_index_can_filter_by_category(): void
    {
        $category = Category::create(['name' => 'Spices', 'is_active' => true]);
        $product1 = $this->createProduct(['name' => 'Pepper In Category']);
        $product2 = $this->createProduct(['name' => 'Salt No Category']);

        $product1->categories()->attach($category->id);

        $response = $this->get(route('products.index', ['category' => $category->slug]));

        $response->assertStatus(200);
        $response->assertSee('Pepper In Category');
        $response->assertDontSee('Salt No Category');
    }

    // ─── Product Detail (show) ────────────────────────────────────────────────

    /** @test */
    public function product_show_page_loads_for_active_product(): void
    {
        $product = $this->createProduct(['name' => 'Detail Product']);

        $response = $this->get(route('products.show', $product->slug));

        $response->assertStatus(200);
        $response->assertViewIs('products.show');
        $response->assertSee('Detail Product');
    }

    /** @test */
    public function product_show_returns_404_for_inactive_product(): void
    {
        $product = $this->createProduct(['name' => 'Inactive Product', 'status' => 'inactive']);

        $response = $this->get(route('products.show', $product->slug));

        $response->assertStatus(404);
    }

    /** @test */
    public function product_show_returns_404_for_non_existent_product(): void
    {
        $response = $this->get(route('products.show', 'non-existent-slug'));

        $response->assertStatus(404);
    }

    /** @test */
    public function product_show_passes_related_products_to_view(): void
    {
        $category = Category::create(['name' => 'Spices', 'is_active' => true]);

        $main    = $this->createProduct(['name' => 'Main Product']);
        $related = $this->createProduct(['name' => 'Related Product']);

        $main->categories()->attach($category->id);
        $related->categories()->attach($category->id);

        $response = $this->get(route('products.show', $main->slug));

        $response->assertStatus(200);
        $response->assertViewHas('relatedProducts');
    }
}
