
<div class="row">
    <div class="col-sm-12 col-md-5 col-lg-3">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Personal Information</h5>
            </div>
            <div class="card-body">
                <div class="image-container text-center mb-3">
                    <div class="row">
                        <div class="col-12">
                            <img id="client_photo_preview" class="img-thumbnail" src="{{url('/assets/images/user/6.jpg')}}">
                        </div>
                        <div class="col-12">
                            <input type="file" id="client_details[photo]" name="client_details[photo]" accept="image/*" style="display: none;" />
                            <button id="btn_img_upload" type="button" class="btn btn-primary mt-1"><i class="fa fa-camera"></i> Upload Image</button>
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
                                    @foreach($branches as $branch)
                                        <option value="{{$branch->id}}">{{$branch->name}}</option>
                                    @endforeach
                            </select>
                        </label>
                    </div>

                    <!-- first name -->
                    <div class="col-12">
                        <label class="w-100">
                            First Name
                            <input type="text"
                                class="form-control mt-1"
                                name="client_details[firstname]"
                                placeholder="First Name"
                            >
                        </label>
                    </div>

                    <!-- middle name -->
                    <div class="col-12">
                        <label class="w-100">
                           Middle Name
                            <input type="text"
                                class="form-control mt-1"
                                name="client_details[middlename]"
                                placeholder="Middle Name"
                            >
                        </label>
                    </div>

                    <!-- last name -->
                    <div class="col-12">
                        <label class="w-100">
                            Last Name
                            <input type="text"
                                class="form-control mt-1"
                                name="client_details[lastname]"
                                placeholder="Last Name"
                            >
                        </label>
                    </div>

                    <!-- birth date -->
                    <div class="col-12">
                        <label class="w-100">
                            Birth Date
                            <input type="date"
                                name="client_details[birthdate]"
                                class="form-control"
                            >
                        </label>
                    </div>

                    <!-- gender -->
                    <div class="col-12">
                        <label class="w-100">
                            Gender
                            <select class="form-select" name="client_details[gender]" required>
                                <option value="" selected hidden disabled>-- Select --</option>
                                @foreach(config('constants.genders') as $key => $val)
                                    <option value="{{$key}}">{{$val}}</option>
                                @endforeach
                            </select>
                        </label>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-12 col-md-7 col-lg-9">

        <div class="card">
            <div class="card-header">
                <h5>Additional Details</h5>
            </div>
            <div class ="card-body">
                <div class="row">

                    <!-- Email -->
                    <div class="col-sm-12 col-md-8 col-lg-5">
                        <label class="w-100">
                            Email
                            <input type="email"
                                class="form-control"
                                name="client_details[email]"
                                placeholder="Enter your email address"
                                require
                            >
                        </label>
                    </div>

                    <!-- Contact -->
                    <div class="col-sm-12 col-md-4 col-lg-3">
                        <label class="w-100">
                            Contact No.
                            <div class="input-group">
                                <span class="input-group-text">{{config('constants.default_area_code')}}</span>
                                <input type="number" class="form-control" name="client_details[contact_info]" placeholder="Contact No." oninput="this.value=this.value.slice(0, 10)" required />
                            </div>
                        </label>
                    </div>
                </div>

                <div class="row">
                    <!-- suffix -->
                    <div class="col-sm-12 col-md-4 col-lg-3">
                        <label class="w-100">
                            Suffix
                                <select class="form-select custom-select-arrow" name="client_details[suffix]" id="client_details[suffix]">
                                    <option value="" selected hidden disabled>-- Select --</option>
                                    @foreach(config('constants.suffix_list') as $key => $val)
                                        <option value="{{$key}}">{{$val}}</option>
                                    @endforeach
                                </select>
                        </label>
                    </div>

                    <!-- civil status -->
                    <div class="col-sm-12 col-md-4 col-lg-3">
                        <label class="w-100">
                            Civil Status
                            <select class="form-select" name="client_details[civil_status]" required>
                                <option value="" selected hidden disabled>-- Select --</option>
                                @foreach(config('constants.civil_status') as $key => $val)
                                    <option value="{{$key}}">{{$val}}</option>
                                @endforeach
                            </select>
                        </label>
                    </div>

                    <!-- nationality -->
                    <div class="col-sm-12 col-md-4 col-lg-3">
                        <label class="w-100">
                            Nationality
                            <select class="form-select" name="client_details[nationality]" required>
                                <option value="" selected hidden disabled>-- Select --</option>
                                    @foreach($nationalities as $nationality)
                                        <option value="{{$nationality->id}}">{{$nationality->nationality}}</option>
                                    @endforeach
                            </select>
                        </label>
                    </div>
                </div>

            </div>
        </div>

        <!-- present address details -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Present Address Details</h5>
            </div>
            <div class="card-body">
                <div class="row">

                    <!-- present address province -->
                    <div class="col-sm-12 col-md-5 col-lg-4">
                        <label class="w-100">
                            Province
                            <select class="form-select" name="client_details[present_province]" data-target="client_present_city"required>
                                <option value="" selected hidden disabled>-- Select --</option>
                                    @foreach($provinces as $key  => $val)
                                        <option value="{{$val['id']}}">{{$val['name']}}</option>
                                    @endforeach
                            </select>
                        </label>
                    </div>

                    <!-- present address city -->
                    <div class="col-sm-12 col-md-4 col-lg-3">
                        <label class="w-100">
                            City
                            <select class="form-select" id="client_present_city" name="client_details[present_city]" required>
                                <option value="" selected hidden disabled>-- Select --</option>
                            </select>
                        </label>
                    </div>

                    <!-- present address zipcode -->
                    <div class="col-sm-12 col-md-4 col-lg-3">
                        <label class="w-100">
                            Zipcode
                            <input type="number"
                                class="form-control"
                                name="client_details[present_zipcode]"
                                placeholder="Zipcode"
                                required
                            >
                        </label>
                    </div>

                    <div class="col-12">
                        <label class="w-100">
                            Present Address
                            <textarea class= "form-control presentAddress"
                                placeholder="Present Address"
                                name="client_details[present_address]"
                                rows="5"
                                required
                            ></textarea>
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <!-- permanent address details -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Permanent Address Details</h5>
            </div>
            <div class="card-body">
                <div class="row">

                    <!-- present address province -->
                    <div class="col-sm-12 col-md-5 col-lg-4">
                        <label class="w-100">
                            Province
                            <select class="form-select" name="client_details[permanent_province]" data-target="client_permanent_city" required>
                                <option value="" selected hidden disabled>-- Select --</option>
                                    @foreach($provinces as $key  => $val)
                                        <option value="{{$val['id']}}">{{$val['name']}}</option>
                                    @endforeach
                            </select>
                        </label>
                    </div>

                    <!-- present address city -->
                    <div class="col-sm-12 col-md-4 col-lg-3">
                        <label class="w-100">
                            City
                            <select class="form-select" id="client_permanent_city" name="client_details[permanent_city]" required>
                                <option value="" selected hidden disabled>-- Select --</option>
                            </select>
                        </label>
                    </div>

                    <!-- present address zipcode -->
                    <div class="col-sm-12 col-md-4 col-lg-3">
                        <label class="w-100">
                            Zipcode
                            <input type="number"
                                class="form-control"
                                name="client_details[permanent_zipcode]"
                                placeholder="Zipcode"
                            >
                        </label>
                    </div>

                    <!--Permanent Address -->
                    <div class="col-12">
                        <label class="w-100">
                            Permanent Address
                            <textarea class="form-control permanentAddress"
                                placeholder="Permanent Address"
                                name="client_details[permanent_address]"
                                rows="5"
                                required
                            ></textarea>
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <!-- job information -->
        <div class="card">
            <div class="card-header">
                <h5>Job Information</h5>

                <div class="card-header-right">
                    <button type="button"
                        class="btn btn-primary btn-xs btnJobInformation"
                        data-target=".client-job-informations"
                    >
                        <i class="fa fa-plus text-white"></i>
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="row client-job-informations">

                    <!-- job information item -->
                    {{-- <div class="col-sm-12 col-md-4 col-lg-3 client-job-information-items">
                        <div class="card">
                            <div class="card-body">
                                <button type="button" class="btn btn-danger btn-xs float-end"><i class="fa fa-times"></i></button>
                                <h5>Web Developer</h5>
                                <p class="mb-0">Office: Forty Degress Celcius Inc.</p>
                                <p class="mb-0">Salary: 40,000</p>
                            </div>
                            <div class="card-footer">
                                <p>Active</p>
                            </div>
                        </div>
                    </div> --}}
                </div>
            </div>
        </div>

        <!--Payroll Account Information -->
        <div class="card">
            <div class="card-header">
                <h5>Payroll Account</h5>
                <div class="card-header-right">
                    <button type="button"
                        class="btn btn-primary btn-xs btnPayrollAccount"
                        data-target=".client-payroll-accounts"
                    >
                        <i class="fa fa-plus text-white"></i>
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="row client-payroll-accounts">
                    {{-- <div class="col-sm-12 col-md-4 col-lg-3 client-payroll-items">
                        <div class="card">
                            <div class="card-body">
                                <button type="button" class="btn btn-danger btn-xs float-end"><i class="fa fa-times"></i></button>

                                <h5><strong>Hello world</strong></h5>
                                <p class="mb-0">Bank: Bank of The Philippine Islands</p>
                                <p class="mb-0">Account No: 123334411288</p>
                                <p>Account Name: Al Mausisa</p>
                            </div>
                            <div class= "card-footer">
                                <p>Active</p>
                            </div>
                        </div>
                    </div>  --}}
                </div>
            </div>
        </div>

        <!-- interview notes -->
        <div class="card">
            <div class="card-header">
                <h5>Interview Notes:</h5>
            </div>
            <div class="card-body">
                <textarea id="interviewNotes"class="form-control" rows="5"></textarea>
            </div>
        </div>

        <!-- Recommendations -->
        <div class="card">
            <div class="card-header">
                <h5>Recommendations:</h5>
            </div>
            <div class="card-body">
                <textarea class="form-control" rows="5"></textarea>
            </div>
        </div>

    </div>
</div>
