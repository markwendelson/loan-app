@extends('layouts.app')
@push('css')
    <style>
        .nav-link:hover {
            background-color: #f0f0f0;
            color: #333;
            transition: background-color 0.3s ease;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h4>User Update </h4>
                </div>
                <div class="col-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"> <a href="index.html">
                            <svg class="stroke-icon">
                                <use href="../assets/svg/icon-sprite.svg#stroke-home"></use>
                            </svg></a>
                        </li>
                        <li class="breadcrumb-item">User</li>
                        <li class="breadcrumb-item active">User Update</li>
                        {{-- <li class="breadcrumb-item active">Project List</li> --}}
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <form method="post" action="" enctype="multipart/form-data">
                @csrf

                @include('user.permissions')

                <!-- button container -->
                <div class="row">
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary float-end"><i class="fa fa-save"></i> Update User</button>
                    </div>
                </div>

            </form>
        </div>
    </section>

@endsection

@push('js')

@endpush
