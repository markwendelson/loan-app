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
            <livewire:client.clientList.filterdata />
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <livewire:client.clientlist.tabledata defer />
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
