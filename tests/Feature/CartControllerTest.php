<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Cart;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CartControllerTest extends TestCase
{
    use RefreshDatabase;

    // ─── Helpers ─────────────────────────────────────────────────────────────

    private function createProduct(array $overrides = []): Product
    {
        return Product::create(array_merge([
            'name'           => 'Test Product',
            'description'    => 'A great test product.',
            'price'          => 10.00,
            'stock_quantity' => 100,
            'manage_stock'   => true,
            'in_stock'       => true,
            'status'         => 'active',
        ], $overrides));
    }

    // ─── Cart Index ───────────────────────────────────────────────────────────

    /** @test */
    public function cart_index_is_accessible_to_guests(): void
    {
        $response = $this->get(route('cart.index'));

        $response->assertStatus(200);
        $response->assertViewIs('cart.index');
    }

    /** @test */
    public function cart_index_is_accessible_to_authenticated_users(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('cart.index'));

        $response->assertStatus(200);
    }

    // ─── Add to Cart ──────────────────────────────────────────────────────────

    /** @test */
    public function authenticated_user_can_add_product_to_cart(): void
    {
        $user    = User::factory()->create();
        $product = $this->createProduct();

        $response = $this->actingAs($user)->postJson(route('cart.add'), [
            'product_id' => $product->id,
            'quantity'   => 2,
        ]);

        $response->assertStatus(200)
                 ->assertJson(['success' => true]);

        $this->assertDatabaseHas('cart', [
            'user_id'    => $user->id,
            'product_id' => $product->id,
            'quantity'   => 2,
        ]);
    }

    /** @test */
    public function guest_can_add_product_to_cart_via_session(): void
    {
        $product = $this->createProduct();

        $response = $this->postJson(route('cart.add'), [
            'product_id' => $product->id,
            'quantity'   => 1,
        ]);

        $response->assertStatus(200)
                 ->assertJson(['success' => true]);
    }

    /** @test */
    public function adding_same_product_twice_increments_quantity(): void
    {
        $user    = User::factory()->create();
        $product = $this->createProduct();

        $this->actingAs($user)->postJson(route('cart.add'), ['product_id' => $product->id, 'quantity' => 2]);
        $this->actingAs($user)->postJson(route('cart.add'), ['product_id' => $product->id, 'quantity' => 3]);

        $cartItem = Cart::where('user_id', $user->id)->where('product_id', $product->id)->first();

        $this->assertEquals(5, $cartItem->quantity);
    }

    /** @test */
    public function cannot_add_out_of_stock_product(): void
    {
        $user    = User::factory()->create();
        $product = $this->createProduct(['stock_quantity' => 5, 'manage_stock' => true]);

        $response = $this->actingAs($user)->postJson(route('cart.add'), [
            'product_id' => $product->id,
            'quantity'   => 10, // exceeds stock
        ]);

        $response->assertStatus(200)
                 ->assertJson(['success' => false]);

        $this->assertDatabaseMissing('cart', [
            'user_id'    => $user->id,
            'product_id' => $product->id,
        ]);
    }

    /** @test */
    public function cannot_add_non_existent_product(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson(route('cart.add'), [
            'product_id' => 99999,
            'quantity'   => 1,
        ]);

        $response->assertStatus(422); // Validation fails — product doesn't exist
    }

    /** @test */
    public function add_requires_product_id_and_quantity(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson(route('cart.add'), []);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['product_id', 'quantity']);
    }

    // ─── Update Cart ──────────────────────────────────────────────────────────

    /** @test */
    public function user_can_update_cart_item_quantity(): void
    {
        $user    = User::factory()->create();
        $product = $this->createProduct();

        $cartItem = Cart::create([
            'user_id'    => $user->id,
            'product_id' => $product->id,
            'quantity'   => 1,
        ]);

        $response = $this->actingAs($user)->patchJson(route('cart.update', $cartItem->id), [
            'quantity' => 5,
        ]);

        $response->assertStatus(200)
                 ->assertJson(['success' => true]);

        $this->assertEquals(5, $cartItem->fresh()->quantity);
    }

    /** @test */
    public function user_cannot_update_another_users_cart_item(): void
    {
        $owner   = User::factory()->create();
        $intruder = User::factory()->create();
        $product = $this->createProduct();

        $cartItem = Cart::create([
            'user_id'    => $owner->id,
            'product_id' => $product->id,
            'quantity'   => 1,
        ]);

        $response = $this->actingAs($intruder)->patchJson(route('cart.update', $cartItem->id), [
            'quantity' => 9,
        ]);

        $response->assertStatus(403);
    }

    /** @test */
    public function update_returns_404_for_missing_cart_item(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->patchJson(route('cart.update', 99999), [
            'quantity' => 1,
        ]);

        $response->assertStatus(404);
    }

    // ─── Remove from Cart ─────────────────────────────────────────────────────

    /** @test */
    public function user_can_remove_their_cart_item(): void
    {
        $user    = User::factory()->create();
        $product = $this->createProduct();

        $cartItem = Cart::create([
            'user_id'    => $user->id,
            'product_id' => $product->id,
            'quantity'   => 2,
        ]);

        $response = $this->actingAs($user)->deleteJson(route('cart.remove', $cartItem->id));

        $response->assertStatus(200)
                 ->assertJson(['success' => true]);

        $this->assertDatabaseMissing('cart', ['id' => $cartItem->id]);
    }

    /** @test */
    public function user_cannot_remove_another_users_cart_item(): void
    {
        $owner    = User::factory()->create();
        $intruder = User::factory()->create();
        $product  = $this->createProduct();

        $cartItem = Cart::create([
            'user_id'    => $owner->id,
            'product_id' => $product->id,
            'quantity'   => 1,
        ]);

        $response = $this->actingAs($intruder)->deleteJson(route('cart.remove', $cartItem->id));

        $response->assertStatus(403);
        $this->assertDatabaseHas('cart', ['id' => $cartItem->id]);
    }

    // ─── Clear Cart ───────────────────────────────────────────────────────────

    /** @test */
    public function user_can_clear_their_entire_cart(): void
    {
        $user    = User::factory()->create();
        $product = $this->createProduct();

        Cart::create(['user_id' => $user->id, 'product_id' => $product->id, 'quantity' => 1]);
        Cart::create(['user_id' => $user->id, 'product_id' => $product->id, 'quantity' => 3]);

        $response = $this->actingAs($user)->deleteJson(route('cart.clear'));

        $response->assertStatus(200)
                 ->assertJson(['success' => true]);

        $this->assertDatabaseMissing('cart', ['user_id' => $user->id]);
    }

    // ─── Cart Count ───────────────────────────────────────────────────────────

    /** @test */
    public function cart_count_endpoint_returns_correct_count(): void
    {
        $user    = User::factory()->create();
        $product = $this->createProduct();

        Cart::create(['user_id' => $user->id, 'product_id' => $product->id, 'quantity' => 3]);

        $response = $this->actingAs($user)->getJson(route('cart.count'));

        $response->assertStatus(200)
                 ->assertJson(['count' => 3]);
    }

    /** @test */
    public function cart_count_returns_zero_for_empty_cart(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->getJson(route('cart.count'));

        $response->assertStatus(200)
                 ->assertJson(['count' => 0]);
    }
}
