
<div class="modal fade" id="modalUser" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addModalLabel">User Information</h5>
            </div>

            <div class="modal-body">
                <form id="frmUserModal" class="row g-3 needs-validation custom-input" novalidate="">
                    @csrf
                    <input type="hidden" value="" name="id" id="id">
                    <div class="col-12">
                        <label class="w-100" for="name">Name</label>
                        <input type="text" class="form-control form-control-sm" name="name" placeholder="Name" required />
                    </div>

                    <div class="col-12">
                        <label class="w-100" for="email">Email</label>
                        <input type="email" class="form-control form-control-sm" name="email" placeholder="Email" required />
                    </div>

                    <div class="col-12">
                        <label class="w-100" for="branch">Branch</label>
                        <select class="form-select form-select-sm" name="client_details[branch_id]">
                            <option value="" selected hidden disabled>-- Select --</option>
                            @foreach($branches as $key => $val)
                                <option value="{{$val['id']}}">{{$val['name']}}</option>
                            @endforeach
                        </select>
                </div>

                    <div class="col-12">
                        <label class="w-100" for="role_id">Role</label>
                        <select class="form-select form-select-sm" name="role_id">
                            <option value="" disabled>-- Select --</option>
                            @foreach($roles as $role)
                                <option value="{{$role->id}}">{{$role->name}}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-12">
                        <label class="w-100" for="status">Status</label>
                        <select class="form-select form-select-sm" name="status">
                            <option value="" disabled>-- Select --</option>
                            @foreach(config('constants.status_value') as $key => $val)
                                <option value="{{$key}}">{{$val}}</option>
                            @endforeach
                        </select>
                    </div>

                </form>
            </div>

            <div class="modal-footer gap-1">
                <button type="button" class="btn btn-primary btnSubmit w-100"><i class="fa fa-save"></i> Submit</button>
                <button type="button" class="btn btn-light w-100" onclick="hideModal('modalUser')"><i class="fa fa-close"></i> Cancel</button>
            </div>
        </div>
    </div>
</div>


