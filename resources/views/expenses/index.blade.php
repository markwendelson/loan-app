@extends('layouts.app')

@section('title') Expenses @endsection

@push('css')
    <link rel="stylesheet" type="text/css" href="{{ url('assets/css/vendors/dropzone.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://unpkg.com/viewerjs/dist/viewer.min.css">

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

        .dropdown-menu .dropdown-item {
            opacity: 0.95 !important;
        }

    </style>
@endpush

@section('content')

    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h4>Expenses</h4>
                </div>
                <div class="col-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"> <a href="{{url('')}}">
                            <svg class="stroke-icon">
                                <use href="{{url('assets/svg/icon-sprite.svg#stroke-home')}}"></use>
                            </svg></a>
                        </li>
                        <li class="breadcrumb-item active">Others</li>
                        <li class="breadcrumb-item">Expenses</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>


    @can('create-expense')
    <div class="card">
        <div class="card-body d-flex justify-content-end gap-2">
            <button type="button" id="btnGenerateExpenseReport" class="btn btn-warning"><i class="fa fa-bar-chart-o"></i> Generate Report</button>
            <button type="button" id="btnAddExpense" class="btn btn-primary"><i class="fa fa-plus"></i> New Expense</button>
        </div>
    </div>
    @endcan
    
    
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header ">
                <livewire:Expenses.FilterData />
            </div>
            <div class="card-body p-0">
                <livewire:Expenses.ExpenseData defer />
            </div>
        </div>
    </div>

    {{-- Include Modal --}}
    @include('expenses.modal')

    @include('expenses.modal.generate-report-option')

    <!-- edit modal -->
    <div class="modal fade" id="expenseEditModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog" style="max-width: 90% !important;">
            <!-- content here -->
        </div> 
    </div>

@endsection

