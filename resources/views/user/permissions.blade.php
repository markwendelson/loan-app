
<div class="row">
    <div class="col-sm-12 col-md-5 col-lg-3">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">User Information</h5>
            </div>
            <div class="card-body">
                <div class="image-container text-center mb-3">
                    <div class="row">
                        <div class="col-12">
                            <img class="img-thumbnail" src="{{url('/assets/images/user/6.jpg')}}">
                        </div>
                        <div class="col-12">
                            <button type="button" class="btn btn-primary mt-1"><i class="fa fa-camera"></i> Upload Image</button>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <!-- branch -->
                    <div class="col-12">
                        <label class="w-100">
                            Processing Branch
                            <select class="form-select" name="client_details[branch_id]">
                                <option value="" selected hidden disabled>-- Select --</option>
                                    @foreach($branches as $key => $val)
                                        <option value="{{$val->id}}" {{$user->branch_id == $val->id ? 'selected' : ''}}>{{$val->name}}</option>
                                    @endforeach
                            </select>
                        </label>
                    </div>

                    <div class="col-12">
                        <label class="w-100" for="name">Name</label>
                        <input type="text" class="form-control mt-1" name="name" placeholder="First Name" value="{{$user->name}}"/>
                    </div>

                    <div class="col-12">
                        <label class="w-100" for="email">Email</label>
                        <input type="text" class="form-control mt-1" name="email" placeholder="Email" value="{{$user->email}}"/>
                    </div>

                    <div class="col-12">
                        <button type="button" class="btn btn-primary mt-2 w-100"><i class="fa fa-save"></i> Update User</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-12 col-md-7 col-lg-9">

        <div class="card">
            <div class="card-header">
                <h5>Permissions</h5>
            </div>
            <div class ="card-body">
                <div class="row">

                </div>

                <div class="col-12">
                    <button type="button" class="btn btn-primary mt-2 float-end"><i class="fa fa-save"></i> Update Permissions</button>
                </div>
            </div>
        </div>

    </div>
</div>
