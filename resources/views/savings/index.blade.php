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
                                <use href="{{url('/assets/svg/icon-sprite.svg#stroke-home')}}"></use>
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
    {{-- <div class="card">
        <div class="card-body">
            <button class="btn btn-primary float-end" onclick="showModal('modalSavings')"><i class="fa fa-plus"></i> New Savings</button>
        </div>
    </div> --}}


    <!-- table container -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="tblSavings">
                    <thead class="bg-default">
                        <tr>
                            <th class="text-center" width="120">Client Id</th>
                            <th class="text-center" width="150">Name</th>
                            <th class="text-center" width="150">Balance</th>
                            <th class="text-center" width="180">Status</th>
                            <th class="text-center" width="150"><i class="fa fa-cog"></i></th> <!-- New column for Edit/Delete buttons -->
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
        const table=$('#tblSavings').DataTable({
            ordering: false,
            processing: true,
            serverSide: true,
            stateSave: true,
            searching: true,
            ajax: {
                url: "{{ route('savings.index') }}",
            },
            columns: [
                { data: 'client_code', name: 'client_code' },
                { data: 'fullname', name: 'fullname' },
                { data: 'balance', name: 'balance' },
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
                                <button class="btn btn-primary btn-xs btnTransactions" data-id="${row.id}"><i class="fa fa-table"></i></button>
                            `;
                    }
                }
            ],
            columnDefs: [
                { className: "text-center", targets: [3,4] },
                { className: "text-end", targets: [2] },
            ]
        });

        $("#modalSavings .btnSubmit").click(function (e) {
            e.preventDefault();

            let form=$("#frmSavingsModal")[0];

            if (!form.checkValidity()) {
                form.reportValidity();
            } else {

                //show loading screen
                $(".loader-wrapper").fadeIn("slow", function () {
                    $(this).show();
                });

                let formData=$("#frmSavingsModal").serializeArray();

                var id=$("#modalSavings [name='id']").val();
                if(id) {
                    _method='PUT';
                    _url=`{{route('user.update', ':id')}}`.replace(':id',id);
                } else {
                    _method='POST';
                    _url=`{{route('user.store')}}`;
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
                            hideModal('modalSavings');
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

        $("#tblSavings").on("click", ".btnTransactions", function(){
            let id=$(this).attr("data-id");
            window.location.href = `/savings/${id}/transactions`;
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
                    url: '/user/' + id,
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
