@extends('layouts.app')

@section('title') Manage Document Types @endsection

@push('css')
    <style>
        .custom-header {
            background-color: #147369;
            color: white !important;
        }

        .custom-header th {
            color: white !important; /* Change text color for contrast */
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h4>Manage Document Types</h4>
                </div>
                <div class="col-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"> <a href="{{ route('home') }}">
                            <svg class="stroke-icon">
                                <use href="{{url('assets/svg/icon-sprite.svg#stroke-home')}}"></use>
                            </svg></a>
                        </li>
                        <li class="breadcrumb-item">Libraries</li>
                        <li class="breadcrumb-item">Document Types</li>
                        <li class="breadcrumb-item active">Manage Document Types</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h5><i class="fa fa-archive"></i> Total Records : {{ $documentTypes->count() }}</h5>
                </div>

                <div>
                    <!-- new button -->
                    @can('create-document-type')
                    <button type="button" class="btn btn-primary float-end" id="newDocumentTypeBtn" data-url="{{ route('document-type.add') }}" data-method="POST"><i class="fa fa-plus"></i> &nbsp; New Document Type</button>
                    @endcan
                </div>
                

            </div>
        </div>
    </div>

    <!-- table data -->
    <div class="mt-2">
        <div class="card">
            <div class="card-body p-0">
                <table class="table table-hover table-bordered" id="tblDocumentType">
                    <thead class="custom-header text-white">
                        <tr>
                            <th>Document Type</th>
                            <th>Description</th>
                            <th class="text-center">Status</th>
                            @can('edit-document-type')
                            <th class="text-center" width="50"><i class="fa fa-cog"></i></th>
                            @endcan
                        </tr>
                    </thead>
                    <tbody>
                        @if(isset($documentTypes) && $documentTypes)
                            @foreach($documentTypes as $val)
                                <tr data-id="{{ $val->id }}" 
                                    data-type-name="{{ $val->type_name }}"
                                    data-description="{{ $val->description }}"
                                    data-status="{{ $val->status }}"
                                >
                                    <td>{{ $val->type_name ?? ''; }}</td>
                                    <td>{{ $val->description ?? ''; }}</td>
                                    <td class="text-center"><span class="{{ config('constants.status_badge')[$val->status] ?? ''; }}">{{ config('constants.status_value')[$val->status] ?? ''; }}</span></td>

                                    @can('edit-document-type')
                                    <td class="text-center">
                                        <a href="#" class="btn btn-outline-info btn-sm btnUpdate" data-url="{{ route('document-type.update') }}" data-method="PUT"><span class="fa fa-edit"></span></a>
                                    </td>
                                    @endcan
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
        {{ $documentTypes->appends(request()->input())->onEachSide(0)->links('partials.pagination.default') }}
    </div>

    <!-- modal form -->
    <div class="modal fade" id="documentTypeModal"  data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Document Type</h5>
                </div>
                <div class="modal-body">

                    <input type="hidden" id="documentTypeUrl">
                    <input type="hidden" id="documentMethodType">

                    <form id="frmDocumentType">
                        <input type="hidden" name="id">

                        <div class="row">
                            <div class="col-lg-8">
                                <label class="w-100">
                                    Document Type Name <span class="text-danger">*</span>
                                    <input type="text"
                                        name="type_name"
                                        class="form-control"
                                        placeholder="Document Type Name..."
                                        required
                                    >
                                </label>
                            </div>

                            <div class="col-lg-4">
                                <label class="w-100">
                                    Status <span class="text-danger">*</span>
                                    <select class="form-select"
                                        name="status"
                                        required
                                    >
                                        <option value="" selected hidden disabled>Select</option>
                                        @foreach(config('constants.status_value') as $key => $val)
                                            <option value="{{ $key }}">{{ $val }}</option>
                                        @endforeach
                                    </select>
                                </label>
                            </div>
                            <div class="col-lg-12">
                                <label class="w-100">
                                    Description
                                    <textarea
                                        class="form-control"
                                        name="description"
                                        placeholder="Description..."
                                    ></textarea>
                                </label>
                            </div>
                        </div> 
                    </form>
                </div>
                <div class="modal-footer">
                    <!-- save button -->
                    <button type="button" class="btn btn-primary btnSubmit"><i class="fa fa-save"></i> Submit Document Type</button>
                    <!-- cancel button -->
                    <button type="button" class="btn btn-danger btnCancel"><i class="fa fa-times"></i> Cancel</button>
                </div>
            </div>
        </div>
    </div>

    
    
@endsection

@push('js')

    <script>
        $(document).ready(function() {
            //handle new button click
            $("#newDocumentTypeBtn").click(function(e) {
                e.preventDefault();

                $("#documentTypeModal #documentTypeUrl").val($(this).data('url'));
                $("#documentTypeModal #documentMethodType").val($(this).data('method'));

                $("#documentTypeModal").modal("show");
            });

            //handle update button click
            $("#tblDocumentType tbody").on("click", "tr .btnUpdate", function(e) {
                e.preventDefault();

                const tr = $(this).closest("tr");

                $("#documentTypeModal #documentTypeUrl").val($(this).data('url'));
                $("#documentTypeModal #documentMethodType").val($(this).data('method'));

                //pass data to modal form
                $("#documentTypeModal input[name='id']").val(tr.data("id"));
                $("#documentTypeModal input[name='type_name']").val(tr.data("type-name"));
                $("#documentTypeModal select[name='status']").val(tr.data("status"));
                $("#documentTypeModal textarea[name='description']").val(tr.data("description"));

                //show modal form
                $("#documentTypeModal").modal("show");
            });

            //handle cancel button click
            $("#documentTypeModal .btnCancel").click(function(e) {
                clearModalForm();

                $("#documentTypeModal").modal("hide");
            });

            //handle submit button click
            $("#documentTypeModal .btnSubmit").click(function(e){
                let submitUrl = $("#documentTypeModal #documentTypeUrl").val();
                let methodType = $("#documentTypeModal #documentMethodType").val();

                let formData = $("#documentTypeModal form").serializeArray();
                let form = $("#documentTypeModal form")[0];

                if(form.checkValidity()) {
                    //show loading form
                    $(".loader-wrapper").fadeIn("slow");

                    //form is valid, proceed with ajax submission
                    $.ajax({
                        type: methodType,
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
                            

                            if(typeof response !== 'undefined' && response.status == 'success') {
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
                }else{
                    form.reportValidity();
                    return false;
                }
            });
        });

        function clearModalForm() {
            $("#documentTypeModal input").val('');
            $("#documentTypeModal textarea").val('');
            $("#documentTypeModal select").val('');
        }
        
    </script>
@endpush