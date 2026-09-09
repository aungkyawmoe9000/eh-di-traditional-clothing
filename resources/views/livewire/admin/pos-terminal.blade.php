<div class="grid grid-cols-1 lg:grid-cols-12 gap-6 p-6">
    <!-- Left Side: Product Catalog (col-span-8) -->
    <div class="lg:col-span-8 flex flex-col h-full bg-white dark:bg-neutral-900 rounded-xl shadow-sm border border-neutral-200 dark:border-neutral-800 p-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
            <div>
                <h1 class="text-2xl font-bold text-neutral-900 dark:text-neutral-100">POS Terminal</h1>
                <p class="text-sm text-neutral-500 dark:text-neutral-400">Search and select product variants to check out.</p>
            </div>

            <!-- Search input -->
            <div class="relative w-full sm:w-72">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-neutral-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </span>
                <input
                    type="text"
                    wire:model.live="search"
                    placeholder="Search product name or SKU..."
                    class="w-full pl-10 pr-4 py-2 border border-neutral-300 dark:border-neutral-700 bg-white dark:bg-neutral-800 text-neutral-900 dark:text-neutral-100 rounded-lg shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm" />
            </div>
        </div>

        <!-- Product Variant Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4 overflow-y-auto max-h-[calc(100vh-250px)] pr-2">
            @forelse ($this->products as $variant)
                @php
                    $isOutOfStock = $variant->stock_quantity <= 0;
                @endphp
                <div
                    wire:click="{{ !$isOutOfStock ? 'addToCart(' . $variant->id . ')' : '' }}"
                    class="group relative flex flex-col justify-between p-4 bg-neutral-50 dark:bg-neutral-800/40 rounded-xl border {{ $isOutOfStock ? 'border-neutral-200 dark:border-neutral-800 opacity-60' : 'border-neutral-200 dark:border-neutral-800 hover:border-indigo-500 dark:hover:border-indigo-500 cursor-pointer transition-all duration-200 shadow-sm hover:shadow' }}"
                >
                    <div>
                        <!-- Product Name -->
                        <h3 class="font-semibold text-neutral-900 dark:text-neutral-100 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">
                            {{ $variant->product->name }}
                        </h3>

                        <!-- Variant Info -->
                        <p class="text-xs text-neutral-500 dark:text-neutral-400 mt-1">
                            SKU: <span class="font-mono">{{ $variant->sku }}</span>
                        </p>

                        <!-- Color/Size Attributes -->
                        <div class="flex flex-wrap gap-1 mt-2">
                            @if($variant->color)
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-neutral-100 dark:bg-neutral-800 text-neutral-800 dark:text-neutral-200 border border-neutral-200 dark:border-neutral-700">
                                    Color: {{ $variant->color }}
                                </span>
                            @endif
                            @if($variant->size)
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-neutral-100 dark:bg-neutral-800 text-neutral-800 dark:text-neutral-200 border border-neutral-200 dark:border-neutral-700">
                                    Size: {{ $variant->size }}
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="mt-4 flex items-center justify-between">
                        <!-- Stock Status -->
                        <div>
                            @if($isOutOfStock)
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-rose-50 dark:bg-rose-950/20 text-rose-700 dark:text-rose-400 border border-rose-200 dark:border-rose-900/40">
                                    Out of stock
                                </span>
                            @else
                                <span class="text-xs text-neutral-500 dark:text-neutral-400">
                                    Stock: <span class="font-semibold text-neutral-700 dark:text-neutral-300">{{ $variant->stock_quantity }}</span>
                                </span>
                            @endif
                        </div>

                        <!-- Price -->
                        <div class="text-right">
                            <span class="text-base font-bold text-indigo-600 dark:text-indigo-400">
                                ${{ number_format($variant->selling_price, 2) }}
                            </span>
                        </div>
                    </div>

                    <!-- Hover Add overlay/button indicator (for desktop) -->
                    @if(!$isOutOfStock)
                        <div class="absolute top-2 right-2 opacity-0 group-hover:opacity-100 transition-opacity">
                            <span class="p-1.5 bg-indigo-600 text-white rounded-full block shadow shadow-indigo-500/30">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                                </svg>
                            </span>
                        </div>
                    @endif
                </div>
            @empty
                <div class="col-span-full py-12 text-center text-neutral-500 dark:text-neutral-400">
                    No product variants found.
                </div>
            @endforelse
        </div>
    </div>

    <!-- Right Side: Cart / Checkout (col-span-4) -->
    <div class="lg:col-span-4 flex flex-col bg-white dark:bg-neutral-900 rounded-xl shadow-sm border border-neutral-200 dark:border-neutral-800 p-6 h-full min-h-[500px]">
        <div class="border-b border-neutral-200 dark:border-neutral-800 pb-4 mb-4">
            <h2 class="text-lg font-bold text-neutral-900 dark:text-neutral-100 flex items-center gap-2">
                <svg class="w-5 h-5 text-neutral-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                </svg>
                Current Order
                @if(!empty($this->cart))
                    <span class="ml-auto inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-indigo-50 dark:bg-indigo-950/30 text-indigo-700 dark:text-indigo-400">
                        {{ count($this->cart) }} {{ Str::plural('item', count($this->cart)) }}
                    </span>
                @endif
            </h2>
        </div>

        <!-- Cart Items List -->
        <div class="flex-1 overflow-y-auto max-h-[calc(100vh-420px)] divide-y divide-neutral-100 dark:divide-neutral-800 pr-2">
            @forelse ($this->cartItems as $item)
                <div class="py-3 flex items-start gap-3">
                    <div class="flex-1 min-w-0">
                        <h4 class="font-medium text-neutral-900 dark:text-neutral-100 text-sm truncate">
                            {{ $item->variant->product->name }}
                        </h4>
                        <p class="text-xs text-neutral-500 dark:text-neutral-400 mt-0.5">
                            {{ $item->variant->color }} / {{ $item->variant->size }}
                        </p>
                        <p class="text-xs text-indigo-600 dark:text-indigo-400 font-medium mt-1">
                            ${{ number_format($item->variant->selling_price, 2) }} each
                        </p>
                    </div>

                    <!-- Qty adjustments & Total -->
                    <div class="flex flex-col items-end gap-2">
                        <!-- Qty adjustment controls -->
                        <div class="flex items-center border border-neutral-300 dark:border-neutral-700 rounded bg-white dark:bg-neutral-800 overflow-hidden">
                            <button
                                type="button"
                                wire:click="decrementQuantity({{ $item->variant->id }})"
                                class="px-2 py-1 text-neutral-500 hover:bg-neutral-100 dark:hover:bg-neutral-700/50 cursor-pointer text-xs"
                            >
                                &minus;
                            </button>
                            <input
                                type="text"
                                value="{{ $item->qty }}"
                                wire:change="updateQuantity({{ $item->variant->id }}, $event.target.value)"
                                class="w-8 text-center text-xs border-x border-neutral-300 dark:border-neutral-700 bg-transparent text-neutral-900 dark:text-neutral-100 focus:outline-none p-0 h-6"
                            />
                            <button
                                type="button"
                                wire:click="incrementQuantity({{ $item->variant->id }})"
                                class="px-2 py-1 text-neutral-500 hover:bg-neutral-100 dark:hover:bg-neutral-700/50 cursor-pointer text-xs"
                            >
                                &plus;
                            </button>
                        </div>

                        <!-- Subtotal & remove -->
                        <div class="flex items-center gap-2">
                            <span class="text-sm font-semibold text-neutral-900 dark:text-neutral-100">
                                ${{ number_format($item->subtotal, 2) }}
                            </span>
                            <button
                                type="button"
                                wire:click="removeFromCart({{ $item->variant->id }})"
                                class="text-neutral-400 hover:text-rose-600 dark:hover:text-rose-400 cursor-pointer"
                                title="Remove"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            @empty
                <div class="py-12 text-center text-neutral-500 dark:text-neutral-400">
                    Cart is empty. Select products on the left to add them here.
                </div>
            @endforelse
        </div>

        <!-- Checkout calculations -->
        <div class="mt-auto border-t border-neutral-200 dark:border-neutral-800 pt-4 space-y-3">
            <!-- Payment Method Select -->
            <div class="flex items-center justify-between gap-4 text-sm">
                <label for="payment_method" class="text-neutral-500 dark:text-neutral-400 font-medium">Payment Method</label>
                <select
                    id="payment_method"
                    wire:model="payment_method"
                    class="border border-neutral-300 dark:border-neutral-700 bg-white dark:bg-neutral-800 text-neutral-900 dark:text-neutral-100 rounded-lg py-1 px-2 text-sm focus:ring-indigo-500 focus:border-indigo-500"
                >
                    <option value="Cash">Cash</option>
                    <option value="Card">Card</option>
                    <option value="Mobile">Mobile Wallet</option>
                </select>
            </div>

            <!-- Subtotal -->
            <div class="flex justify-between text-sm">
                <span class="text-neutral-500 dark:text-neutral-400">Subtotal</span>
                <span class="font-semibold text-neutral-900 dark:text-neutral-100">${{ number_format($this->subtotal, 2) }}</span>
            </div>

            <!-- Discount Input -->
            <div class="flex items-center justify-between gap-4 text-sm">
                <span class="text-neutral-500 dark:text-neutral-400">Discount ($)</span>
                <div class="relative w-28">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-2 pointer-events-none text-neutral-400 text-xs">$</span>
                    <input
                        type="number"
                        step="0.01"
                        min="0"
                        wire:model.live="discount"
                        class="w-full pl-5 pr-2 py-1 text-right border border-neutral-300 dark:border-neutral-700 bg-white dark:bg-neutral-800 text-neutral-900 dark:text-neutral-100 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500"
                        placeholder="0.00"
                    />
                </div>
            </div>

            <!-- Grand Total -->
            <div class="flex justify-between border-t border-neutral-100 dark:border-neutral-800 pt-3 text-base">
                <span class="font-bold text-neutral-900 dark:text-neutral-100">Grand Total</span>
                <span class="font-extrabold text-indigo-600 dark:text-indigo-400 text-lg">${{ number_format($this->grandTotal, 2) }}</span>
            </div>

            <!-- Checkout button -->
            <button
                type="button"
                wire:click="checkout"
                @if(empty($this->cart)) disabled @endif
                class="w-full inline-flex items-center justify-center px-4 py-3 mt-2 bg-indigo-600 hover:bg-indigo-700 disabled:bg-neutral-300 dark:disabled:bg-neutral-800 disabled:text-neutral-500 disabled:cursor-not-allowed text-white font-semibold text-sm rounded-lg shadow-sm transition-colors duration-200 cursor-pointer"
            >
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                </svg>
                Complete Checkout
            </button>
        </div>
    </div>
</div>
