<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Category;
use Livewire\WithPagination;

class AdminCategories extends Component
{
    use WithPagination;

    public $search = '';

    protected $queryString = ['search'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function toggleStatus($id)
    {
        $category = Category::findOrFail($id);
        $category->is_active = !$category->is_active;
        $category->save();
        session()->flash('success', 'Category status updated.');
    }

    public function delete($id)
    {
        $category = Category::findOrFail($id);
        
        if ($category->courses()->count() > 0) {
            session()->flash('error', 'Cannot delete category with associated courses.');
            return;
        }

        $category->delete();
        session()->flash('success', 'Category deleted successfully.');
    }

    public function reorder($orderedIds)
    {
        foreach ($orderedIds as $index => $id) {
            Category::where('id', $id)->update(['order' => $index]);
        }
    }

    public function render()
    {
        $query = Category::query();
        
        if ($this->search) {
            $query->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('description', 'like', '%' . $this->search . '%');
        }

        return view('livewire.admin-categories', [
            'categories' => $query->orderBy('order')->orderBy('name')->paginate(20)
        ]);
    }
}
