<?php

namespace App\Livewire\CheckEncashment;

use Livewire\Component;

//models
use App\Models\Bank;

class FilterData extends Component
{
    public $q;
    public $entries = 10;
    public $bank;

    public $bankData;

    public function updated()
    {
        $this->dispatch('encashment-filter-updated', [
            'q' => $this->q,
            'entries' => $this->entries,
            'bank' => $this->bank,
        ]);
    }

    public function mount()
    {
        //get all bank list from database
        $this->bankData = Bank::where('status', 1)->orderBy('name', 'ASC')->get();
    }

    public function render()
    {
        return view('livewire.check-encashment.filter-data');
    }
}
