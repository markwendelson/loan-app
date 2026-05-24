<div class="modal fade" id="modalAssignBranchRole" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addModalLabel">Branch & Role Information</h5>
            </div>

            <div class="modal-body">
                <form id="frmAssignBranchRole" class="row g-3 needs-validation custom-input" novalidate="" action="{{ route('user.assign-role', $user) }}">
                    @csrf
                    <div class="col-12">
                        <label class="w-100" for="branch">Branch</label>
                        <select class="form-select form-select-sm" name="branch_id">
                            <option value="" selected hidden disabled>-- Select --</option>
                            @foreach($branches as $key => $val)
                                <option value="{{$val['id']}}" {{ auth()->user()->current_branch_id == $val['id'] ? 'selected' : '' }}>{{$val['name']}}</option>
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

                </form>
            </div>

            <div class="modal-footer gap-1">
                <button type="button" class="btn btn-primary btnSubmit w-100"><i class="fa fa-save"></i> Submit</button>
                <button type="button" class="btn btn-light w-100" onclick="hideModal('modalAssignBranchRole')"><i class="fa fa-close"></i> Cancel</button>
            </div>
        </div>
    </div>
</div>


