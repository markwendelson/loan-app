@extends('layouts.app')

@section("title") Manage User @endsection

@php
    $version = uniqid();
@endphp

@push('css')
    <style>
        .bg-default {
            background-color: #1f807f;
        }
        .bg-default th {
            color: white; /* Set white text for <th> elements */
            border: 1px solid white;
        }
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
                    <h4>User | Manage User</h4>
                </div>
                <div class="col-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"> <a href="{{url('')}}">
                            <svg class="stroke-icon">
                                <use href="{{url('assets/svg/icon-sprite.svg#stroke-home')}}"></use>
                            </svg></a>
                        </li>
                        <li class="breadcrumb-item">Administration</li>
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
        <div class="card-header">
            <form action="{{ url()->current() }}" method="GET">
                <div class="d-flex justify-content-between gap-2">
                    <div class="d-flex gap-2 align-items-center">
                        <span>Show</span>
                        <select name="entries" class="form-select">
                            <option value="10" {{ isset($filterData['entries']) && $filterData['entries'] == 10 ? 'selected' : ''; }}>10</option>
                            <option value="25" {{ isset($filterData['entries']) && $filterData['entries'] == 25 ? 'selected' : ''; }}>25</option>
                            <option value="50" {{ isset($filterData['entries']) && $filterData['entries'] == 50 ? 'selected' : ''; }}>50</option>
                            <option value="100" {{ isset($filterData['entries']) && $filterData['entries'] == 100 ? 'selected' : ''; }}>100</option>
                        </select>
                    </div>
                    <!-- branch select -->
                    <div class="d-flex justify-content-start align-items-center gap-2">
                        <input type="search"
                            class="form-control"
                            placeholder="Search users..."
                            value="{{ $filterData['q'] ?? ''; }}"
                            name="q"
                        >

                        <select name="branch_id"
                            class="form-select"
                        >
                            <option value=""> -- All Branch --</option>
                            @if(isset($branches) && $branches)
                                @foreach($branches as $key => $val)
                                    <option value="{{ $val->id }}" {{ isset($filterData['branch_id']) && $filterData['branch_id'] == $val->id ? 'selected' : ''; }}>{{ $val->name }}</option>
                                @endforeach
                            @endif
                        </select>

                        <button type="submit" class="btn btn-success"><i class="fa fa-search"></i></button>

                    </div>

                </div>
            </form>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-bordered" id="tblUsers">
                    <thead>
                        <tr>
                            <th width="100"></th>
                            <th>Name</th>
                            <th>Email</th>
                            <th width="250" class="text-center">Role</th>
                            <th>Branch</th>
                            <th class="text-center" width="150">Status</th>
                            <th width="50"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(isset($users) && $users)
                            @foreach($users as $key => $val)
                                <tr data-id="{{ $val->id }}">
                                    <td class="text-center">
                                        @php
                                            $photo = $val->photo;
                                            if ($photo && file_exists(public_path('storage/' . $photo))) {
                                                $photoUrl = asset('storage/' . $photo);
                                            } else {
                                                $photoUrl = asset('assets/images/user/user.png');
                                            }
                                        @endphp

                                        <img class="rounded bordered"
                                            src="{{ $photoUrl }}"
                                            width="50"
                                            height="50"
                                            alt=""
                                            loading="lazy"
                                        >
                                    </td>
                                    <td>
                                        <span>{{ $val->name }}</span>
                                    </td>
                                    <td>{{ $val->email }}</td>
                                    <td class="text-center">{{ $val->role }}</td>
                                    <td>{{ $val->branch_name }}</td>
                                    <td class="text-center"><span class="{{ config('constants.status_badge')[$val->status] }}">{{ config('constants.status_value')[$val->status] }}</span></td>
                                    <td>
                                        <div class="dropdown  position-static">
                                            <button class="btn btn-light btn-sm" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class="fa fa-ellipsis-v"></i>
                                            </button>
                                            <ul class="dropdown-menu">
                                                @can('edit-user')
                                                <li><a class="dropdown-item" href="{{ url('user/'. $val->id .'/edit') }}"><i class="fa fa-edit me-1"></i>Edit</a></li>
                                                @endcan
                                                <li><a class="dropdown-item btnResetPassword" href="#"><i class="fa fa-refresh me-1"></i>Reset Password</a></li>
                                                @can('delete-user')
                                                <li><hr class="dropdown-divider"></li>
                                                <li><a class="dropdown-item txt-danger" href="#" onclick="deleteData({{ $val->id }})"><i class="fa fa-trash me-1"></i>Delete</a></li>
                                                @endcan
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="7" class="text-center">No users found.</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- pagination -->
    <div class="d-flex justify-content-end">
        {{ $users->appends(request()->input())->onEachSide(0)->links('partials.pagination.default') }}
    </div>

    @include('user.modal')

    @include('user.modal.reset-password')
@endsection

@push('js')
    <script>
        var canEdit = {{ auth()->user()->can('edit-user') ? 'true' : 'false' }};
        var canDelete = {{ auth()->user()->can('delete-user') ? 'true' : 'false' }};
        var userEndpoint = `{{ route('user.index') }}`;
    </script>
    <script src="{{ asset('/assets/js/users.js?v=' . $version) }}"></script>
@endpush
