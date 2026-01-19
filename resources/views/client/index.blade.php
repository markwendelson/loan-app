@extends('layouts.app')

@push('css')
<style>
    .bg-default {
         background-color: #1f807f;
     }
     .bg-default th {
         color: white; /* Set white text for <th> elements */
         border: 1px solid white;
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
                        <li class="breadcrumb-item">Client</li>
                        <li class="breadcrumb-item active">Manage Client</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- button container -->
    <div class="card">
    <div class="card-body">
        <a href="{{route('client.create')}}" class="btn btn-primary float-end"><i class="fa fa-plus"></i> New Client</a>
    </div>
    </div>


    <!-- table container -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="tblClient">
                    <thead class="bg-default">
                        <tr>
                            <th class="text-center" width="40">No.</th>
                            <th class="text-leftr" width="120">Code</th>
                            <th class="text-leftr" width="150">Name</th>
                            <th class="text-leftr" width="150">Address</th>
                            <th class="text-center" width="80">Status</th>
                            <th class="text-center" width="150"><i class="fa fa-cog"></i></th> <!-- New column for Edit/Delete buttons -->
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    <!-- job modal -->
    @include('insurance-fee-schedule.modal')

@endsection

@push('js')

<script>
    $(document).ready(function () {
        const canViewLedger = {{ auth()->user()->can('view-ledger') ? 1 : 0 }};
        const canViewClient = {{ auth()->user()->can('view-client') ? 1 : 0 }};
        const canEdit = {{ auth()->user()->can('edit-client') ? 1 : 0 }};
        const canDelete = {{ auth()->user()->can('delete-client') ? 1 : 0 }};

        const table = $('#tblClient').DataTable({
            ordering: false,
            processing: true,
            serverSide: true,
            stateSave: true,
            searching: true,
            ajax: {
                url: "{{ route('client.index') }}",
            },
            columns: [
                { data: 'id', name: 'id' },
                { data: 'client_code', name: 'client_code' },
                { data: 'details.fullname', name: 'fullname' },
                { data: 'details.present_full_address', name: 'address' },
                { data: 'status', name: 'status', render: function(data, type, row) {
                    if (data == 1) {
                        return '<span class="badge badge-success">Active</span>';
                    } else {
                        return '<span class="badge badge-danger">Inactive</span>';
                    }
                } },
                {
                    data: null,
                    name: 'actions',
                    render: function(data, type, row) {
                        let buttons = '';
                        if (canViewLedger) {
                            buttons += `<button class="btn btn-primary btn-xs btnLedger" title="Ledger" data-id="${row.id}"><i class="fa fa-table"></i></button> `;
                        }
                        if (canViewClient) {
                            buttons += `<button class="btn btn-primary btn-xs btnHistory" title="Loan History" data-id="${row.id}"><i class="fa fa-list"></i></button> `;
                        }
                        if (canEdit) {
                            buttons += `<button class="btn btn-primary btn-xs btnEdit" title="Edit" data-id="${row.id}"><i class="fa fa-pencil"></i></button> `;
                        }
                        if (canDelete) {
                            buttons += `<button class="btn btn-danger btn-xs" title="Delete" onclick="deleteData(${row.id})"><i class="fa fa-trash"></i></button>`;
                        }
                        return buttons;
                    }
                }
            ],
            columnDefs: [
                { className: "text-center", targets: [4,5] },
            ]
        });



        $("#tblClient").on("click", ".btnEdit", function(){
            let id = $(this).attr("data-id");
            window.location.href = `/client/${id}/edit`;
        })

        $("#tblClient").on("click", ".btnLedger", function(){
            let id = $(this).attr("data-id");
            window.location.href = `/client/${id}/ledger`;
        })


        $("#tblClient").on("click", ".btnHistory", function(){
            let id = $(this).attr("data-id");
            window.location.href = `/client/${id}/loan-history`;
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
                        showToast.success(response.message)
                        $('#tblClient').DataTable().ajax.reload();
                    }
                });

            }
        })
    }

    </script>

@endpush
