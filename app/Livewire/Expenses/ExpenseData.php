<?php

namespace App\Livewire\Expenses;

use Livewire\Component;
use Livewire\Attributes\On;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\WithPagination;

//models
use App\Models\Expense;

class ExpenseData extends Component
{
    use WithPagination;

    public $q;
    public $entries = 10;
    public $branch_id;
    public $payment_method;
    public $author;

    #[On('expense-filter-updated')]
    public function filterUpdated($data)
    {
        $this->entries = $data['entries'];
        $this->branch_id = $data['branch_id'];
        $this->payment_method = $data['payment_method'];
        $this->author = $data['author'];

        $this->resetPage();
    }

    #[On('refresh-expense-data')]
    public function refresh()
    {
        $this->resetPage();
    }

    public function placeholder()
    {
        return <<<'HTML'
        <div>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th width="50">
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
                        <th width="50" class="text-center">
                            <p class="placeholder-glow">
                                <span class="placeholder col-12"></span>
                            </p>
                        </th>
                        <th width="50" class="text-center">
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
        $query = Expense::select('expenses.*', 
                'branch.name AS branch_name', 
                'users.name AS user_name', 
                'users.email'
            )
            ->leftJoin('branch', 'branch.id' , '=', 'expenses.branch_id')
            ->leftJoin('users', 'users.id', '=', 'expenses.created_by')
            ->orderBy('expenses.expense_date', 'DESC');

        //branch_id
        if($this->branch_id){
            $query->where('expenses.branch_id', $this->branch_id);
        }

        //author
        if($this->author) {
            $query->where('expenses.created_by', $this->author);
        }

        //payment method
        if($this->payment_method) {
            $query->where('expenses.payment_method', $this->payment_method);
        }

        $expenses = $query->paginate($this->entries)->appends([
            'q' => $this->q,
            'entries' => $this->entries,
            'branch_id' => $this->branch_id,
            'author' => $this->author, 
            'payment_method' => $this->payment_method
        ]);

        return view('livewire.expenses.expense-data', compact(
            'expenses'
        ));
    }
}

