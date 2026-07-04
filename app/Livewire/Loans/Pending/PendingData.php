<?php

namespace App\Livewire\Loans\Pending;

use App\Enums\LoanStatus;

use Livewire\Component;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\WithPagination;

//models
use App\Models\Loan;

class PendingData extends Component
{
    use WithPagination;

    public $q;
    public $entries = 10;
    public $branch_id;
    public $status;
    
    public $from;
    public $to;

    public $prod_id;

    protected $queryString = [
        'q',
        'branch_id',
        'status',
        'from',
        'to',
        'prod_id',
    ];
    
    public $current_module = 'pending';

    public function mount()
    {
        $this->branch_id = auth()->user()->current_branch_id;
    }

    #[On('all-loans-filter-updated')]
    public function filterUpdated($data)
    {
        $this->q = $data['q'];
        $this->entries = $data['entries'];
        $this->branch_id = $data['branch_id'];
        $this->status = $data['status'];
        $this->from = $data['from'];
        $this->to = $data['to'];
        $this->prod_id = $data['prod_id'];

        $this->resetPage();
    }

    public function placeholder()
    {
        return <<<'HTML'
        <div>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th width="200">
                            <p class="placeholder-glow">
                                <span class="placeholder col-12"></span>
                            </p>
                        </th>
                        <th>
                            <p class="placeholder-glow">
                                <span class="placeholder col-12"></span>
                            </p>
                        </th>
                        <th>
                            <p class="placeholder-glow">
                                <span class="placeholder col-12"></span>
                            </p>
                        </th>
                        <th class="text-center" width="120">
                            <p class="placeholder-glow">
                                <span class="placeholder col-12"></span>
                            </p>
                        </th>
                        <th class="text-center">
                            <p class="placeholder-glow">
                                <span class="placeholder col-12"></span>
                            </p>
                        </th>
                        <th width="50" class="text-center">
                            <p class="placeholder-glow">
                                <span class="placeholder col-12"></span>
                            </p>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @for ($x = 1; $x < 10; $x++)
                        <tr>
                            <td>
                                <p class="placeholder-glow">
                                    <span class="placeholder col-12"></span>
                                </p>
                            </td>
                            <td>
                                <p class="placeholder-glow">
                                    <span class="placeholder col-12"></span>
                                    <span class="placeholder col-12"></span>
                                </p>
                            </td>
                            <td>
                                <p class="placeholder-glow">
                                    <span class="placeholder col-12"></span>
                                </p>
                            </td>
                            <td>
                                <p class="placeholder-glow">
                                    <span class="placeholder col-12"></span>
                                </p>
                            </td>
                            <td>
                                <p class="placeholder-glow">
                                    <span class="placeholder col-12"></span>
                                </p>
                            </td>
                            <td>
                                <p class="placeholder-glow">
                                    <span class="placeholder col-12"></span>
                                </p>
                            </td>
                        </tr>
                    @endfor
                </tbody>

            </table>
        </div>
        HTML;
    }

    public function render()
    {
        $query = Loan::select(
            'loans.id',
            'loans.loan_date',
            'loans.loan_no',
            'loans.status',
            'loans.disbursed_date',
            'loan_products.name AS product_name',
            'clients.client_code',
            'client_details.firstname',
            'client_details.middlename',
            'client_details.lastname',
        )
        ->leftJoin('loan_products', 'loan_products.id', '=', 'loans.loan_product_id')
        ->leftJoin('client_details', 'client_details.client_id', '=', 'loans.borrower_id')
        ->leftJoin('clients', 'clients.id', '=', 'loans.borrower_id')
        ->leftJoin('loan_withdraw_requests', 'loan_withdraw_requests.loan_id', '=', 'loans.id');

        if (auth()->user()->can('approve-secondary-approval')) {
            $query->where(function ($q) {
                $q->where('loans.status', LoanStatus::PENDING)
                ->orWhere('loans.status', LoanStatus::REQUEST_APPROVE);
            });
        }else{
            $query->where('loans.status', LoanStatus::PENDING)
                ->orWhere('loan_withdraw_requests.status', 'pending');
        }

        //q
        if ($this->q) {
            $qVal = $this->q;

            $query->where(function ($q) use ($qVal) {
                $q->where('loans.loan_no', 'like', '%' . $qVal . '%')
                ->orWhere('client_details.firstname', 'like', '%' . $qVal . '%')
                ->orWhere('client_details.lastname', 'like', '%' . $qVal . '%')
                ->orWhere('client_details.middlename', 'like', '%' . $qVal . '%')
                ->orWhere('clients.client_code', 'like', '%' . $qVal . '%');
            });
        }

        //branch_id
        if($this->branch_id){
            $query->where('loans.branch_id', $this->branch_id);
        }

        //status
        if($this->status){
            $query->where('loans.status', $this->status);
        }
        
        //prod_id
        if($this->prod_id){
            $query->where('loans.loan_product_id', $this->prod_id);
        }

        //loan_date
        if($this->from && $this->to){
            $query->whereBetween('loans.loan_date', [$this->from, $this->to]);
        }

        $query->orderByDesc('loans.loan_date')
        ->orderByDesc('loans.loan_no')
        ->orderByDesc('loans.id');
        
        $loans = $query->paginate($this->entries)->appends([
            'q' => $this->q,
            'status' => $this->status,
            'branch_id' => $this->branch_id,
            'from' => $this->from,
            'to' => $this->to,
            'prod_id' => $this->prod_id
        ]);
        return view('livewire.loans.loans-data', compact(
            'loans'
        ));
    }
}
