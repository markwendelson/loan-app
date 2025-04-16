
<div class = "modal fade" id = "modalDepartment">
    <div class = "modal-dialog modal-sm">
        <div class = "modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addModalLabel">Department Information</h5>
            </div>

            <div class="modal-body">
                <form id="frmDepartmentModal">
                    <div class = "row">
                        @csrf
                        <!-- id -->
                        <input type = "hidden" value = "" name = "id">

                        <!-- Code -->
                        <div class = "col-12">
                            <label class = "w-100">
                                Code
                                <input type = "text"
                                    class = "form-control form-control-sm"
                                    name = "code"
                                    placeholder = "Code..."
                                    value = ""
                                    required
                                >
                            </label>
                        </div>

                        <!-- name -->
                        <div class = "col-12">
                            <label class = "w-100">
                                Department Name
                                <input type = "text"
                                    class = "form-control form-control-sm"
                                    placeholder = "Department Name..."
                                    name = "name"
                                >
                            </label>
                        </div>

                        <!-- status -->
                        <div class = "col-12">
                            <label class = "w-100">
                                Status
                                <select class = "form-select form-select-sm" name = "status">
                                    <option value = "" selected hidden disabled>-- Select --</option>
                                    @foreach(config('constants.status_value') as $key => $val)
                                        <option value = "{{$key}}">{{$val}}</option>
                                    @endforeach
                                </select>
                            </label>
                        </div>
                    </div>

                </form>
            </div>

            <div class="modal-footer gap-1">
                <button type="button" class="btn btn-primary btnSubmit w-100"><i class = "fa fa-save"></i> Submit</button>
                <button type="button" class="btn btn-ligh w-100" onclick = "hideModal()"><i class = "fa fa-close"></i> Cancel</button>
            </div>
        </div>
    </div>
</div>