@push('js')
    <script src="{{ url('assets/js/dropzone/dropzone.js') }}"></script>
    <script src="{{ url('assets/js/dropzone/dropzone-script.js') }}"></script>

    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://unpkg.com/viewerjs/dist/viewer.min.js"></script>

    <script>
        var vendors = @json($vendorsData);

        $(document).ready(function() {
            initializeDropzone();
        })

        $("#btnAddExpense").click(function() {
            initializeSelect2();
            $("#expenseModal").modal("show");
        })

        $("#expenseModal .btnCancel").click(function(e) {
            e.preventDefault();

            $("#expenseModal").modal("hide");

            resetModalForm();
        })

        //add line handles click function
        $(document).on("click", "#expenseModal .btnAddline, #expenseEditModal .btnAddline", function(e) {
            e.preventDefault();

            let targetTable = $(this).data("addline-target-table") || "";
            let parentModal = $(this).data("parent-modal")

            addLine(targetTable);
            initializeSelect2(parentModal);
        });

        //handle lost focus function for qty and 
        $(document).on("focusout", "#expenseModal #expenseItems tbody tr input[name='sub_amount[]'], #expenseEditModal #expenseItems tbody tr input[name='sub_amount[]'],#expenseEditModal #expenseItems tbody tr input[name='old_sub_amount[]']", function(e) {
            e.preventDefault();

            let modal = $(this).closest(".modal");
        
            computeTotal(modal);
        });

        //handle modal refresh vendor
        $(document).on("click", "#expenseModal .btnRefreshVendor, #expenseEditModal .btnRefreshVendor", function(e) {
            e.preventDefault();

            //show loading form
            $(".loader-wrapper").fadeIn("slow");

            $.ajax({
                type: "POST",
                url: "{{ route('vendor.get-vendors-list') }}",
                data: {

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

                    if(typeof response.status !=='undefined' && response.status =='success') {

                        vendors = response.data;
                        
                        $("#expenseModal #expenseItems tbody tr").each(function() {
                            let vendorSelect = $(this).find("select[name='vendor_id[]']");
                            let selectedVendor = vendorSelect.val();

                            vendorSelect.empty();

                            //append default option
                            vendorSelect.append('<option value="" selected hidden disabled>-- Select --</option>');

                            response.data.forEach(val =>{
                                vendorSelect.append(`<option value="${val.id}"${selectedVendor == val.id ? ' selected' : ''}>${val.name}</option>`);
                            });
                        });

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
                            dangerMode: true,
                        });
                    }
                }
            });
        })

        //handle modal submit click function
        $(document).on("click", "#expenseModal .btnSubmit, #expenseEditModal .btnSubmit", function(e) {
            e.preventDefault();
            let submitUrl = $(this).data("url");

            let modal = $(this).closest(".modal-content");


            let formElement =  $(modal).find("form")[0];
            let formData = new FormData(formElement);

            // Add Dropzone files manually
            Dropzone.forElement($(modal).find("#myDropzone")[0]).getAcceptedFiles().forEach((file, index) => {
                formData.append("attachments[]", file, file.name);
            });

            if (!$(modal).find("#expenseForm")[0].checkValidity()) {
                swal({
                    title: 'Error',
                    text: 'Please fill all required fields',
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
            }else{
                //show loading form
                $(".loader-wrapper").fadeIn("slow");

                $.ajax({
                    type: 'POST',
                    url: submitUrl,
                    data: formData,
                    dataType: "json",
                    processData: false,
                    contentType: false,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        //hide loading form
                        $(".loader-wrapper").fadeOut("slow", function () {
                            $(this).hide();
                        });

                        if(response.status !== 'undifined' && response.status == 'success') {
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
                                    Livewire.dispatch('refresh-expense-data');

                                    $("#expenseEditModal").modal("hide");
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
                    },
                    error: function(xhr, status, error) {
                        swal({
                            title: 'Error',
                            text: error,
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
                });
            }

        })

        //items expense remove button click function
        $(document).on("click", "#expenseModal #expenseItems tbody tr .btnRemove, #expenseEditModal #expenseItems tbody tr .btnRemove", function(e) {
            e.preventDefault();
            let modal = $(this).closest(".modal");

            let itemId = $(this).closest("tr").data("id");
            let removedItemsIdsInput = $("#expenseEditModal input[name='deleted_items']");
            let removedItemsIds = removedItemsIdsInput.val() ? removedItemsIdsInput.val().split(",") : [];
            removedItemsIds.push(itemId);
            removedItemsIdsInput.val(removedItemsIds.join(","));

            $(this).closest("tr").remove();

            computeTotal(modal);
        })

        //handle edit button click function
        $(document).on("click", "#expenseTable tbody tr .btnEdit", function(e){
            e.preventDefault();

            //show loading form
            $(".loader-wrapper").fadeIn("slow");

            $.ajax({
                type: "GET",
                url: "{{ route('expense.edit-modal') }}",
                data: {
                    expense_id : $(this).closest("tr").data("id")
                },
                dataType: "json",
                success: function (response) {
                    //hide loading form
                    $(".loader-wrapper").fadeOut("slow", function () {
                        $(this).hide();
                    });

                    if(typeof response.status !== 'undefined' && response.status == 'success') {
                        $("#expenseEditModal .modal-dialog").html(response.html);
                        initializeViewerZoom();
                        initializeDropzone("#expenseEditModal .myDropzone");
                        initializeSelect2("#expenseEditModal");

                        $("#expenseEditModal").modal("show");
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

        //handle edit modal cancel button click function
        $("#expenseEditModal").on("click", ".btnCancel", function(e) {
            e.preventDefault();

            $("#expenseEditModal").modal("hide");
        });

        //handle remove button for attachments in edit modal
        $("#expenseEditModal").on("click", ".btnRemoveAttachments", function(e) {
            e.preventDefault();

            let attachmentId = $(this).data("id");
            let removedAttachmentIdsInput = $(`#expenseEditModal input[name="deleted_attachments"]`).val();
            let removedAttachmentIds = removedAttachmentIdsInput ? removedAttachmentIdsInput.split(",") : [];
            removedAttachmentIds.push(attachmentId);
            $(`#expenseEditModal input[name="deleted_attachments"]`).val(removedAttachmentIds.join(","));

            $(this).closest(".attachment-image").closest("a").remove();
        });

        //handle view button click function
        $(document).on("click", "#expenseTable tbody tr .btnViewExpense", function(e) {
            e.preventDefault();

            $(".loader-wrapper").fadeIn("slow");

            $.ajax({
                type: "GET",
                url: "{{ route('expense.view-expense-data') }}",
                data: {
                    expense_id : $(this).closest("tr").data("id")
                },
                dataType: "json",
                success: function (response) {
                    //hide loading form
                    $(".loader-wrapper").fadeOut("slow", function () {
                        $(this).hide();
                    });

                    if(typeof response.status  !== 'undefined' && response.status == 'success') {
                        $("#expenseEditModal .modal-dialog").html(response.html);

                        initializeViewerZoom();
                        $("#expenseEditModal").modal("show");
                    }
                }
            });
        });

        //handle delete expense button click function
        $(document).on("click", "#expenseTable tbody tr .btnDelete", function(e) {
            e.preventDefault(); 

            swal({
                title: 'Delete Expense?',
                text: 'Are you sure you want to delete this expense?',
                icon: 'warning',
                buttons: {
                    confirm: {
                        text: 'Yes, Delete',
                        visible: true,
                        closeModal: true
                    },
                    cancel: {
                        text: 'Cancel',
                        visible: true,
                        closeModal: true
                    },
                },
                dangerMode: true,
            }).then((willDelete) => {
                if (willDelete) {

                    $(".loader-wrapper").fadeIn("slow");

                    $.ajax({
                        type: "DELETE",
                        url: "{{ route('expense.delete') }}",
                        data: {
                            expense_id : $(this).closest("tr").data("id")
                        },
                        dataType: "json",
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function (response) {

                            $(".loader-wrapper").fadeOut("slow", function () {
                                $(this).hide();
                            });

                            if (typeof response.status !== 'undefined' && response.status === 'success') {
                                swal({
                                    title: 'Deleted!',
                                    text: 'Expense has been deleted successfully.',
                                    icon: 'success',
                                    button: 'OK'
                                }).then(function () {
                                    location.reload();
                                });
                            }
                        }
                    });
                }
            });
        });

        //handle generate expense report button
        $(document).on("click", "#btnGenerateExpenseReport", function(e) {
            e.preventDefault();

            $("#modalGenerateReportOptions").modal("show");
        });

        //handle modal generate options cancel button
        $("#modalGenerateReportOptions .btnCancel").on("click", function(e) {
            e.preventDefault();

            $("#modalGenerateReportOptions").modal("hide");
        })

        //handles modal generates report options select
        $(document).on("change","#modalGenerateReportOptions select[name='report_type']", function(e) {
            e.preventDefault();
            let reportType = $(this).val();

            // hide all report forms first
            $(".report-forms > div").removeClass("d-block").addClass("d-none");

            // show selected report type
            $(".report-forms ." + reportType)
                .removeClass("d-none")
                .addClass("d-block");
        });

        //handleles modal submit button click function
        $(document).on("click", "#modalGenerateReportOptions .btnSubmit", function(e) {
            e.preventDefault();

            let formData = $("#modalGenerateReportOptions form").serialize();

            let url = "{{ route('expense.generate-expense-report') }}";

            window.open(url + "?" + formData, "_blank");
        });

        function initializeDropzone(target) {
            Dropzone.autoDiscover = false;

            let defaultDropzoneElement = '#expenseModal .myDropzone';

            // Destroy existing Dropzone instance if exists
            if (Dropzone.instances.length > 0) {
                Dropzone.instances.forEach(function(dz) {
                    dz.destroy();
                });
            }

            var myDropzone = new Dropzone(target || defaultDropzoneElement, {
                url: "/upload",
                paramName: "attachments",
                maxFilesize: 2,
                acceptedFiles: "image/jpeg,image/png",
                addRemoveLinks: true,
                autoProcessQueue: false,
                uploadMultiple: true,
                parallelUploads: 10,
                maxFiles: 10,
            });
        }

        function computeTotal(e) {
            // let defaultTable = "#expenseModal #expenseItems tbody"
            let total = 0;
            $(e).find("#expenseItems tbody tr").each(function() {
                total += parseFloat($(this).find("input[name='sub_amount[]']").val() || $(this).find("input[name='old_sub_amount[]']").val());
            });

            if(isNaN(total)) {
                total = 0.00;
            }

            //pass values to hidden inputs
            $(e).find("input[name='total_amount']").val(total);
            $(e).find(".total-amount").html(formatNumber(total));
        }

        function formatNumber(number) {
            let formattedNumber = new Intl.NumberFormat('en-US', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }).format(number);

            return formattedNumber;

        }

        function resetModalForm() {

            $("#expenseModal #expenseForm input").val("");
            $("#expenseModal #expenseForm select").val("");
            $("#expenseModal #expenseForm textarea").val("");

            //reset label values
            $("#expenseModal .total-amount").html("0.00");

            //set initial tax rate to 12

            //clear add line from modal form
            $("#expenseModal #expenseItems tbody").html("");
            addLine();

            initializeDropzone();
        }

        function addLine(target) {

            let options = '';
            let defaultTable = '#expenseModal #expenseItems tbody';

            vendors.forEach(val =>{
                options += `<option value="${val.id}">${val.name}</option>`;
            });

            let trData = `
                <tr>
                    <td>
                        <select class="form-select selectVendor" name="vendor_id[]">
                            <option value="" selected hidden disabled>-- Select --</option>
                            ${options}
                        </select>
                    </td>
                    <td>
                        <input type="text"
                            name="description[]"
                            class="form-control"
                        >
                    </td>
                    <td>
                        <input type="text"
                            class="form-control"
                            name="ref_no[]"
                            placeholder="E.g. Invoice #, Receipt #, etc."
                        >
                    </td>
                    <td>
                        <input type="number"
                            class="form-control text-end"
                            name="sub_amount[]"
                            data-target-table="${target || defaultTable}"
                            placeholder="0.00"
                        >
                    </td>
                    <td class="text-center">
                        <a href="#" class="text-danger btnRemove"><i class="fa fa-trash  fs-5"></i></a>
                    </td>
                </tr>
            `;
            
            $(target || defaultTable).append(trData);
        }

        function initializeSelect2(parentModal) {
            $(".selectVendor").select2({
                placeholder : '-- Select --',
                dropdownParent: parentModal || '#expenseModal'
            });
        }

        function initializeViewerZoom() {
            var $attachmentImgs = $('.image-container, .attachments-container');
            if ($attachmentImgs.length) {
                $attachmentImgs.each(function () {
                    new Viewer(this, {
                    toolbar: false,
                    title: false,
                    viewed: function() {
                        // this.viewer.zoomTo(1);
                    }
                    });
                });
            }
        }
    </script>
@endpush