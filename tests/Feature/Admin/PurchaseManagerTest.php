<?php

namespace Tests\Feature\Admin;

use App\Models\ProductVariant;
use App\Models\Supplier;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\User;
use App\Livewire\Admin\PurchaseManager;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class PurchaseManagerTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Supplier $supplier;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->supplier = Supplier::factory()->create([
            'name' => 'Acme Supplier',
        ]);
    }

    public function test_purchases_page_is_accessible_by_authenticated_user(): void
    {
        $response = $this->actingAs($this->user)->get(route('admin.purchases'));
        $response->assertOk();
    }

    public function test_can_open_create_modal_and_initialize(): void
    {
        Livewire::actingAs($this->user)
            ->test(PurchaseManager::class)
            ->call('openCreateModal')
            ->assertSet('showModal', true)
            ->assertSet('purchase_date', now()->format('Y-m-d'))
            ->assertSet('status', 'completed')
            ->assertSet('items', []);
    }

    public function test_can_add_variant_to_purchase_items(): void
    {
        $variant = ProductVariant::factory()->create([
            'stock_quantity' => 5,
            'cost_price' => 20.00,
        ]);

        Livewire::actingAs($this->user)
            ->test(PurchaseManager::class)
            ->call('addVariant', $variant->id)
            ->assertSet('items', [
                $variant->id => [
                    'variant_id' => $variant->id,
                    'name' => $variant->product->name,
                    'sku' => $variant->sku,
                    'color' => $variant->color,
                    'size' => $variant->size,
                    'quantity' => 1,
                    'unit_cost' => 20.00,
                    'current_cost' => 20.00,
                ]
            ]);
    }

    public function test_adding_existing_item_increments_quantity(): void
    {
        $variant = ProductVariant::factory()->create([
            'stock_quantity' => 5,
            'cost_price' => 20.00,
        ]);

        Livewire::actingAs($this->user)
            ->test(PurchaseManager::class)
            ->call('addVariant', $variant->id)
            ->call('addVariant', $variant->id)
            ->assertSet('items.' . $variant->id . '.quantity', 2);
    }

    public function test_can_remove_item_from_purchase_list(): void
    {
        $variant = ProductVariant::factory()->create();

        Livewire::actingAs($this->user)
            ->test(PurchaseManager::class)
            ->call('addVariant', $variant->id)
            ->call('removeItem', $variant->id)
            ->assertSet('items', []);
    }

    public function test_can_save_purchase_and_update_stock_and_cost(): void
    {
        $variant = ProductVariant::factory()->create([
            'stock_quantity' => 10,
            'cost_price' => 15.00,
        ]);

        Livewire::actingAs($this->user)
            ->test(PurchaseManager::class)
            ->set('supplier_id', $this->supplier->id)
            ->set('purchase_date', '2026-06-09')
            ->set('status', 'completed')
            ->call('addVariant', $variant->id)
            // Modify quantity and unit cost in the list
            ->set('items.' . $variant->id . '.quantity', 5)
            ->set('items.' . $variant->id . '.unit_cost', 18.50) // different cost
            ->call('savePurchase')
            ->assertHasNoErrors()
            ->assertSet('showModal', false)
            ->assertSet('items', []);

        // Assert database records
        $this->assertDatabaseHas('purchases', [
            'supplier_id' => $this->supplier->id,
            'purchase_date' => '2026-06-09',
            'status' => 'completed',
            'total_amount' => 18.50 * 5, // 92.50
        ]);

        $purchase = Purchase::first();
        $this->assertNotNull($purchase->invoice_no);
        $this->assertStringStartsWith('PUR-', $purchase->invoice_no);

        $this->assertDatabaseHas('purchase_items', [
            'purchase_id' => $purchase->id,
            'product_variant_id' => $variant->id,
            'quantity' => 5,
            'unit_cost' => 18.50,
            'subtotal' => 92.50,
        ]);

        // Assert stock was incremented (10 + 5 = 15)
        $this->assertEquals(15, $variant->fresh()->stock_quantity);

        // Assert cost price was updated to 18.50 (since it is different)
        $this->assertEquals(18.50, (float)$variant->fresh()->cost_price);
    }
}
