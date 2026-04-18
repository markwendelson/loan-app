@extends('layouts.app')

@section('title') Expense Category Maintenance @endsection

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
                    <h4>Expense Category Maintenance</h4>
                </div>
                <div class="col-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"> <a href="{{ route('home') }}">
                            <svg class="stroke-icon">
                                <use href="{{url('assets/svg/icon-sprite.svg#stroke-home')}}"></use>
                            </svg></a>
                        </li>
                        <li class="breadcrumb-item">Libraries</li>
                        <li class="breadcrumb-item">Expense Category</li>
                        <li class="breadcrumb-item active">Expense Category Maintenance</li>
                    </ol>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body d-flex justify-content-between align-items-center">
                <h5><i class="fa fa-archive"></i> Total Records : {{ $categories->count() }}</h5>
                @can('create-expense-category')
                    <button type="button" class="btn btn-primary btnNew" data-submit-url="{{ route('expense-category.create') }}"><i class="fa fa-plus"></i> New Category</button>
                @endcan
            </div>
        </div>

        <!-- table container -->
        <div class="card">
            <div class="card-body p-0">
                <table class="table table-bordered table-hover" id="tblExpenseCategories">
                    <thead>
                        <tr class="custom-header">
                            <th width="100" class="text-center">Code</th>
                            <th>Name</th>
                            <th>Scope</th>
                            <th>Status</th>
                            @can('edit-expense-category')
                            <th width="50" class="text-center"><i class="fa fa-cog"></i></th>
                            @endcan
                        </tr>
                    </thead>
                    <tbody>
                        @if($categories->isNotEmpty())
                            @foreach($categories as $key => $val)
                                <tr data-id="{{ $val->id }}"  data-abbrv="{{ $val->abbreviation }}" data-name="{{ $val->name }}" data-scope="{{ $val->scope }}" data-status="{{ $val->status }}">
                                    <td class="text-center">{{ $val->abbreviation ?? ''; }}</td>
                                    <td>{{ $val->name ?? ''; }}</td>
                                    <td>{{ $val->scope ?? ''; }}</td>
                                    <td class="text-center">
                                        <span class="{{ config('constants.status_badge')[$val->status] }}">{{ config('constants.status_value')[$val->status] }}</span>
                                    </td>
                                    @can('edit-expense-category')
                                    <td class="text-center">
                                        <button type="button" class="btn btn-outline-info btn-sm btnEdit" data-submit-url="{{ route('expense-category.update') }}"><i class="fa fa-edit"></i></button>
                                    </td>
                                    @endcan
                                </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
            </div>
        </div>

        <!-- pagination container -->
        <div class="d-flex justify-content-end">
            {{ $categories->appends(request()->input())->onEachSide(0)->links('partials.pagination.default') }}
        </div>


    </div>

    <!-- modal -->
    <div class="modal fade" id="modalExpenseCategories">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Expense Categories</h4>
                </div>
                <div class="modal-body">
                    <form id="frmExpenseCategories">

                        <input type="hidden" name="id">

                        <input type="hidden" id="inputSubmitUrl">
                        <input type="hidden" id="inputMethodType">

                        <div class="row">
                            <div class="col-lg-5">
                                <label class="w-100">
                                    Code <span class="text-danger">*</span>
                                    <input type="text" 
                                        class="form-control"
                                        name="abbreviation"
                                        placeholder="ex. Subs"
                                    >
                                </label>
                            </div>

                            <div class="col-lg-7">
                                <label class="w-100">
                                    Name <span class="text-danger">*</span>
                                    <input type="text"
                                        class="form-control"
                                        name="name"
                                        placeholder="ex. Subscription..."
                                        required
                                    >
                                </label>
                            </div>
                            <div class="col-lg-5">
                                <label class="w-100">
                                    Status <span class="text-danger">*</span>
                                    <select class="form-select" name="status" required>
                                        <option value="" selected hidden disabled>-Select Status-</option>
                                        @foreach(config('constants.status_value') as $key => $val)
                                            <option value="{{ $key }}">{{ $val }}</option>
                                        @endforeach
                                    </select>
                                </label>
                            </div>

                            <div class="col-lg-12">
                                <label class="w-100">Description (optional)</label>
                                <textarea class="form-control"
                                    placeholder="Enter description here..."
                                    rows="5"
                                    name="scope"
                                ></textarea>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary btnSubmit"><i class="fa fa-save"></i> Submit Category</button>
                    <button type="button" class="btn btn-danger btnCancel"><i class="fa fa-times"></i> Cancel</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script>
        $(document).ready(function(e) {
            //handle new category click
            $(".btnNew").click(function(e) {
                $("#modalExpenseCategories .modal-title").html("New Expense Category");
                $("#modalExpenseCategories #inputSubmitUrl").val($(this).data("submit-url"));
                $("#modalExpenseCategories #inputMethodType").val("POST");

                $("#modalExpenseCategories").modal("show");
            });

            //handle edit category click
            $("#tblExpenseCategories tbody tr").on("click", ".btnEdit", function() {
                let data = $(this).closest("tr");
                $("#modalExpenseCategories .modal-title").html("Edit Expense Category");
                $("#modalExpenseCategories #inputSubmitUrl").val($(this).data("submit-url"));
                $("#modalExpenseCategories #inputMethodType").val("PUT");

                //pass data to modal form
                $("#modalExpenseCategories input[name='id']").val(data.data("id"));
                $("#modalExpenseCategories input[name='abbreviation']").val(data.data("abbrv"));
                $("#modalExpenseCategories input[name='name']").val(data.data("name"));
                $("#modalExpenseCategories select[name='status']").val(data.data("status"));
                $("#modalExpenseCategories textarea[name='scope']").val(data.data("scope"));


                $("#modalExpenseCategories").modal("show");
            })

            //handle cancel button
            $("#modalExpenseCategories .btnCancel").click(function() {
                clearModalForm();
                $("#modalExpenseCategories").modal("hide");
            });

            //handle modal submit button
            $("#modalExpenseCategories .btnSubmit").click(function() {
                let submitUrl = $("#modalExpenseCategories #inputSubmitUrl").val();
                let methodType = $("#modalExpenseCategories #inputMethodType").val();

                let formData = $("#modalExpenseCategories form").serializeArray();
                let form = $("#modalExpenseCategories form")[0];

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
            })
        });

        function clearModalForm(){
            $("#modalExpenseCategories input").val("");
            $("#modalExpenseCategories select").val("");
            $("#modalExpenseCategories textarea").val("");
        }

    </script>
@endpush