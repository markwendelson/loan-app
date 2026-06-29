<?php

namespace App\Livewire\User;

use Livewire\Component;

//models
use App\Models\Branch;
use App\Models\BranchUser;

class FilterData extends Component
{
    public $q = '';
    public $entries = 10;
    public $branch_id = '';

    public $branchData;
    public $allowedBranches;

    protected $queryString = [
        'q',
        'entries',
        'branch_id',
    ];

    public function mount()
    {
        $this->branch_id = auth()->user()->current_branch_id;
        $this->branchData = Branch::where('status', 1)->get();

        $this->allowedBranches = BranchUser::where('user_id', auth()->id())
            ->where('is_active', 1)
            ->pluck('branch_id')
            ->toArray();
    }

    public function updated()
    {
        $this->dispatch('user-filter-updated', [
            'q' => $this->q,
            'entries' => $this->entries,
            'branch_id' => $this->branch_id
        ]);
    }

    public function render()
    {
        return view('livewire.user.filter-data');
    }
}
