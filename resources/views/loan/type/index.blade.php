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
                    <h4>Loan Type | Manage Loan Type</h4>
                </div>
                <div class="col-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"> <a href="{{url('')}}">
                            <svg class="stroke-icon">
                                <use href="../assets/svg/icon-sprite.svg#stroke-home"></use>
                            </svg></a>
                        </li>
                        <li class="breadcrumb-item">Loan Type</li>
                        <li class="breadcrumb-item active">Manage Loan Type</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- button container -->
    <div class="card">
    <div class="card-body">
        <button class="btn btn-primary float-end" onclick="showModal('modalLoanType')"><i class="fa fa-plus"></i> New Loan Type</button>
    </div>
    </div>


    <!-- table container -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="tblLoanType">
                    <thead class="bg-default">
                        <tr>
                            <th class="text-center" width="120">No.</th>
                            <th class="text-leftr" width="150">Loan Type</th>
                            <th>Description</th>
                            <th class="text-center" width="180">Status</th>
                            <th class="text-center" width="150"><i class="fa fa-cog"></i></th> <!-- New column for Edit/Delete buttons -->
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    <!-- job modal -->
    @include('loan.type.modal')

@endsection

@push('js')
<script>
    $(document).ready(function () {

        const table = $('#tblLoanType').DataTable({
            ordering: false,
            processing: true,
            serverSide: true,
            stateSave: true,
            searching: true,
            ajax: {
                url: "{{ route('loan-type.index') }}",
            },
            columns: [
                { data: 'id', name: 'id' },
                { data: 'name', name: 'name' },
                { data: 'description', name: 'description' },
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

        $("#modalLoanType .btnSubmit").click(function (e) {
            e.preventDefault();

            let form = $("#frmLoanTypeModal")[0];

            if (!form.checkValidity()) {
                form.reportValidity();
            } else {

                //show loading screen
                $(".loader-wrapper").fadeIn("slow", function () {
                    $(this).show();
                });

                let formData = $("#frmLoanTypeModal").serializeArray();

                var id = $("#modalLoanType [name='id']").val();
                if(id) {
                    _method = 'PUT';
                    _url = `{{route('loan-type.update', ':id')}}`.replace(':id',id);
                } else {
                    _method = 'POST';
                    _url = `{{route('loan-type.store')}}`;
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
                            hideModal('modalLoanType');
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

        $("#tblLoanType").on("click", ".btnEdit", function(){
            let id = $(this).attr("data-id");
            getLoanType(id)
        })

        async function getLoanType(id) {
            try {
                let response = await fetch(`/loan-type/${id}`, {
                    method: "GET",
                });

                if (!response.ok) throw new Error("Network response was not ok");

                let data = await response.json();

                $("#modalLoanType [name='id']").val(data.id);
                $("#modalLoanType [name='name']").val(data.name);
                $("#modalLoanType [name='description']").val(data.description);
                $("#modalLoanType [name='status']").val(data.status);
                showModal('modalLoanType')

            } catch (error) {
                console.error("Error fetching data:", error);
            }
        }
    });


    // function showModal(){
    //     $("#modalLoanType").modal("show");
    // }

    // function hideModal(){
    //     clearFormInputs();
    //     $("#modalLoanType").modal("hide");
    // }

    // function clearFormInputs() {
    //     $("#modalLoanType [name='id']").val("");
    //     $("#modalLoanType [name='name']").val("");
    //     $("#modalLoanType [name='description']").val("");
    //     $("#modalLoanType [name='status']").val("");
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
                    url: '/loan-type/' + id,
                    type: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        showToast.success(response.message)
                        $('#tblLoanType').DataTable().ajax.reload();
                    }
                });

            }
        })
    }

    </script>

@endpush
