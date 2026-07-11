<div>
    <div class="position-relative">
        <table class="table table-bordered table-hover" id="tblVendorTypes">
            <thead>
                <tr>
                    <th>Type</th>
                    <th>Description</th>
                    <th width="120" class="text-center">Status</th>
                    <th width="50"></th>
                </tr>
            </thead>
            <tbody>
                @if(isset($vendorTypes) && $vendorTypes)
                    @foreach($vendorTypes as $key => $val)
                        <tr wire:key="vendor-type-{{$val->id}}" data-id="{{$val->id}}">
                            <td>{{ $val->name }}</td>
                            <td>{{ $val->description }}</td>
                            <td><span class="{{ config('constants.status_badge')[$val->status] }} }}">{{ config('constants.status_value')[$val->status] }}</span></td>
                            <td>
                                <div class="dropdown">
                                    <button class="btn btn-light" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="fa fa-ellipsis-v"></i>
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item btnEdit" href="#"><i class="fa fa-edit me-1"></i>Edit</a></li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li><a class="dropdown-item btnDelete" href="#"><i class="fa fa-trash txt-danger me-1"></i>Delete</a></li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                @endif
            </tbody>
            @if($vendorTypes->hasPages())
            <tfoot>
                <tr>
                    <td colspan="9">
                        <div class="d-flex justify-content-end">
                            {{ $vendorTypes->links('partials.pagination.default-livewire') }}
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
