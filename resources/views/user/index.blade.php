@extends('layouts.app')

@section("title") Manage User @endsection


@push('css')
    <style>
        .bg-default {
            background-color: #1f807f;
        }
        .bg-default th {
            color: white; /* Set white text for <th> elements */
            border: 1px solid white;
        }
    </style>
@endpush

@section('content')

    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h4>User | Manage User</h4>
                </div>
                <div class="col-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"> <a href="{{url('')}}">
                            <svg class="stroke-icon">
                                <use href="{{url('assets/svg/icon-sprite.svg#stroke-home')}}"></use>
                            </svg></a>
                        </li>
                        <li class="breadcrumb-item">User</li>
                        <li class="breadcrumb-item active">Manage User</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- button container -->
    <div class="card">
    <div class="card-body">
        <button class="btn btn-primary float-end" onclick="showModal('modalUser')"><i class="fa fa-plus"></i> New User</button>
    </div>
    </div>


    <!-- table container -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="tblUser">
                    <thead class="bg-default">
                        <tr>
                            <th class="text-center" width="120">Id</th>
                            <th class="text-center" width="150">Name</th>
                            <th class="text-center" width="150">Email</th>
                            <th class="text-center" width="150">Role</th>
                            <th class="text-center" width="180">Status</th>
                            <th class="text-center" width="150"><i class="fa fa-cog"></i></th> <!-- New column for Edit/Delete buttons -->
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    @include('user.modal')

@endsection

@push('js')
<script>
    var canEdit = {{ auth()->user()->can('edit-user') ? 'true' : 'false' }};
    var canDelete = {{ auth()->user()->can('delete-user') ? 'true' : 'false' }};
    var userEndpoint = `{{ route('user.index') }}`;
</script>
<script src="{{ asset('/assets/js/users.js') }}"></script>
@endpush
