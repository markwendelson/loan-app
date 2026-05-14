@extends('layouts.app')

@section('title') Manage Savings @endsection

@push('css')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .bg-default {
        background-color: #1f807f;
    }
    .bg-default th {
        color: white; /* Set white text for <th> elements */
        border: 1px solid white;
    }

    .select2-selection__arrow {
        display: none !important; /* Hide the dropdown arrow */
    }
    
    .select2-container--default .select2-selection--single {
        height: 38px !important;
        line-height: 36px !important;  /* Slightly smaller line-height to compensate for borders */
        padding-top: 5px !important;
        border-color: #dee2e6 !important;
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

    <!-- table container -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="tblSavings">
                    <thead class="bg-default">
                        <tr>
                            <th width="120">Account No</th>
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
    @include('savings.modal.multiple-entries');

@endsection

@push('js')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    $(document).ready(function () {

        //intialize select
        initializeSelect2();

        var clientOptions = `{!! $clientOptions !!}`;
        var classificationOptions = `{!! $classificationOptions !!}`

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
                { 
                    className: "text-center mx-0",
                    targets: [3,4],
                    width: "20"
                    
                },

                { className: "text-center", targets: [3,4] },
                { className: "text-end", targets: [2] },
            ]
        });

        //new multiple transaction click function
        $(".btnNewTransactions").click(function(e) {
            e.preventDefault();

            $("#modalMultipleEntries").modal("show");
        });

        //modal cancel button click function
        $("#modalMultipleEntries").on("click", ".btnCancel", function(e) {
            e.preventDefault();

            //empty first table
            $("#modalMultipleEntries table tbody").html("");
            addRow();

            $("#modalMultipleEntries").modal("hide");
        })

        //handle modal remove button
        $("#modalMultipleEntries").on("click", ".btnRemove", function(e) {
            e.preventDefault();

            $(this).closest("tr").remove();
        })

        //handle modal more inputs button
        $("#modalMultipleEntries").on("click", ".btnMoreInputs", function(e) {
            e.preventDefault();

            addRow();
        });

        //handle client select change function
        $("#modalMultipleEntries").on("change", "table tbody .selectClient", function(e) {
            e.preventDefault();

            let clientId = $(this).val();
            let rowIndex = $(this).closest("tr").index();

            //update naming
            $(this).attr("name", `${rowIndex}[client_id]`);
            $(this).closest("tr").find(".selectTransactionType").attr("name", `${rowIndex}[transaction_type]`);
            $(this).closest("tr").find(".inputDate").attr("name", `${rowIndex}[date]`);
            $(this).closest("tr").find(".selectClassifications").attr("name", `${rowIndex}[classifications]`);
            $(this).closest("tr").find(".inputTransactionCode").attr("name", `${rowIndex}[transaction_code]`);
            $(this).closest("tr").find(".inputAtmBalance").attr("name", `${rowIndex}[atm_balance]`);
            $(this).closest("tr").find(".inputAmountWithdrawn").attr("name", `${rowIndex}[amount_withdrawn]`);
            $(this).closest("tr").find(".inputDescription").attr("name", `${rowIndex}[description]`);
        });

        //handle transaction type change function
        $("#modalMultipleEntries").on("change", "table tbody .selectTransactionType", function(e) {
            e.preventDefault();

            $(this).closest("tr").find(".inputAtmBalance").val("");
            if ($(this).val() != 'atm_withdrawal') {
                $(this).closest("tr").find(".inputAtmBalance").prop("disabled", true);
            } else {
                $(this).closest("tr").find(".inputAtmBalance").prop("disabled", false);
            }
        });

        //handle multiple entries modal submit function
        $("#modalMultipleEntries").on("click", ".btnSubmit", function(e) {
            e.preventDefault();

            let formData = $("#modalMultipleEntries #frmMultipleSavings").serializeArray();

            //show loading modal
            $(".loader-wrapper").fadeIn("slow");

            $.ajax({
                type: "POST",
                url: "{{ route('savings.save-multiple-entries') }}",
                data: formData,
                dataType: "json",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (response) {
                    //hide loading form
                    $(".loader-wrapper").fadeOut("slow", function () {
                        $(this).hide();
                    });

                    if(typeof response.status !== 'undefined' && response.status == 'success') {
                        swal({
                            title: 'Success!',
                            text: response.message,
                            icon: 'success',
                            buttons: {
                                confirm: {
                                    text: "OK",
                                    value: true,
                                    visible: true,
                                    className: "btn btn-primary",
                                    closeModal: true
                                }
                            },
                        }).then((willClose) => {
                            if (willClose) {
                                //reload data table
                                table.ajax.reload();

                                $("#modalMultipleEntries table tbody").html("");
                                addRow();
                                $("#modalMultipleEntries").modal("hide");
                            }
                        });
                    }else{
                        swal({
                            title: 'Error',
                            text: response.message,
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
                    }
                }
            });
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

        });

        function addRow() {
            let trData = `
                <tr>
                    <td>
                        <select class="form-select selectClient">
                            <option value="" selected hidden disabled>-- Client --</option>
                            ${clientOptions}
                        </select>
                    </td>
                    <td>
                        <select class="form-select selectTransactionType">
                            <option value="" selected hidden disabled>-- Transaction Type --</option>
                            <option value="atm_withdrawal">ATM Withdrawal</option>
                            <option value="bank_transfer">Bank Transfer </option>
                            <option value="palawan">Palawan</option>
                            <option value="cash">Cash</option>
                        </select>
                    </td>
                    <td>
                        <input type="date"
                            class="form-control inputDate"
                        >
                    </td>
                    <td>
                        <select class="form-select selectClassifications">
                            <option value="" selected hidden disabled>-- Classification --</option>
                            ${classificationOptions}
                        </select>
                    </td>
                    <td>
                        <input type="text"
                            class="form-control inputTransactionCode"
                            placeholder="ex. TXN123456789"
                        >
                    </td>
                    <td>
                        <input type="number"
                            class="form-control text-end inputAtmBalance"
                            placeholder="0.00"
                        >
                    </td>
                    <td>
                        <input type="number"
                            class="form-control text-end inputAmountWithdrawn"
                            placeholder="0.00"
                        >
                    </td>
                    <td>
                        <input type="text"
                            class="form-control inputDescription"
                            placeholder="ex. hello 123"
                        >
                    </td>
                    <td class ="text-center">
                        <a href="#" class="btnRemove"><i class="fa fa-trash text-danger fs-6"></i></a>
                    </td>
                </tr>
            `;

            $("#modalMultipleEntries #tblMultipleSavings tbody").append(trData);
            initializeSelect2();
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

    function initializeSelect2() {
        $(".selectClient").select2({
            placeholder : '-- Client --',
            dropdownParent: '#modalMultipleEntries'
        });
    }

    

</script>
@endpush
