@extends('layouts.app')

@section('title') Manage Savings @endsection

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

    <div class="card">
        <div class="card-body d-flex justify-content-end">
            <button type="button" class="btn btn-primary btnNewTransactions"><i class="fa fa-plus"></i> Multiple Entries</button>
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
                            <th class="text-center" width="30"></th>
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

    <!-- modal multiple entries -->
    <div class="modal fade" id="modalMultipleEntries" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <!-- content here -->
        </div>
    </div>

@endsection

@push('js')
<script>
    $(document).ready(function () {

        //clear local storage
        localStorage.removeItem("table_selections");

        //new multiple transaction click function
        $(".btnNewTransactions").click(function(e) {
            e.preventDefault();
            
            //get selected accounts
            let selectedAccounts = JSON.parse(localStorage.getItem("table_selections") || "[]"); 

            if(selectedAccounts.length <= 0) {
                swal({
                    title: 'Error',
                    text: "Please select atleast one record!",
                    icon: 'warning',
                    buttons: {
                        confirm: {
                            text: "OK",
                            value: true,
                            visible: true,
                            className: "btn btn-primary",
                            closeModal: true
                        }
                    },
                    dangerMode: true,
                });

                return;
            }

            $(".loader-wrapper").fadeIn("slow");

            $.ajax({
                type: "POST",
                url: "{{ route('savings.get-multiple-entries-modal') }}",
                data: {
                    client_id : selectedAccounts
                },
                dataType: "json",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (response) {
                    //hide loading form
                    $(".loader-wrapper").fadeOut("slow", function () {
                        $(this).hide();
                    });

                    if(typeof response != 'undefined' && response.status == 'success') {
                        if(typeof response.html != 'undefined' && response.html) {
                            $("#modalMultipleEntries .modal-dialog").html(response.html);
                            $("#modalMultipleEntries").modal("show");
                        }
                    }
                }
            });
        })

        //handle modal submit click function
        $("#modalMultipleEntries").on("click", ".btnSubmit", function(e) {
            e.preventDefault();


            alert("hello world");
        });

        //modal cancel button click function
        $("#modalMultipleEntries").on("click", ".btnCancel", function(e) {
            e.preventDefault();

            $("#modalMultipleEntries").modal("hide");
        })

        //handle td check holder click function
        $("#tblSavings").on("click", ".tdCheck", function(e) {
            let chckbox = $(this).find(".chckSelection");

            let isChecked = $(chckbox).prop("checked");
            $(chckbox).prop("checked", !isChecked);
            $(chckbox).trigger("change");
        });

        //handle table checkboxes click function
        $("#tblSavings").on("change", "tr .chckSelection", function(e) {
            e.stopPropagation();

            let selectedItems = JSON.parse(localStorage.getItem("table_selections")) || [];

            let value = $(this).val();

            if ($(this).prop("checked")) {
                if (!selectedItems.includes(value)) {
                    selectedItems.push(value);
                }
            } else {
                selectedItems = selectedItems.filter(item => item !== value);
            }

            localStorage.setItem("table_selections", JSON.stringify(selectedItems));
        });

        //transaction type buttons handle click function
        $("#modalMultipleEntries").on("change", ".btn-check", function(e) {
            e.preventDefault();

            let targetElement = $(this).data("bs-target");
            
            $(targetElement).closest(".tab-content").find('.tab-pane').removeClass("show active");

            $(targetElement).addClass("show active");
        })

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
                {
                    data: null,
                    name: 'checkbox',
                    render : function(data, type, row) {
                        return `
                            <div class="form-check checkbox checkbox-primary mb-0">
                                <input class="form-check-input chckAll chckSelection" id="chck${row.id}" type="checkbox" value="${row.id}">
                                <label class="form-check-label mb-1" for="chck${row.id}"></label>
                            </div>
                        `;
                    }
                },
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
                { 
                    className: "text-center mx-0 tdCheck",
                    targets: [0],
                    width: "20"
                    
                },
                { 
                    className: "text-center mx-0",
                    targets: [4,5],
                    width: "20"
                    
                },

                { className: "text-center", targets: [0,4,5] },
                { className: "text-end", targets: [3] },
            ]
        });

        //handle additional table pagination function
        $('#tblSavings').on('draw.dt', function() {
            loadSelectedRows();
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

    function loadSelectedRows() {
        //get all items
        let selectedItems = JSON.parse(localStorage.getItem("table_selections")) || [];

        $("#tblSavings tbody tr").each(function() {
            let chckbox = $(this).find(".chckSelection");

            if (selectedItems.includes($(chckbox).val())) {
                $(chckbox).prop("checked", true);
            }
        });
    }

</script>
@endpush
