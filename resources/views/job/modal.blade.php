
<div class = "modal fade" id = "modalJobTitle">
    <div class = "modal-dialog modal-sm">
        <div class = "modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addModalLabel">Job Title Information</h5>
            </div>

            <div class="modal-body">
                <form id="frmJobTitleModal">
                    <div class = "row">
                        @csrf
                        <!-- id -->
                        <input type = "hidden" value = "" name = "id">

                        <!-- Job Title -->
                        <div class = "col-12">
                            <label class = "w-100">
                                Job Title
                                <input type = "text"
                                    class = "form-control form-control-sm"
                                    placeholder = "Job title..."
                                    name = "title"
                                >
                            </label>
                        </div>

                        <!-- Description -->
                        <div class = "col-12">
                            <label class = "w-100">
                                Description
                                <input type = "text"
                                    class = "form-control form-control-sm"
                                    placeholder = "Description..."
                                    name = "description"
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
                <button type="button" class="btn btn-secondary w-100" onclick = "hideModal()"><i class = "fa fa-close"></i> Cancel</button>
            </div>
        </div>
    </div>
</div>
