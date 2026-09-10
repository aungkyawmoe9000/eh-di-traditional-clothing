<div class="p-6 bg-white dark:bg-neutral-900 rounded-xl shadow-sm border border-neutral-200 dark:border-neutral-800">

    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-neutral-900 dark:text-neutral-100">Products</h1>
            <p class="text-sm text-neutral-500 dark:text-neutral-400">Manage your product catalog, prices, and stock levels.</p>
        </div>
        <flux:button
            wire:click="openCreateModal"
            variant="primary"
            class="cursor-pointer"
        >
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Add New Product
        </flux:button>
    </div>

    <!-- Session Flash Messages -->
    @if (session()->has('success'))
        <div class="mb-4 p-4 bg-emerald-50 dark:bg-emerald-950/30 border border-emerald-200 dark:border-emerald-800/50 text-emerald-800 dark:text-emerald-300 rounded-lg flex items-center justify-between" x-data="{ show: true }" x-show="show" x-transition>
            <div class="flex items-center">
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                </svg>
                <span class="text-sm font-medium">{{ session('success') }}</span>
            </div>
            <button @click="show = false" class="text-emerald-600 hover:text-emerald-800 dark:text-emerald-400 dark:hover:text-emerald-200">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                </svg>
            </button>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="mb-4 p-4 bg-rose-50 dark:bg-rose-950/30 border border-rose-200 dark:border-rose-800/50 text-rose-800 dark:text-rose-300 rounded-lg flex items-center justify-between" x-data="{ show: true }" x-show="show" x-transition>
            <div class="flex items-center">
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                </svg>
                <span class="text-sm font-medium">{{ session('error') }}</span>
            </div>
            <button @click="show = false" class="text-rose-600 hover:text-rose-800 dark:text-rose-400 dark:hover:text-rose-200">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                </svg>
            </button>
        </div>
    @endif

    <!-- Search Bar -->
    <div class="mb-6">
        <div class="relative max-w-md">
            <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-neutral-400">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </span>
            <input
                type="text"
                wire:model.live="search"
                placeholder="Search products, SKUs, categories..."
                class="w-full pl-10 pr-4 py-2 border border-neutral-300 dark:border-neutral-700 bg-white dark:bg-neutral-800 text-neutral-900 dark:text-neutral-100 rounded-lg shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm" />
        </div>
    </div>

    <!-- Table Section -->
    <div class="overflow-x-auto border border-neutral-200 dark:border-neutral-800 rounded-lg bg-white dark:bg-neutral-900">
        <table class="min-w-full divide-y divide-neutral-200 dark:divide-neutral-800 text-left text-sm">
            <thead class="bg-neutral-50 dark:bg-neutral-800/50 text-neutral-700 dark:text-neutral-300 font-semibold uppercase tracking-wider">
                <tr>
                    <th scope="col" class="px-6 py-4">Name</th>
                    <th scope="col" class="px-6 py-4">Category</th>
                    <th scope="col" class="px-6 py-4">SKU</th>
                    <th scope="col" class="px-6 py-4">Price</th>
                    <th scope="col" class="px-6 py-4">Stock</th>
                    <th scope="col" class="px-6 py-4 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-neutral-200 dark:divide-neutral-800 bg-white dark:bg-neutral-900 text-neutral-900 dark:text-neutral-100">
                @forelse ($products as $product)
                    <tr class="hover:bg-neutral-50 dark:hover:bg-neutral-800/30 transition-colors">
                        <!-- Name & Description -->
                        <td class="px-6 py-4 font-medium whitespace-nowrap">
                            <div class="flex flex-col">
                                <span class="text-neutral-900 dark:text-neutral-100 font-semibold">{{ $product->name }}</span>
                                @if($product->description)
                                    <span class="text-xs text-neutral-500 dark:text-neutral-400 max-w-xs truncate">{{ $product->description }}</span>
                                @endif
                            </div>
                        </td>

                        <!-- Category -->
                        <td class="px-6 py-4 whitespace-nowrap text-neutral-600 dark:text-neutral-300">
                            {{ $product->category->name ?? 'Uncategorized' }}
                        </td>

                        <!-- SKU -->
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($product->variants->count() > 1)
                                <div class="flex flex-col">
                                    <span class="text-xs font-semibold text-neutral-500 dark:text-neutral-400">Multiple ({{ $product->variants->count() }})</span>
                                    <span class="text-xs text-neutral-400 font-mono">{{ $product->variants->first()->sku }}...</span>
                                </div>
                            @else
                                <span class="text-xs font-mono bg-neutral-100 dark:bg-neutral-800 px-2 py-1 rounded border border-neutral-200 dark:border-neutral-700">
                                    {{ $product->variants->first()->sku ?? 'N/A' }}
                                </span>
                            @endif
                        </td>

                        <!-- Price -->
                        <td class="px-6 py-4 whitespace-nowrap font-medium text-neutral-900 dark:text-neutral-100">
                            @php
                                $prices = $product->variants->pluck('selling_price')->filter()->unique();
                            @endphp
                            @if($prices->count() > 1)
                                ${{ number_format($prices->min(), 2) }} - ${{ number_format($prices->max(), 2) }}
                            @else
                                ${{ number_format($prices->first() ?? 0, 2) }}
                            @endif
                        </td>

                        <!-- Stock -->
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php
                                $stock = $product->variants_sum_stock_quantity ?? 0;
                            @endphp
                            @if($stock == 0)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-rose-100 text-rose-800 dark:bg-rose-950/40 dark:text-rose-300">
                                    Out of stock
                                </span>
                            @elseif($stock < 5)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800 dark:bg-amber-950/40 dark:text-amber-300">
                                    Low Stock ({{ $stock }})
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800 dark:bg-emerald-950/40 dark:text-emerald-300">
                                    {{ $stock }} in stock
                                </span>
                            @endif
                        </td>

                        <!-- Actions -->
                        <td class="px-6 py-4 text-right whitespace-nowrap text-sm font-medium">
                            <button
                                wire:click="loadProduct({{ $product->id }})"
                                class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300 mr-3 cursor-pointer">
                                Edit
                            </button>
                            <button
                                wire:click="confirmDelete({{ $product->id }})"
                                class="text-rose-600 hover:text-rose-900 dark:text-rose-400 dark:hover:text-rose-300 cursor-pointer">
                                Delete
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-neutral-500 dark:text-neutral-400">
                            No products found matching your criteria.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-4">
        {{ $products->links() }}
    </div>

    <!-- Create/Edit Product Modal -->
    <flux:modal
        wire:model="showModal"
        class="max-w-4xl"
    >
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">{{ $productId ? 'Edit Product' : 'Create New Product' }}</flux:heading>
                <flux:subheading>Enter product details and configure its variants.</flux:subheading>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Product Name -->
                <div>
                    <flux:input
                        wire:model="name"
                        label="Product Name"
                        placeholder="e.g. Traditional Karen Dress"
                        required
                    />
                </div>

                <!-- Product Category -->
                <div>
                    <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-1">Category</label>
                    <select
                        wire:model="category_id"
                        class="w-full px-3 py-2 border border-neutral-300 dark:border-neutral-700 bg-white dark:bg-neutral-800 text-neutral-900 dark:text-neutral-100 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm"
                        required
                    >
                        <option value="">Select a Category</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                    @error('category_id') <span class="text-xs text-rose-600 mt-1 block">{{ $message }}</span> @enderror
                </div>
            </div>

            <!-- Product Description -->
            <div>
                <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-1">Description (Optional)</label>
                <textarea
                    wire:model="description"
                    rows="3"
                    class="w-full px-3 py-2 border border-neutral-300 dark:border-neutral-700 bg-white dark:bg-neutral-800 text-neutral-900 dark:text-neutral-100 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm"
                    placeholder="Enter product description..."
                ></textarea>
                @error('description') <span class="text-xs text-rose-600 mt-1 block">{{ $message }}</span> @enderror
            </div>

            <!-- Variants Section -->
            <div class="space-y-4">
                <div class="flex items-center justify-between border-b border-neutral-200 dark:border-neutral-800 pb-2">
                    <h4 class="font-semibold text-neutral-900 dark:text-neutral-100 text-sm">Product Variants</h4>
                    <flux:button
                        type="button"
                        wire:click="addVariant"
                        size="sm"
                        variant="ghost"
                        class="cursor-pointer"
                    >
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Add Variant
                    </flux:button>
                </div>

                <div class="space-y-3 max-h-96 overflow-y-auto pr-1">
                    @foreach($variants as $index => $variant)
                        <div class="relative p-4 border border-neutral-200 dark:border-neutral-800 rounded-lg bg-neutral-50/50 dark:bg-neutral-800/20">
                            <!-- Remove Button if multiple variants exist -->
                            @if(count($variants) > 1)
                                <button
                                    type="button"
                                    wire:click="removeVariant({{ $index }})"
                                    class="absolute top-2 right-2 text-neutral-400 hover:text-rose-500 dark:hover:text-rose-400 transition-colors cursor-pointer"
                                    title="Remove Variant"
                                >
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            @endif

                            <div class="grid grid-cols-1 md:grid-cols-6 gap-3">
                                <!-- SKU -->
                                <div class="md:col-span-2">
                                    <label class="block text-xs font-medium text-neutral-600 dark:text-neutral-400 mb-1">SKU</label>
                                    <input
                                        type="text"
                                        wire:model="variants.{{ $index }}.sku"
                                        class="w-full px-2.5 py-1.5 border border-neutral-300 dark:border-neutral-700 bg-white dark:bg-neutral-800 text-neutral-900 dark:text-neutral-100 rounded-md focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-xs font-mono"
                                        placeholder="SKU-XXXX"
                                        required
                                    />
                                    @error("variants.{$index}.sku") <span class="text-xs text-rose-600 mt-1 block">{{ $message }}</span> @enderror
                                </div>

                                <!-- Color -->
                                <div>
                                    <label class="block text-xs font-medium text-neutral-600 dark:text-neutral-400 mb-1">Color</label>
                                    <input
                                        type="text"
                                        wire:model="variants.{{ $index }}.color"
                                        class="w-full px-2.5 py-1.5 border border-neutral-300 dark:border-neutral-700 bg-white dark:bg-neutral-800 text-neutral-900 dark:text-neutral-100 rounded-md focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-xs"
                                        placeholder="e.g. Red"
                                    />
                                    @error("variants.{$index}.color") <span class="text-xs text-rose-600 mt-1 block">{{ $message }}</span> @enderror
                                </div>

                                <!-- Size -->
                                <div>
                                    <label class="block text-xs font-medium text-neutral-600 dark:text-neutral-400 mb-1">Size</label>
                                    <input
                                        type="text"
                                        wire:model="variants.{{ $index }}.size"
                                        class="w-full px-2.5 py-1.5 border border-neutral-300 dark:border-neutral-700 bg-white dark:bg-neutral-800 text-neutral-900 dark:text-neutral-100 rounded-md focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-xs"
                                        placeholder="e.g. M"
                                    />
                                    @error("variants.{$index}.size") <span class="text-xs text-rose-600 mt-1 block">{{ $message }}</span> @enderror
                                </div>

                                <!-- Cost Price -->
                                <div>
                                    <label class="block text-xs font-medium text-neutral-600 dark:text-neutral-400 mb-1">Cost Price</label>
                                    <input
                                        type="number"
                                        step="0.01"
                                        wire:model="variants.{{ $index }}.cost_price"
                                        class="w-full px-2.5 py-1.5 border border-neutral-300 dark:border-neutral-700 bg-white dark:bg-neutral-800 text-neutral-900 dark:text-neutral-100 rounded-md focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-xs"
                                        placeholder="0.00"
                                        required
                                    />
                                    @error("variants.{$index}.cost_price") <span class="text-xs text-rose-600 mt-1 block">{{ $message }}</span> @enderror
                                </div>

                                <!-- Selling Price -->
                                <div>
                                    <label class="block text-xs font-medium text-neutral-600 dark:text-neutral-400 mb-1">Selling Price</label>
                                    <input
                                        type="number"
                                        step="0.01"
                                        wire:model="variants.{{ $index }}.selling_price"
                                        class="w-full px-2.5 py-1.5 border border-neutral-300 dark:border-neutral-700 bg-white dark:bg-neutral-800 text-neutral-900 dark:text-neutral-100 rounded-md focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-xs"
                                        placeholder="0.00"
                                        required
                                    />
                                    @error("variants.{$index}.selling_price") <span class="text-xs text-rose-600 mt-1 block">{{ $message }}</span> @enderror
                                </div>

                                <!-- Stock Quantity -->
                                <div>
                                    <label class="block text-xs font-medium text-neutral-600 dark:text-neutral-400 mb-1">Stock Qty</label>
                                    <input
                                        type="number"
                                        wire:model="variants.{{ $index }}.stock_quantity"
                                        class="w-full px-2.5 py-1.5 border border-neutral-300 dark:border-neutral-700 bg-white dark:bg-neutral-800 text-neutral-900 dark:text-neutral-100 rounded-md focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-xs"
                                        placeholder="0"
                                        required
                                    />
                                    @error("variants.{$index}.stock_quantity") <span class="text-xs text-rose-600 mt-1 block">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Modal Action Buttons -->
            <div class="flex gap-3 justify-end pt-4 border-t border-neutral-200 dark:border-neutral-800">
                <flux:button
                    variant="outline"
                    wire:click="$set('showModal', false)"
                    class="cursor-pointer"
                >
                    Cancel
                </flux:button>
                <flux:button
                    variant="primary"
                    wire:click="save"
                    class="cursor-pointer"
                >
                    Save Product
                </flux:button>
            </div>
        </div>
    </flux:modal>

    <!-- Delete Confirmation Modal -->
    <flux:modal
        wire:model="showDeleteModal"
        class="max-w-md"
    >
        <div class="space-y-6">
            <div class="space-y-2">
                <flux:heading size="lg">Delete Product</flux:heading>
                <flux:text>
                    Are you sure you want to delete this product? This action cannot be undone and will permanently delete the product and all associated variants from the database.
                </flux:text>
            </div>

            <div class="flex gap-3 justify-end">
                <flux:button
                    variant="outline"
                    wire:click="$set('showDeleteModal', false)"
                    class="cursor-pointer"
                >
                    Cancel
                </flux:button>
                <flux:button
                    variant="danger"
                    wire:click="delete"
                    class="cursor-pointer"
                >
                    Delete Product
                </flux:button>
            </div>
        </div>
    </flux:modal>

</div>
