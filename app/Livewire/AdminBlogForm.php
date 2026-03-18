<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\BlogPost;
use Livewire\WithFileUploads;
use Illuminate\Support\Str;

class AdminBlogForm extends Component
{
    use WithFileUploads;

    public $postId = null;
    public $title = '';
    public $slug = '';
    public $excerpt = '';
    public $content = '';
    public $meta_description = '';
    public $keywords = '';
    public $is_published = false;
    public $image;
    public $existingImage;

    protected $rules = [
        'title' => 'required|string|max:255',
        'slug' => 'required|string|max:255',
        'excerpt' => 'nullable|string',
        'content' => 'required|string',
        'meta_description' => 'nullable|string|max:160',
        'keywords' => 'nullable|string|max:255',
        'is_published' => 'boolean',
        'image' => 'nullable|image|max:2048',
    ];

    public function mount($id = null)
    {
        if ($id) {
            $post = BlogPost::findOrFail($id);
            $this->postId = $post->id;
            $this->title = $post->title;
            $this->slug = $post->slug;
            $this->excerpt = $post->excerpt;
            $this->content = $post->content;
            $this->meta_description = $post->meta_description;
            $this->keywords = $post->keywords;
            $this->is_published = $post->is_published;
            $this->existingImage = $post->getFirstMediaUrl('featured_image');
        }
    }

    public function updatedTitle($value)
    {
        if (! $this->postId) {
            $this->slug = Str::slug($value);
        }
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
                'meta_description' => $this->meta_description,
                'keywords' => $this->keywords,
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

        session()->flash('success', 'Blog post saved successfully.');
        return redirect()->route('admin.blogs.index');
    }

    public function render()
    {
        return view('livewire.admin-blog-form');
    }
}
