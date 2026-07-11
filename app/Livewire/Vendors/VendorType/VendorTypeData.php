<?php

namespace App\Livewire\Vendors\VendorType;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\On;
use Illuminate\Pagination\LengthAwarePaginator;

//model
use App\Models\VendorType;

class VendorTypeData extends Component
{

    use WithPagination;

    public $q;
    public $entries = 10;
    public $status = 1;

    protected $queryString = [
        'q',
        'entries',
        'status'
    ];

    #[On('vendor-type-filter-updated')]
    public function vendorTypeFilterUpdated($data){
        $this->q = $data['q'];
        $this->entries = $data['entries'];
        $this->status = $data['status'];
    }

    #[On('vendor-type-refresh')]
    public function refreshVendorTypes()
    {
        // $this->resetPage();
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
                        <th colspan="2">
                            <p class="placeholder-glow">
                                <span class="placeholder col-12"></span>
                            </p>
                        </th>
                        <th width="120">
                            <p class="placeholder-glow">
                                <span class="placeholder col-12"></span>
                            </p>
                        </th>
                        <th width="50" colspan="2" class="text-center">
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
                            <td width="120">
                                <p class="placeholder-glow">
                                    <span class="placeholder col-12"></span>
                                </p>
                            </td>
                            <td width="50">
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
        $query = VendorType::where('status', 1)
        ->when(filled($this->q), function($query) {
            $query->where('vendor_types.name', 'like', '%'. $this->q .'%');
        })
        ->when(filled($this->status), function ($query) {
            $query->where('vendor_types.status', $this->status);
        });

        $vendorTypes = $query->paginate($this->entries)->appends([
            'q' => $this->q,
            'entries' => $this->entries,
            'status' => $this->status
        ]);

        return view('livewire.vendors.vendor-type.vendor-type-data', compact(
            'vendorTypes'
        ));
    }
}
