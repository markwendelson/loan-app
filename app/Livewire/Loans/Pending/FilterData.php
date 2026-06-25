<?php

namespace App\Livewire\Loans\Pending;

use Livewire\Component;

//models
use App\Models\Branch;
use App\Models\BranchUser;
use App\Models\LoanProduct;

class FilterData extends Component
{
    public $q;
    public $entries = 10;
    public $branch_id;
    public $status;
    public $from;
    public $to;
    public $prod_id;

    public $branch;
    public $products;
    public $allowedBranches;

    protected $queryString = [
        'q',
        'branch_id',
        'status',
        'from',
        'to',
        'prod_id',
    ];
    

    public function mount()
    {
        $this->branch_id = auth()->user()->current_branch_id;
        $this->branch = Branch::where('status', 1)->get();
        $this->products = LoanProduct::where('status', 1)->get();
        $this->allowedBranches = BranchUser::where('user_id', auth()->user()->id)
            ->where('is_active', 1)->pluck('branch_id')->toArray();
    }
    
    public function updated()
    {
        $this->dispatch('all-loans-filter-updated', [
            'q' => $this->q,
            'entries' => $this->entries,
            'branch_id' => $this->branch_id,
            'status' => $this->status,
            'from' => $this->from,
            'to'=> $this->to,
            'prod_id' => $this->prod_id,
        ]);
    }

    public function render()
    {
        return view('livewire.loans.filter-data');
    }
}
