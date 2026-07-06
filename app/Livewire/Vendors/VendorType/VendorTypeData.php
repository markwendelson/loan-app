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

        return view('livewire.vendors.vendor-type.vendor-data', compact(
            'vendorTypes'
        ));
    }
}
