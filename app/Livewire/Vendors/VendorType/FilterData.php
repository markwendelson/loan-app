<?php

namespace App\Livewire\Vendors\VendorType;

use Livewire\Component;

class FilterData extends Component
{

    public $q;
    public $entries = 10;
    public $status = 1;

    public function updated(){
        $this->dispatch('vendor-type-filter-updated', [
            'q' => $this->q,
            'entries' => $this->entries,
            'status' => $this->status
        ]);
    }

    public function render()
    {
        return view('livewire.vendors.vendor-type.filter-data');
    }
}
