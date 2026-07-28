<div>
    <div class="position-relative">
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th width="50"></th>
                        <th>Client</th>
                        <th>Address</th>
                        <th class="text-center" width="80">Status</th>
                        <th width="50" class="text-center"><i class="fa fa-cog"></i></th>
                    </tr>
                </thead>
                <tbody>
                    @if(isset($clientData) && $clientData)
                        @foreach($clientData as $key => $val)
                            <tr>
                                <td>
                                    @php
                                        $photo = $val->details?->photo;
                                        if ($photo && file_exists(public_path('storage/' . $photo))) {
                                            $photoUrl = asset('storage/' . $photo);
                                        } else {
                                            $photoUrl = asset('assets/images/user/user.png');
                                        }
                                    @endphp

                                    <img class="rounded bordered"
                                        src="{{ $photoUrl }}"
                                        width="50"
                                        height="50"
                                        alt=""
                                        loading="lazy"
                                    >
                                </td>
                                <td class="align-center">
                                    <div class="d-flex flex-column">
                                        <span>{{ Str::title((($val->details->lastname) ?? '' ) . ' ' . (($val->details->firstname) ?? '') . ' ' . (($val->details->middlename) ?? '')) }} </span>
                                        <span class="fw-semibold">{{ $val->client_code }}</span>
                                        <span class="badge bg-light p-1">{{ $val->clientGroupTagging->type_name ?? ''; }}</span>
                                    </div>
                                </td>
                                <td class="align-center">{{ $val->details->present_full_address ?? '' }}</td>
                                <td class="text-center align-center">
                                    @if($val->status == 1)
                                        <span class="badge badge-success w-100">Active</span>
                                    @else
                                        <span class="badge badge-danger w-100">Inactive</span>
                                    @endif
                                </td>
                                <td class="align-center">
                                    <div class="dropdown">
                                        <button class="btn btn-outline-light btn-sm" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="fa fa-ellipsis-v"></i>
                                        </button>
                                        <ul class="dropdown-menu">                                                
                                            @can('edit-client')
                                            <li><a class="dropdown-item" href="{{ url('client/'. $val->id.'/edit') }}" target="_blank"><i class="fa fa-pencil"></i> Edit</a></li>
                                            @endcan
                                            @can('view-ledger')
                                            <li><a class="dropdown-item" href="{{ url('client/'. $val->id.'/ledger') }}" target="_blank"><i class="fa fa-table"></i> Ledger</a></li>
                                            @endcan
                                            @can('view-ledger')
                                            <li><a class="dropdown-item" href="{{ url('client/'. $val->id .'/loan-history') }}" target="_blank"><i class="fa fa-list"></i> Loan History</a></li>
                                            @endcan
                                            <li><hr class="dropdown-divider"></li>
                                            @can('delete')
                                            <li><a class="dropdown-item txt-danger" href="#" onclick="deleteData({{ $val->id }})" ><i class="fa fa-trash"></i> Delete</a></li>
                                            @endcan
                                            @if(isset($val->clientGroupTagging->type_name) && $val->clientGroupTagging->type_name && auth()->user()->can('delete-client-group'))
                                            <li><hr class="dropdown-divider"></li>
                                            <li><a class="dropdown-item txt-danger btnRemoveFromGroup" href="#" onclick="removeFromGroup({{ $val->id }})"><i class="fa fa-minus-square me-1"></i>Remove From Group</a></li>
                                            @endif
                                        </ul> 
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    @else
                    <tr>
                        <td colspan="6" class="text-center">-- No Data --</td>
                    </tr>
                    @endif
                </tbody>

                @if($clientData->hasPages())
                <tfoot>
                    <tr>
                        <td colspan="5">
                            <div class="d-flex justify-content-end">
                                {{ $clientData->links('partials.pagination.default-livewire') }}
                            </div>
                        </td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
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
