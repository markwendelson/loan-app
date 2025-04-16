<div class = "modal fade" id = "modalFamInfo">
    <div class = "modal-dialog">
        <div class = "modal-content">
            <div class = "modal-header">
                <h5>Family Information</h5>
            </div>
            <div class = "modal-body">
                <div class = "row">
                    <!-- target field -->
                    <input type = "hidden" id = "FamInformationTarget" value = "">

                    <!-- Firstname -->
                    <div class = "col-12" id=firstNameContainer>
                        <label class = "w-100">
                            First Name
                            <input type = "text"
                                class = "form-control"
                                id = "fname"
                                oninput="capitalizeFirstLetter(this)"
                            >
                        </label>
                    </div>

                     <!-- Lastname -->
                     <div class = "col-12"  id=lastNameContainer>
                        <label class = "w-100">
                            Last Name
                            <input type = "text"
                                class = "form-control"
                                id = "lname"
                                oninput="capitalizeFirstLetter(this)"
                            >
                        </label>
                    </div>

                      <!-- Lastname -->
                      <div class = "col-12"  id=middleNameContainer>
                        <label class = "w-100">
                            Middle Name
                            <input type = "text"
                                class = "form-control"
                                id = "mname"
                                oninput="capitalizeFirstLetter(this)"

                            >
                        </label>
                    </div>

                    <!-- Birtdate -->
                    <div class = "col-12" id="birthDateContainer">
                        <label class = "w-100">
                            Birth Date
                            <input type = "date"
                                id = "birthdate"
                                class = "form-control"
                                placholder = "Birth date"
                            >
                        </label>
                    </div>

                    <!-- Employee Relation -->
                    <div class="col-sm-12">
                        <label class="w-100">
                            Relation
                            <select class="form-select custom-select-arrow" id="relation">
                                <option value="" selected hidden disabled>-- Select --</option>
                                @foreach(config('constants.employee_relation', []) as $key => $val)
                                    <option value="{{ $key }}">{{ $val }}</option>
                                @endforeach
                            </select>
                        </label>
                    </div>

                </div>
            </div>

            <div class = "modal-footer">
                <button type = "button" id="addFamInfo" class = "btn btn-primary btn-sm btnAdd"><i class = "fa fa-save"></i> Add</button>
                <button type="button" id="updateFamInfo" class="btn btn-success" style="display:none;">Update</button>
                <button type = "button" class = "btn btn-danger btn-sm btnCancel"><i class = "fa fa-close"></i> Cancel</button>
            </div>
        </div>
    </div>
</div>

@push('js')
<script>

    $(document).on('click', '#fname', function() {
        // Select the text inside the salary field when clicked
        $(this).select();
    });

    $(document).on('click', '#lname', function() {
        // Select the text inside the salary field when clicked
        $(this).select();
    });

    $(document).on('click', '#mname', function() {
        // Select the text inside the salary field when clicked
        $(this).select();
    });

    function capitalizeFirstLetter(input) {
        const value = input.value;
        input.value = value.charAt(0).toUpperCase() + value.slice(1);
    }
</script>
@endpush