
<div class = "row">
    <div class = "col-sm-12 col-md-5 col-lg-3">
        <div class = "card">
            <div class = "card-header">
                <h5 class = "card-title">Personal Information</h5>
            </div>
                 <div class = "card-body">
                     <div class = "image-container text-center mb-3">
                     <div class="row">
                         <div class="col-12">
                             <!-- Image Preview    style="max-width: 200px; max-height: 200px;"-->
                             <img id="profileImagePreview" class="img-thumbnail thumbnail-size"
                                  src="{{ isset($employee['img']) && $employee['img'] ? url('storage/uploads/employee/'.$employee['img']) : url('/assets/images/user/6.jpg') }}"
                                  alt="User Image">
                         </div>
                         <div class="col-12">
                             <!-- Upload Button "uploadImageButton" -->
                             <button type="button" id="uploadImageButton"
                                     class="btn btn-primary mt-1">
                                 <i class="fa fa-camera"></i> Upload Image
                             </button>
                             <!-- Hidden File Input -->
                             <input type="file" id="imageInput" name="img" class="form-control d-none"
                                    accept="image/*" onchange="previewImage(event)">
                         </div>
                     </div>
                 </div>

                <div class = "row">
                    <!-- Employee Code -->
                    <div class="col-12">
                        <label class="w-100">
                            Employee Code
                            <input type="text"
                                   class="form-control mt-1"
                                   name="employee_userinfo[emp_code]"
                                   value="{{ old('employee_userinfo.emp_code', isset($employee['emp_code']) ? $employee['emp_code'] : $newEmpCode) }}"
                                   placeholder="Emp. Code"
                                   readonly>

                            <input type="hidden"
                                   name="employee_userinfo[emp_code]"
                                   value="{{ isset($employee['emp_code']) ? $employee['emp_code'] : '' }}">
                        </label>
                    </div>

                  <!-- branch -->
                  <div class="col-12">
                      <label class="w-100">
                          Processing Branch
                          <select class="form-select" name="employee_details[branch_id]">
                              <option value="" selected hidden disabled>-- Select --</option>
                              @if(isset($branch_data) && count($branch_data) > 0)
                                  @foreach($branch_data as $key => $val)
                                  <option value="{{ $val['id'] }}"
                                      @if(isset($employee['branch_id']) && $employee['branch_id'] == $val['id']) selected @endif>
                                            {{ $val['name'] }}
                                  </option>
                                  @endforeach
                              @else
                                  <option value="" disabled>No branches available</option>
                              @endif
                          </select>
                      </label>
                  </div>

                    <!-- suffix -->
                    <div class="col-sm-12">
                        <label class="w-100">
                            Suffix
                              <select class="form-select custom-select-arrow" name="employee_details[suffix]">
                                  <option value="" selected hidden disabled>-- Select --</option>
                                  @foreach(config('constants.suffix_list') as $key => $val)
                                      <option value="{{ $key }}" {{isset($employee['suffix']) && $employee['suffix'] ==  $key ? 'selected' : ''}} >
                                          {{ $val }}
                                      </option>
                                  @endforeach
                              </select>
                        </label>
                    </div>


                    <!-- first name -->
                    <div class = "col-12">
                        <label class = "w-100">
                            First Name
                            <input type = "text"
                                class = "form-control mt-1"
                                name = "employee_details[fname]"
                                id="fName"
                                placeholder = "First Name"
                                oninput="capitalizeFirstLetter(this)"
                                value="{{ old('employee_details.fname', isset($employee['fname']) ? $employee['fname']  : '') }}"
                                required
                            >
                        </label>
                    </div>

                    <!-- middle name -->
                    <div class = "col-12">
                        <label class = "w-100">
                           Middle Name
                            <input type = "text"
                                class = "form-control mt-1"
                                name = "employee_details[mname]"
                                id="mName"
                                placeholder = "Middle Name"
                                oninput="capitalizeFirstLetter(this)"
                                value="{{ old('employee_details.mname', isset($employee['mname']) && $employee['mname'] ? $employee['mname']  : '') }}"
                                required
                            >
                        </label>
                    </div>

                    <!-- last name -->
                    <div class = "col-12">
                        <label class = "w-100">
                            Last Name
                            <input type = "text"
                                class = "form-control mt-1"
                                name = "employee_details[lname]"
                                id = lName
                                placeholder = "Last Name"
                                oninput="capitalizeFirstLetter(this)"
                                value="{{ old('employee_details.lname', isset($employee['lname']) && $employee['lname']  ? $employee['lname'] : '') }}"
                                required
                            >
                        </label>
                    </div>

                    <!-- birth date -->
                    <div class = "col-12">
                        <label class = "w-100">
                            Birth Date
                            <input type = "date"
                                name = "employee_details[birthdate]"
                                class = "form-control"
                                value="{{ old('employee_details.birthdate', isset($employee['birthdate']) && $employee['birthdate'] ? $employee['birthdate'] : '') }}"
                            >
                        </label>
                    </div>

                    <!-- gender -->
                    <div class = "col-12">
                        <label class = "w-100">
                            Gender
                            <select class = "form-select" name = "employee_details[gender]" required>
                                <option value = "" selected hidden disabled>-- Select --</option>
                                @foreach(config('constants.genders') as $key => $val)
                                    <option value="{{ $key }}" {{isset($employee['gender']) && $employee['gender'] ==  $key ? 'selected' : ''}} >
                                        {{ $val }}
                                    </option>
                                @endforeach
                            </select>
                        </label>
                    </div>




                </div>
            </div>
        </div>
    </div>

    <div class = "col-sm-12 col-md-7 col-lg-9">
        <div class = "card">
            <div class = "card-header">
                <h5>Employee User Information</h5>
            </div>

            <div class ="card-body">
                <div class = "row">
                     <!-- Display Name -->
                     <div class = "col-12 col-md-4 col-lg-3">
                        <label class = "w-100">
                            Display Name
                            <input type = "text"
                                class = "form-control mt-1"
                                name = "employee_userinfo[display_name]"
                                placeholder = "Display Name"
                                id = "display_name"
                                value="{{ old('employee_userinfo.display_name', isset($employee) ? $employee['display_name']  : '') }}"
                                required
                            >
                        </label>
                    </div>

                    <!-- Email Address -->
                     <div class="col-sm-12 col-md-8 col-lg-5">
                        <label for="email" class="w-100">
                            Email
                            <input type="email"
                                class="form-control"
                                name="employee_userinfo[username]"
                                id="username"
                                placeholder="Enter your email address"
                                require
                                value="{{ old('employee_userinfo.username', isset($employee['username']) && $employee['username'] ? $employee['username']  : '') }}"
                            >
                        </label>
                    </div>
                </div>

                <div class = "row">
                    <!-- Password -->
                    <div class="col-lg-3 col-md-4 col-sm-6 mb-2">
                       <label class="w-100">
                           Password
                           <div class="input-group">
                               <input type="password"
                                   class="form-control mt-1"
                                   name="employee_userinfo[password]"
                                   id="passwordInput"
                                   placeholder="Enter Password"

                               >
                               <button type="button"
                                       class="btn btn-outline-secondary mt-1"
                                       onclick="togglePassword()"
                                       style="padding: 0 10px;">
                                   <i id="eyeIcon" class="bi bi-eye-slash"></i>
                               </button>
                           </div>
                       </label>
                    </div>

                    <!-- Confirm -->
                    <div class="col-lg-3 col-md-4 col-sm-6 mb-2">
                        <label class="w-100">
                            Confirm Password
                            <div class="input-group">
                                <input type="password"
                                    class="form-control mt-1"
                                    id="confirmPasswordInput"
                                    placeholder="Confirm Password"
                                    oninput="checkPasswordMatch()"
                                >
                            </div>
                            <small id="passwordHelp" class="form-text text-danger" style="display: none;">Passwords do not match.</small>
                        </label>
                    </div>

                     <!-- User Type -->
                     <div class = "col-lg-3 col-md-4 col-sm-6 mb-2">
                        <label class = "w-100">
                            User Type
                            <select class = "form-select" name = "employee_userinfo[user_type]" required>
                                <option value = "" selected hidden disabled>-- Select --</option>
                                @foreach(config('constants.designation') as $key => $val)
                                    <option value="{{ $key }}"  {{isset($employee['user_type']) && $employee['user_type'] ==  $key ? 'selected' : ''}} >
                                            {{ $val }}
                                    </option>
                                    <!-- <option value = "{{$key}}">{{$val}}</option> -->
                                @endforeach
                            </select>
                        </label>
                    </div>

                </div>
               <!-- #region -->

               <div class = "row">

                   <!-- Department -->
                   <div class="col-12 col-md-4 col-lg-3">
                      <label class="w-100">
                          Department
                          <select class="form-select" name="employee_details[department_id]">
                              <option value="" selected hidden disabled>-- Select --</option>
                              @if(isset($department_data) && count($department_data) > 0)  <!-- Use count() for array -->
                                  @foreach($department_data as $key => $val)
                                        <option value="{{ $val['id'] }}"
                                        {{ ($employee['department_id'] ?? null) == $val['id'] ? 'selected' : '' }}>
                                            {{ $val['name'] }}

                                        </option>
                                      <!-- <option value="{{ $val['id'] }}">{{ $val['name'] }}</option> -->
                                  @endforeach
                              @else
                                  <option value="" disabled>No department available</option>
                              @endif
                          </select>
                      </label>
                  </div>

                    <!-- Contact -->
                    <div class="col-sm-12 col-md-4 col-lg-3">
                        <label for="contact_info" class="w-100">
                            Contact No.
                            <input type="number"
                                class="form-control"
                                name="employee_details[contact]"
                                id="contact_info"
                                placeholder="Contact No."
                                value="{{ old('employee_details.contact', isset($employee['contact']) && $employee['contact'] ? $employee['contact']  : '') }}"
                                required
                            >
                        </label>
                    </div>

                    <!-- Hire Date -->
                    <div class="col-sm-12 col-md-4 col-lg-3">
                        <label for="hire_date" class="w-100">
                            Hire Date
                            <input type="date"
                                class="form-control"
                                name="employee_details[hire_date]"
                                id="hire_date"
                                placeholder="Hire Date"
                                value ="{{ old('employee_details.hire_date', isset($employee['hire_date']) && $employee['hire_date'] ? $employee['hire_date']  : '') }}"
                                required
                            >
                        </label>
                    </div>


               </div>

                <div class = "row">

                    <!-- Status Value -->
                     <!-- Status Value -->
                    <div class="col-lg-3 col-md-4 col-sm-6 mb-2">
                        <label class="w-100">
                            Status
                            <select class="form-select" name="employee_userinfo[status]" required>
                                <option value="" hidden disabled>-- Select --</option>
                                @foreach(config('constants.status_value') as $key => $val)
                                    <option value="{{ $key }}"  {{isset($employee['status_value']) && $employee['status_value'] ==  $key ? 'selected' : ''}} >
                                         {{ $val }}
                                    </option>
                                @endforeach
                            </select>
                        </label>
                    </div>


                    <!-- civil status -->
                    <div class = "col-sm-12 col-md-4 col-lg-3">
                        <label class = "w-100">
                            Civil Status
                            <select class = "form-select" name = "employee_details[civil_status]" required>
                                <option value = "" selected hidden disabled>-- Select --</option>
                                @foreach(config('constants.civil_status') as $key => $val)
                                    <option value="{{ $key }}"
                                            {{ ($employee['civil_status'] ?? null) == $key ? 'selected' : '' }}>
                                            {{ $val }}
                                    </option>
                                    <!-- <option value = "{{$key}}">{{$val}}</option> -->
                                @endforeach
                            </select>
                        </label>
                    </div>

                    <!-- nationality -->


                     <div class="col-sm-12 col-md-4 col-lg-3">
                        <label class="w-100">
                        Nationality
                                <select class="form-select custom-select-arrow" name="employee_details[nationality]">
                                    <option value="" selected hidden disabled>-- Select --</option>
                                    @foreach(config('constants.nationality_value') as $key => $val)
                                        <option value="{{ $key }}" {{isset($employee['nationality']) && $employee['nationality'] ==  $key ? 'selected' : ''}} >
                                              {{ $val }}
                                        </option>
                                        <!-- <option value = "{{$key}}">{{$val}}</option> -->
                                    @endforeach
                                </select>
                        </label>
                    </div>

                </div>

            </div>
        </div>

        <!-- present address details -->
        <div class = "card">
            <div class = "card-header">
                <h5 class = "card-title">Present Address Details</h5>
            </div>
            <div class = "card-body">
                <div class = "row">
                    <!-- Present address province -->
                    <div class="col-sm-12 col-md-5 col-lg-4">
                        <label class="w-100">
                            Province
                            <select class="form-select" id="employee_present_province" name="employee_details[present_province_id]" data-target="employee_present_city" required>
                                <option value="" selected hidden disabled>-- Select --</option>
                                @if(isset($province_data) && $province_data)
                                    @foreach($province_data as $val)
                                        <option value="{{ $val['id'] }}"
                                            {{ (isset($employee['present_province_id']) && $employee['present_province_id']  == $val['id']) ? 'selected' : '' }}>
                                            {{ $val['name'] }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                        </label>
                    </div>

                    <!-- Present address city -->
                    <div class="col-sm-12 col-md-4 col-lg-3">
                        <label class="w-100">
                            City
                            <select class="form-select" id="employee_present_city" name="employee_details[present_city_id]" required>
                                <option value="" selected hidden disabled>-- Select --</option>

                            </select>
                        </label>
                    </div>

                    <!-- present address zipcode -->
                    <div class = "col-sm-12 col-md-4 col-lg-3">
                        <label class = "w-100">
                            Zipcode
                            <input type = "number"
                                class = "form-control"
                                name = "employee_details[present_postal_code]"
                                placeholder = "Zipcode"
                                value ="{{ old('employee_details.present_postal_code', $employee['present_postal_code'] ?? '') }}"
                                required
                            >
                        </label>
                    </div>

                    <div class="col-12">
                        <label class="w-100">
                            Present Address
                            <textarea class= "form-control presentAddress"
                                placeholder = "Present Address - (House/Bldg. No., St. and Brgy.)"
                                name = "employee_details[present_address]"
                                rows = "5"
                                style="text-align: left; vertical-align: top;"
                                required
                            >{{ old('employee_details.present_address', isset($employee['present_address']) ? $employee['present_address']  : '') }}
                            </textarea>
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <!-- permanent address details -->
        <div class = "card">
            <div class = "card-header">
                <h5 class = "card-title">Permanent Address Details</h5>
            </div>
            <div class = "card-body">

                <div class = "row">
                    <div class="col-sm-12 col-md-5 col-lg-4 d-flex align-items-center">
                        <!-- Checkbox -->
                         <div class="form-check">
                                <input class="form-check-input checkbox-wrapper" type="checkbox" id="copyAddress">
                                <label class="form-check-label" for="copyAddress">
                                    Same as Present Addres
                                </label>
                         </div>
                    </div>
                </div>

                <div class = "row">
                    <!-- present address province -->
                    <div class = "col-sm-12 col-md-5 col-lg-4">
                        <label class = "w-100">
                            Province
                            <select class = "form-select" id="employee_permanent_province" name = "employee_details[permanent_province_id]" data-target = "emp_permanent_city" required>
                                <option value = "" selected hidden disabled>-- Select --</option>
                                @if(isset($province_data) && $province_data)
                                    @foreach($province_data as $key  => $val)
                                        <option value="{{ $val['id'] }}"
                                            {{ (isset($employee['permanent_province_id']) && $employee['permanent_province_id']  == $val['id']) ? 'selected' : '' }}>
                                            {{ $val['name'] }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                        </label>
                    </div>

                    <!-- present address city -->
                    <div class = "col-sm-12 col-md-4 col-lg-3">
                        <label class = "w-100">
                            City
                            <select class = "form-select" id = "emp_permanent_city" name = "employee_details[permanent_city_id]" required>
                                <option value = "" selected hidden disabled>-- Select --</option>
                            </select>
                        </label>
                    </div>

                    <!-- present address zipcode -->
                    <div class = "col-sm-12 col-md-4 col-lg-3">
                        <label class = "w-100">
                            Zipcode
                            <input type = "number"
                                class = "form-control"
                                name = "employee_details[permanent_postal_code]"
                                placeholder = "Zipcode"
                                value ="{{ old('employee_details.permanent_postal_code', $employee['permanent_postal_code'] ?? '')  }}"
                                required
                            >
                        </label>
                    </div>

                    <!--Permanent Address -->
                    <div class="col-12">
                        <label class="w-100">
                            Permanent Address
                            <textarea class = "form-control permanentAddress"
                                placeholder = "Permanent Address - (House/Bldg. No., St. and Brgy.)"
                                name = "employee_details[permanent_address]"
                                rows = "5"
                                style="text-align: left; vertical-align: top;"
                                required
                            >{{ old('employee_details.permanent_address', isset($employee['permanent_address']) ? $employee['permanent_address']  : '') }}
                            </textarea>
                        </label>

                    </div>
                </div>
            </div>
        </div>

        <!-- job information -->
        <div class = "card">
            <div class = "card-header">
                <h5>Job Information</h5>
                <div class = "card-header-right">
                    <button type = "button"
                        class = "btn btn-primary btn-xs btnJobInformation"
                        data-target = ".employee-job-informations"
                    >
                        <i class = "fa fa-plus text-white"></i>
                    </button>
                </div>
            </div>

            <div class = "card-body">
                <div class = "row employee-job-informations">
                        @if(isset($empJobs) && is_array($empJobs))
                        @foreach($empJobs as $key => $val)
                        <div id="{{$val['id'] ?? '' }}" class="col-sm-12 col-md-4 col-lg-3 employee-job-information-items">
                            <div class="card"
                                data-id = "{{ $val['id'] ?? '' }}"
                                data-effective-date="{{ $val['effective_date'] ?? '' }}"
                                data-salary="{{ $val['salary'] ?? '0' }}"
                                data-job-id="{{ $val['job_id'] ?? '0' }}">

                                <input type="hidden" name="emp_job[{{$val['id'] ?? '' }}][is_old]" value="1" >
                                <input type="hidden" name="emp_job[{{$val['id'] ?? '' }}][id]" value="{{$val['id']}}">
                                <input type="hidden" name="emp_job[{{$val['id'] ?? '' }}][job_id]" value="{{$val['job_id']}}">
                                <input type="hidden" name="emp_job[{{$val['id'] ?? '' }}][effective_date]" value="{{$val['effective_date']}}">
                                <input type="hidden" name="emp_job[{{$val['id'] ?? '' }}][salary]" value="{{$val['salary']}}">

                                <div class="card-body">
                                    <button type="button" class="btn btn-danger btn-xs float-end btnRemove" data-id="{{$val['id'] ?? '' }}">
                                        <i class="fa fa-times"></i>
                                    </button>

                                    <h5>{{ $newJobs_data[$val['job_id']] ?? 'N/A' }}</h5>
                                    <p class="mb-0">Effectivity Date: {{ $val['effective_date'] ?? 'N/A' }}</p>
                                    <p class="mb-0">Salary: {{ number_format($val['salary'], 0) ?? 'N/A' }}</p>
                                </div>
                                <div class="card-footer">
                                    <p>Active</p>
                                </div>
                            </div>
                         </div>
                        @endforeach
                        @endif
                </div>
            </div>
        </div>


        <div class = "card">
            <div class = "card-header">
                <h5 class = "card-title">Educational Background</h5>
            </div>
            <div class = "card-body">
                <div class = "row">
                     <!-- School -->
                     <div class="col-sm-12 col-md-4 col-lg-5">
                        <label for="contact_info" class="w-100">
                            School Name
                            <input type="text"
                                class="form-control"
                                name="employee_educ[school_name]"
                                id="school_name"
                                placeholder="School name."
                                value ="{{ old('employee_educ.school_name', isset($employee) ? $employee['school_name']  : '') }}"
                                required
                            >
                        </label>
                    </div>

                      <!-- Date Started -->
                      <div class="col-sm-12 col-md-4 col-lg-3">
                        <label for="date_started" class="w-100">
                            Date Started
                            <input type="date"
                                class="form-control"
                                name="employee_educ[date_started]"
                                id="date_started"
                                value ="{{ old('employee_educ.date_started', isset($employee) ? $employee['date_started']  : '') }}"
                                placeholder="Date Started"
                                required
                            >
                        </label>
                    </div>

                    <!-- Date Graduate -->
                    <div class="col-sm-12 col-md-4 col-lg-3">
                        <label for="date_graduated" class="w-100">
                            Date Graduated
                            <input type="date"
                                class="form-control"
                                name="employee_educ[date_graduated]"
                                id="date_graduated"
                                value ="{{ old('employee_educ.date_graduated', isset($employee) ? $employee['date_graduated']  : '') }}"
                                placeholder="Date Graduated"
                                required
                            >
                        </label>
                    </div>

                </div>
            </div>
        </div>



    </div>
</div>


@include('employee.register.modal.job_information_modal', ['jobs_data' => $jobs_data])
<!-- job information modal -->
<div class = "job-information-modal-container">
    @include('employee.register.modal.job_information_modal')
</div>


@push('js')
<!-- Toggle Password -->
<script>
    // Toggle password visibility
    function togglePassword() {
        var passwordInput = document.getElementById("passwordInput");
        var eyeIcon = document.getElementById("eyeIcon");

        if (passwordInput.type === "password") {
            passwordInput.type = "text";
            eyeIcon.classList.remove("bi-eye-slash");
            eyeIcon.classList.add("bi-eye");
        } else {
            passwordInput.type = "password";
            eyeIcon.classList.remove("bi-eye");
            eyeIcon.classList.add("bi-eye-slash");
        }
    }

    // Check if passwords match
    function checkPasswordMatch() {
        var password = document.getElementById("passwordInput").value;
        var confirmPassword = document.getElementById("confirmPasswordInput").value;
        var passwordHelp = document.getElementById("passwordHelp");

        if (password !== confirmPassword) {
            passwordHelp.style.display = "block"; // Show mismatch warning
        } else {
            passwordHelp.style.display = "none"; // Hide warning
        }
    }
</script>

<!-- Place this script at the end of your Blade view file -->
<script>
 // Trigger file input when the upload button is clicked
 document.getElementById('uploadImageButton').onclick = function() {
        document.getElementById('imageInput').click();
    };
    // Preview the selected image
    function previewImage(event) {
        const file = event.target.files[0];
        const reader = new FileReader();

        reader.onload = function(e) {
            // Update the preview image with the selected file
            document.getElementById('profileImagePreview').src = e.target.result;
        };

        if (file) {
            reader.readAsDataURL(file);
        }
    }
</script>


<script>
    var payrollCount = 1;
    var jobInformationCount = 1;

    //Employee Province function
    // Pass the city IDs from the backend to JavaScript
    var presentCityId = "{{ $presentCityId  ?? ''  }}";
    var permanentCityId = "{{ $permanentCityId  ?? ''  }}";

    $(document).ready(function () {
        // For both present and permanent province change
        $("[name='employee_details[present_province_id]'], [name='employee_details[permanent_province_id]']").change(function() {
            let params = {
                province_id: $(this).val()
            };

            let targetData = $(this).attr("data-target"); // This is the target city dropdown (id of the dropdown)
            const dropdownName = $(this).attr("name"); // Identify which province triggered the event
            const cityId = dropdownName.includes("present") ? "present_city_id" : "permanent_city_id"; // Select city ID field

            // Get cities based on the selected province
            getCities(params, function(response) {
                if(response && response.has_error === 0){
                    const targetSelect = $("#" + targetData);
                    targetSelect.empty().append("<option value='' selected hidden disabled>-- Select --</option>");

                    if(response.html){
                        targetSelect.append(response.html); // Add options dynamically
                    }

                    // Set selected city based on the respective city ID (present or permanent)
                    setSelectedCity(targetSelect, cityId);
                }
            });
        });

        // Trigger the change event for already selected provinces if they exist
        if ($("[name='employee_details[present_province_id]']").val()) {
            $("[name='employee_details[present_province_id]']").change();
        }

        if ($("[name='employee_details[permanent_province_id]']").val()) {
            $("[name='employee_details[permanent_province_id]']").change();
        }
    });


function setSelectedCity(targetSelect, cityId) {
        let cityIdValue = cityId === "present_city_id" ? presentCityId : permanentCityId; // Determine the city ID (present or permanent)

        if (cityIdValue) {
            let found = false;
            targetSelect.find("option").each(function() {
                if ($(this).val() == cityIdValue) {
                    $(this).prop("selected", true); // Select the matching option
                    found = true;
                }
            });

            if (!found) {
                console.log("City ID not found in dropdown:", cityIdValue); // Log if city not found
            }
        }
    }

//JobInfo retrival
$(document).ready(function () {
    // Use event delegation to attach click event to dynamically generated cards
     $(document).on('click', '.employee-job-informations .card', function (event) {
        let cardElement = $(this).closest('.card');

        // Check if the clicked element is a "Remove" button or the card itself
        if ($(event.target).hasClass('btnRemove')) {

            event.stopPropagation();
            // Trigger the remove logic here
            let cardId = $(this).data('id');
            $(`#${cardId}`).fadeOut(300, function () {
                $(this).remove();
            });
        } else {
         if (cardElement.parent().hasClass('new-job-card')) {
             // Extract data from the dynamically added card
             let params = {
                 cardId: cardElement.parent().attr('id'),
                 effective_date: cardElement.find('input[name*="[effective_date]"]').val(),
                 job_id: cardElement.find('input[name*="[job_id]"]').val(),
                 salary: cardElement.find('input[name*="[salary]"]').val(),
             };

             console.log("Dynamically Added Job Information:", params);

            //  alert(params.cardId);
             // Populate modal fields with data
             $('#job_id').val(params.job_id);
             $('#effective_date').val(params.effective_date);
             $('#salary').val(params.salary);

             // Store the card ID in the hidden field
             $('#jobInformationTarget').val(params.cardId);

            // Show the "Add" button and hide the "Update" button
            $('#addJobInfo').hide();
             $('#updateJobInfo').show();

             // Set add mode flag (optional, if needed)
             $('#modalJobInformation').data('isEditMode', true);

             // Show the modal
             $('#modalJobInformation').modal('show');

         } else {
             // Handle database-generated card logic here
             let params = {
                 cardId: $(this).data('id'),
                 effective_date: $(this).data('effective-date'),
                 job_id: $(this).data('job-id'),
                 salary: $(this).data('salary'),
             };

             console.log("Database Job Information:", params);

             // Populate modal fields with data
             $('#job_id').val(params.job_id);
             $('#effective_date').val(params.effective_date);
             $('#salary').val(params.salary);

             // Store the card ID in the hidden field
             $('#jobInformationTarget').val(params.cardId);

              // Show the "Add" button and hide the "Update" button
             $('#addJobInfo').hide();
             $('#updateJobInfo').show();

             // Show the modal
             $('#modalJobInformation').modal('show');
         }
      }
    });

     // Remove Job Information
     $(document).on("click", ".btnRemove", function(event) {
        event.stopPropagation();
        // Get the unique card ID from the button's data attribute
        let cardId = $(this).data("id");

        // Remove the card with the specified ID
        $(`#${cardId}`).fadeOut(300, function() {
            $(this).remove();
        });
     });
});


function findCardByIdOrDataId(cardId) {
    let card = $('#' + cardId); // Try ID selector first
    if (card.length === 0) {
        card = $('.card[data-id="' + cardId + '"]'); // Fallback to data-id selector
    }
    return card;
}

// On "Update" button click, update the card with the new values
$(document).on('click', '#updateJobInfo', function () {
    // Get the updated values from the modal

    let updatedSalary = $('#salary').val();
    let updatedEffectiveDate = $('#effective_date').val();
    let updatedJobId = $('#job_id').val();
    let updatedJobTitle = $('#job_id option:selected').text(); // Gets the text of the selected option

    // Get the card ID stored in the hidden field
    let cardId = $('#jobInformationTarget').val();

    let card;

    if (cardId.includes('job-card')) {
        card = $('#' + cardId); // Find card using the card's ID directly
    } else {
        card = $('.card[data-id="' + cardId + '"]'); // Find the card by its data-id attribute
    }
    // alert(cardId);

    console.log("Card ID to update:", cardId);

    if (!card || card.length === 0) {
        // Log error if the card still cannot be found
        alert('Error: Could not find the card to update. Please try again.');
        console.error('Card not found with ID: ' + cardId);
        return;
    }

    // Update the card content
    card.find('.card-body h5').text(updatedJobTitle);
    card.find('.card-body .mb-0').eq(0).text('Effective Date: ' + updatedEffectiveDate);

    // Format and update the salary
    let formattedSalary = updatedSalary && !isNaN(updatedSalary)
        ? parseFloat(updatedSalary).toLocaleString('en-US')
        : '0';
    card.find('.card-body .mb-0').eq(1).text('Salary: ' + formattedSalary);

    // Update the card's data attributes in the DOM
    card.attr('data-salary', updatedSalary);
    card.attr('data-effective-date', updatedEffectiveDate);
    card.attr('data-job-id', updatedJobId);

    // Update the hidden input fields inside the card (for form submission)
    card.find('input[name$="[job_id]"]').val(updatedJobId);
    card.find('input[name$="[effective_date]"]').val(updatedEffectiveDate);
    card.find('input[name$="[salary]"]').val(updatedSalary);

    // Refresh jQuery's internal cache
    card.data('salary', updatedSalary);
    card.data('effective-date', updatedEffectiveDate);
    card.data('job-id', updatedJobId);

    // Clear modal fields
    clearJobInformationModal();

    // Close the modal
    $('#modalJobInformation').modal('hide');

});

// Set the target field to the container where you want to add the job card
$("#modalJobInformation").on("show.bs.modal", function() {
    // Set the hidden input to the target container
    $('#job_id').val('');
    $('#effective_date').val('');
    $('#salary').val('');
});


// Function to format a number as currency without the currency symbol
function formatCurrency(amount) {
    // Remove commas first if there are any
    let number = amount.replace(/,/g, '');

    // Convert to number and format as currency (without the dollar sign)
    return new Intl.NumberFormat('en-US').format(number); // No currency symbol
}


    //job information functions Rightside button--------------------------------------------------------------------------------------------------------------------------------------
    $("#tab-personal-information .btnJobInformation").on("click", function(){
        let targetField = $(this).attr("data-target");

        $("#modalJobInformation #jobInformationTarget").val(targetField);

        clearJobInformationModal();

        // Set add mode flag
        $('#modalJobInformation').data('isEditMode', false);

        showJobInformationModal();
    })

    $("#modalJobInformation .btnAdd").on("click", function(){
            let params = {
                targetField : $("#modalJobInformation #jobInformationTarget").val(),
                effective_date : $("#modalJobInformation #effective_date").val(),
                salary : $("#modalJobInformation #salary").val(),
                job_id : $("#modalJobInformation #job_id").val(),
                job_title : $("#modalJobInformation #job_id option:selected").text(),
                isEditMode: $("#modalJobInformation").data('isEditMode') // Check mode
            };

            console.log(params);

            if (params.isEditMode) {
                // Handle update logic here
                console.log("Updating existing job info...");
                updateJobInformation(params); // Create this function if needed
            } else {
                // Handle add logic here
                console.log("Adding new job info...");
                addJobInformation(params); // Existing function
            }
        })

        $("#modalJobInformation .btnCancel").on("click", function(){
        hideJobInformationModal();
    })

    function showJobInformationModal(){
        $('#addJobInfo').show();
        $('#updateJobInfo').hide();

        $("#modalJobInformation").modal("show");
    }

    function hideJobInformationModal() {

        clearJobInformationModal();
        $("#modalJobInformation").modal("hide");
    }

    function clearJobInformationModal() {
        $("#modalJobInformation #jobInformationTarget").val("");
        $("#modalJobInformation #effective_date").val("");
        $("#modalJobInformation #salary").val("");
        $("#modalJobInformation #job_id").val("");
    }

    // let jobInformationCount = 0;
    function addJobInformation(params){

        if (typeof jobInformationCount === 'undefined') {
            jobInformationCount = 0;
        }

        // Creating a unique ID for the job information card
        let uniqueId = `job-card-${Date.now()}`;

        // HTML structure for the job information card
        let card = `
            <div id="${uniqueId}" class="col-sm-12 col-md-4 col-lg-3 employee-job-information-items new-job-card">
                <div class="card">
                    <input type="hidden" name="emp_job[${jobInformationCount}][job_id]" value="${params.job_id}">
                    <input type="hidden" name="emp_job[${jobInformationCount}][effective_date]" value="${params.effective_date}">
                    <input type="hidden" name="emp_job[${jobInformationCount}][salary]" value="${params.salary}">

                    <div class="card-body">
                        <button type="button" class="btn btn-danger btn-xs float-end btnRemove" data-id="${uniqueId}">
                            <i class="fa fa-times"></i>
                        </button>
                        <h5>${params.job_title}</h5>
                        <p class="mb-0">Effective Date: ${params.effective_date}</p>
                        <p class="mb-0">Salary: ${formatSalary(params.salary)}</p>

                    </div>
                    <div class="card-footer">
                        <p>Active</p>
                    </div>
                </div>
            </div>`;

        // Append the new card to the target container
        $(".employee-job-informations").append(card);

        // Increment the job information counter
        jobInformationCount++;

        // Optionally, trigger a reflow to ensure layout updates properly
        setTimeout(function () {
            targetField[0].offsetHeight; // Trigger reflow
        }, 300);

        // Hide the modal after adding the job information
        hideJobInformationModal();
    }


    function formatSalary(salary) {
    if (!salary || isNaN(salary)) {
        return 'N/A'; // Fallback if salary is invalid
    }
    return new Intl.NumberFormat().format(salary); // Format the salary with commas
}

document.addEventListener('DOMContentLoaded', function() {
    const fname = document.getElementById('fName');
    const mname = document.getElementById('mName');
    const lname = document.getElementById('lName');
    const displayName = document.getElementById('display_name');

    function updateDisplayName() {
        const middleInitial = mname.value ? mname.value.charAt(0).toUpperCase() + '.' : ''; // Get first letter of Mname
        displayName.value = `${fname.value} ${middleInitial} ${lname.value}`.trim();
    }

    fname.addEventListener('keyup', updateDisplayName);
    mname.addEventListener('keyup', updateDisplayName);
    lname.addEventListener('keyup', updateDisplayName);
});



</script>


<script>

document.getElementById('copyAddress').addEventListener('change', function() {
    if (this.checked) {
        // Copy province
        document.getElementById('employee_permanent_province').value = document.getElementById('employee_present_province').value;

        // Copy city
        document.getElementById('emp_permanent_city').value = document.getElementById('employee_present_city').value;

        // Copy zipcode
        document.querySelector('input[name="employee_details[permanent_postal_code]"]').value =
            document.querySelector('input[name="employee_details[present_postal_code]"]').value;

        // Copy address
        document.querySelector('textarea[name="employee_details[permanent_address]"]').value =
            document.querySelector('textarea[name="employee_details[present_address]"]').value;
    } else {
        // Clear permanent address fields if unchecked
        document.getElementById('employee_permanent_province').value = "";
        document.getElementById('emp_permanent_city').value = "";
        document.querySelector('input[name="employee_details[permanent_postal_code]"]').value = "";
        document.querySelector('textarea[name="employee_details[permanent_address]"]').value = "";
    }
});

</script>

<script>
    $(document).on('click', '#fName', function() {
        // Select the text inside the salary field when clicked
        $(this).select();
    });

    $(document).on('click', '#mName', function() {
        // Select the text inside the salary field when clicked
        $(this).select();
    });

    $(document).on('click', '#lName', function() {
        // Select the text inside the salary field when clicked
        $(this).select();
    });

    $(document).on('click', '#display_name', function() {
        // Select the text inside the salary field when clicked
        $(this).select();
    });

    $(document).on('click', '#username', function() {
        // Select the text inside the salary field when clicked
        $(this).select();
    });


    $(document).on('click', '#school_name', function() {
        // Select the text inside the salary field when clicked
        $(this).select();
    });

    function capitalizeFirstLetter(input) {
        const value = input.value;
        input.value = value.charAt(0).toUpperCase() + value.slice(1);
    }
</script>
@endpush