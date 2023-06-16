<?php

namespace App\Http\Livewire;

use Livewire\Component;

class Select2Dropdown extends Component
{
    public $ottPlatform = '';

    public $webseries = [
        'Wanda Vision',
        'Money Heist',
        'Lucifer',
        'Stranger Things'
    ];

    public function render()
    {
        return view('livewire.select2-dropdown');
    }
}
