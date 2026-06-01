@extends('layouts.app')

@section("title") Group Types @endsection

@section('content')
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h4>Group Types</h4>
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
                        <li class="breadcrumb-item active">Group Types</li>
                    </ol>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body d-flex justify-content-end gap-2 align-items-center">
                <button type="button" 
                    class="btn btn-primary btnNewGroupType"
                    data-submit-url="{{ route('client-group.store') }}"
                    data-method="post"
                ><i class="fa fa-plus"></i> New Group Type</button>
            </div>
        </div>

        <div class="card">
            <div class="card-header"></div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover" id="tblGroupTypes">
                        <thead>
                            <tr>
                                <th width="300">Name</th>
                                <th>Description</th>
                                <th class="text-center" width="50">Status</th>
                                <th class="text-center" width="50">
                                    
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(isset($groupTypesData) && $groupTypesData)
                                @foreach(@$groupTypesData as $key => $val)
                                    <tr data-id="{{ $val->id }}">
                                        <td>{{ $val->name }}</td>
                                        <td>{{ $val->description }}</td>
                                        <td>
                                            <span class="{{ config('constants.status_badge')[$val->status] }}">{{ config('constants.status_value')[$val->status] }}</span>
                                        </td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-light btnEditGroupTypes"><i class="fa fa-pencil"></i></button>
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- pagination container -->
        <div class="d-flex justify-content-end">
            {{ $groupTypesData->appends(request()->input())->onEachSide(0)->links('partials.pagination.default') }}
        </div>
    </div>

    <!-- modal -->
    @include('client.group.group-type.modal.group-type')
@endsection

@push('js')
    <script>
        $(document).ready(function() {
            //handle new group type button click function
            $(".btnNewGroupType").on("click", function(e) {
                e.preventDefault();

                //show loading modal
                $(".loader-wrapper").fadeIn("slow");

                //transfer data
                $("#modalGroupTypes .btnSubmit").data("submit-url", $(this).data("submit-url"));
                $("#modalGroupTypes .btnSubmit").data("method", $(this).data("method"));

                $("#modalGroupTypes").modal("show");

                //hide loading form
                $(".loader-wrapper").fadeOut("slow", function () {
                    $(this).hide();
                });
            });

            //handle modal cancel button
            $("#modalGroupTypes").on("click", ".btnCancel", function(e) {
                e.preventDefault();

                $("#modalGroupTypes").modal("hide");
            });

            //handle edit button click function
            $("#tblGroupTypes").on("click", "tbody .btnEditGroupTypes", function(e) {
                e.preventDefault();

                $.ajax({
                    type: "get",
                    url: "{{ route('') }}",
                    data: "data",
                    dataType: "dataType",
                    success: function (response) {
                        
                    }
                });
            });

            //handle modal submit button
            $("#modalGroupTypes").on("click", ".btnSubmit", function(e) {
                e.preventDefault();

                let submitUrl = $(this).data("submit-url");
                let method = $(this).data("method");
                let formData = $("#modalGroupTypes #frmGroupTypes").serializeArray();

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
        });

        function clearModalForm() {
            $("#modalGroupTypes #frmGroupTypes")[0].reset();
        }
    </script>
@endpush