<?php

namespace App\Livewire\Admin;

use App\Models\Category;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

#[Layout('layouts.app')]
class CategoryManager extends Component
{
    use WithPagination;

    public $search = '';
    public $name = '';
    public $slug = '';
    public $description = '';
    public $categoryId = null;

    protected $queryString = [
        'search' => ['except' => ''],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatedName($value)
    {
        $this->slug = Str::slug($value);
    }

    public function openCreateModal()
    {
        $this->reset(['name', 'slug', 'description', 'categoryId']);
        $this->resetErrorBag();
        $this->dispatch('open-create-modal');
    }

    public function createCategory()
    {
        $validated = $this->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:categories,slug',
            'description' => 'nullable|string',
        ]);

        Category::create($validated);

        $this->reset(['name', 'slug', 'description']);
        $this->dispatch('close-modals');
        session()->flash('success', 'Category created successfully!');
    }

    public function loadCategory($id)
    {
        $this->resetErrorBag();
        $category = Category::findOrFail($id);
        $this->categoryId = $category->id;
        $this->name = $category->name;
        $this->slug = $category->slug;
        $this->description = $category->description;

        $this->dispatch('open-edit-modal');
    }

    public function updateCategory()
    {
        $category = Category::findOrFail($this->categoryId);

        $validated = $this->validate([
            'name' => 'required|string|max:255',
            'slug' => [
                'required',
                'string',
                'max:255',
                Rule::unique('categories', 'slug')->ignore($category->id),
            ],
            'description' => 'nullable|string',
        ]);

        $category->update($validated);

        $this->reset(['name', 'slug', 'description', 'categoryId']);
        $this->dispatch('close-modals');
        session()->flash('success', 'Category updated successfully!');
    }

    public function confirmDelete($id)
    {
        $this->categoryId = $id;
        $this->dispatch('open-delete-modal');
    }

    public function deleteCategory()
    {
        $category = Category::findOrFail($this->categoryId);
        $category->delete();

        $this->reset(['categoryId']);
        $this->dispatch('close-modals');
        session()->flash('success', 'Category deleted successfully!');
    }

    public function render()
    {
        $categories = Category::query()
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('description', 'like', '%' . $this->search . '%');
            })
            ->orderBy('id', 'desc')
            ->paginate(10);

        return view('livewire.admin.category-manager', [
            'categories' => $categories,
        ]);
    }
}
