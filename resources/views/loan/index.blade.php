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
                    <h4>Loan | Manage Loan</h4>
                </div>
                <div class="col-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"> <a href="{{url('')}}">
                            <svg class="stroke-icon">
                                <use href="{{url('assets/svg/icon-sprite.svg#stroke-home')}}"></use>
                            </svg></a>
                        </li>
                        <li class="breadcrumb-item">Loan</li>
                        <li class="breadcrumb-item active">Manage Loan</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- button container -->
    <div class="card">
    <div class="card-body">
        <a class="btn btn-primary float-end" href="{{route('loan.create')}}"><i class = "fa fa-plus"></i> New Loan</a>
    </div>
    </div>


    <!-- table container -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id = "tblLoan">
                    <thead class = "bg-default">
                        <tr>
                            <th class = "text-center" width = "120">Loan Date.</th>
                            <th class = "text-center" width = "120">Loan No.</th>
                            <th class = "text-leftr" width = "150">Loan Product</th>
                            <th class = "text-leftr" width = "150">Borrower</th>
                            <th class = "text-center" width = "180">Status</th>
                            <th class = "text-center" width = "150"><i class = "fa fa-cog"></i></th> <!-- New column for Edit/Delete buttons -->
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

@endsection

@push('js')
<script>
    $(document).ready(function () {
        const canView = {{ auth()->user()->can('view-loan') ? 1 : 0 }};
        const canEdit = {{ auth()->user()->can('edit-loan') ? 1 : 0 }};
        const canDelete = {{ auth()->user()->can('delete-loan') ? 1 : 0 }};

        const table = $('#tblLoan').DataTable({
            ordering: false,
            processing: true,
            serverSide: true,
            stateSave: true,
            searching: true,
            ajax: {
                url: "{{ route('loan.index') }}",
            },
            columns: [
                { data: 'loan_date', name: 'loan_date' },
                { data: 'loan_no', name: 'loan_no' },
                { data: 'loan_product.name', name: 'loan_product' },
                { data: 'borrower.details.fullname', name: 'name' },
                { data: 'status', name: 'status', render: function(data, type, row) {
                    if (data == 1) {
                        return '<span class="badge badge-success">Approved</span>';
                    } else if (data == 2) {
                        return '<span class="badge badge-danger">Declined</span>';
                    } else if (data == 3) {
                        return '<span class="badge badge-warning">Withdrawn</span>';
                    } else if (data == 5) {
                        return '<span class="badge badge-dark">Closed</span>';
                    } else {
                        return '<span class="badge badge-primary">Pending</span>';
                    }
                } },
                {
                    data: null,
                    name: 'actions',
                    render: function(data, type, row) {
                        let buttons = '';

                        if (canView) {
                            buttons += `<button class="btn btn-primary btn-xs btnView" data-id="${row.id}" title="View Loan"><i class="fa fa-eye"></i></button> `;
                            buttons += `<button class="btn btn-primary btn-xs btnPrintReceipt" data-id="${row.id}" title="Print Receipt"><i class="fa fa-print"></i></button> `;
                        }

                        if (row.status != 1 && row.status != 5) {
                            if (canEdit) {
                                buttons += `<button class="btn btn-primary btn-xs btnEdit" data-id="${row.id}"><i class="fa fa-pencil"></i></button> `;
                            }
                            if (canDelete) {
                                buttons += `<button class="btn btn-danger btn-xs" onclick="deleteData(${row.id})"><i class="fa fa-trash"></i></button>`;
                            }
                        }

                        return buttons;
                    }
                }
            ],
            columnDefs: [
                { className: "text-center", targets: [4,5] },
            ]
        });

        $("#modalLoanProduct .btnSubmit").click(function (e) {
            e.preventDefault();

            let form = $("#frmLoanProductModal")[0];

            if (!form.checkValidity()) {
                form.reportValidity();
            } else {

                //show loading screen
                $(".loader-wrapper").fadeIn("slow", function () {
                    $(this).show();
                });

                let formData = $("#frmLoanProductModal").serializeArray();

                var id = $("#modalLoanProduct [name='id']").val();
                if(id) {
                    _method = 'PUT';
                    _url = `{{route('loan-product.update', ':id')}}`.replace(':id',id);
                } else {
                    _method = 'POST';
                    _url = `{{route('loan-product.store')}}`;
                }


                $.ajax({
                    method: _method,
                    url: _url,
                    data: formData,
                    dataType: "json",
                    success: function (response) {
                        //hide loading screen
                        $(".loader-wrapper").fadeOut("slow", function () {
                            $(this).hide();
                        });
                        if(response.status == 'success'){
                            hideModal('modalLoanProduct');
                            showToast.success(response.message)
                            table.ajax.reload();
                        }else{
                            showToast.error(response.message)
                        }
                    },
                    error: function (response) {
                        //hide loading screen
                        $(".loader-wrapper").fadeOut("slow", function () {
                            $(this).hide();
                        });
                        showToast.error(response.responseJSON.message)
                    }
                });
            }
        });

        $("#tblLoan").on("click", ".btnEdit", function(){
            let id = $(this).attr("data-id");
            window.location.href = `/loan/${id}/edit`
        })

        $("#tblLoan").on("click", ".btnView", function(){
            let id = $(this).attr("data-id");
            window.location.href = `/loan/${id}`
        })

        $("#tblLoan").on("click", ".btnPrintReceipt", function(){
            let id = $(this).attr("data-id");
            window.open('/loan/' + id + '/print', '_blank'); 
        })

    });


    // function showModal(){
    //     $("#modalLoanProduct").modal("show");
    // }

    // function hideModal(){
    //     clearFormInputs();
    //     $("#modalLoanProduct").modal("hide");
    // }

    // function clearFormInputs() {
    //     $("#modalLoanProduct [name='id']").val("");
    //     $("#modalLoanProduct [name='name']").val("");
    //     $("#modalLoanProduct [name='description']").val("");
    //     $("#modalLoanProduct [name='status']").val("");
    // }

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
                    url: '/loan/' + id,
                    type: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        showToast.success(response.message)
                        $('#tblLoan').DataTable().ajax.reload();
                    }
                });

            }
        })
    }

</script>
@endpush
