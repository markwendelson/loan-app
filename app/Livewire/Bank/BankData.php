<?php

namespace App\Livewire\Bank;

use Livewire\Component;
use Livewire\Attributes\On;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\WithPagination;

//models
use App\Models\Bank;

class BankData extends Component
{
    use WithPagination;

    public $entries = 10;
    public $q;

    protected $queryString = [
        'entries',
        'q',
    ];

    #[On('bank-filter-updated')]
    public function filterUpdated($data)
    {
        $this->entries = $data['entries'];
        $this->q = $data['q'];
    }

    #[On('refresh-bank-data')]
    public function refreshBankData()
    {
        
    }

    public function placeholder()
    {
        return <<<'HTML'
        <div>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th width="150">
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
                        </tr>
                    @endfor
                </tbody>

            </table>
        </div>
        HTML;
    }

    public function render()
    {
        $query = Bank::where('status', 1)
            ->when($this->q, function ($query) {
                $query->where(function ($query) {
                    $query->where('name', 'like', '%' . $this->q . '%')
                        ->orWhere('bank_code', 'like', '%' . $this->q . '%');
                });
            })
            ->orderBy('name', 'ASC');

        $banks = $query->paginate($this->entries)->appends([
            'q' => $this->q
        ]);

        return view('livewire.bank.bank-data', compact(
            'banks'
        ));
    }
}
