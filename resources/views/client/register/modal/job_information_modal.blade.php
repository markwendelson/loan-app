<div class = "modal fade" id = "modalJobInformation">
    <div class = "modal-dialog">
        <div class = "modal-content">
            <div class = "modal-header">
                <h5>Job Information</h5>
            </div>
            <div class = "modal-body">
                <div class = "row">
                    <!-- target field -->
                    <input type = "hidden" id = "jobInformationTarget" value = "">

                    <!-- Office Name -->
                    <div class = "col-12">
                        <label class = "w-100">
                            Office Name
                            <input type = "text"
                                class = "form-control"
                                id = "office_name"
                                placholder = "Office Name"
                            >
                        </label>
                    </div>

                    <!-- salary -->
                    <div class = "col-12">
                        <label class = "w-100">
                            Salary
                            <input type = "number"
                                id = "salary"
                                class = "form-control"
                                placholder = "Salary"
                            >
                        </label>
                    </div>

                    <!-- job title -->
                    <div class = "col-12">
                        <label class = "w-100">
                            Job Title
                            <select class = "form-select" id = "job_id">
                                <option value = "" selected hidden disabled>-- Select --</option>
                                @if(isset($jobs_data) && $jobs_data)
                                    @foreach($jobs_data as $key => $val)
                                        <option value = "{{$val['id']}}">{{$val['title']}}</option>
                                    @endforeach
                                @endif
                            </select>
                        </label>
                    </div>

                    <!-- status -->
                    <div class = "col-12">
                        <label class = "w-100">
                            Status
                            <select class = "form-select" id = "status">
                                <option value = "" selected hidden disabled >-- Select --</option>
                                @foreach(config('constants.status_value') as $key => $val)
                                    <option value = "{{$key}}">{{$val}}</option>
                                @endforeach
                            </select>
                        </label>
                    </div>
                </div>
            </div>

            <div class = "modal-footer">
                <button type = "button" class = "btn btn-primary btn-sm btnAdd" ><i class = "fa fa-save"></i> Add</button>
                <button type = "button" class = "btn btn-danger btn-sm btnCancel" onclick = "hideJobInformationModal(this)"><i class = "fa fa-close"></i> Cancel</button>
            </div>
        </div>
    </div>
</div>
