<div class = "modal fade" id = "modalJobInformation">
    <div class = "modal-dialog">
        <div class = "modal-content">
            <div class = "modal-header">
                <h5>Job Information</h5>
            </div>
            <div class ="modal-body">
                <div class = "row">
                    <!-- target field -->
                    <input type = "hidden" id = "jobInformationTarget" value = "">


                    <!-- <div id = "jobInformationTarget"></div> -->

                    <!-- Effictivity -->
                    <div class = "col-12">
                        <label class = "w-100">
                            Effectivity Date
                            <input type = "date"
                                class = "form-control"
                                id = "effective_date"
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
                                placeholder = "Salary"
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



                </div>

            </div>

            <div class = "modal-footer">
                <button type = "button" id="addJobInfo" class = "btn btn-primary btn-sm btnAdd"><i class = "fa fa-save"></i> Add</button>
                <button type="button" id="updateJobInfo" class="btn btn-success" style="display:none;">Update</button>
                <button type = "button" class = "btn btn-danger btn-sm btnCancel"><i class = "fa fa-close"></i> Cancel</button>
            </div>
        </div>
    </div>
</div>

@push('js')
<script>
    $(document).on('click', '#salary', function() {
        // Select the text inside the salary field when clicked
        $(this).select();
    });
</script>
@endpush