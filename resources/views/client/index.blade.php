@extends('layouts.app')

@section("title") Manage Client @endsection

@push('css')
    <style>
        .bg-default {
            background-color: #1f807f;
        }
        .bg-default th {
            color: white; /* Set white text for <th> elements */
            border: 1px solid white;
        }
        .dropdown-menu .dropdown-item {
            opacity: 0.95 !important;
        }
    </style>
@endpush

@section('content')

    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h4>Client | Manage Client</h4>
                </div>
                <div class="col-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"> <a href="{{url('')}}">
                            <svg class="stroke-icon">
                                <use href="{{url('assets/svg/icon-sprite.svg#stroke-home')}}"></use>
                            </svg></a>
                        </li>
                        <li class="breadcrumb-item">Transaction</li>
                        <li class="breadcrumb-item">Clients</li>
                        <li class="breadcrumb-item active">Manage Client</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- button container -->
    <div class="card">
        <div class="card-body">
            <a href="{{route('client.registration')}}" class="btn btn-primary float-end"><i class="fa fa-plus"></i> New Client</a>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <form id="frmFilter" method="GET" action="{{ url()->current() }}">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="d-flex gap-2 align-items-center">
                        <span>Show</span>
                        <select class="form-select" name="entries">
                            <option value="10" {{ isset($filterData['entries']) && $filterData['entries'] == 10 ? 'selected' : ''; }}>10</option>
                            <option value="25" {{ isset($filterData['entries']) && $filterData['entries'] == 25 ? 'selected' : ''; }}>25</option>
                            <option value="50" {{ isset($filterData['entries']) && $filterData['entries'] == 50 ? 'selected' : ''; }}>50</option>
                            <option value="100" {{ isset($filterData['entries']) && $filterData['entries'] == 100 ? 'selected' : ''; }}>100</option>
                        </select>
                    </div>
                    <div class="d-flex gap-2 align-items-center">

                        <!-- search textbox -->
                        <input type="search"
                            class="form-control"
                            name="q"
                            value="{{ $filterData['q'] ?? ''; }}"
                            placeholder="Search..."
                        >

                        <select class="form-select" name="branch_id">
                            @if(isset($branchData) && $branchData)
                            <option value="">-- Branch --</option>
                                @foreach($branchData as $key => $val)
                                    <option value="{{ $val->id }}" {{ isset($filterData['branch_id']) && $filterData['branch_id'] == $val->id ? 'selected' : ''; }}>{{ $val->name }}</option>
                                @endforeach
                            @endif
                        </select>

                        <select class="form-select" name="group_type">
                            <option value="">-- Tag Type --</option>
                            @if(isset($groupTypes) && $groupTypes)
                                @foreach($groupTypes as $key => $val)
                                    <option value="{{ $val->id }}" {{ isset($filterData['group_type']) && $filterData['group_type'] == $val->id ? 'selected' : ''; }}>{{ $val->name }}</option>
                                @endforeach
                            @endif
                        </select>

                        <button type="submit" class="btn btn-primary"><i class="fa fa-search"></i></button>
                    </div>
                </div>
                
            </form>
        </div>
        <div class="card-body p-0">
            <div class="table-response">
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th width="50"></th>
                            <th>Client</th>
                            <th>Address</th>
                            <th class="text-center" width="120">Status</th>
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
                                            <span>{{ (($val->details->lastname) ?? '' ) . ' ' . (($val->details->firstname) ?? '') . ' ' . (($val->details->middlename) ?? '') }} </span>
                                            <span class="fw-semibold">{{ $val->client_code }}</span>
                                            <span class="badge bg-light p-1">{{ $val->clientGroupTagging->type_name ?? ''; }}</span>
                                        </div>
                                    </td>
                                    <td class="align-center">{{ $val->details->present_full_address ?? '' }}</td>
                                    <td class="text-center align-center">
                                        @if($val->status == 1)
                                            <span class="badge badge-success">Active</span>
                                        @else
                                            <span class="badge badge-danger">Inactive</span>
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
                </table>
            </div>
        </div>
    </div>

    <!-- pagination -->
    <div class="d-flex justify-content-end">
        {{ $clientData->appends(request()->input())->onEachSide(0)->links('partials.pagination.default') }}
    </div>

    <!-- job modal -->
    @include('insurance-fee-schedule.modal')

@endsection

@push('js')

<script>
    $(document).ready(function () {
        const canDelete = {{ auth()->user()->can('delete-client') ? 1 : 0 }};
    });


    function deleteData(id) {
        swal({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            buttons: true,
            dangerMode: true,
        }).then((result) => {
            if (result) {

                $.ajax({
                    url: '/client/' + id,
                    type: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if(response.status == 'success') {
                            showToast.success(response.message)
                            location.reload();
                        }
                        if(response.status == 'error') {
                            showToast.error(response.message)
                        }
                    }
                });

            }
        })
    }

    function removeFromGroup(id) {
        swal({
            title: 'Confirm Action',
            text: "Are you sure you want to remove this client from the group?",
            icon: 'warning',
            buttons: true,
            dangerMode: true,
        }).then((result) => {
            if (result) {
                $.ajax({
                    url: '/client-group/remove-grouping/' + id,
                    type: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if(response.status == 'success') {
                            showToast.success(response.message)

                            location.reload();
                        }
                        if(response.status == 'error') {
                            showToast.error(response.message)
                        }
                    }
                });
            }
            else{
                alert("haha");
            }
        })
    }

    </script>

@endpush
