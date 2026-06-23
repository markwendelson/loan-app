<?php

namespace App\Livewire\Role;

use Livewire\Component;
use Livewire\Attributes\On;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\WithPagination;

//models
use Spatie\Permission\Models\Role;

class RoleData extends Component
{
    use WithPagination;

    public $q;
    public $entries = 10;

    #[On('role-filter-updated')]
    public function filterUpdated($data)
    {
        $this->q = $data['q'];
        $this->entries = $data['entries'];
    }

    #[On('role-updated-permission')]
    public function updated()
    {
        $this->dispatch('$refresh');
    }

    public function placeholder()
    {
        return <<<'HTML'
        <div>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>
                            <p class="placeholder-glow">
                                <span class="placeholder col-12"></span>
                            </p>
                        </th>
                        <th width="50">
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
                        </tr>
                    @endfor
                </tbody>

            </table>
        </div>
        HTML;
    }

    public function render()
    {
        $query = Role::where('name', '!=', 'super_admin');

        //q
        if($this->q) {
            $qVal = $this->q;
            $query->where(function ($query) use ($qVal) {
                $query->orWhere('roles.name', 'like', '%' . $qVal . '%');
            });
        }

        $roles = $query->paginate($this->entries)
            ->appends([
                'q' => $this->q
            ]);
        return view('livewire.role.role-data', compact(
            'roles'
        ));
    }
}
