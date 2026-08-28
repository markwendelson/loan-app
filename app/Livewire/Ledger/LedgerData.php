<?php

namespace App\Livewire\Ledger;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\On;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\WithPagination;
use Livewire\WithoutUrlPagination;

//models
use App\Models\Client;
use App\Models\ClientDetails;
use App\Models\LoanProduct;

class LedgerData extends Component
{
    use WithPagination;

    public $entries = 10;
    public $q = '';
    public $branch_id = null;

    public $loanProducts = [];

    public function mount(){
        $this->loanProducts = LoanProduct::where('status', 1)
            ->get();

        $this->branch_id = auth()->user()->current_branch_id;
    }

    #[On('ledger-filter-updated')]
    public function ledgerFilterUpdated($data)
    {
        $this->entries = $data['entries'];
        $this->q = $data['q'];
        $this->branch_id = $data['branch_id'];
    }

    public function render()
    {
        $ledgerData = Client::select(
            'clients.*',
            DB::raw('CONCAT(client_details.lastname, " ", client_details.firstname, ", ", client_details.middlename) AS fullname')
        )
        ->leftJoin('client_details', 'client_details.client_id', '=', 'clients.id')
        ->when($this->q, function ($query, $search) {
            $query->where(function ($query) use ($search) {
                $query->where('clients.client_code', $search)
                    ->orwhere('client_details.lastname', 'like', "%{$search}%")
                    ->orWhere('client_details.firstname', 'like', "%{$search}%")
                    ->orWhere('client_details.middlename', 'like', "%{$search}%");
            });
        })
        ->when($this->branch_id, function($query, $branch_id) {
            $query->where('clients.branch_id', $branch_id);
        })
        ->paginate($this->entries)
        ->appends([
            'q' => $this->q,
            'branch_id' => $this->branch_id
        ]);

        $colCount = ($this->loanProducts->count()) + 3;

        return view('livewire.ledger.ledger-data', compact(
            'ledgerData',
            'colCount'
        ));
    }
}
