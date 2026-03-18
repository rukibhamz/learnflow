<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\BlogPost;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Illuminate\Support\Str;

class AdminBlogs extends Component
{
    use WithPagination, WithFileUploads;

    public $search = '';
    public $showForm = false;
    
    public $postId = null;
    public $title = '';
    public $slug = '';
    public $excerpt = '';
    public $content = '';
    public $is_published = false;
    public $image;

    protected $queryString = ['search'];

    protected $rules = [
        'title' => 'required|string|max:255',
        'slug' => 'required|string|max:255',
        'excerpt' => 'nullable|string',
        'content' => 'required|string',
        'is_published' => 'boolean',
        'image' => 'nullable|image|max:2048',
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatedTitle($value)
    {
        if (! $this->postId) {
            $this->slug = Str::slug($value);
        }
    }

    public function create()
    {
        $this->reset(['postId', 'title', 'slug', 'excerpt', 'content', 'is_published', 'image']);
        $this->showForm = true;
    }

    public function edit($id)
    {
        $post = BlogPost::findOrFail($id);
        $this->postId = $post->id;
        $this->title = $post->title;
        $this->slug = $post->slug;
        $this->excerpt = $post->excerpt;
        $this->content = $post->content;
        $this->is_published = $post->is_published;
        $this->showForm = true;
    }

    public function save()
    {
        $this->validate();

        $post = BlogPost::updateOrCreate(
            ['id' => $this->postId],
            [
                'title' => $this->title,
                'slug' => $this->slug,
                'excerpt' => $this->excerpt,
                'content' => $this->content,
                'is_published' => $this->is_published,
                'published_at' => $this->is_published && (! $this->postId || ! BlogPost::find($this->postId)->is_published) ? now() : ($this->postId ? BlogPost::find($this->postId)->published_at : null),
                'user_id' => auth()->id(),
            ]
        );

        if ($this->image) {
            $post->addMedia($this->image->getRealPath())
                 ->usingName($this->image->getClientOriginalName())
                 ->toMediaCollection('featured_image');
        }

        $this->showForm = false;
        session()->flash('success', 'Blog post saved successfully.');
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
