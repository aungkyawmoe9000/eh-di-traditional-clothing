<div class="p-6 bg-white dark:bg-neutral-900 rounded-xl shadow-sm border border-neutral-200 dark:border-neutral-800">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-neutral-900 dark:text-neutral-100">Purchase Management (Stock In)</h1>
            <p class="text-sm text-neutral-500 dark:text-neutral-400">Restock products, manage supplier purchases, and track unit costs.</p>
        </div>
        <flux:button wire:click="openCreateModal" variant="primary" class="cursor-pointer">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
            </svg>
            Create Purchase
        </flux:button>
    </div>

    <!-- Search/Filter Bar -->
    <div class="mb-6">
        <div class="relative max-w-md w-full">
            <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-neutral-400">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </span>
            <input type="text" wire:model.live="search" placeholder="Search by invoice no or supplier..." class="w-full pl-10 pr-4 py-2 border border-neutral-300 dark:border-neutral-700 bg-white dark:bg-neutral-800 text-neutral-900 dark:text-neutral-100 rounded-lg shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm" />
        </div>
    </div>

    <!-- Table Section -->
    <div class="overflow-x-auto border border-neutral-200 dark:border-neutral-800 rounded-lg">
        <table class="min-w-full divide-y divide-neutral-200 dark:divide-neutral-800">
            <thead class="bg-neutral-50 dark:bg-neutral-800/50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider">Date</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider">Invoice / Voucher No</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider">Supplier</th>
                    <th scope="col" class="px-6 py-3 text-right text-xs font-semibold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider">Total Amount</th>
                    <th scope="col" class="px-6 py-3 text-center text-xs font-semibold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider">Status</th>
                    <th scope="col" class="px-6 py-3 text-right text-xs font-semibold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-neutral-950 divide-y divide-neutral-200 dark:divide-neutral-800">
                @forelse ($purchases as $purchase)
                    <tr class="hover:bg-neutral-50 dark:hover:bg-neutral-900/40">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-neutral-900 dark:text-neutral-100">
                            {{ \Carbon\Carbon::parse($purchase->purchase_date)->format('M d, Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-mono font-medium text-indigo-600 dark:text-indigo-400">
                            {{ $purchase->invoice_no }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-neutral-700 dark:text-neutral-300">
                            {{ $purchase->supplier->name }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-semibold text-neutral-900 dark:text-neutral-100">
                            ${{ number_format($purchase->total_amount, 2) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 dark:bg-emerald-950/30 text-emerald-800 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800/50">
                                {{ ucfirst($purchase->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <flux:button size="sm" variant="outline" wire:click="viewPurchase({{ $purchase->id }})" class="cursor-pointer">
                                View Items
                            </flux:button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-10 text-center text-sm text-neutral-500 dark:text-neutral-400">
                            No purchases found. Click "Create Purchase" to restock.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $purchases->links() }}
    </div>

    <!-- Create Purchase Modal -->
    <flux:modal wire:model="showModal" class="max-w-6xl">
        <div class="space-y-6">
            <div class="border-b border-neutral-200 dark:border-neutral-800 pb-4">
                <flux:heading size="xl">Record Supplier Purchase (Stock In)</flux:heading>
                <flux:text>Restock inventory items from suppliers. All items will immediately increment stock and update unit costs.</flux:text>
            </div>

            <!-- Form Fields -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Supplier Selection -->
                <div>
                    <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-2">Supplier</label>
                    <select
                        wire:model="supplier_id"
                        class="w-full px-3 py-2 border border-neutral-300 dark:border-neutral-700 bg-white dark:bg-neutral-800 text-neutral-900 dark:text-neutral-100 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm"
                    >
                        <option value="">-- Select Supplier --</option>
                        @foreach($this->suppliers as $supplier)
                            <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                        @endforeach
                    </select>
                    @error('supplier_id') <span class="text-xs text-rose-600 mt-1 block">{{ $message }}</span> @enderror
                </div>

                <!-- Date -->
                <div>
                    <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-2">Purchase Date</label>
                    <input
                        type="date"
                        wire:model="purchase_date"
                        class="w-full px-3 py-2 border border-neutral-300 dark:border-neutral-700 bg-white dark:bg-neutral-800 text-neutral-900 dark:text-neutral-100 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm"
                    />
                    @error('purchase_date') <span class="text-xs text-rose-600 mt-1 block">{{ $message }}</span> @enderror
                </div>

                <!-- Status -->
                <div>
                    <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-2">Status</label>
                    <select
                        wire:model="status"
                        class="w-full px-3 py-2 border border-neutral-300 dark:border-neutral-700 bg-white dark:bg-neutral-800 text-neutral-900 dark:text-neutral-100 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm"
                    >
                        <option value="completed">Completed (Instantly Adds Stock)</option>
                        <option value="pending">Pending</option>
                    </select>
                    @error('status') <span class="text-xs text-rose-600 mt-1 block">{{ $message }}</span> @enderror
                </div>
            </div>

            <!-- Variant Search Section -->
            <div class="border-t border-neutral-200 dark:border-neutral-800 pt-6">
                <label class="block text-sm font-semibold text-neutral-900 dark:text-neutral-100 mb-2">Add Products to Purchase</label>
                
                <div class="relative mb-4">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-neutral-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </span>
                    <input
                        type="text"
                        wire:model.live="searchVariant"
                        placeholder="Search product variant by name or SKU to add..."
                        class="w-full pl-10 pr-4 py-2 border border-neutral-300 dark:border-neutral-700 bg-white dark:bg-neutral-800 text-neutral-900 dark:text-neutral-100 rounded-lg shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm" />

                    <!-- Search Results Dropdown -->
                    @if(strlen(trim($searchVariant)) >= 2)
                        <div class="absolute z-50 left-0 right-0 mt-1 bg-white dark:bg-neutral-900 rounded-lg shadow-xl border border-neutral-200 dark:border-neutral-800 max-h-60 overflow-y-auto">
                            @forelse($this->variantResults as $variant)
                                <div
                                    wire:click="addVariant({{ $variant->id }})"
                                    class="px-4 py-3 hover:bg-indigo-50 dark:hover:bg-neutral-800 cursor-pointer flex justify-between items-center border-b border-neutral-100 dark:border-neutral-800 last:border-0"
                                >
                                    <div>
                                        <div class="text-sm font-semibold text-neutral-900 dark:text-neutral-100">
                                            {{ $variant->product->name }}
                                        </div>
                                        <div class="text-xs text-neutral-500 dark:text-neutral-400">
                                            SKU: <span class="font-mono font-bold">{{ $variant->sku }}</span> 
                                            @if($variant->color) | Color: {{ $variant->color }} @endif
                                            @if($variant->size) | Size: {{ $variant->size }} @endif
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <div class="text-xs text-neutral-600 dark:text-neutral-400">
                                            Cost: <span class="font-semibold">${{ number_format($variant->cost_price, 2) }}</span>
                                        </div>
                                        <div class="text-xs text-neutral-500">
                                            Stock: {{ $variant->stock_quantity }}
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="px-4 py-3 text-sm text-neutral-500 dark:text-neutral-400">No matching variants found.</div>
                            @endforelse
                        </div>
                    @endif
                </div>
            </div>

            <!-- Selected Items List -->
            <div class="border-t border-neutral-200 dark:border-neutral-800 pt-4">
                <div class="overflow-x-auto border border-neutral-200 dark:border-neutral-800 rounded-lg max-h-80 font-medium">
                    <table class="min-w-full divide-y divide-neutral-200 dark:divide-neutral-800">
                        <tbody class="bg-white dark:bg-neutral-950 divide-y divide-neutral-200 dark:divide-neutral-800">
                            @forelse($items as $variantId => $item)
                                <tr class="hover:bg-neutral-50 dark:hover:bg-neutral-900/20">
                                    <td class="px-4 py-3 text-sm">
                                        <div class="font-bold text-neutral-900 dark:text-neutral-100">{{ $item['name'] }}</div>
                                        <div class="text-xs text-neutral-500 font-mono">
                                            SKU: {{ $item['sku'] }}
                                            @if($item['color']) | {{ $item['color'] }} @endif
                                            @if($item['size']) | {{ $item['size'] }} @endif
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <label class="block text-xs font-semibold text-neutral-500 mb-1">Qty</label>
                                        <input type="number" min="1" wire:model.live="items.{{ $variantId }}.quantity" class="w-20 text-center py-1 border border-neutral-300 dark:border-neutral-700 bg-white dark:bg-neutral-800 text-neutral-900 dark:text-neutral-100 rounded text-sm" />
                                        @error('items.' . $variantId . '.quantity') <span class="text-[10px] text-rose-600 mt-1 block">{{ $message }}</span> @enderror
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <label class="block text-xs font-semibold text-neutral-500 mb-1">Unit Cost ($)</label>
                                        <input type="number" step="0.01" min="0" wire:model.live="items.{{ $variantId }}.unit_cost" class="w-24 text-center py-1 border border-neutral-300 dark:border-neutral-700 bg-white dark:bg-neutral-800 text-neutral-900 dark:text-neutral-100 rounded text-sm" />
                                        @if(abs($item['unit_cost'] - $item['current_cost']) > 0.001)
                                            <span class="text-[9px] text-indigo-600 dark:text-indigo-400 block mt-0.5 font-medium">Cost updates (Was: ${{ number_format($item['current_cost'], 2) }})</span>
                                        @endif
                                        @error('items.' . $variantId . '.unit_cost') <span class="text-[10px] text-rose-600 mt-1 block">{{ $message }}</span> @enderror
                                    </td>
                                    <td class="px-4 py-3 text-right text-sm font-bold text-neutral-900 dark:text-neutral-100">
                                        <span class="block text-xs font-semibold text-neutral-500 mb-1">Subtotal</span>
                                        ${{ number_format(((int)($item['quantity'] ?? 0)) * ((float)($item['unit_cost'] ?? 0)), 2) }}
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <label class="block text-xs font-semibold text-neutral-500 mb-1">Action</label>
                                        <button type="button" wire:click="removeItem({{ $variantId }})" class="text-rose-500 hover:text-rose-700 cursor-pointer font-semibold text-xs">Remove</button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-4 py-6 text-center text-sm text-neutral-500">Please search and select product variants to add them.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @error('items') <span class="text-xs text-rose-600 mt-2 block">{{ $message }}</span> @enderror
            </div>

            <!-- Grand Total and Actions Footer -->
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4 pt-6 border-t border-neutral-200 dark:border-neutral-800">
                <div class="text-left">
                    <span class="text-sm text-neutral-500 dark:text-neutral-400 font-medium">Total Purchase Amount:</span>
                    <h2 class="text-3xl font-extrabold text-indigo-600 dark:text-indigo-400">${{ number_format($this->totalAmount, 2) }}</h2>
                </div>
                <div class="flex gap-3 justify-end">
                    <flux:button
                        variant="outline"
                        wire:click="$set('showModal', false)"
                        class="cursor-pointer"
                    >
                        Cancel
                    </flux:button>
                    <flux:button
                        variant="primary"
                        wire:click="savePurchase"
                        class="cursor-pointer"
                        @if(empty($items)) disabled @endif
                    >
                        Save Purchase
                    </flux:button>
                </div>
            </div>
        </div>
    </flux:modal>

    <!-- View Purchase Details Modal -->
    <flux:modal
        wire:model="showViewModal"
        class="max-w-4xl"
    >
        @if($viewingPurchase)
            <div class="space-y-6">
                <div class="border-b border-neutral-200 dark:border-neutral-800 pb-4 flex justify-between items-start">
                    <div>
                        <flux:heading size="xl">Purchase Order Details</flux:heading>
                        <flux:text>Voucher No: <span class="font-mono font-semibold text-neutral-900 dark:text-white">{{ $viewingPurchase->invoice_no }}</span></flux:text>
                    </div>
                    <div>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 dark:bg-emerald-950/30 text-emerald-800 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800/50">
                            {{ ucfirst($viewingPurchase->status) }}
                        </span>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-6 bg-neutral-50 dark:bg-neutral-800/30 p-4 rounded-lg text-sm text-neutral-700 dark:text-neutral-300">
                    <div>
                        <div class="font-bold text-neutral-900 dark:text-neutral-200">Supplier: {{ $viewingPurchase->supplier->name }}</div>
                        @if($viewingPurchase->supplier->phone) <div class="mt-1">Phone: {{ $viewingPurchase->supplier->phone }}</div> @endif
                    </div>
                    <div>
                        <div>Date: <span class="font-semibold">{{ \Carbon\Carbon::parse($viewingPurchase->purchase_date)->format('F d, Y') }}</span></div>
                    </div>
                </div>

                <div class="border border-neutral-200 dark:border-neutral-800 rounded-lg overflow-hidden">
                    <table class="min-w-full divide-y divide-neutral-200 dark:divide-neutral-800 text-sm">
                        <thead class="bg-neutral-50 dark:bg-neutral-800/50">
                            <tr>
                                <th class="px-4 py-2 text-left text-neutral-500 dark:text-neutral-400">Item</th>
                                <th class="px-4 py-2 text-center text-neutral-500 dark:text-neutral-400">Qty</th>
                                <th class="px-4 py-2 text-right text-neutral-500 dark:text-neutral-400">Cost</th>
                                <th class="px-4 py-2 text-right text-neutral-500 dark:text-neutral-400">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-neutral-950 divide-y divide-neutral-200 dark:divide-neutral-800">
                            @foreach($viewingPurchase->items as $item)
                                <tr>
                                    <td class="px-4 py-3">
                                        <div class="font-bold text-neutral-900 dark:text-neutral-100">{{ $item->productVariant->product->name ?? 'Deleted Product' }}</div>
                                        <div class="text-xs text-neutral-500 font-mono">
                                            SKU: {{ $item->productVariant->sku ?? 'N/A' }}
                                            @if($item->productVariant->color ?? null) | {{ $item->productVariant->color }} @endif
                                            @if($item->productVariant->size ?? null) | {{ $item->productVariant->size }} @endif
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 text-center text-neutral-700 dark:text-neutral-300">{{ $item->quantity }}</td>
                                    <td class="px-4 py-3 text-right text-neutral-700 dark:text-neutral-300">${{ number_format($item->unit_cost, 2) }}</td>
                                    <td class="px-4 py-3 text-right font-bold text-neutral-900 dark:text-neutral-100">${{ number_format($item->subtotal, 2) }}</td>
                                </tr>
                            @endforeach
                            <tr class="bg-neutral-50 dark:bg-neutral-800/20 font-bold text-neutral-900 dark:text-neutral-100">
                                <td colspan="3" class="px-4 py-3 text-right">Grand Total:</td>
                                <td class="px-4 py-3 text-right text-indigo-600 dark:text-indigo-400 text-base">${{ number_format($viewingPurchase->total_amount, 2) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="flex justify-end pt-4">
                    <flux:button variant="outline" wire:click="$set('showViewModal', false)" class="cursor-pointer">Close</flux:button>
                </div>
            </div>
        @endif
    </flux:modal>
</div>
