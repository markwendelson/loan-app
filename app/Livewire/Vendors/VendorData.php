<?php

namespace App\Livewire\Vendors;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\On;
use Illuminate\Pagination\LengthAwarePaginator;


//model
use App\Models\Vendor;

class VendorData extends Component
{
    use WithPagination;

    public $entries = 10;
    public $q;
    public $is_deleted;
    public $vendor_type;
    public $author;

    protected $queryString = [
        'q',
        'entries',
        'is_deleted',
        'vendor_type',
        'author',
    ];

    #[On('vendor-filter-updated')]
    public function filterUpdated($data)
    {
        $this->entries = $data['entries'];
        $this->q = $data['q'];
        $this->is_deleted = $data['is_deleted'];
        $this->vendor_type = $data['vendor_type'];
        $this->author = $data['author'];

        $this->resetPage();
    }

    #[On('refresh-vendor-data')]
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
                        <th class="text-center">
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
                            <td width="100">
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
        $query = Vendor::select(
                'vendors.*',
                'country_codes.country_name'
            )
            ->leftJoin('country_codes', 'country_codes.id', '=', 'vendors.country_id');

        //soft delete
        if($this->is_deleted) {
            $query->withTrashed();
        }

        //q
        if ($this->q) {
            $qVal = $this->q;
            $query->where(function($q) use ($qVal) {
                $q->where('name', 'like', '%' . $qVal . '%')
                ->orWhere('contact_no', $qVal)
                ->orWhere('email', $qVal)
                ->orWhere('website', $qVal);
            });
        }

        //vendor_type
        if($this->vendor_type) {
            $query->where('vendor_type', $this->vendor_type);
        }

        //author
        if($this->author) {
            $query->where('created_by', $this->author);
        }

        $vendorData = $query->paginate($this->entries)->appends([
            'entries' => $this->entries,
            'q' => $this->q,
            'vendor_type' => $this->vendor_type,
            'author' => $this->author
        ]);

        return view('livewire.vendors.vendor-data', compact(
            'vendorData'
        ));
    }
}
