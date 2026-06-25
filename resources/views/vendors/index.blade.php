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

        @can('create-vendors')
        <div class="card">
            <div class="card-body d-flex justify-content-end align-items-center">
                <button type="button" class="btn btn-primary btnNewVendor"><i class="fa fa-plus"></i> New Vendor</button>
            </div>
        </div>
        @endcan

        <div class="card">
            <div class="card-header">
                <livewire:Vendors.FilterData />
            </div>
            <div class="card-body p-0">
                <livewire:Vendors.VendorData defer />
            </div>
        </div>
    </div>

    <!-- vendor modal -->  
    @include('vendors.modal.vendorModal')

    @include('vendors.modal.view-vendor')
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

            function clearVendorModalForm() {
                $("#vendorModal input").val("");
                $("#vendorModal select").val("");
                $("#vendorModal textarea").val("");

                $("#vendorModal select[name='country_id']").val("{{ config('constants.default_country_id') }}").trigger("change");
            }
        });
    </script>
@endpush