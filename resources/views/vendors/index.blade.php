@extends('layouts.app')

@section('title') Vendors @endsection

@push('css')
    <style>
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
                    <h4>Vendors</h4>
                </div>
                <div class="col-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"> <a href="{{url('')}}">
                            <svg class="stroke-icon">
                                <use href="{{url('assets/svg/icon-sprite.svg#stroke-home')}}"></use>
                            </svg></a>
                        </li>
                        <li class="breadcrumb-item">Settings</li>
                        <li class="breadcrumb-item">Libraries</li>
                        <li class="breadcrumb-item active">Vendors</li>
                    </ol>
                </div>
            </div>
        </div>

        <div class="row project-cards">
            <div class="col-md-12 project-list">
                <div class="card">
                    <div class="row">
                        <ul class="nav nav-tabs border-tab" id="top-tab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active"
                                    id="vendors-tab"
                                    data-bs-toggle="tab"
                                    data-bs-target="#vendors"
                                    type="button"
                                    role="tab"
                                    aria-controls="vendors"
                                    aria-selected="true">
                                    <i data-feather="truck"></i> Vendors
                                </button>
                            </li>

                            <li class="nav-item" role="presentation">
                                <button class="nav-link"
                                    id="vendor-types-tab"
                                    data-bs-toggle="tab"
                                    data-bs-target="#vendor-types"
                                    type="button"
                                    role="tab"
                                    aria-controls="vendor-types"
                                    aria-selected="false">
                                    <i data-feather="box"></i> Vendor Types
                                </button>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <div class="tab-content" id="top-tabContent">

            <!-- Vendors -->
            <div class="tab-pane fade show active"
                id="vendors"
                role="tabpanel"
                aria-labelledby="vendors-tab">

                @can('create-vendors')
                <div class="card">
                    <div class="card-body d-flex justify-content-end">
                        <button type="button" class="btn btn-primary btnNewVendor">
                            <i class="fa fa-plus"></i> New Vendor
                        </button>
                    </div>
                </div>
                @endcan

                <div class="card">
                    <div class="card-header">
                        <livewire:Vendors.FilterData />
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <livewire:Vendors.VendorData defer />
                        </div>
                    </div>
                </div>

            </div>

            <!-- Vendor Types -->
            <div class="tab-pane fade"
                id="vendor-types"
                role="tabpanel"
                aria-labelledby="vendor-types-tab">

                <div class="card">
                    <div class="card-body d-flex justify-content-end">
                        <button type="button" class="btn btn-primary btnNewVendorType">
                            <i class="fa fa-plus"></i> New Vendor Type
                        </button>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        @livewire('Vendors.VendorType.FilterData')
                    </div>
                    <div class="card-body p-0">
                        {{-- @livewire('Vendors.VendorType.VendorTypeData', ['defer' => true]) --}}
                        <livewire:Vendors.VendorType.VendorTypeData defer />
                    </div>
                </div>

            </div>

        </div>
    </div>

    <!-- vendor modal -->  
    @include('vendors.modal.vendorModal')

    @include('vendors.modal.view-vendor')

    @include('vendors.vendor-type.modal.create')
@endsection

