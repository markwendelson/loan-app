@extends('layouts.app')

@section('title') Expenses @endsection

@push('css')
    <link rel="stylesheet" type="text/css" href="{{ url('assets/css/vendors/dropzone.css') }}">
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


    <div class="card">
        <div class="card-body d-flex justify-content-end">
            <button type="button" id="btnAddExpense" class="btn btn-primary"><i class="fa fa-plus"></i> New Expense</button>
        </div>
    </div>

    
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header ">
                <form id="frmFilter" method="GET">
                    <div class="d-flex justify-content-end gap-2">
                        <div class="d-flex align-items-center gap-2">
                            <select class="form-select"
                                name="branch_id"
                            >
                                <option value="">-- All Branch --</option>
                                @if(isset($branches) && $branches)
                                    @foreach($branches as $key => $val)
                                        <option value="{{ $val->id }}">{{ $val->name }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>

                        <div>
                            @if($user->hasRole(['admin', 'super_admin']) && $usersData->isNotEmpty())
                            <select class="form-control" name="author">
                                <option value="">-- All Author --</option>
                                @foreach($usersData as $key => $val)
                                    <option value="{{ $val->id }}" {{ isset($filterData['author']) && $filterData['author'] == $val->id ? 'selected' : ''; }}>{{ $val->name }}</option>
                                @endforeach
                            </select>
                            @endif
                        </div>
                        <button type="submit" class="btn btn-primary"><i class="fa fa-search"></i></button>

                    </div>

                        
                </form>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover" id="expenseTable">
                        <thead>
                            <tr>
                                <th class="align-middle">Date</th>
                                <th class="align-middle">Branch</th>
                                <th class="align-middle text-center" width="150">Payment Method</th>
                                <th class="align-middle">Description</th>
                                <th class="align-middle text-center">Amount</th>
                                <th class="align-middle">Created By</th>
                                <th class="text-center align-middle"><i class="fa fa-cog"></i></th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(isset($expenses) && $expenses)
                                @foreach($expenses as $key => $val)
                                    <tr data-id="{{ $val->id }}">
                                        <td>{{ $val->expense_date }}</td>
                                        <td class="{{ isset($val->branch_name) && $val->branch_name ? '' : 'text-center'; }}">{{ $val->branch_name ?? '-' }}</td>
                                        <td class="text-center">{{ config('constants.payment_methods')[$val->payment_method]}}</td>
                                        <td>{{ $val->description ?? ''; }}</td>
                                        <td class="text-end">{{ number_format($val->amount, 2) ?? ''; }}</td>
                                        <td class="{{ (isset($val->user_name) && $val->user_name) && (isset($val->email) && $val->email) ? '' : 'text-center'; }}">{{ ($val->user_name ?? $val->email) ?? '-'; }}</td>
                                        <td class="text-center">
                                            <a href="#" class="btnEdit"><i class="fa fa-edit fs-5"></i></a>
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="8" class="text-center">No Items Found!</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- pagination -->
    <div class="d-flex justify-content-end">
        {{ $expenses->appends(request()->input())->onEachSide(0)->links('partials.pagination.default') }}
    </div>


    {{-- Include Modal --}}
    @include('expenses.modal')

@endsection

@push('js')
    <script src="{{ url('assets/js/dropzone/dropzone.js') }}"></script>
    <script src="{{ url('assets/js/dropzone/dropzone-script.js') }}"></script>

    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        var vendors = @json($vendorsData);

        $(document).ready(function() {
            initializeDropzone();
            initializeSelect2();
        })

        $("#btnAddExpense").click(function() {
            $("#expenseModal").modal("show");
        })

        //handle edit button click function
        $("#expenseTable tbody").on("click", "tr .btnEdit", function(e){
            e.preventDefault();

            alert("edit button clicked!");
        })

        $("#expenseModal .btnCancel").click(function(e) {
            e.preventDefault();

            $("#expenseModal").modal("hide");

            resetModalForm();
        })

        //add line handles click function
        $(".btnAddline").on("click", function(e) {
            e.preventDefault();

            addLine();
        });

        //handle lost focus function for qty and 
        $("#expenseModal #expenseItems tbody").on("focusout", "tr input[name='sub_amount[]']", function(e) {
            e.preventDefault();
            computeTotal();
        });

        //handle modal refresh vendor
        $("#expenseModal .btnRefreshVendor").on("click", function(e) {
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
        $("#expenseModal .btnSubmit").click(function(e) {
            e.preventDefault();

            let formElement = $("#expenseModal #expenseForm")[0];
            let formData = new FormData(formElement);

            // Add Dropzone files manually
            Dropzone.forElement("#myDropzone").getAcceptedFiles().forEach((file, index) => {
                formData.append("attachments[]", file, file.name);
            });

            if (!$("#expenseModal #expenseForm")[0].checkValidity()) {
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
                $.ajax({
                    type: "POST",
                    url: "{{ route('expenses.store') }}",
                    data: formData,
                    dataType: "json",
                    processData: false,
                    contentType: false,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
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
                                    location.reload();
                                }
                            });
                        }
                        console.log(response);  
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
        $("#expenseItems tbody").on("click", "tr .btnRemove", function(e) {
            e.preventDefault();

            $(this).closest("tr").remove();

            computeTotal();
        })

        function initializeDropzone() {
            Dropzone.autoDiscover = false;

            // Destroy existing Dropzone instance if exists
            if (Dropzone.instances.length > 0) {
                Dropzone.instances.forEach(function(dz) {
                    dz.destroy();
                });
            }

            var myDropzone = new Dropzone("#myDropzone", {
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

        function computeTotal() {
            let total = 0;
            // let taxRate = $("#expenseModal input[name='tax_rate']").val();

            //get total amount
            $("#expenseModal #expenseItems tbody tr").each(function() {
                total += parseFloat($(this).find("input[name='sub_amount[]']").val());
            });

            if(isNaN(total)) {
                total = 0.00;
            }

            //pass values to hidden inputs
            $("#expenseModal input[name='total_amount']").val(total);

            $("#expenseModal .total-amount").html(formatNumber(total));

            console.log(total);
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

        function addLine() {

            let options = '';

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
                            placeholder="0.00"
                        >
                    </td>
                    <td class="text-center">
                        <a href="#" class="text-danger btnRemove"><i class="fa fa-trash  fs-5"></i></a>
                    </td>
                </tr>
            `;

            $("#expenseModal #expenseItems tbody").append(trData);

            initializeSelect2();

        }

        function initializeSelect2() {
            $(".selectVendor").select2({
                placeholder : '-- Select --',
                dropdownParent: '#expenseModal'
            });
        }
    </script>
@endpush