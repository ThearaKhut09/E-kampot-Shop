<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserOrderControllerTest extends TestCase
{
    use RefreshDatabase;

    // ─── Helpers ──────────────────────────────────────────────────────────────

    /**
     * Create a user with the 'customer' role (required by CustomerAuth middleware).
     */
    private function createCustomer(): User
    {
        $role = Role::firstOrCreate(['name' => 'customer', 'guard_name' => 'web']);
        $user = User::factory()->create([
            'first_name' => 'Test',
            'last_name'  => 'Customer',
        ]);
        $user->assignRole($role);
        return $user;
    }

    private function createOrder(User $user, array $overrides = []): Order
    {
        $address = [
            'first_name'  => 'Test',
            'last_name'   => 'User',
            'address'     => '123 Main St',
            'city'        => 'Kampot',
            'postal_code' => '00000',
            'country'     => 'Cambodia',
            'phone'       => '012345678',
        ];
        return Order::create(array_merge([
            'user_id'          => $user->id,
            'status'           => 'pending',
            'subtotal'         => 100.00,
            'total_amount'     => 100.00,
            'payment_method'   => 'bakong',
            'payment_status'   => 'pending',
            'billing_address'  => $address,
            'shipping_address' => $address,
        ], $overrides));
    }

    // ─── Order Index ──────────────────────────────────────────────────────────

    /** @test */
    public function guest_is_redirected_from_orders_index(): void
    {
        $response = $this->get(route('orders.index'));

        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function customer_can_view_their_orders(): void
    {
        $user = $this->createCustomer();
        $this->createOrder($user, ['status' => 'delivered']);
        $this->createOrder($user, ['status' => 'pending']);

        $response = $this->actingAs($user)->get(route('orders.index'));

        $response->assertStatus(200)
                 ->assertViewIs('orders.index')
                 ->assertViewHas('orders');
    }

    /** @test */
    public function customer_only_sees_their_own_orders(): void
    {
        $user1 = $this->createCustomer();
        $user2 = $this->createCustomer();

        $order1 = $this->createOrder($user1);
        $order2 = $this->createOrder($user2);

        $response = $this->actingAs($user1)->get(route('orders.index'));

        $orders = $response->viewData('orders');

        $this->assertTrue($orders->contains('id', $order1->id));
        $this->assertFalse($orders->contains('id', $order2->id));
    }

    // ─── Order Show ───────────────────────────────────────────────────────────

    /** @test */
    public function customer_can_view_their_own_order(): void
    {
        $user  = $this->createCustomer();
        $order = $this->createOrder($user);

        $response = $this->actingAs($user)->get(route('orders.show', $order));

        $response->assertStatus(200)
                 ->assertViewIs('orders.show');
    }

    /** @test */
    public function customer_cannot_view_another_users_order(): void
    {
        $owner    = $this->createCustomer();
        $intruder = $this->createCustomer();
        $order    = $this->createOrder($owner);

        $response = $this->actingAs($intruder)->get(route('orders.show', $order));

        $response->assertStatus(403);
    }

    /** @test */
    public function guest_is_redirected_from_order_show(): void
    {
        $user  = $this->createCustomer();
        $order = $this->createOrder($user);

        $response = $this->get(route('orders.show', $order));

        $response->assertRedirect(route('login'));
    }

    // ─── Cancel Order ─────────────────────────────────────────────────────────

    /** @test */
    public function customer_can_cancel_pending_order(): void
    {
        $user  = $this->createCustomer();
        $order = $this->createOrder($user, ['status' => 'pending']);

        $response = $this->actingAs($user)->patch(route('orders.cancel', $order));

        $response->assertRedirect();
        $this->assertEquals('cancelled', $order->fresh()->status);
    }

    /** @test */
    public function customer_can_cancel_processing_order(): void
    {
        $user  = $this->createCustomer();
        $order = $this->createOrder($user, ['status' => 'processing']);

        $response = $this->actingAs($user)->patch(route('orders.cancel', $order));

        $response->assertRedirect();
        $this->assertEquals('cancelled', $order->fresh()->status);
    }

    /** @test */
    public function customer_cannot_cancel_delivered_order(): void
    {
        $user  = $this->createCustomer();
        $order = $this->createOrder($user, ['status' => 'delivered']);

        $response = $this->actingAs($user)->patch(route('orders.cancel', $order));

        $response->assertRedirect();
        $response->assertSessionHas('error');
        $this->assertEquals('delivered', $order->fresh()->status);
    }

    /** @test */
    public function customer_cannot_cancel_another_users_order(): void
    {
        $owner    = $this->createCustomer();
        $attacker = $this->createCustomer();
        $order    = $this->createOrder($owner, ['status' => 'pending']);

        $response = $this->actingAs($attacker)->patch(route('orders.cancel', $order));

        $response->assertStatus(403);
        $this->assertEquals('pending', $order->fresh()->status);
    }

    /** @test */
    public function cancelling_order_restores_managed_product_stock(): void
    {
        $user    = $this->createCustomer();
        $product = Product::create([
            'name'           => 'Stock Product',
            'description'    => 'A managed stock product.',
            'price'          => 10.00,
            'stock_quantity' => 5,
            'manage_stock'   => true,
            'in_stock'       => true,
            'status'         => 'active',
        ]);

        $order = $this->createOrder($user, ['status' => 'pending']);

        OrderItem::create([
            'order_id'     => $order->id,
            'product_id'   => $product->id,
            'product_name' => $product->name,
            'product_sku'  => 'SKU-MANAGED-001',
            'quantity'     => 3,
            'price'        => 10.00,
            'total'        => 30.00,
        ]);

        $this->actingAs($user)->patch(route('orders.cancel', $order));

        $this->assertEquals(8, $product->fresh()->stock_quantity);
    }

    /** @test */
    public function cancelling_order_does_not_restore_unmanaged_stock(): void
    {
        $user    = $this->createCustomer();
        $product = Product::create([
            'name'           => 'Unmanaged Stock',
            'description'    => 'An unmanaged stock product.',
            'price'          => 10.00,
            'stock_quantity' => 5,
            'manage_stock'   => false,
            'in_stock'       => true,
            'status'         => 'active',
        ]);

        $order = $this->createOrder($user, ['status' => 'pending']);

        OrderItem::create([
            'order_id'     => $order->id,
            'product_id'   => $product->id,
            'product_name' => $product->name,
            'product_sku'  => 'SKU-UNMANAGED-001',
            'quantity'     => 3,
            'price'        => 10.00,
            'total'        => 30.00,
        ]);

        $this->actingAs($user)->patch(route('orders.cancel', $order));

        $this->assertEquals(5, $product->fresh()->stock_quantity); // unchanged
    }
}