@push('js')
    <script>
        $(document).ready(function() {
            
            //handle new vendor button click
            $(".btnNewVendor").click(function(e) {
                e.preventDefault();

                $("#vendorModal .btnSubmit").data("submit-url", "{{ route('vendor.store') }}");
                $("#vendorModal .btnSubmit").data("method", "POST");

                $("#vendorModal").modal("show");
            });

            //handle modal cancel button click
            $("#vendorModal").on("click", ".btnCancel", function(e) {
                clearVendorModalForm();

                $("#vendorModal").modal("hide");
            });

            //handle modal submit button click
            $("#vendorModal").on("click", ".btnSubmit", function(e) {
                e.preventDefault();

                let submitUrl = $(this).data("submit-url");
                let method = $(this).data("method");
                let formData = $("#vendorModal #frmVendor").serializeArray();

                //show loading modal
                $(".loader-wrapper").fadeIn("slow");

                $.ajax({
                    type: method,
                    url: submitUrl,
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
                                    //reload page
                                    Livewire.dispatch("refresh-vendor-data");
                                    $("#vendorModal").modal("hide");
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

            //handle table vendor edit button
            $(document).on("click", "#tblVendors tbody .btnEditVendor", function(e) {
                e.preventDefault();

                let vendorId = $(this).closest("tr").data("id");
                $(".loader-wrapper").fadeIn("slow");

                $("#vendorModal .btnSubmit").data("submit-url", "{{ route('vendor.update') }}");
                $("#vendorModal .btnSubmit").data("method", "PUT");

                $.ajax({
                    type: "POST",
                    url: "{{ route('vendor.get-vendor-data') }}",
                    data: {
                        vendor_id : vendorId
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

                        if((typeof response.status != 'undefined' && response.status) && (typeof response.data !== 'undefined' && response.data)) {
                            $("#vendorModal input[name='vendor_id']").val(response.data.id);
                            $("#vendorModal input[name='name']").val(response.data.name);
                            $("#vendorModal input[name='tin']").val(response.data.tin);
                            $("#vendorModal select[name='tax_classification']").val(response.data.tax_classification);

                            $("#vendorModal select[name='vendor_type']").val(response.data.vendor_type);
                            $("#vendorModal input[name='website']").val(response.data.website);
                            $("#vendorModal input[name='contact_no']").val(response.data.contact_no);
                            $("#vendorModal input[name='email']").val(response.data.email);
                            $("#vendorModal input[name='fax']").val(response.data.fax);
                            $("#vendorModal input[name='country_id']").val(response.data.country_id);
                            $("#vendorModal textarea[name='address']").val(response.data.address);

                            $("#vendorModal").modal("show");
                        }
                    }
                });
            });

            //handle table delete button
            $("#tblVendors").on("click", ".btnDelete", function(e) {
                e.preventDefault();

                swal({
                    title: 'Are you sure?',
                    text: "Do you want to proceed?",
                    icon: 'warning',
                    buttons: {
                        confirm: {
                            text: "Yes",
                            value: true,
                            visible: true,
                            className: "btn btn-primary",
                            closeModal: true
                        },
                        cancel: {
                            text: "No",
                            value: false,
                            visible: true,
                            className: "btn btn-danger",
                            closeModal: true,
                        },
                        
                    },
                }).then((willProceed) => {
                    //yes
                    if (willProceed) {
                        //show loading form
                        $(".loader-wrapper").fadeIn("slow");

                        $.ajax({
                            type: "DELETE",
                            url: "{{ route('vendor.destroy') }}",
                            data: {
                                vendor_id : $(this).closest("tr").data("id")
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
                                            //reload page
                                            location.reload();
                                        }
                                    });
                                }
                            }
                        });
                    }
                });

            });

            //handle view vendor button click function
            $(document).on("click", "#tblVendors .viewVendor", function(e) {
                e.preventDefault();

                //show loading form
                $(".loader-wrapper").fadeIn("slow");

                $.ajax({
                    type: "POST",
                    url: "{{ route('vendor.get-vendor-data') }}",
                    data: {
                        vendor_id : $(this).closest("tr").data('id')
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

                        if(typeof response.status !== 'undefined' && response.status == 'success') {
                            $("#viewVendorModal .vendorName").html(response.data.name);
                            $("#viewVendorModal .vendorTin").html(response.data.tin);
                            $("#viewVendorModal .vendorTaxClassification").html(response.data.tax_classification);
                            $("#viewVendorModal .vendorType").html(response.data.vendor_type);
                            $("#viewVendorModal .vendorCountry").html(response.data.country_name);
                            $("#viewVendorModal .vendorEmail").html(response.data.email);
                            $("#viewVendorModal .vendorFax").html(response.data.fax);
                            $("#viewVendorModal .vendorContactNo").html(response.data.contact_no);
                            $("#viewVendorModal .vendorWebsite").html(response.data.website);
                            $("#viewVendorModal .vendorBillingAddress").html(response.data.address);

                            $("#viewVendorModal").modal("show");
                        }
                    }
                });

            });

            //handle cancel button for view vendor modal
            $("#viewVendorModal").on("click",".btnCancel", function(e) {
                e.preventDefault();

                $("#viewVendorModal").modal("hide");
            })

            //handle new vendor type button click function
            $(".btnNewVendorType").on("click", function(e) {
                e.preventDefault();

                $("#modalVendorType").modal("show");
            })

            //handle vendor type cancel button click function
            $("#modalVendorType .btnCancel").on("click", function(e){
                e.preventDefault();

                clearVendorTypesForm();
                $("#modalVendorType").modal("hide");
            })

            //handles vendor types edit button click function
            $(document).on("click", "#tblVendorTypes tbody tr .btnEdit", function(e) {
                e.preventDefault();

                let vendorTypeId = $(this).closest("tr").data("id");

                //show loading form
                $(".loader-wrapper").fadeIn("slow");

                //fetch data from table
                $.ajax({
                    type: "get",
                    url: `{{ url('vendors/get-vendor-type-data') }}/${vendorTypeId}`,
                    data: {},
                    dataType: "json",
                    success: function (response) {
                        //hide loading form
                        $(".loader-wrapper").fadeOut("slow", function () {
                            $(this).hide();
                        });

                        if(typeof response !== 'undefined' && response.status =='success') {
                            //adjust modal header title
                            $("#modalVendorType .modal-title").html("Edit Vendor Type");

                            $("#modalVendorType form input[name='id']").val(response.data.id);
                            $("#modalVendorType form input[name='name']").val(response.data.name);
                            $("#modalVendorType form select[name='status']").val(response.data.status);
                            $("#modalVendorType form textarea[name='description']").val(response.data.description);

                            $("#modalVendorType").modal("show");
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

            })

            //handle vendor type modal submit button click
            $(document).on("click", "#modalVendorType .btnSubmit", function(e) {
                e.preventDefault();

                let modal = $(this).closest(".modal-content");

                let vendorTypeId = $(modal).find(`input[name='id']`).val();
                
                let submitType = 'POST';
                let submitUrl = vendorTypeId ? "{{ route('vendor.update-vendor-type') }}" : "{{ route('vendor.create-vendor-type') }}";

                let formElement =  $(modal).find("form")[0];
                let formData = new FormData(formElement);

                if(vendorTypeId){
                    formData.append('_method', 'PUT');
                }

                if (!$(modal).find("#frmVendorType")[0].checkValidity()) {
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
                        type: submitType,
                        url: submitUrl,
                        data: formData,
                        dataType: "json",
                        processData: false,
                        contentType: false,
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
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
                                        Livewire.dispatch('vendor-type-refresh');

                                        clearVendorTypesForm();

                                        $("#modalVendorType").modal("hide");
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

            

            function clearVendorModalForm() {
                $("#vendorModal input").val("");
                $("#vendorModal select").val("");
                $("#vendorModal textarea").val("");

                $("#vendorModal select[name='country_id']").val("{{ config('constants.default_country_id') }}").trigger("change");
            }

            function clearVendorTypesForm() {
                $("#modalVendorType form")[0].reset();
            }
        });
    </script>
@endpush