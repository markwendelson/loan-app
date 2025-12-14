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
                    <h4>Loan | Awaiting Disbursement</h4>
                </div>
                <div class="col-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"> <a href="{{url('')}}">
                            <svg class="stroke-icon">
                                <use href="{{url('assets/svg/icon-sprite.svg#stroke-home')}}"></use>
                            </svg></a>
                        </li>
                        <li class="breadcrumb-item">Loan</li>
                        <li class="breadcrumb-item active">Awaiting Disbursement</li>
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

    @include('loan.modal-disbursement')

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
                url: "{{ route('loan.awaiting-disbursement') }}",
            },
            columns: [
                { data: 'loan_date', name: 'loan_date' },
                { data: 'loan_no', name: 'loan_no' },
                { data: 'loan_product.name', name: 'loan_product' },
                { data: 'borrower.details.fullname', name: 'name' },
                { data: 'status', name: 'status', render: function(data, type, row) {
                        return '<span class="badge badge-success">Approved</span>';
                } },
                {
                    data: null,
                    name: 'actions',
                    render: function(data, type, row) {
                        // Use row.net_proceeds which is now available from the backend
                        let amount = row.net_proceeds ? parseFloat(row.net_proceeds).toFixed(2) : '0.00';
                        return `
                                <button class="btn btn-primary btn-xs btn-cash-out" data-id="${row.id}" data-amount="${amount}"><i class="fa fa-money"></i></button>
                            `;
                    }
                },
                { data: 'net_proceeds', name: 'net_proceeds', visible: false },
            ],
            columnDefs: [
                { className: "text-center", targets: [4,5] },
            ]
        });

        $("#modalDisbursement .btnSubmit").click(function (e) {
            e.preventDefault();

            let form = $("#frmDisbursementModal")[0];

            if (!form.checkValidity()) {
                form.reportValidity();
            } else {

                //show loading screen
                $(".loader-wrapper").fadeIn("slow", function () {
                    $(this).show();
                });

                let formData = $("#frmDisbursementModal").serializeArray();

                var id = $("#modalDisbursement [name='id']").val();
                var _url = `{{route('loan.disburse',':id')}}`.replace(':id',id);

                $.ajax({
                    method: 'POST',
                    url: _url,
                    data: formData,
                    dataType: "json",
                    success: function (response) {
                        //hide loading screen
                        $(".loader-wrapper").fadeOut("slow", function () {
                            $(this).hide();
                        });
                        if(response.status == 'success'){
                            hideModal('modalDisbursement');
                            showToast.success(response.message)
                            table.ajax.reload();
                            
                            // Trigger print preview (open loan details/voucher in new tab)
                            // Assuming /loan/{id} is the printable view or voucher
                            window.open('/loan/' + id + '/print', '_blank'); 
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

        $("#tblLoan").on("click", ".btn-cash-out", function(){
            let id = $(this).attr("data-id");
            let amount = $(this).attr("data-amount"); // get amount
            
            $("#modalDisbursement [name='id']").val(id);
            $("#modalDisbursement [name='amount']").val(amount); // set amount
            
            showModal('modalDisbursement')
        })

        function cashOutLoan(id) {
            swal({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                buttons: true,
                dangerMode: true,
            }).then((result) => {
                if (result) {

                    $.ajax({
                        url: '/loan/disburse/' + id,
                        type: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            showToast.success(response.message)
                            table.ajax.reload()
                        }
                    });

                }
            })
        }
    });


    // function showModal(){
    //     $("#modalDisbursement").modal("show");
    // }

    // function hideModal(){
    //     clearFormInputs();
    //     $("#modalDisbursement").modal("hide");
    // }

    // function clearFormInputs() {
    //     $("#modalDisbursement [name='id']").val("");
    //     $("#modalDisbursement [name='name']").val("");
    //     $("#modalDisbursement [name='description']").val("");
    //     $("#modalDisbursement [name='status']").val("");
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
