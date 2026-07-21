<?php

namespace App\Livewire\Bank;

use Livewire\Component;

class FilterData extends Component
{
    public $entries = 10;
    public $q;

    public function updated()
    {
        $this->dispatch('bank-filter-updated', [
            'entries' => $this->entries,
            'q' => $this->q
        ]);
    }
    

    public function render()
    {
        return view('livewire.bank.filter-data');
    }
}
