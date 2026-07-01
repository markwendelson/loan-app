<?php

namespace App\Livewire\Walkin;

use Livewire\Component;

//models
use App\Models\Branch;
use App\Models\BranchUser;

class FilterData extends Component
{
    public $q;
    public $entries = 10;
    public $branch_id;

    public $branches;
    public $allowed_branches;

    public function mount()
    {
        $this->branch_id = auth()->user()->current_branch_id;
        $this->branches = Branch::where('status', 1)->get();
        $this->allowed_branches = BranchUser::where('user_id', auth()->user()->id)
            ->where('is_active', 1)->pluck('branch_id')->toArray();

    }

    public function updated()
    {
        $this->dispatch('walkin-filter-updated', [
            'q' => $this->q,
            'entries' => $this->entries,
            'branch_id' => $this->branch_id
        ]);
    }

    public function render()
    {
        return view('livewire.walkin.filter-data');
    }
}
