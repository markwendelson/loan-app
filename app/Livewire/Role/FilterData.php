<?php

namespace App\Livewire\Role;

use Livewire\Component;

class FilterData extends Component
{
    public $q;
    public $entries = 10;

    public function updated()
    {
        $this->dispatch('role-filter-updated', [
            'q' => $this->q,
            'entries' => $this->entries
        ]);
    }

    public function render()
    {
        return view('livewire.role.filter-data');
    }
}
