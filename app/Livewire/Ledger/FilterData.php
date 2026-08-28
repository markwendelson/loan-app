<?php

namespace App\Livewire\Ledger;

use Livewire\Component;

//models
use App\Models\Branch;

class FilterData extends Component
{
    public $entries = 10;
    public $q = '';
    public $branch_id = '';

    public $branchesData = [];

    protected $queryString = [
        'entries',
        'q',
        'branch_id'
    ];

    public function updated()
    {
        $this->dispatch('ledger-filter-updated', [
            'entries' => $this->entries,
            'q' => $this->q,
            'branch_id' => $this->branch_id
        ]);
    }

    public function mount()
    {   $this->branch_id = auth()->user()->current_branch_id;
        $this->branchesData = Branch::where('status', 1)->get();
    }

    public function render()
    {
        return view('livewire.ledger.filter-data');
    }
}
