<?php

namespace App\Livewire\Client\ClientList;

use Livewire\Component;

//models
use App\Models\Branch;
use App\Models\ClientGroupType;

class FilterData extends Component
{
    public $q = '';
    public $entries = 10;
    public $branch_id = '';
    public $group_type= '';

    public $branchData;
    public $groupTypes;

    public function mount()
    {
        $this->branchData = Branch::all();
        $this->groupTypes = ClientGroupType::all();
        $this->branch_id =  auth()->user()->current_branch_id;
    }

    public function updated() {
        $this->dispatch('client-list-filter-updated', [
            'q' => $this->q,
            'entries' => $this->entries,
            'branch_id' => $this->branch_id,
            'group_type' => $this->group_type,
        ]);
    }

    public function render()
    {
        return view('livewire.client.client-list.filter-data');
    }
}
