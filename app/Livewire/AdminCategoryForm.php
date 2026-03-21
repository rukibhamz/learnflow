<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Category;
use Illuminate\Support\Str;

class AdminCategoryForm extends Component
{
    public ?Category $category = null;
    
    public $name = '';
    public $slug = '';
    public $icon = '';
    public $description = '';
    public $is_active = true;
    public $order = 0;

    public function mount(?Category $category = null)
    {
        if ($category && $category->exists) {
            $this->category = $category;
            $this->name = $category->name;
            $this->slug = $category->slug;
            $this->icon = $category->icon;
            $this->description = $category->description;
            $this->is_active = $category->is_active;
            $this->order = $category->order;
        }
    }

    public function updatedName($value)
    {
        $this->slug = Str::slug($value);
    }

    public function save()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:categories,slug' . ($this->category ? ',' . $this->category->id : ''),
            'icon' => 'nullable|string|max:50',
            'description' => 'nullable|string|max:1000',
            'is_active' => 'boolean',
            'order' => 'integer|min:0',
        ]);

        $data = [
            'name' => $this->name,
            'slug' => $this->slug,
            'icon' => $this->icon,
            'description' => $this->description,
            'is_active' => $this->is_active,
            'order' => $this->order,
        ];

        if ($this->category) {
            $this->category->update($data);
            session()->flash('success', 'Category updated successfully.');
        } else {
            Category::create($data);
            session()->flash('success', 'Category created successfully.');
        }

        return redirect()->route('admin.categories.index');
    }

    public function render()
    {
        return view('livewire.admin-category-form');
    }
}
