@extends('layouts.app')

@section('title') Vendors @endsection

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

            </div>
            <div class="card-body p-0">
                <table class="table table-bordered table-hover" id="tblVendors">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>TIN</th>
                            <th>Type</th>
                            <th>Contact</th>
                            <th>Email</th>
                            <th>Website</th>
                            <th colspan="2">Country</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(isset($vendorData) && $vendorData->isNotEmpty())
                            @foreach($vendorData as $key => $val)
                                <tr data-id="{{ $val->id }}">
                                    <td>{{ $val->name }}</td>
                                    <td>{{ $val->tin }}</td>
                                    <td>{{ (config('constants.vendor_types')[$val->vendor_type]) ?? '' }}</td>
                                    <td>{{ $val->contact_no }}</td>
                                    <td>{{ $val->email }}</td>
                                    <td>{{ $val->website }}</td>
                                    <td>{{ $val->country_name }}</td>
                                    <td class="text-center" width="50">
                                        <div class="dropdown">
                                            <button class="btn btn-light btn-sm" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class="fa fa-ellipsis-v"></i>
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li><a class="dropdown-item" href="#"><i class="fa fa-eye txt-infos"></i> View Vendor</a></li>
                                                <li><hr class="dropdown-divider"></li>
                                                <li><a class="dropdown-item btnEditVendor" href="#"><i class="fa fa-edit txt-primary"></i> Edit</a></li>
                                                <li><a class="dropdown-item" href="#"><i class="fa fa-trash txt-danger"></i> Delete</a></li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td class="text-center" colspan="8">No Record Found!</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- vendor modal -->  
    @include('vendors.modal.vendorModal') 
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
                                    location.reload();
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
            $("#tblVendors").on("click", "tbody .btnEditVendor", function(e) {
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
                            $("#vendorModal select[name='vendor_type']").val(response.data.vendor_type);
                            $("#vendorModal input[name='website']").val(response.data.website);
                            $("#vendorModal input[name='contact_no']").val(response.data.contact_no);
                            $("#vendorModal input[name='email']").val(response.data.email);
                            $("#vendorModal input[name='fax']").val(response.data.fax);
                            $("#vendorModal input[name='country_id']").val(response.data.country_id);
                            $("#vendorModal input[name='billing_address']").val(response.data.billing_address);
                            $("#vendorModal input[name='shipping_address']").val(response.data.shipping_address);

                            $("#vendorModal").modal("show");
                        }
                    }
                });
            });

            function clearVendorModalForm() {
                $("#vendorModal input").val("");
                $("#vendorModal select").val("");
                $("#vendorModal textarea").val("");

                $("#vendorModal select[name='country_id']").val("{{ config('constants.default_country_id') }}").trigger("change");
            }
        });
    </script>
@endpush