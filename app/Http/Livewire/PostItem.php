<?php

namespace App\Http\Livewire;

use Livewire\Component;

class PostItem extends Component
{
    public $post; // As a public attribute it is available in the view

    public function render()
    {
        return view('livewire.post-item');
    }
}
