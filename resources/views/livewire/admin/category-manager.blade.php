<div x-data="{
    showCreateModal: false,
    showEditModal: false,
    showDeleteModal: false
}"
@open-create-modal.window="showCreateModal = true"
@open-edit-modal.window="showEditModal = true"
@open-delete-modal.window="showDeleteModal = true"
@close-modals.window="showCreateModal = false; showEditModal = false; showDeleteModal = false"
class="p-6 bg-white dark:bg-neutral-900 rounded-xl shadow-sm border border-neutral-200 dark:border-neutral-800">

    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-neutral-900 dark:text-neutral-100">Category Management</h1>
            <p class="text-sm text-neutral-500 dark:text-neutral-400">Create, edit, and organize clothing categories.</p>
        </div>
        <button
            wire:click="openCreateModal"
            class="inline-flex items-center justify-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium text-sm rounded-lg shadow-sm transition-colors duration-200 cursor-pointer">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Add Category
        </button>
    </div>

    <!-- Session Flash Message -->
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

    <!-- Search/Filter Bar -->
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
                placeholder="Search categories..."
                class="w-full pl-10 pr-4 py-2 border border-neutral-300 dark:border-neutral-700 bg-white dark:bg-neutral-800 text-neutral-900 dark:text-neutral-100 rounded-lg shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm" />
        </div>
    </div>

    <!-- Table Section -->
    <div class="overflow-x-auto border border-neutral-200 dark:border-neutral-800 rounded-lg">
        <table class="min-w-full divide-y divide-neutral-200 dark:divide-neutral-800 text-left text-sm">
            <thead class="bg-neutral-50 dark:bg-neutral-800/50 text-neutral-700 dark:text-neutral-300 font-semibold uppercase tracking-wider">
                <tr>
                    <th scope="col" class="px-6 py-4">Name</th>
                    <th scope="col" class="px-6 py-4">Slug</th>
                    <th scope="col" class="px-6 py-4">Description</th>
                    <th scope="col" class="px-6 py-4 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-neutral-200 dark:divide-neutral-800 bg-white dark:bg-neutral-900 text-neutral-900 dark:text-neutral-100">
                @forelse ($categories as $category)
                    <tr class="hover:bg-neutral-50 dark:hover:bg-neutral-800/30 transition-colors">
                        <td class="px-6 py-4 font-medium whitespace-nowrap">{{ $category->name }}</td>
                        <td class="px-6 py-4 text-neutral-500 dark:text-neutral-400 whitespace-nowrap">
                            <span class="px-2 py-1 bg-neutral-100 dark:bg-neutral-800 text-xs rounded border border-neutral-200 dark:border-neutral-700">
                                {{ $category->slug }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-neutral-500 dark:text-neutral-400 max-w-xs truncate">
                            {{ $category->description ?? 'No description provided.' }}
                        </td>
                        <td class="px-6 py-4 text-right whitespace-nowrap text-sm font-medium">
                            <button
                                wire:click="loadCategory({{ $category->id }})"
                                class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300 mr-3 cursor-pointer">
                                Edit
                            </button>
                            <button
                                wire:click="confirmDelete({{ $category->id }})"
                                class="text-rose-600 hover:text-rose-900 dark:text-rose-400 dark:hover:text-rose-300 cursor-pointer">
                                Delete
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-6 py-8 text-center text-neutral-500 dark:text-neutral-400">
                            No categories found matching your criteria.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-4">
        {{ $categories->links() }}
    </div>

    <!-- CREATE MODAL -->
    <div
        x-show="showCreateModal"
        class="fixed inset-0 z-50 overflow-y-auto"
        style="display: none;"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0">

        <!-- Backdrop -->
        <div class="fixed inset-0 bg-neutral-900/55 backdrop-blur-sm" @click="showCreateModal = false"></div>

        <!-- Modal Content Container -->
        <div class="flex items-center justify-center min-h-screen p-4">
            <div
                class="relative bg-white dark:bg-neutral-900 rounded-xl max-w-md w-full p-6 shadow-xl border border-neutral-200 dark:border-neutral-800"
                x-transition:enter="transition ease-out duration-300 transform"
                x-transition:enter-start="scale-95 opacity-0"
                x-transition:enter-end="scale-100 opacity-100"
                x-transition:leave="transition ease-in duration-200 transform"
                x-transition:leave-start="scale-100 opacity-100"
                x-transition:leave-end="scale-95 opacity-0">

                <h3 class="text-lg font-bold text-neutral-900 dark:text-neutral-100 mb-4">Create New Category</h3>

                <form wire:submit="createCategory" class="space-y-4">
                    <!-- Name -->
                    <div>
                        <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-1">Name</label>
                        <input
                            type="text"
                            wire:model.live="name"
                            class="w-full px-3 py-2 border border-neutral-300 dark:border-neutral-700 bg-white dark:bg-neutral-800 text-neutral-900 dark:text-neutral-100 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm"
                            required />
                        @error('name') <span class="text-xs text-rose-600 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <!-- Slug (Readonly or editable with auto-fill hint) -->
                    <div>
                        <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-1">Slug</label>
                        <input
                            type="text"
                            wire:model.live="slug"
                            class="w-full px-3 py-2 border border-neutral-300 dark:border-neutral-700 bg-neutral-50 dark:bg-neutral-950 text-neutral-500 dark:text-neutral-400 rounded-lg text-sm"
                            readonly />
                        @error('slug') <span class="text-xs text-rose-600 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <!-- Description -->
                    <div>
                        <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-1">Description (Optional)</label>
                        <textarea
                            wire:model="description"
                            rows="3"
                            class="w-full px-3 py-2 border border-neutral-300 dark:border-neutral-700 bg-white dark:bg-neutral-800 text-neutral-900 dark:text-neutral-100 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm"></textarea>
                        @error('description') <span class="text-xs text-rose-600 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <!-- Actions -->
                    <div class="flex justify-end space-x-3 pt-4 border-t border-neutral-200 dark:border-neutral-800">
                        <button
                            type="button"
                            @click="showCreateModal = false"
                            class="px-4 py-2 bg-neutral-100 hover:bg-neutral-200 text-neutral-700 dark:bg-neutral-800 dark:hover:bg-neutral-700 dark:text-neutral-300 text-sm font-medium rounded-lg transition-colors cursor-pointer">
                            Cancel
                        </button>
                        <button
                            type="submit"
                            class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg shadow-sm transition-colors cursor-pointer">
                            Save Category
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- EDIT MODAL -->
    <div
        x-show="showEditModal"
        class="fixed inset-0 z-50 overflow-y-auto"
        style="display: none;"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0">

        <!-- Backdrop -->
        <div class="fixed inset-0 bg-neutral-900/55 backdrop-blur-sm" @click="showEditModal = false"></div>

        <!-- Modal Content Container -->
        <div class="flex items-center justify-center min-h-screen p-4">
            <div
                class="relative bg-white dark:bg-neutral-900 rounded-xl max-w-md w-full p-6 shadow-xl border border-neutral-200 dark:border-neutral-800"
                x-transition:enter="transition ease-out duration-300 transform"
                x-transition:enter-start="scale-95 opacity-0"
                x-transition:enter-end="scale-100 opacity-100"
                x-transition:leave="transition ease-in duration-200 transform"
                x-transition:leave-start="scale-100 opacity-100"
                x-transition:leave-end="scale-95 opacity-0">

                <h3 class="text-lg font-bold text-neutral-900 dark:text-neutral-100 mb-4">Edit Category</h3>

                <form wire:submit="updateCategory" class="space-y-4">
                    <!-- Name -->
                    <div>
                        <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-1">Name</label>
                        <input
                            type="text"
                            wire:model.live="name"
                            class="w-full px-3 py-2 border border-neutral-300 dark:border-neutral-700 bg-white dark:bg-neutral-800 text-neutral-900 dark:text-neutral-100 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm"
                            required />
                        @error('name') <span class="text-xs text-rose-600 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <!-- Slug -->
                    <div>
                        <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-1">Slug</label>
                        <input
                            type="text"
                            wire:model.live="slug"
                            class="w-full px-3 py-2 border border-neutral-300 dark:border-neutral-700 bg-neutral-50 dark:bg-neutral-950 text-neutral-500 dark:text-neutral-400 rounded-lg text-sm"
                            readonly />
                        @error('slug') <span class="text-xs text-rose-600 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <!-- Description -->
                    <div>
                        <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-1">Description (Optional)</label>
                        <textarea
                            wire:model="description"
                            rows="3"
                            class="w-full px-3 py-2 border border-neutral-300 dark:border-neutral-700 bg-white dark:bg-neutral-800 text-neutral-900 dark:text-neutral-100 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm"></textarea>
                        @error('description') <span class="text-xs text-rose-600 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <!-- Actions -->
                    <div class="flex justify-end space-x-3 pt-4 border-t border-neutral-200 dark:border-neutral-800">
                        <button
                            type="button"
                            @click="showEditModal = false"
                            class="px-4 py-2 bg-neutral-100 hover:bg-neutral-200 text-neutral-700 dark:bg-neutral-800 dark:hover:bg-neutral-700 dark:text-neutral-300 text-sm font-medium rounded-lg transition-colors cursor-pointer">
                            Cancel
                        </button>
                        <button
                            type="submit"
                            class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg shadow-sm transition-colors cursor-pointer">
                            Update Category
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- DELETE CONFIRMATION MODAL -->
    <div
        x-show="showDeleteModal"
        class="fixed inset-0 z-50 overflow-y-auto"
        style="display: none;"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0">

        <!-- Backdrop -->
        <div class="fixed inset-0 bg-neutral-900/55 backdrop-blur-sm" @click="showDeleteModal = false"></div>

        <!-- Modal Content Container -->
        <div class="flex items-center justify-center min-h-screen p-4">
            <div
                class="relative bg-white dark:bg-neutral-900 rounded-xl max-w-sm w-full p-6 shadow-xl border border-neutral-200 dark:border-neutral-800"
                x-transition:enter="transition ease-out duration-300 transform"
                x-transition:enter-start="scale-95 opacity-0"
                x-transition:enter-end="scale-100 opacity-100"
                x-transition:leave="transition ease-in duration-200 transform"
                x-transition:leave-start="scale-100 opacity-100"
                x-transition:leave-end="scale-95 opacity-0">

                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-rose-100 dark:bg-rose-950/50 mb-4">
                    <svg class="h-6 w-6 text-rose-600 dark:text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>

                <h3 class="text-lg font-bold text-neutral-900 dark:text-neutral-100 mb-2 text-center">Delete Category</h3>
                <p class="text-sm text-neutral-500 dark:text-neutral-400 text-center mb-6">Are you sure you want to delete this category? This action cannot be undone and will delete all products under this category.</p>

                <!-- Actions -->
                <div class="flex justify-center space-x-3">
                    <button
                        type="button"
                        @click="showDeleteModal = false"
                        class="px-4 py-2 bg-neutral-100 hover:bg-neutral-200 text-neutral-700 dark:bg-neutral-800 dark:hover:bg-neutral-700 dark:text-neutral-300 text-sm font-medium rounded-lg transition-colors cursor-pointer">
                        No, Keep It
                    </button>
                    <button
                        type="button"
                        wire:click="deleteCategory"
                        class="px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white text-sm font-medium rounded-lg shadow-sm transition-colors cursor-pointer">
                        Yes, Delete
                    </button>
                </div>
            </div>
        </div>
    </div>

</div>
