<?php

namespace App\Livewire\Admin;

use App\Models\Supplier;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class SupplierManager extends Component
{
    use WithPagination;

    public $search = '';
    public $name = '';
    public $phone = '';
    public $address = '';
    public $supplierId = null;

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
            'phone' => 'nullable|string|max:255',
            'address' => 'nullable|string',
        ];
    }

    public function openCreateModal()
    {
        $this->reset(['name', 'phone', 'address', 'supplierId']);
        $this->resetErrorBag();
        $this->showModal = true;
    }

    public function loadSupplier($id)
    {
        $this->resetErrorBag();

        $supplier = Supplier::findOrFail($id);
        $this->supplierId = $supplier->id;
        $this->name = $supplier->name;
        $this->phone = $supplier->phone;
        $this->address = $supplier->address;

        $this->showModal = true;
    }

    public function save()
    {
        $validated = $this->validate();

        if ($this->supplierId) {
            $supplier = Supplier::findOrFail($this->supplierId);
            $supplier->update($validated);

            session()->flash('success', 'Supplier updated successfully!');
        } else {
            Supplier::create($validated);

            session()->flash('success', 'Supplier created successfully!');
        }

        $this->reset(['name', 'phone', 'address', 'supplierId']);
        $this->showModal = false;
    }

    public function confirmDelete($id)
    {
        $this->supplierId = $id;
        $this->showDeleteModal = true;
    }

    public function delete()
    {
        $supplier = Supplier::findOrFail($this->supplierId);

        if ($supplier->purchases()->exists()) {
            $this->showDeleteModal = false;
            session()->flash('error', 'This supplier cannot be deleted because it has existing purchase records.');

            return;
        }

        $supplier->delete();

        $this->reset(['supplierId']);
        $this->showDeleteModal = false;
        session()->flash('success', 'Supplier deleted successfully!');
    }

    public function render()
    {
        $suppliers = Supplier::query()
            ->withCount('purchases')
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%'.$this->search.'%')
                    ->orWhere('phone', 'like', '%'.$this->search.'%')
                    ->orWhere('address', 'like', '%'.$this->search.'%');
            })
            ->orderBy('name')
            ->paginate(10);

        return view('livewire.admin.supplier-manager', [
            'suppliers' => $suppliers,
        ]);
    }
}
