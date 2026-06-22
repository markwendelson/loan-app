<div>
    <div class="position-relative">
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
                                <div class="dropdown position-static">
                                    <button class="btn btn-light btn-sm" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="fa fa-ellipsis-v"></i>
                                    </button>
                                    <ul class="dropdown-menu" style="z-index: 9999; position: absolute;">
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
            @if($users->hasPages())
            <tfoot>
                <tr>
                    <td colspan="5">
                        <div class="d-flex justify-content-end">
                            {{ $users->links('partials.pagination.default-livewire') }}
                        </div>
                    </td>
                </tr>
            </tfoot>
            @endif
        </table>

        <div
            wire:loading.delay
            wire:loading.class.remove="d-none"
            class="position-absolute top-0 start-0 w-100 h-100 d-flex justify-content-center align-items-center bg-white bg-opacity-50 d-none"
            style="z-index: 10;"
        >
        
            <div class="card shadow-sm border-0">
                <div class="card-body py-3 px-4">
                    <div class="d-flex align-items-center gap-2">
                        <div class="custom-loader" role="status"></div>
                        <span>Please wait...</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
