<?php

namespace App\Livewire\Vendors;

use Livewire\Component;

//models
use App\Models\User;
use App\Models\VendorType;

class FilterData extends Component
{

    public $entries = 10;
    public $q;
    public $is_deleted;
    public $vendor_type;
    public $author;

    public $usersData;
    public $vendorTypes;

    protected $queryString = [
        'q',
        'entries',
        'is_deleted',
        'vendor_type',
        'author',
    ];

    public function mount()
    {
        $this->usersData = User::withTrashed()
            ->where('status', 1)->get();
        $this->vendorTypes = VendorType::where('status', 1)
            ->orderBy('name', 'ASC')
            ->get();
    }

    public function updated()
    {
        $this->dispatch('vendor-filter-updated', [
            'entries' => $this->entries,
            'q' => $this->q,
            'is_deleted' => $this->is_deleted,
            'vendor_type' => $this->vendor_type,
            'author' => $this->author,
        ]);
    }

    public function render()
    {
        return view('livewire.vendors.filter-data');
    }
}
