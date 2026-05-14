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
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>TIN</th>
                            <th>Type</th>
                            <th>Contact</th>
                            <th>Email</th>
                            <th>Website</th>
                            <th>Country</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(isset($vendorData) && $vendorData->isNotEmpty())
                            @foreach($vendorData as $key => $val)
                                <tr>

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

                $("#vendorModal").modal("show");
            });

            //handle modal cancel button click
            $("#vendorModal").on("click", ".btnCancel", function(e) {
                $("#vendorModal").modal("hide");
            });
        });
    </script>
@endpush