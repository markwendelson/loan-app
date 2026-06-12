@extends('layouts.app')

@section('title') User Update @endsection

@push('css')
    <style>
        .nav-link:hover {
            background-color: #f0f0f0;
            color: #333;
            transition: background-color 0.3s ease;
        }

        table tbody tr:last-child td {
            border-bottom: 1px solid #dee2e6;
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
                                <use href="{{url('assets/svg/icon-sprite.svg#stroke-home')}}"></use>
                            </svg></a>
                        </li>
                        <li class="breadcrumb-item">User</li>
                        <li class="breadcrumb-item active">User Update</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                    <div class="col-sm-12 col-md-5 col-lg-3">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title">User Information</h5>
                            </div>
                            <div class="card-body">
                                <form method="post" action="{{route('user.update', $user->id)}}" enctype="multipart/form-data">
                                    @csrf
                                    @method('PUT')
                                    <div class="image-container text-center mb-3">
                                        <div class="row">
                                            <div class="col-12">
                                                @if(isset($user))
                                                    <img id="user_photo_preview" class="img-thumbnail" src="{{ $user->photo ? asset('storage/'.$user->photo) : url('/assets/images/user/6.jpg') }}">
                                                @else
                                                    <img id="user_photo_preview" class="img-thumbnail" src="{{url('/assets/images/user/6.jpg')}}">
                                                @endif
                                            </div>
                                            <div class="col-12">
                                                <input type="file" id="user_photo" name="user_photo" accept="image/*" style="display: none;" />
                                                <button type="button" id="btn_img_upload" class="btn btn-primary mt-1"><i class="fa fa-camera"></i> Upload Image</button>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <!-- branch -->
                                        <div class="col-12">
                                            <label class="w-100">
                                                Processing Branch
                                                <select class="form-select" name="branch_id" required>
                                                    <option value="" selected hidden disabled>-- Select --</option>
                                                        @foreach($branches as $key => $val)
                                                            <option value="{{$val->id}}" {{$user->current_branch_id == $val->id ? 'selected' : ''}}>{{$val->name}}</option>
                                                        @endforeach
                                                </select>
                                            </label>
                                        </div>

                                        <div class="col-12">
                                            <label class="w-100" for="name">Name</label>
                                            <input type="text" class="form-control mt-1" name="name" placeholder="Name" value="{{$user->name}}" required/>
                                        </div>

                                        <div class="col-12">
                                            <label class="w-100" for="email">Email</label>
                                            <input type="email" class="form-control mt-1" name="email" placeholder="Email" value="{{$user->email}}" required/>
                                        </div>

                                        <div class="col-12">
                                            <button type="submit" class="btn btn-primary mt-2 w-100"><i class="fa fa-save"></i> Update User</button>
                                        </div>
                                    </div>
                                </form>

                            </div>
                        </div>
                    </div>

                    <div class="col-sm-12 col-md-7 col-lg-9">

                        @include('user.roles')

                        <div class="card" id="permissions">
                            <div class="card-body d-flex justify-content-center align-items-center bg-white" style="height: 200px;">
                                <h4>Select in role to view permissions</h4>
                            </div>
                        </div>

                    </div>
            </div>
        </div>
    </section>

@endsection

@push('js')
<script>
    $(function() {
        // image preview
        $("[name='user_photo']").change(function(event) {
            const file=event.target.files[0];
            if (file) {
                const reader=new FileReader();
                reader.onload=function(e) {
                    $('#user_photo_preview').attr('src', e.target.result).show();
                };
                reader.readAsDataURL(file);
            }
        });

        $('#btn_img_upload').click(function() {
            $("[name='user_photo").click();
        });

        $(document).on('click', '.btn-view-permissions', function () {
            //show loading screen
            $(".loader-wrapper").fadeIn("slow", function () {
                $(this).show();
            });
            let user_id = $(this).data('user-id')
            let branch_id = $(this).data('branch-id')
            let _url = `{{ route('user.get-branch-permission', ['user' => ':user', 'branch' => ':branch']) }}`;
            _url = _url.replace(':user', user_id) .replace(':branch', branch_id);

            $('#permissions').html('')
            $.ajax({
                url: _url,
                method: 'GET',
                success: function (response) {
                    $('#permissions').html(response);
                },
                error: function () {
                    $('#permissions').html(`
                        <div class="text-danger">
                            Failed to load permissions
                        </div>
                    `);
                }
            });
            $(".loader-wrapper").fadeOut("slow", function () {
                $(this).hide();
            });
        })
    })


</script>
@endpush
