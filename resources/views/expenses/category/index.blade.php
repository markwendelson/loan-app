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
                <button type="button" class="btn btn-primary btnNew"><i class="fa fa-plus"></i> New Category</button>
            </div>
        </div>

        <!-- table container -->
        <div class="card">
            <div class="card-body p-0">
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr class="custom-header">
                            <th width="100" class="text-center">Code</th>
                            <th>Name</th>
                            <th>Scope</th>
                            <th width="50" class="text-center"><i class="fa fa-cog"></i></th>
                        </tr>
                    </thead>
                    <tbody>
                        @if($categories->isNotEmpty())
                            @foreach($categories as $key => $val)
                                <tr>
                                    <td class="text-center">{{ $val->abbreviation ?? ''; }}</td>
                                    <td>{{ $val->name ?? ''; }}</td>
                                    <td>{{ $val->scope ?? ''; }}</td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-outline-info btn-sm"><i class="fa fa-edit"></i></button>
                                    </td>
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
                        <div class="row">
                            <div class="col-lg-4">
                                <label class="w-100">
                                    Code <span class="text-danger">*</span>
                                    <input type="text" 
                                        class="form-control"
                                        placeholder="ex. Subs"
                                    >
                                </label>
                            </div>

                            <div class="col-lg-8">
                                <label class="w-100">
                                    Name <span class="text-danger">*</span>
                                    <input type="text"
                                        class="form-control"
                                        placeholder="ex. Subscription..."
                                        required
                                    >
                                </label>
                            </div>

                            <div class="col-lg-12">
                                <label class="w-100">Description (optional)</label>
                                <textarea class="form-control"
                                    placeholder="Enter description here..."
                                ></textarea>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary"><i class="fa fa-save"></i> Submit Category</button>
                    <button type="button" class="btn btn-danger"><i class="fa fa-times"></i> Cancel</button>
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
                $("#modalExpenseCategories").modal("show");
            });
        });
    </script>
@endpush