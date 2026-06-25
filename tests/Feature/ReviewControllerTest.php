<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Product;
use App\Models\Review;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ReviewControllerTest extends TestCase
{
    use RefreshDatabase;

    // ─── Helpers ──────────────────────────────────────────────────────────────

    private function createProduct(): Product
    {
        return Product::create([
            'name'           => 'Reviewed Product',
            'description'    => 'A product available for review.',
            'price'          => 10.00,
            'stock_quantity' => 50,
            'status'         => 'active',
            'in_stock'       => true,
        ]);
    }

    /**
     * Create a user with the 'customer' role (required by CustomerAuth middleware).
     */
    private function createCustomer(): User
    {
        $role = Role::firstOrCreate(['name' => 'customer', 'guard_name' => 'web']);
        $user = User::factory()->create();
        $user->assignRole($role);
        return $user;
    }

    // ─── Store Review ─────────────────────────────────────────────────────────

    /** @test */
    public function authenticated_customer_can_submit_a_review(): void
    {
        $user    = $this->createCustomer();
        $product = $this->createProduct();

        $response = $this->actingAs($user)->postJson(
            route('reviews.store', $product),
            ['rating' => 5, 'title' => 'Excellent', 'comment' => 'Best pepper ever!']
        );

        $response->assertStatus(200)
                 ->assertJson(['success' => true]);

        $this->assertDatabaseHas('reviews', [
            'user_id'    => $user->id,
            'product_id' => $product->id,
            'rating'     => 5,
            'title'      => 'Excellent',
        ]);
    }

    /** @test */
    public function review_submission_updates_product_average_rating(): void
    {
        $user    = $this->createCustomer();
        $product = $this->createProduct();

        $this->actingAs($user)->postJson(
            route('reviews.store', $product),
            ['rating' => 4, 'title' => 'Good', 'comment' => 'Nice product']
        );

        $this->assertEquals(4.00, (float) $product->fresh()->average_rating);
        $this->assertEquals(1, $product->fresh()->review_count);
    }

    /** @test */
    public function customer_cannot_submit_duplicate_review(): void
    {
        $user    = $this->createCustomer();
        $product = $this->createProduct();

        Review::create([
            'user_id'    => $user->id,
            'product_id' => $product->id,
            'rating'     => 3,
            'title'      => 'First',
            'comment'    => 'First review',
        ]);

        $response = $this->actingAs($user)->postJson(
            route('reviews.store', $product),
            ['rating' => 5, 'title' => 'Second', 'comment' => 'Trying again']
        );

        $response->assertStatus(422)
                 ->assertJson(['success' => false]);

        $this->assertCount(1, Review::where('user_id', $user->id)->where('product_id', $product->id)->get());
    }

    /** @test */
    public function review_requires_a_rating(): void
    {
        $user    = $this->createCustomer();
        $product = $this->createProduct();

        $response = $this->actingAs($user)->postJson(
            route('reviews.store', $product),
            ['title' => 'No rating', 'comment' => 'Missing rating']
        );

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['rating']);
    }

    /** @test */
    public function review_rating_must_be_between_1_and_5(): void
    {
        $user    = $this->createCustomer();
        $product = $this->createProduct();

        $response = $this->actingAs($user)->postJson(
            route('reviews.store', $product),
            ['rating' => 6, 'title' => 'Out of range', 'comment' => 'Bad rating']
        );

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['rating']);
    }

    /** @test */
    public function guest_cannot_submit_a_review(): void
    {
        $product = $this->createProduct();

        $response = $this->postJson(
            route('reviews.store', $product),
            ['rating' => 5, 'title' => 'Guest Review', 'comment' => 'Should fail']
        );

        // CustomerAuth middleware redirects guests to login page
        $this->assertContains($response->status(), [401, 302]);
    }

    // ─── Update Review ────────────────────────────────────────────────────────

    /** @test */
    public function customer_can_update_their_own_review(): void
    {
        $user    = $this->createCustomer();
        $product = $this->createProduct();

        $review = Review::create([
            'user_id'    => $user->id,
            'product_id' => $product->id,
            'rating'     => 3,
            'title'      => 'Original',
            'comment'    => 'Original comment',
        ]);

        $response = $this->actingAs($user)->patchJson(
            route('reviews.update', $review),
            ['rating' => 5, 'title' => 'Updated', 'comment' => 'Changed my mind!']
        );

        $response->assertStatus(200)
                 ->assertJson(['success' => true]);

        $this->assertEquals(5, $review->fresh()->rating);
        $this->assertEquals('Updated', $review->fresh()->title);
    }

    /** @test */
    public function customer_cannot_update_another_users_review(): void
    {
        $owner    = $this->createCustomer();
        $attacker = $this->createCustomer();
        $product  = $this->createProduct();

        $review = Review::create([
            'user_id'    => $owner->id,
            'product_id' => $product->id,
            'rating'     => 3,
            'title'      => 'Owner Review',
            'comment'    => 'Original',
        ]);

        $response = $this->actingAs($attacker)->patchJson(
            route('reviews.update', $review),
            ['rating' => 1, 'title' => 'Hacked', 'comment' => 'Tampered']
        );

        $response->assertStatus(403);
        $this->assertEquals('Owner Review', $review->fresh()->title);
    }

    // ─── Delete Review ────────────────────────────────────────────────────────

    /** @test */
    public function customer_can_delete_their_own_review(): void
    {
        $user    = $this->createCustomer();
        $product = $this->createProduct();

        $review = Review::create([
            'user_id'    => $user->id,
            'product_id' => $product->id,
            'rating'     => 4,
            'title'      => 'To Delete',
            'comment'    => 'Will be gone',
        ]);

        $response = $this->actingAs($user)->deleteJson(route('reviews.destroy', $review));

        $response->assertStatus(200)
                 ->assertJson(['success' => true]);

        $this->assertDatabaseMissing('reviews', ['id' => $review->id]);
    }

    /** @test */
    public function deleting_review_recalculates_product_rating(): void
    {
        $user1   = $this->createCustomer();
        $user2   = $this->createCustomer();
        $product = $this->createProduct();

        $review1 = Review::create(['user_id' => $user1->id, 'product_id' => $product->id, 'rating' => 5, 'title' => 'R1', 'comment' => 'c1']);
        $review2 = Review::create(['user_id' => $user2->id, 'product_id' => $product->id, 'rating' => 1, 'title' => 'R2', 'comment' => 'c2']);
        $product->updateAverageRating(); // avg = 3

        $this->actingAs($user1)->deleteJson(route('reviews.destroy', $review1));

        // After deleting the 5-star review, average should drop to 1
        $this->assertEquals(1.00, (float) $product->fresh()->average_rating);
    }

    /** @test */
    public function customer_cannot_delete_another_users_review(): void
    {
        $owner    = $this->createCustomer();
        $attacker = $this->createCustomer();
        $product  = $this->createProduct();

        $review = Review::create([
            'user_id'    => $owner->id,
            'product_id' => $product->id,
            'rating'     => 4,
            'title'      => 'Protected',
            'comment'    => 'Stays here',
        ]);

        $response = $this->actingAs($attacker)->deleteJson(route('reviews.destroy', $review));

        $response->assertStatus(403);
        $this->assertDatabaseHas('reviews', ['id' => $review->id]);
    }
}
