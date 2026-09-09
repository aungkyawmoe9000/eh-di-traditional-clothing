<?php

namespace App\Livewire\Admin;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class ProductManager extends Component
{
    use WithPagination;

    public $search = '';
    public $productId = null;
    public $name = '';
    public $category_id = '';
    public $description = '';
    public $variants = [];

    // Modal state properties
    public $showModal = false;
    public $showDeleteModal = false;

    protected $queryString = [
        'search' => ['except' => ''],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    protected function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'description' => 'nullable|string',
            'variants' => 'required|array|min:1',
            'variants.*.id' => 'nullable|integer',
            'variants.*.sku' => [
                'required',
                'string',
                'max:255',
                function ($attribute, $value, $fail) {
                    preg_match('/variants\.(\d+)\.sku/', $attribute, $matches);
                    $index = $matches[1] ?? null;
                    if ($index !== null) {
                        $variantId = $this->variants[$index]['id'] ?? null;
                        $exists = ProductVariant::query()
                            ->where('sku', $value)
                            ->when($variantId, fn($q) => $q->where('id', '!=', $variantId))
                            ->exists();
                        if ($exists) {
                            $fail('The SKU has already been taken.');
                        }
                    }
                }
            ],
            'variants.*.color' => 'nullable|string|max:255',
            'variants.*.size' => 'nullable|string|max:255',
            'variants.*.cost_price' => 'required|numeric|min:0',
            'variants.*.selling_price' => [
                'required',
                'numeric',
                'min:0',
                function ($attribute, $value, $fail) {
                    preg_match('/variants\.(\d+)\.selling_price/', $attribute, $matches);
                    $index = $matches[1] ?? null;
                    if ($index !== null) {
                        $costPrice = $this->variants[$index]['cost_price'] ?? 0;
                        if ($value <= $costPrice) {
                            $fail('The selling price must be greater than the cost price.');
                        }
                    }
                }
            ],
            'variants.*.stock_quantity' => 'required|integer|min:0',
        ];
    }

    protected $validationAttributes = [
        'category_id' => 'category',
        'variants.*.sku' => 'SKU',
        'variants.*.color' => 'color',
        'variants.*.size' => 'size',
        'variants.*.cost_price' => 'cost price',
        'variants.*.selling_price' => 'selling price',
        'variants.*.stock_quantity' => 'stock quantity',
    ];

    public function openCreateModal()
    {
        $this->reset(['name', 'category_id', 'description', 'productId']);
        $this->variants = [
            [
                'id' => null,
                'sku' => '',
                'color' => '',
                'size' => '',
                'cost_price' => '',
                'selling_price' => '',
                'stock_quantity' => 0
            ]
        ];
        $this->resetErrorBag();
        $this->showModal = true;
    }

    public function addVariant()
    {
        $this->variants[] = [
            'id' => null,
            'sku' => '',
            'color' => '',
            'size' => '',
            'cost_price' => '',
            'selling_price' => '',
            'stock_quantity' => 0
        ];
    }

    public function removeVariant($index)
    {
        if (count($this->variants) > 1) {
            unset($this->variants[$index]);
            $this->variants = array_values($this->variants);
        } else {
            session()->flash('error', 'At least one variant is required.');
        }
    }

    public function loadProduct($id)
    {
        $this->resetErrorBag();
        $product = Product::with('variants')->findOrFail($id);
        $this->productId = $product->id;
        $this->name = $product->name;
        $this->category_id = $product->category_id;
        $this->description = $product->description;

        $this->variants = $product->variants->map(fn($v) => [
            'id' => $v->id,
            'sku' => $v->sku,
            'color' => $v->color ?? '',
            'size' => $v->size ?? '',
            'cost_price' => $v->cost_price,
            'selling_price' => $v->selling_price,
            'stock_quantity' => $v->stock_quantity,
        ])->toArray();

        $this->showModal = true;
    }

    public function save()
    {
        $this->validate();

        DB::transaction(function () {
            if ($this->productId) {
                // Update Product
                $product = Product::findOrFail($this->productId);
                $product->update([
                    'name' => $this->name,
                    'category_id' => $this->category_id,
                    'description' => $this->description,
                ]);

                $keepVariantIds = [];
                foreach ($this->variants as $variantData) {
                    $variant = $product->variants()->updateOrCreate(
                        ['id' => $variantData['id'] ?? null],
                        [
                            'sku' => $variantData['sku'],
                            'color' => $variantData['color'] ?: null,
                            'size' => $variantData['size'] ?: null,
                            'cost_price' => $variantData['cost_price'],
                            'selling_price' => $variantData['selling_price'],
                            'stock_quantity' => $variantData['stock_quantity'] ?: 0,
                        ]
                    );
                    $keepVariantIds[] = $variant->id;
                }

                // Remove non-retained variants
                $product->variants()->whereNotIn('id', $keepVariantIds)->delete();
                session()->flash('success', 'Product and its variants updated successfully!');
            } else {
                // Create Product
                $product = Product::create([
                    'name' => $this->name,
                    'category_id' => $this->category_id,
                    'description' => $this->description,
                ]);

                foreach ($this->variants as $variantData) {
                    $product->variants()->create([
                        'sku' => $variantData['sku'],
                        'color' => $variantData['color'] ?: null,
                        'size' => $variantData['size'] ?: null,
                        'cost_price' => $variantData['cost_price'],
                        'selling_price' => $variantData['selling_price'],
                        'stock_quantity' => $variantData['stock_quantity'] ?: 0,
                    ]);
                }
                session()->flash('success', 'Product and its variants created successfully!');
            }
        });

        $this->reset(['name', 'category_id', 'description', 'variants', 'productId']);
        $this->showModal = false;
    }

    public function confirmDelete($id)
    {
        $this->productId = $id;
        $this->showDeleteModal = true;
    }

    public function delete()
    {
        $product = Product::findOrFail($this->productId);

        DB::transaction(function () use ($product) {
            // Delete associated variants too (SoftDeletes on both will trigger appropriately)
            $product->variants()->delete();
            $product->delete();
        });

        $this->reset(['productId']);
        $this->showDeleteModal = false;
        session()->flash('success', 'Product deleted successfully!');
    }

    public function render()
    {
        $categories = Category::orderBy('name')->get();

        $products = Product::query()
            ->with('category')
            ->withSum('variants', 'stock_quantity')
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('description', 'like', '%' . $this->search . '%')
                    ->orWhereHas('category', function ($q) {
                        $q->where('name', 'like', '%' . $this->search . '%');
                    })
                    ->orWhereHas('variants', function ($q) {
                        $q->where('sku', 'like', '%' . $this->search . '%')
                            ->orWhere('color', 'like', '%' . $this->search . '%')
                            ->orWhere('size', 'like', '%' . $this->search . '%');
                    });
            })
            ->orderBy('id', 'desc')
            ->paginate(10);

        return view('livewire.admin.product-manager', [
            'products' => $products,
            'categories' => $categories,
        ]);
    }
}
