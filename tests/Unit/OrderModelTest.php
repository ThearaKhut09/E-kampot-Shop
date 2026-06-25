<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class OrderModelTest extends TestCase
{
    use RefreshDatabase;

    // ─── Helper ───────────────────────────────────────────────────────────────

    private function makeOrder(User $user, array $overrides = []): Order
    {
        $address = [
            'first_name' => 'Test',
            'last_name'  => 'User',
            'address'    => '123 Main St',
            'city'       => 'Kampot',
            'country'    => 'Cambodia',
            'phone'      => '012345678',
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

    // ─── Order Number ─────────────────────────────────────────────────────────

    /** @test */
    public function it_auto_generates_order_number_on_create(): void
    {
        $user  = User::factory()->create();
        $order = $this->makeOrder($user);

        $this->assertNotNull($order->order_number);
        $this->assertStringStartsWith('ORD-', $order->order_number);
    }

    /** @test */
    public function it_uses_provided_order_number_if_given(): void
    {
        $user  = User::factory()->create();
        $order = $this->makeOrder($user, ['order_number' => 'ORD-CUSTOM123']);

        $this->assertEquals('ORD-CUSTOM123', $order->order_number);
    }

    // ─── canBeCancelled ───────────────────────────────────────────────────────

    /** @test */
    public function can_be_cancelled_returns_true_for_pending_orders(): void
    {
        $user  = User::factory()->create();
        $order = $this->makeOrder($user, ['status' => 'pending']);

        $this->assertTrue($order->canBeCancelled());
    }

    /** @test */
    public function can_be_cancelled_returns_true_for_processing_orders(): void
    {
        $user  = User::factory()->create();
        $order = $this->makeOrder($user, ['status' => 'processing', 'payment_status' => 'paid']);

        $this->assertTrue($order->canBeCancelled());
    }

    /** @test */
    public function can_be_cancelled_returns_false_for_delivered_orders(): void
    {
        $user  = User::factory()->create();
        $order = $this->makeOrder($user, ['status' => 'delivered', 'payment_status' => 'paid']);

        $this->assertFalse($order->canBeCancelled());
    }

    /** @test */
    public function can_be_cancelled_returns_false_for_cancelled_orders(): void
    {
        $user  = User::factory()->create();
        $order = $this->makeOrder($user, ['status' => 'cancelled']);

        $this->assertFalse($order->canBeCancelled());
    }

    // ─── isCompleted ──────────────────────────────────────────────────────────

    /** @test */
    public function is_completed_returns_true_only_for_delivered_orders(): void
    {
        $user      = User::factory()->create();
        $delivered = $this->makeOrder($user, ['status' => 'delivered', 'payment_status' => 'paid']);
        $pending   = $this->makeOrder($user, ['status' => 'pending']);

        $this->assertTrue($delivered->isCompleted());
        $this->assertFalse($pending->isCompleted());
    }

    // ─── Scope ────────────────────────────────────────────────────────────────

    /** @test */
    public function scope_status_filters_orders_by_status(): void
    {
        $user = User::factory()->create();
        $this->makeOrder($user, ['status' => 'pending']);
        $this->makeOrder($user, ['status' => 'delivered', 'payment_status' => 'paid']);
        $this->makeOrder($user, ['status' => 'cancelled']);

        $pendingOrders = Order::status('pending')->get();

        $this->assertCount(1, $pendingOrders);
        $this->assertEquals('pending', $pendingOrders->first()->status);
    }

    // ─── Address Formatting ───────────────────────────────────────────────────

    /** @test */
    public function formatted_shipping_address_joins_array_values(): void
    {
        $user  = User::factory()->create();
        $order = $this->makeOrder($user, [
            'shipping_address' => ['123 Main St', 'Kampot', 'Cambodia'],
        ]);

        $formatted = $order->formatted_shipping_address;

        $this->assertStringContainsString('123 Main St', $formatted);
        $this->assertStringContainsString('Kampot', $formatted);
        $this->assertStringContainsString('Cambodia', $formatted);
    }

    /** @test */
    public function formatted_shipping_address_returns_null_when_not_set(): void
    {
        // Test the model's accessor logic directly without hitting DB NOT NULL constraint.
        // The DB column is NOT NULL in production, but the method itself handles null gracefully.
        $order = new Order();
        $order->shipping_address = null;

        $this->assertNull($order->formatted_shipping_address);
    }

    /** @test */
    public function formatted_billing_address_joins_array_values(): void
    {
        $user  = User::factory()->create();
        $order = $this->makeOrder($user, [
            'billing_address' => ['Jane Doe', '456 Market St', 'Phnom Penh'],
        ]);

        $formatted = $order->formatted_billing_address;

        $this->assertStringContainsString('Jane Doe', $formatted);
        $this->assertStringContainsString('456 Market St', $formatted);
        $this->assertStringContainsString('Phnom Penh', $formatted);
    }

    // ─── Relationships ────────────────────────────────────────────────────────

    /** @test */
    public function order_belongs_to_user(): void
    {
        $user  = User::factory()->create();
        $order = $this->makeOrder($user);

        $this->assertEquals($user->id, $order->user->id);
    }
}
