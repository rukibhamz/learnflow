<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\BlogPost;
use Livewire\WithPagination;

class AdminBlogs extends Component
{
    use WithPagination;

    public $search = '';

    protected $queryString = ['search'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function togglePublish($id)
    {
        $post = BlogPost::findOrFail($id);
        $post->is_published = !$post->is_published;
        if ($post->is_published && !$post->published_at) {
            $post->published_at = now();
        }
        $post->save();
        session()->flash('success', 'Post status updated.');
    }

    public function delete($id)
    {
        BlogPost::findOrFail($id)->delete();
        session()->flash('success', 'Blog post deleted successfully.');
    }

    public function render()
    {
        $query = BlogPost::query();
        if ($this->search) {
            $query->where('title', 'like', '%' . $this->search . '%')
                  ->orWhere('excerpt', 'like', '%' . $this->search . '%');
        }

        return view('livewire.admin-blogs', [
            'posts' => $query->latest()->paginate(10)
        ]);
    }
}
