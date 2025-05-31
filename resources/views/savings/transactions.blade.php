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
                    <h4>Savings | Manage Savings</h4>
                </div>
                <div class="col-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"> <a href="{{url('')}}">
                            <svg class="stroke-icon">
                                <use href="../assets/svg/icon-sprite.svg#stroke-home"></use>
                            </svg></a>
                        </li>
                        <li class="breadcrumb-item">Savings</li>
                        <li class="breadcrumb-item active">Manage Savings</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- button container -->
    <div class="card">
        <div class="card-body">
            <div class="d-flex justify-content-between">
                <h2>Balance: {{$client->balance}}</h2>
                <button class="btn btn-primary float-end" onclick="showModal('modalTransaction')"><i class="fa fa-plus"></i> New Transaction</button>
            </div>
        </div>
    </div>


    <!-- table container -->
    <div class="card">
        <div class="card-body">
            <input type="hidden" name="client_id" id="client_id" value="{{$client->id}}"/>
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="tblSavings">
                    <thead class="bg-default">
                        <tr>
                            <th class="text-center" width="120">Date</th>
                            <th class="text-center" width="150">Description</th>
                            <th class="text-center" width="150">Amount</th>
                            <th class="text-center" width="150">Notes</th>
                            <th class="text-center" width="150">Transaction Type</th>
                            <th class="text-center" width="150">Classifications</th>
                            <th class="text-center" width="150"><i class="fa fa-cog"></i></th> <!-- New column for Edit/Delete buttons -->
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>


    @include('savings.modal')

@endsection

@push('js')
<script>
    $(document).ready(function () {

        var client_id = $("#client_id").val();

        const table=$('#tblSavings').DataTable({
            ordering: false,
            processing: true,
            serverSide: true,
            stateSave: true,
            searching: true,
            ajax: {
                url: `{{ route('savings.transactions',':client_id') }}`.replace(':client_id',client_id)

            },
            columns: [
                { data: 'transaction_date', name: 'transaction_date' },
                { data: 'description', name: 'description' },
                { data: 'amount', name: 'amount' },
                { data: 'notes', name: 'notes' },
                {
                    data: 'transaction_type',
                    name: 'transaction_type',
                    render: function (data, type, row) {
                        if (data == 'deposit') {
                            return '<span class="badge badge-success">deposit</span>';
                        } else if (data == 'withdraw') {
                            return '<span class="badge badge-danger">withdraw</span>';
                        }
                    }
                },
                { data: 'classifications', name: 'classifications' },
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
                { className: "text-center", targets: [6] },
                { className: "text-end", targets: [2] },
            ]
        });

        $("#modalTransaction .btnSubmit").click(function (e) {
            e.preventDefault();

            let form=$("#frmTransactionModal")[0];

            if (!form.checkValidity()) {
                form.reportValidity();
            } else {

                //show loading screen
                $(".loader-wrapper").fadeIn("slow", function () {
                    $(this).show();
                });

                let formData=$("#frmTransactionModal").serializeArray();
                formData.push({ name: 'client_id', value: client_id });


                var id=$("#modalTransaction [name='id']").val();
                if(id) {
                    _method='PUT';
                    _url=`{{route('savings.update', ':id')}}`.replace(':id',id);
                } else {
                    _method='POST';
                    _url=`{{route('savings.store')}}`;
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
                            hideModal('modalTransaction');
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

        table.on("click", ".btnEdit", function(){
            let id = $(this).attr("data-id");
            getTransaction(id)
        })

        async function getTransaction(id) {
            try {
                let response = await fetch(`/savings/${id}`, {
                    method: "GET",
                });

                if (!response.ok) throw new Error("Network response was not ok");

                let data = await response.json();

                $("#modalTransaction [name='id']").val(data.id);
                $("#modalTransaction [name='transaction_date']").val(data.transaction_date);
                $("#modalTransaction [name='transaction_type']").val(data.transaction_type);
                $("#modalTransaction [name='amount']").val(data.amount);
                $("#modalTransaction [name='description']").val(data.description);
                $("#modalTransaction [name='notes']").val(data.notes);
                $("#modalTransaction [name='classifications']").val(data.classifications);
                showModal('modalTransaction')

            } catch (error) {
                console.error("Error fetching data:", error);
            }
        }

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
                    url: '/savings/' + id,
                    type: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        showToast.success(response.message)
                        $('#tblSavings').DataTable().ajax.reload();
                    }
                });

            }
        })
    }

</script>
@endpush
