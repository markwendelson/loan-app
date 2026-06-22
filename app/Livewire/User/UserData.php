<?php

namespace App\Livewire\User;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\On;
use Illuminate\Pagination\LengthAwarePaginator;

//models
use App\Models\BranchUser;
use App\Models\User;

class UserData extends Component
{
    use WithPagination;

    public $q;
    public $entries = 10;
    public $branch_id;

    public $branchData;
    public $allowedBranches;

    public function mount()
    {
        $this->branch_id = auth()->user()->current_branch_id;
        $this->allowedBranches = BranchUser::where('user_id', auth()->user()->id)
            ->where('is_active', 1)->pluck('branch_id')->toArray();
    }

    #[On('user-filter-updated')]
    public function updateFilters($data)
    {
        $this->q = $data['q'];
        $this->entries = $data['entries'];
        $this->branch_id = $data['branch_id'];

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

    #[On("user-created")]
    public function refresh() 
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = User::currentBranch()
            ->select('users.*', 'branch.name as branch_name')
            ->notsuper_admin()
            ->leftJoin('branch', 'branch.id', '=', 'users.current_branch_id')
            ->where('users.id', '!=', auth()->id());

        // branch filter
        if($this->branch_id) {
            $query->where('current_branch_id', $this->branch_id);
        }else{
            $query->whereIn('current_branch_id', $this->allowedBranches);
        }

        // search filter
        $query->when($this->q, function ($q) {
            $q->where(function ($sub) {
                $sub->where('users.name', 'like', "%{$this->q}%")
                    ->orWhere('users.email', 'like', "%{$this->q}%");
            });
        });

        $users = $query->paginate($this->entries)->appends([
            'q' => $this->q,
            'branch_id' => $this->branch_id,
            'entries' => $this->entries
        ]);

        // transform roles ONLY ONCE
        $users->getCollection()->transform(function ($user) {
            $user->role = getUserRoles($user, null);
            return $user;
        });

        return view('livewire.user.user-data', [
            'users' => $users
        ]);
    }
}