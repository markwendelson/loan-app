@extends('layouts.app')

@section('title') Manage Notary Fee Schedule

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
                    <h4>Notary Fee Schedule | Manage Notary Fee Schedule</h4>
                </div>
                <div class="col-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"> <a href="{{url('')}}">
                            <svg class="stroke-icon">
                                <use href="{{url('assets/svg/icon-sprite.svg#stroke-home')}}"></use>
                            </svg></a>
                        </li>
                        <li class="breadcrumb-item">Notary Fee Schedule</li>
                        <li class="breadcrumb-item active">Manage Notary Fee Schedule</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- button container -->
    <div class="card">
        <div class="card-body">
            <button class="btn btn-primary float-end" onclick="showModal('modalNotaryFeeSchedule')"><i class="fa fa-plus"></i> New Notary Fee Schedule</button>
        </div>
    </div>


    <!-- table container -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="tblNotaryFeeSchedule">
                    <thead class="bg-default">
                        <tr>
                            <th class="text-center" width="120">No.</th>
                            <th class="text-leftr" width="150">Minimum Amount</th>
                            <th class="text-leftr" width="150">Maximum Amount</th>
                            <th class="text-leftr" width="150">Type</th>
                            <th class="text-leftr" width="150">Fee</th>
                            <th class="text-center" width="180">Status</th>
                            <th class="text-center" width="150"><i class="fa fa-cog"></i></th> <!-- New column for Edit/Delete buttons -->
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    <!-- job modal -->
    @include('notary-fee-schedule.modal')

@endsection

@push('js')
<script>
    $(document).ready(function () {
        const canEdit = {{ auth()->user()->can('edit-notary-fee') ? 1 : 0 }};
        const canDelete = {{ auth()->user()->can('delete-notary-fee') ? 1 : 0 }};

        const table = $('#tblNotaryFeeSchedule').DataTable({
            ordering: false,
            processing: true,
            serverSide: true,
            stateSave: true,
            searching: true,
            ajax: {
                url: "{{ route('notary-fee-schedule.index') }}",
            },
            columns: [
                { data: 'id', name: 'id' },
                { data: 'min_amount', name: 'min_amount', render : function(data) {
                    return Number(data).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
                } },
                { data: 'max_amount', name: 'max_amount', render:function(data){
                    return Number(data).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
                } },
                { data: 'type', name: 'type' },
                { data: 'fee', name: 'fee', render : function(data){
                    return Number(data).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
                } },
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
                        if (canEdit) {
                            buttons += `<button class="btn btn-primary btn-xs btnEdit" data-id="${row.id}"><i class="fa fa-pencil"></i></button> `;
                        }
                        if (canDelete) {
                            buttons += `<button class="btn btn-danger btn-xs" onclick="deleteData(${row.id})"><i class="fa fa-trash"></i></button>`;
                        }
                        return buttons;
                    }
                }
            ],
            columnDefs: [
                { className: "text-center", targets: [0,3,5,6] },
                { className: "text-end", targets: [1,2,4] },
            ]
        });

        $("#modalNotaryFeeSchedule .btnSubmit").click(function (e) {
            e.preventDefault();

            let form = $("#frmNotaryFeeScheduleModal")[0];

            if (!form.checkValidity()) {
                form.reportValidity();
            } else {

                //show loading screen
                $(".loader-wrapper").fadeIn("slow", function () {
                    $(this).show();
                });

                let formData = $("#frmNotaryFeeScheduleModal").serializeArray();

                var id = $("#modalNotaryFeeSchedule [name='id']").val();
                if(id) {
                    _method = 'PUT';
                    _url = `{{route('notary-fee-schedule.update', ':id')}}`.replace(':id',id);
                } else {
                    _method = 'POST';
                    _url = `{{route('notary-fee-schedule.store')}}`;
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
                            hideModal('modalNotaryFeeSchedule');
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

        $("#tblNotaryFeeSchedule").on("click", ".btnEdit", function(){
            let id = $(this).attr("data-id");
            getNotaryFeeSchedule(id)
        })

        async function getNotaryFeeSchedule(id) {
            try {
                let response = await fetch(`/notary-fee-schedule/${id}`, {
                    method: "GET",
                });

                if (!response.ok) throw new Error("Network response was not ok");

                let data = await response.json();

                $("#modalNotaryFeeSchedule [name='id']").val(data.id);
                $("#modalNotaryFeeSchedule [name='min_amount']").val(data.min_amount);
                $("#modalNotaryFeeSchedule [name='max_amount']").val(data.max_amount);
                $("#modalNotaryFeeSchedule [name='type']").val(data.type);
                $("#modalNotaryFeeSchedule [name='fee']").val(data.fee);
                $("#modalNotaryFeeSchedule [name='status']").val(data.status);
                showModal('modalNotaryFeeSchedule')

            } catch (error) {
                console.error("Error fetching data:", error);
            }
        }
    });


    // function showModal(){
    //     $("#modalNotaryFeeSchedule").modal("show");
    // }

    // function hideModal(){
    //     clearFormInputs();
    //     $("#modalNotaryFeeSchedule").modal("hide");
    // }

    // function clearFormInputs() {
    //     $("#modalNotaryFeeSchedule [name='id']").val("");
    //     $("#modalNotaryFeeSchedule [name='name']").val("");
    //     $("#modalNotaryFeeSchedule [name='description']").val("");
    //     $("#modalNotaryFeeSchedule [name='status']").val("");
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
                    url: '/notary-fee-schedule/' + id,
                    type: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        showToast.success(response.message)
                        $('#tblNotaryFeeSchedule').DataTable().ajax.reload();
                    }
                });

            }
        })
    }

    </script>

@endpush
