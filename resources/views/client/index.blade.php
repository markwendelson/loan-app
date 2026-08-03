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
        <div class="card-body d-flex justify-content-end gap-2 align-items-center flex-wrap">
            <a href="#" class="btn btn-light btnColor"><i class="fa fa-paint-brush me-1"></i>Color Coding</a>
            <a href="{{route('client.registration')}}" class="btn btn-primary"><i class="fa fa-plus"></i> New Client</a>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <livewire:Client.ClientList.FilterData />
        </div>
        <div class="card-body p-0">
            <livewire:Client.ClientList.TableData defer />
        </div>
    </div>

    <!-- job modal -->
    @include('insurance-fee-schedule.modal')

    <!-- color coding modal -->
    @include('client.modal.color_coding')

@endsection

@push('js')

<script>
    $(document).ready(function () {
        const canDelete = {{ auth()->user()->can('delete-client') ? 1 : 0 }};

        //handle show color coding modal
        $(".btnColor").click(function(e) {
            e.preventDefault();

            $("#modalColor").modal("show");
        })

        //handle modal color cancel button
        $("#modalColor .btnCancel").on("click", function(e) {
            e.preventDefault();

            $("#modalColor").modal("hide");
        })
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
