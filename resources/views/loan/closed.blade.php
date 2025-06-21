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
                    <h4>Loan | Closed</h4>
                </div>
                <div class="col-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"> <a href="{{url('')}}">
                            <svg class="stroke-icon">
                                <use href="{{url('assets/svg/icon-sprite.svg#stroke-home')}}"></use>
                            </svg></a>
                        </li>
                        <li class="breadcrumb-item">Loan</li>
                        <li class="breadcrumb-item active">Closed</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- button container -->
    <div class="card">
    <div class="card-body">
        <button class="btn btn-primary float-end" onclick="showModal('modalLoanProduct')"><i class = "fa fa-plus"></i> New Loan</button>
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
        const table = $('#tblLoan').DataTable({
            ordering: false,
            processing: true,
            serverSide: true,
            stateSave: true,
            searching: true,
            ajax: {
            url: "{{ route('loan.closed') }}",
            },
            columns: [
                { data: 'loan_date', name: 'loan_date' },
                { data: 'loan_no', name: 'loan_no' },
                { data: 'loan_product.name', name: 'loan_product' },
                { data: 'borrower.details.fullname', name: 'name' },
                { data: 'status', name: 'status', render: function(data, type, row) {
                        return '<span class="badge badge-primary">Closed</span>';
                } },
                {
                    data: null,
                    name: 'actions',
                    render: function(data, type, row) {
                        return `
                                <button class="btn btn-primary btn-xs btnEdit" data-id="${row.id}"><i class="fa fa-pencil"></i></button>
                                <button class="btn btn-danger btn-xs" onclick="deleteData(${row.id})"><i class="fa fa-trash"></i></button>
                            `;
                    }
                }
            ],
            columnDefs: [
                { className: "text-center", targets: [3,4] },
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
            getLoanType(id)
        })

        async function getLoanType(id) {
            try {
                let response = await fetch(`/loan-product/${id}`, {
                    method: "GET",
                });

                if (!response.ok) throw new Error("Network response was not ok");

                let data = await response.json();

                $("#modalLoanProduct [name='id']").val(data.id);
                $("#modalLoanProduct [name='name']").val(data.name);
                $("#modalLoanProduct [name='description']").val(data.description);
                $("#modalLoanProduct [name='loan_type_id']").val(data.loan_type_id);
                $("#modalLoanProduct [name='status']").val(data.status);
                showModal('modalLoanProduct')

            } catch (error) {
                console.error("Error fetching data:", error);
            }
        }
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
                    url: '/loan-product/' + id,
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
