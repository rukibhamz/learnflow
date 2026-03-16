<?php

namespace App\Livewire;

use Livewire\Component;

class WishlistButton extends Component
{
    public $courseId;
    public $isInWishlist = false;

    public function mount($courseId)
    {
        $this->courseId = $courseId;
        
        if (auth()->check()) {
            $wishlist = auth()->user()->wishlist ?? [];
            $this->isInWishlist = in_array($courseId, $wishlist);
        }
    }

    public function toggle()
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();
        $wishlist = $user->wishlist ?? [];

        if ($this->isInWishlist) {
            $wishlist = array_values(array_diff($wishlist, [$this->courseId]));
        } else {
            $wishlist[] = $this->courseId;
        }

        $user->update(['wishlist' => $wishlist]);
        $this->isInWishlist = !$this->isInWishlist;
    }

    public function render()
    {
        return view('livewire.wishlist-button');
    }
}
