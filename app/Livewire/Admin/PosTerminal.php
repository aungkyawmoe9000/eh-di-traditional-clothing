<?php

namespace App\Livewire\Admin;

use App\Models\ProductVariant;
use App\Models\Sale;
use App\Models\SaleItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Computed;
use Flux\Flux;

#[Layout('layouts.app')]
class PosTerminal extends Component
{
    public $search = '';
    public array $cart = []; // [variant_id => qty]
    public $discount = 0;
    public $payment_method = 'Cash';

    public function updatingSearch()
    {
        // No pagination reset needed if we list products, but we can reset if needed
    }

    public function addToCart($variantId)
    {
        $variant = ProductVariant::find($variantId);

        if (!$variant) {
            Flux::toast(variant: 'error', text: 'Product variant not found!');
            return;
        }

        if ($variant->stock_quantity <= 0) {
            Flux::toast(variant: 'error', text: 'This item is out of stock!');
            return;
        }

        $currentQty = $this->cart[$variantId] ?? 0;

        if ($currentQty + 1 > $variant->stock_quantity) {
            Flux::toast(variant: 'error', text: "Cannot add more than available stock ({$variant->stock_quantity} available)!");
            return;
        }

        $this->cart[$variantId] = $currentQty + 1;
        Flux::toast(variant: 'success', text: 'Added to cart.');
    }

    public function updateQuantity($variantId, $qty)
    {
        $qty = (int) $qty;

        if ($qty <= 0) {
            unset($this->cart[$variantId]);
            Flux::toast(variant: 'success', text: 'Item removed from cart.');
            return;
        }

        $variant = ProductVariant::find($variantId);
        if (!$variant) {
            unset($this->cart[$variantId]);
            Flux::toast(variant: 'error', text: 'Product variant not found!');
            return;
        }

        if ($qty > $variant->stock_quantity) {
            $this->cart[$variantId] = $variant->stock_quantity;
            Flux::toast(variant: 'warning', text: "Quantity adjusted to maximum available stock ({$variant->stock_quantity})!");
            return;
        }

        $this->cart[$variantId] = $qty;
    }

    public function incrementQuantity($variantId)
    {
        $currentQty = $this->cart[$variantId] ?? 0;
        $this->updateQuantity($variantId, $currentQty + 1);
    }

    public function decrementQuantity($variantId)
    {
        $currentQty = $this->cart[$variantId] ?? 0;
        $this->updateQuantity($variantId, $currentQty - 1);
    }

    public function removeFromCart($variantId)
    {
        unset($this->cart[$variantId]);
        Flux::toast(variant: 'success', text: 'Item removed from cart.');
    }

    #[Computed]
    public function products()
    {
        return ProductVariant::with('product')
            ->whereHas('product', function ($query) {
                $query->whereNull('deleted_at');
            })
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('sku', 'like', '%' . $this->search . '%')
                      ->orWhereHas('product', function ($pq) {
                          $pq->where('name', 'like', '%' . $this->search . '%');
                      });
                });
            })
            ->get();
    }

    #[Computed]
    public function cartItems()
    {
        if (empty($this->cart)) {
            return collect();
        }

        $variants = ProductVariant::with('product')->find(array_keys($this->cart));

        return $variants->map(function ($variant) {
            $qty = $this->cart[$variant->id];
            $subtotal = $variant->selling_price * $qty;
            return (object) [
                'variant' => $variant,
                'qty' => $qty,
                'subtotal' => $subtotal,
            ];
        });
    }

    #[Computed]
    public function subtotal()
    {
        return $this->cartItems()->sum('subtotal');
    }

    #[Computed]
    public function grandTotal()
    {
        $discountAmount = (float) $this->discount;
        if ($discountAmount < 0) {
            $discountAmount = 0;
        }
        return max(0, $this->subtotal() - $discountAmount);
    }

    public function checkout()
    {
        if (empty($this->cart)) {
            Flux::toast(variant: 'error', text: 'Your cart is empty!');
            return;
        }

        // Validate discount is numeric and non-negative
        $discountAmount = (float) $this->discount;
        if ($discountAmount < 0) {
            Flux::toast(variant: 'error', text: 'Discount cannot be negative!');
            return;
        }

        $subtotal = $this->subtotal();
        if ($discountAmount > $subtotal) {
            Flux::toast(variant: 'error', text: 'Discount cannot exceed subtotal!');
            return;
        }

        try {
            DB::transaction(function () use ($subtotal, $discountAmount) {
                // Lock product variants for update to prevent race conditions on stock
                $variants = ProductVariant::whereIn('id', array_keys($this->cart))
                    ->lockForUpdate()
                    ->get()
                    ->keyBy('id');

                // Check stock before checking out
                foreach ($this->cart as $variantId => $qty) {
                    $variant = $variants->get($variantId);
                    if (!$variant) {
                        throw new \Exception("Product variant ID {$variantId} no longer exists.");
                    }
                    if ($variant->stock_quantity < $qty) {
                        throw new \Exception("Insufficient stock for {$variant->product->name} ({$variant->sku}). Only {$variant->stock_quantity} remaining.");
                    }
                }

                // Auto-generate voucher_no
                $today = now()->format('Ymd');
                $count = Sale::whereDate('sale_date', now()->toDateString())->count() + 1;
                $runningNo = str_pad($count, 4, '0', STR_PAD_LEFT);
                $voucherNo = 'INV-' . $today . '-' . $runningNo;

                // Create Sale
                $sale = Sale::create([
                    'voucher_no' => $voucherNo,
                    'user_id' => Auth::id(),
                    'sale_date' => now(),
                    'total_amount' => $subtotal,
                    'discount' => $discountAmount,
                    'net_amount' => max(0, $subtotal - $discountAmount),
                    'payment_method' => $this->payment_method,
                ]);

                // Create SaleItems & Deduct Stock
                foreach ($this->cart as $variantId => $qty) {
                    $variant = $variants->get($variantId);

                    SaleItem::create([
                        'sale_id' => $sale->id,
                        'product_variant_id' => $variant->id,
                        'quantity' => $qty,
                        'unit_price' => $variant->selling_price,
                        'unit_cost' => $variant->cost_price,
                        'subtotal' => $variant->selling_price * $qty,
                    ]);

                    // Deduct the sold quantity
                    $variant->decrement('stock_quantity', $qty);
                }
            });

            // Reset cart
            $this->cart = [];
            $this->discount = 0;
            $this->search = '';

            Flux::toast(variant: 'success', text: 'Sale checked out successfully!');
        } catch (\Exception $e) {
            Flux::toast(variant: 'error', text: 'Checkout failed: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.admin.pos-terminal');
    }
}
