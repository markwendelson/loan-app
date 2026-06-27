<?php

namespace App\Livewire\Client\ClientList;

use Livewire\Component;
use Livewire\Attributes\On;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\WithPagination;


//models
use App\Models\BranchUser;
use App\Models\Client;

class TableData extends Component
{
    use WithPagination;
    
    public $entries = 10;
    public $q = '';
    public $branch_id = '';
    public $group_type= '';

    protected $queryString= [
        'entries',
        'q',
        'branch_id',
        'group_type'
    ];

    public $allowedBranches;

    #[On('client-list-filter-updated')]
    public function updateFilters($data)
    {
        $this->q = $data['q'];
        $this->entries = $data['entries'];
        $this->branch_id = $data['branch_id'];
        $this->group_type = $data['group_type'];

        $this->resetPage();
    }

    public function mount()
    {
        $this->branch_id =  auth()->user()->current_branch_id;
        $this->allowedBranches = BranchUser::where('user_id', auth()->user()->id)
            ->where('is_active', 1)->pluck('branch_id')->toArray();
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
                        </tr>
                    @endfor
                </tbody>

            </table>
        </div>
        HTML;
    }

    public function render()
    {
        $query = Client::with('details','clientGroupTagging');

        //q
        if($this->q) {
            $qVal = $this->q;
            $query->where(function($q) use ($qVal) {
                $q->where('client_code', 'like', '%' . $qVal . '%')
                    ->orWhereHas('details', function($q2) use ($qVal) {
                        $q2->where('firstname', 'like', '%' . $qVal . '%');
                        $q2->orWhere('middlename', 'like', '%' . $qVal . '%');
                        $q2->orWhere('lastname', 'like', '%' . $qVal . '%');
                        $q2->orWhere('email', 'like', '%' . $qVal . '%');
                    });
            });
        }

        //branch
        if($this->branch_id) {
            $query->where('branch_id', $this->branch_id);
        }else{
            $query->whereIn('branch_id', $this->allowedBranches);
        }

        //group_type
        if($this->group_type) {
            $groupType = $this->group_type;
            $query->whereHas('clientGroupTagging', function($q) use($groupType) {
                $q->where('client_group_type_id', $groupType);
            });
        }

        $clientData = $query->paginate($this->entries ?? 10)->appends([
            'entries' => $this->entries,
            'q' => $this->q,
            'branch_id' => $this->branch_id,
            'group_type' => $this->group_type,
        ]);

        return view('livewire.client.client-list.table-data', compact(
            'clientData'
        ));
    }

    public function updated()
    {
        $this->resetPage();
    }
}
