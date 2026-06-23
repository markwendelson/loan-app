<?php

namespace App\Livewire\Expenses;

use Livewire\Component;

//models
use App\Models\Branch;
use App\Models\BranchUser;
use App\Models\User;

class FilterData extends Component
{
    public $q;
    public $entries = 10;
    public $branch_id;
    public $payment_method;
    public $author;

    public $branches;
    public $userData;
    public $allowedBranches;

    public function mount()
    {
        $this->branch_id = auth()->user()->current_branch_id;
        $this->branches = Branch::all();
        $this->userData = User::withTrashed()
            ->where('status', 1)->get();

        $this->allowedBranches = BranchUser::where('user_id', auth()->user()->id)
            ->where('is_active', 1)->pluck('branch_id')->toArray();
    }

    public function updated()
    {
        $this->dispatch('expense-filter-updated', [
            'branch_id' => $this->branch_id,
            'payment_method' => $this->payment_method,
            'author' => $this->author,
            'entries' => $this->entries
        ]);
    }

    public function render()
    {
        return view('livewire.expenses.filter-data');
    }
}
