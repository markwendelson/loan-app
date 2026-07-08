<div>
    <div class="position-relative">
        <table class="table table-bordered table-hover" id="tblVendors">
            <thead>
                <tr>
                    <th>Name</th>
                    <th colspan="2">TIN</th>
                    <th>Type</th>
                    <th>Contact</th>
                    <th>Email</th>
                    <th>Website</th>
                    <th colspan="2">Country</th>
                </tr>
            </thead>
            <tbody>
                @if(isset($vendorData) && $vendorData->isNotEmpty())
                    @foreach($vendorData as $key => $val)
                        <tr data-id="{{ $val->id }}" wire:key="vendor-{{ $val->id }}">
                            <td>{{ $val->name }}</td>
                            <td>{{ $val->tin }}</td>
                            <td class="text-center">{{ $val->tax_classification ?? '-'}}</td>
                            <td>{{ $val->vendor_type_name }}</td>
                            <td>{{ $val->contact_no }}</td>
                            <td>{{ $val->email }}</td>
                            <td>{{ $val->website }}</td>
                            <td>{{ $val->country_name }}</td>
                            <td class="text-center" width="50">
                                <div class="dropdown">
                                    <button class="btn btn-light btn-sm" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="fa fa-ellipsis-v"></i>
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item viewVendor" href="#"><i class="fa fa-eye txt-infos"></i> View Vendor</a></li>
                                        <li><hr class="dropdown-divider"></li>
                                        @can('edit-vendors')
                                        <li><a class="dropdown-item btnEditVendor" href="#"><i class="fa fa-edit txt-primary"></i> Edit</a></li>
                                        @endcan
                                        @can('delete-vendors')
                                        <li><a class="dropdown-item btnDelete" href="#"><i class="fa fa-trash txt-danger"></i> Delete</a></li>
                                        @endcan
                                    </ul>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td class="text-center" colspan="8">No Record Found!</td>
                    </tr>
                @endif
            </tbody>
            @if($vendorData->hasPages())
            <tfoot>
                <tr>
                    <td colspan="9">
                        <div class="d-flex justify-content-end">
                            {{ $vendorData->links('partials.pagination.default-livewire') }}
                        </div>
                    </td>
                </tr>
            </tfoot>
            @endif
        </table>
        <div
            wire:loading.delay
            wire:loading.class.remove="d-none"
            class="position-absolute top-0 start-0 w-100 h-100 d-flex justify-content-center align-items-center bg-white bg-opacity-50 d-none"
            style="z-index: 10;"
        >
        
            <div class="card shadow-sm border-0">
                <div class="card-body py-3 px-4">
                    <div class="d-flex align-items-center gap-2">
                        <div class="custom-loader" role="status"></div>
                        <span>Please wait...</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
