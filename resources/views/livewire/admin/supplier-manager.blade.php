<div class="p-6 bg-white dark:bg-neutral-900 rounded-xl shadow-sm border border-neutral-200 dark:border-neutral-800">

    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-neutral-900 dark:text-neutral-100">Supplier Management</h1>
            <p class="text-sm text-neutral-500 dark:text-neutral-400">Manage the suppliers you restock inventory from.</p>
        </div>
        <flux:button wire:click="openCreateModal" variant="primary" class="cursor-pointer">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Add Supplier
        </flux:button>
    </div>

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
                placeholder="Search by name, phone, or address..."
                class="w-full pl-10 pr-4 py-2 border border-neutral-300 dark:border-neutral-700 bg-white dark:bg-neutral-800 text-neutral-900 dark:text-neutral-100 rounded-lg shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm" />
        </div>
    </div>

    <!-- Table Section -->
    <div class="overflow-x-auto border border-neutral-200 dark:border-neutral-800 rounded-lg bg-white dark:bg-neutral-900">
        <table class="min-w-full divide-y divide-neutral-200 dark:divide-neutral-800 text-left text-sm">
            <thead class="bg-neutral-50 dark:bg-neutral-800/50 text-neutral-700 dark:text-neutral-300 font-semibold uppercase tracking-wider">
                <tr>
                    <th scope="col" class="px-6 py-4">Name</th>
                    <th scope="col" class="px-6 py-4">Phone</th>
                    <th scope="col" class="px-6 py-4">Address</th>
                    <th scope="col" class="px-6 py-4">Purchases</th>
                    <th scope="col" class="px-6 py-4 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-neutral-200 dark:divide-neutral-800 bg-white dark:bg-neutral-900 text-neutral-900 dark:text-neutral-100">
                @forelse ($suppliers as $supplier)
                    <tr class="hover:bg-neutral-50 dark:hover:bg-neutral-800/30 transition-colors">
                        <td class="px-6 py-4 font-medium whitespace-nowrap">{{ $supplier->name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-neutral-600 dark:text-neutral-300">
                            {{ $supplier->phone ?: '—' }}
                        </td>
                        <td class="px-6 py-4 text-neutral-500 dark:text-neutral-400 max-w-xs truncate">
                            {{ $supplier->address ?: '—' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-50 dark:bg-indigo-950/30 text-indigo-700 dark:text-indigo-400">
                                {{ $supplier->purchases_count }} {{ Str::plural('purchase', $supplier->purchases_count) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right whitespace-nowrap text-sm font-medium">
                            <button wire:click="loadSupplier({{ $supplier->id }})" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300 mr-3 cursor-pointer">
                                Edit
                            </button>
                            <button wire:click="confirmDelete({{ $supplier->id }})" class="text-rose-600 hover:text-rose-900 dark:text-rose-400 dark:hover:text-rose-300 cursor-pointer">
                                Delete
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-neutral-500 dark:text-neutral-400">
                            No suppliers found matching your criteria.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $suppliers->links() }}
    </div>

    <!-- Create/Edit Supplier Modal -->
    <flux:modal wire:model="showModal" class="max-w-lg">
        <form wire:submit="save" class="space-y-6">
            <div>
                <flux:heading size="lg">{{ $supplierId ? 'Edit Supplier' : 'Add New Supplier' }}</flux:heading>
                <flux:subheading>Enter the supplier's contact details.</flux:subheading>
            </div>

            <flux:input wire:model="name" label="Supplier Name" placeholder="e.g. Apex Textiles Ltd." required />

            <flux:input wire:model="phone" label="Phone Number (Optional)" placeholder="e.g. +1 555 123 4567" />

            <div>
                <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-1">Address (Optional)</label>
                <textarea
                    wire:model="address"
                    rows="3"
                    class="w-full px-3 py-2 border border-neutral-300 dark:border-neutral-700 bg-white dark:bg-neutral-800 text-neutral-900 dark:text-neutral-100 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm"
                    placeholder="Enter supplier address..."
                ></textarea>
                @error('address') <span class="text-xs text-rose-600 mt-1 block">{{ $message }}</span> @enderror
            </div>

            <div class="flex gap-3 justify-end pt-4 border-t border-neutral-200 dark:border-neutral-800">
                <flux:button type="button" variant="outline" wire:click="$set('showModal', false)" class="cursor-pointer">
                    Cancel
                </flux:button>
                <flux:button type="submit" variant="primary" class="cursor-pointer">
                    {{ $supplierId ? 'Update Supplier' : 'Save Supplier' }}
                </flux:button>
            </div>
        </form>
    </flux:modal>

    <!-- Delete Confirmation Modal -->
    <flux:modal wire:model="showDeleteModal" class="max-w-md">
        <div class="space-y-6">
            <div class="space-y-2">
                <flux:heading size="lg">Delete Supplier</flux:heading>
                <flux:text>Are you sure you want to delete this supplier? This action cannot be undone.</flux:text>
            </div>

            <div class="flex gap-3 justify-end">
                <flux:button variant="outline" wire:click="$set('showDeleteModal', false)" class="cursor-pointer">
                    Cancel
                </flux:button>
                <flux:button variant="danger" wire:click="delete" class="cursor-pointer">
                    Delete Supplier
                </flux:button>
            </div>
        </div>
    </flux:modal>

</div>
