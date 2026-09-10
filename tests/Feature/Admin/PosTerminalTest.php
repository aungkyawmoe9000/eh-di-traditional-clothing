<?php

namespace Tests\Feature\Admin;

use App\Models\ProductVariant;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\User;
use App\Livewire\Admin\PosTerminal;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class PosTerminalTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_pos_terminal_is_accessible_by_authenticated_user(): void
    {
        $response = $this->actingAs($this->user)->get(route('admin.pos'));
        $response->assertOk();
    }

    public function test_can_add_item_to_cart(): void
    {
        $variant = ProductVariant::factory()->create([
            'stock_quantity' => 10,
            'selling_price' => 50.00,
        ]);

        Livewire::actingAs($this->user)
            ->test(PosTerminal::class)
            ->call('addToCart', $variant->id)
            ->assertSet('cart', [$variant->id => 1]);
    }

    public function test_cannot_add_more_than_available_stock_to_cart(): void
    {
        $variant = ProductVariant::factory()->create([
            'stock_quantity' => 2,
            'selling_price' => 50.00,
        ]);

        Livewire::actingAs($this->user)
            ->test(PosTerminal::class)
            ->call('addToCart', $variant->id)
            ->call('addToCart', $variant->id)
            ->call('addToCart', $variant->id) // should fail/not exceed stock
            ->assertSet('cart', [$variant->id => 2]);
    }

    public function test_can_update_quantity_in_cart(): void
    {
        $variant = ProductVariant::factory()->create([
            'stock_quantity' => 10,
            'selling_price' => 50.00,
        ]);

        Livewire::actingAs($this->user)
            ->test(PosTerminal::class)
            ->call('addToCart', $variant->id)
            ->call('updateQuantity', $variant->id, 5)
            ->assertSet('cart', [$variant->id => 5]);
    }

    public function test_can_complete_checkout(): void
    {
        $variant = ProductVariant::factory()->create([
            'stock_quantity' => 10,
            'selling_price' => 50.00,
            'cost_price' => 30.00,
        ]);

        Livewire::actingAs($this->user)
            ->test(PosTerminal::class)
            ->call('addToCart', $variant->id)
            ->call('addToCart', $variant->id) // Qty 2
            ->set('discount', 10.00)
            ->call('checkout')
            ->assertSet('cart', []); // Cart is reset

        // Assert database records
        $this->assertDatabaseHas('sales', [
            'user_id' => $this->user->id,
            'total_amount' => 100.00,
            'discount' => 10.00,
            'net_amount' => 90.00,
        ]);

        $sale = Sale::first();
        $this->assertNotNull($sale->voucher_no);
        $this->assertStringStartsWith('INV-', $sale->voucher_no);

        $this->assertDatabaseHas('sale_items', [
            'sale_id' => $sale->id,
            'product_variant_id' => $variant->id,
            'quantity' => 2,
            'unit_price' => 50.00,
            'unit_cost' => 30.00,
            'subtotal' => 100.00,
        ]);

        // Assert stock decremented
        $this->assertEquals(8, $variant->fresh()->stock_quantity);
    }
}
