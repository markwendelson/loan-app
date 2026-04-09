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
                        <li class="breadcrumb-item"> <a href="{{url('')}}">
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
                    <button type="button" class="btn btn-primary float-end"><i class="fa fa-plus"></i> &nbsp; New Document Type</button>
                </div>
                

            </div>
        </div>
    </div>

    <!-- table data -->
    <div class="mt-2">
        <div class="card">
            <div class="card-body p-0">
                <table class="table table-hover table-bordered">
                    <thead class="custom-header text-white">
                        <tr>
                            <th>Document Type</th>
                            <th>Description</th>
                            <th>Status</th>
                            <th class="text-center" width="50"><i class="fa fa-cog"></i></th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(isset($documentTypes) && $documentTypes)
                            @foreach($documentTypes as $val)
                                <tr>
                                    <td>{{ $val->type_name ?? ''; }}</td>
                                    <td>{{ $val->description ?? ''; }}</td>
                                    <td>...</td>
                                    <td class="text-center">
                                        <a href="#" class="btn btn-outline-info btn-sm"><span class="fa fa-edit"></span></a>
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
@endsection