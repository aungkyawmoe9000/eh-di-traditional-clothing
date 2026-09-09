<?php

namespace App\Livewire\Admin;

use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Supplier;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Computed;
use Flux\Flux;

#[Layout('layouts.app')]
class PurchaseManager extends Component
{
    use WithPagination;

    public $search = '';
    public $showModal = false;
    public $showViewModal = false;
    public ?Purchase $viewingPurchase = null;

    public $supplier_id = '';
    public $purchase_date = '';
    public $status = 'completed';
    public $searchVariant = '';
    public array $items = [];

    protected $queryString = ['search' => ['except' => '']];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    protected function rules()
    {
        return [
            'supplier_id' => 'required|exists:suppliers,id',
            'purchase_date' => 'required|date',
            'status' => 'required|string|in:pending,completed',
            'items' => 'required|array|min:1',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_cost' => 'required|numeric|min:0',
        ];
    }

    public function openCreateModal()
    {
        $this->reset(['supplier_id', 'items', 'searchVariant']);
        $this->purchase_date = now()->format('Y-m-d');
        $this->status = 'completed';
        $this->resetErrorBag();
        $this->showModal = true;
    }

    public function addVariant($variantId)
    {
        $variant = ProductVariant::with('product')->find($variantId);
        if (!$variant) {
            Flux::toast(variant: 'error', text: 'Product variant not found!');
            return;
        }

        if (isset($this->items[$variantId])) {
            $this->items[$variantId]['quantity']++;
            Flux::toast(variant: 'success', text: "Increased quantity of {$variant->product->name}.");
        } else {
            $this->items[$variantId] = [
                'variant_id' => $variant->id,
                'name' => $variant->product->name,
                'sku' => $variant->sku,
                'color' => $variant->color,
                'size' => $variant->size,
                'quantity' => 1,
                'unit_cost' => (float) $variant->cost_price,
                'current_cost' => (float) $variant->cost_price,
            ];
            Flux::toast(variant: 'success', text: "Added {$variant->product->name} to purchase list.");
        }
        $this->searchVariant = '';
    }

    public function removeItem($variantId)
    {
        if (isset($this->items[$variantId])) {
            unset($this->items[$variantId]);
            Flux::toast(variant: 'success', text: 'Removed item.');
        }
    }

    #[Computed]
    public function suppliers()
    {
        return Supplier::orderBy('name')->get();
    }

    #[Computed]
    public function variantResults()
    {
        if (strlen(trim($this->searchVariant)) < 2) {
            return collect();
        }
        return ProductVariant::with('product')
            ->where('sku', 'like', '%' . $this->searchVariant . '%')
            ->orWhereHas('product', function ($pq) {
                $pq->where('name', 'like', '%' . $this->searchVariant . '%');
            })
            ->limit(10)->get();
    }

    #[Computed]
    public function totalAmount()
    {
        $total = 0;
        foreach ($this->items as $item) {
            $total += ((int)($item['quantity'] ?? 0)) * ((float)($item['unit_cost'] ?? 0));
        }
        return $total;
    }

    public function savePurchase()
    {
        $this->validate();

        try {
            DB::transaction(function () {
                $today = now()->format('Ymd');
                $count = Purchase::whereDate('created_at', now()->toDateString())->count() + 1;
                $invoiceNo = 'PUR-' . $today . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);

                while (Purchase::where('invoice_no', $invoiceNo)->exists()) {
                    $count++;
                    $invoiceNo = 'PUR-' . $today . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);
                }

                $totalAmount = $this->totalAmount;

                $purchase = Purchase::create([
                    'invoice_no' => $invoiceNo,
                    'supplier_id' => $this->supplier_id,
                    'purchase_date' => $this->purchase_date,
                    'total_amount' => $totalAmount,
                    'status' => $this->status,
                ]);

                foreach ($this->items as $item) {
                    $variantId = $item['variant_id'];
                    $qty = (int)$item['quantity'];
                    $unitCost = (float)$item['unit_cost'];
                    $subtotal = $qty * $unitCost;

                    PurchaseItem::create([
                        'purchase_id' => $purchase->id,
                        'product_variant_id' => $variantId,
                        'quantity' => $qty,
                        'unit_cost' => $unitCost,
                        'subtotal' => $subtotal,
                    ]);

                    $variant = ProductVariant::lockForUpdate()->findOrFail($variantId);
                    $variant->increment('stock_quantity', $qty);

                    if (abs((float)$variant->cost_price - $unitCost) > 0.001) {
                        $variant->update(['cost_price' => $unitCost]);
                    }
                }
            });

            $this->reset(['supplier_id', 'items', 'searchVariant']);
            $this->showModal = false;
            Flux::toast(variant: 'success', text: 'Purchase recorded successfully and stock updated!');
        } catch (\Exception $e) {
            Flux::toast(variant: 'error', text: 'Failed to record purchase: ' . $e->getMessage());
        }
    }

    public function viewPurchase($id)
    {
        $this->viewingPurchase = Purchase::with(['supplier', 'items.productVariant.product'])->findOrFail($id);
        $this->showViewModal = true;
    }

    public function render()
    {
        $purchases = Purchase::with('supplier')
            ->when($this->search, function ($query) {
                $query->where('invoice_no', 'like', '%' . $this->search . '%')
                    ->orWhereHas('supplier', function ($q) {
                        $q->where('name', 'like', '%' . $this->search . '%');
                    });
            })
            ->orderBy('purchase_date', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(10);

        return view('livewire.admin.purchase-manager', [
            'purchases' => $purchases,
        ]);
    }
}
