<?php

namespace App\Livewire\Walkin;

use Livewire\Component;

class FilterData extends Component
{
    public $q;
    public $entries = 10;

    public function updated()
    {
        $this->dispatch('walkin-filter-updated', [
            'q' => $this->q,
            'entries' => $this->entries,
        ]);
    }

    public function render()
    {
        return view('livewire.walkin.filter-data');
    }
}
