<?php

namespace App\Livewire\CashRequests;

use Livewire\Component;
use App\Models\Branch;

class FilterData extends Component
{
    public $entries = 10;
    public $branch_id;
    public $status = '';
    public $branches;

    public function mount()
    {
        $this->branch_id = auth()->user()->current_branch_id;
        $this->branches = Branch::where('status', 1)->get();
    }

    public function updated()
    {
        $this->dispatch('cash-request-filter-updated', [
            'branch_id' => $this->branch_id,
            'status' => $this->status,
            'entries' => $this->entries
        ]);
    }

    public function render()
    {
        return view('livewire.cash-requests.filter-data');
    }
}
