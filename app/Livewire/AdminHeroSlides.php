<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\HeroSlide;
use Livewire\WithFileUploads;

class AdminHeroSlides extends Component
{
    use WithFileUploads;

    public $showForm = false;
    
    public $slideId = null;
    public $tag = '';
    public $title = '';
    public $description = '';
    public $button_text = '';
    public $button_link = '';
    public $is_active = true;
    public $order = 0;
    public $image;

    protected $rules = [
        'tag' => 'nullable|string|max:255',
        'title' => 'required|string|max:255',
        'description' => 'nullable|string',
        'button_text' => 'nullable|string|max:255',
        'button_link' => 'nullable|string|max:255',
        'is_active' => 'boolean',
        'order' => 'integer',
        'image' => 'nullable|image|max:4096',
    ];

    public function create()
    {
        $this->slideId = null;
        $this->tag = '';
        $this->title = '';
        $this->description = '';
        $this->button_text = '';
        $this->button_link = '';
        $this->is_active = true;
        $this->image = null;
        $this->order = (HeroSlide::max('order') ?? 0) + 1;
        $this->showForm = true;
    }

    public function edit($id)
    {
        $slide = HeroSlide::findOrFail($id);
        $this->slideId = $slide->id;
        $this->tag = $slide->tag;
        $this->title = $slide->title;
        $this->description = $slide->description;
        $this->button_text = $slide->button_text;
        $this->button_link = $slide->button_link;
        $this->order = $slide->order;
        $this->is_active = $slide->is_active;
        $this->showForm = true;
    }

    public function cancel()
    {
        $this->showForm = false;
        $this->image = null;
    }

    public function save()
    {
        $this->validate();

        $slide = HeroSlide::updateOrCreate(
            ['id' => $this->slideId],
            [
                'tag' => $this->tag,
                'title' => $this->title,
                'description' => $this->description,
                'button_text' => $this->button_text,
                'button_link' => $this->button_link,
                'order' => $this->order,
                'is_active' => $this->is_active,
            ]
        );

        if ($this->image) {
            $slide->clearMediaCollection('background');
            $slide->addMedia($this->image->getRealPath())
                 ->usingName($this->image->getClientOriginalName())
                 ->toMediaCollection('background');
        }

        $this->showForm = false;
        $this->image = null;
        session()->flash('success', 'Hero slide saved successfully.');
    }

    public function toggleActive($id)
    {
        $slide = HeroSlide::findOrFail($id);
        $slide->is_active = !$slide->is_active;
        $slide->save();
        session()->flash('success', 'Slide status updated.');
    }

    public function delete($id)
    {
        HeroSlide::findOrFail($id)->delete();
        session()->flash('success', 'Hero slide deleted successfully.');
    }

    public function moveUp($id)
    {
        $slide = HeroSlide::findOrFail($id);
        $previous = HeroSlide::where('order', '<', $slide->order)->orderBy('order', 'desc')->first();
        if ($previous) {
            $temp = $slide->order;
            $slide->order = $previous->order;
            $previous->order = $temp;
            $slide->save();
            $previous->save();
        }
    }

    public function moveDown($id)
    {
        $slide = HeroSlide::findOrFail($id);
        $next = HeroSlide::where('order', '>', $slide->order)->orderBy('order', 'asc')->first();
        if ($next) {
            $temp = $slide->order;
            $slide->order = $next->order;
            $next->order = $temp;
            $slide->save();
            $next->save();
        }
    }

    public function render()
    {
        return view('livewire.admin-hero-slides', [
            'slides' => HeroSlide::orderBy('order')->get()
        ]);
    }
}
