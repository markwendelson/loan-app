<div class="modal modal-xl fade" id="modalCoMaker" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Co-Maker Information</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
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
                                            <img id="comaker_photo_preview" class="img-thumbnail" src="{{url('/assets/images/user/6.jpg')}}">
                                        </div>
                                        <div class="col-12">
                                            <input type="file" id="comaker[photo]" name="comaker[photo]" accept="image/*" style="display: none;" />
                                            <button id="btn_comaker_img_upload" type="button" class="btn btn-primary mt-1"><i class="fa fa-camera"></i> Upload Image</button>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <!-- relation to client -->
                                    <div class="col-12">
                                        <label class="w-100">
                                            <select class="form-select" name="comaker[relation_client]">
                                                <option value="" selected hidden disabled>-- Select --</option>
                                                @foreach(config('constants.client_relation') as $key => $val)
                                                <option value="{{$key}}">{{$val}}</option>
                                                @endforeach
                                            </select>
                                        </label>
                                    </div>

                                    <!-- first name -->
                                    <div class="col-12">
                                        <label class="w-100">First Name</label>
                                        <input type="text" class="form-control mt-1" name="comaker[firstname]" placeholder="First Name" />
                                    </div>

                                    <!-- middle name -->
                                    <div class="col-12">
                                        <label class="w-100">Middle Name</label>
                                        <input type="text" class="form-control mt-1" name="comaker[middlename]" placeholder="Middle Name" />
                                    </div>

                                    <!-- last name -->
                                    <div class="col-12">
                                        <label class="w-100">Last Name</label>
                                        <input type="text"
                                            class="form-control mt-1"
                                            name="comaker[lastname]"
                                            placeholder="Last Name"
                                        />
                                    </div>

                                    <!-- birth date -->
                                    <div class="col-12">
                                        <label class="w-100">
                                            Birth Date
                                            <input type="date"
                                                name="comaker[birthdate]"
                                                class="form-control"
                                            >
                                        </label>
                                    </div>

                                    <!-- gender -->
                                    <div class="col-12">
                                        <label class="w-100">
                                            Gender
                                            <select class="form-select" name="comaker[gender]" required>
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
                        <!-- additional details -->
                        <div class="card">
                            <div class="card-header">
                                <h5>Additional Details</h5>
                            </div>
                            <div class ="card-body">
                                <div class="row">

                                    <div class="col-sm-12 col-md-8 col-lg-5">
                                        <label class="w-100" for="comaker[email]">Email</label>
                                        <input type="email" name="comaker[email]" class="form-control" value="" required />
                                    </div>

                                    <!-- Contact -->
                                    <div class="col-sm-12 col-md-4 col-lg-3">
                                        <label class="w-100">
                                            Contact No.
                                            <div class="input-group">
                                                <span class="input-group-text">{{config('constants.default_area_code')}}</span>
                                                <input type="number"
                                                    class="form-control"
                                                    name="comaker[contact_info]"
                                                    placeholder="Contact No."
                                                    oninput="this.value=this.value.slice(0, 10)"
                                                    required
                                                >
                                            </div>

                                        </label>
                                    </div>
                                </div>

                                <div class="row">
                                    <!-- suffix -->
                                    <div class="col-sm-12 col-md-4 col-lg-3">
                                        <label class="w-100">
                                            Suffix
                                                <select class="form-select custom-select-arrow" name="comaker[suffix]" id="comaker[suffix]">
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
                                            <select class="form-select" name="comaker[civil_status]" required>
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
                                            <select class="form-select" name="comaker[nationality]" required>
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
                                            <select class="form-select" name="comaker[present_province]" data-target="comaker_present_city"required>
                                                <option value="" selected hidden disabled>-- Select --</option>
                                                    @foreach($provinces as $province)
                                                        <option value="{{$province->id}}">{{$province->name}}</option>
                                                    @endforeach
                                            </select>
                                        </label>
                                    </div>

                                    <!-- present address city -->
                                    <div class="col-sm-12 col-md-4 col-lg-3">
                                        <label class="w-100">
                                            City
                                            <select class="form-select" id="comaker_present_city" name="comaker[present_city]" required>
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
                                                name="comaker[present_zipcode]"
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
                                                name="comaker[present_address]"
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
                                            <select class="form-select" name="comaker[permanent_province]" data-target="comaker_permanent_city" required>
                                                <option value="" selected hidden disabled>-- Select --</option>
                                                    @foreach($provinces as $province)
                                                        <option value="{{$province->id}}">{{$province->name}}</option>
                                                    @endforeach
                                            </select>
                                        </label>
                                    </div>

                                    <!-- present address city -->
                                    <div class="col-sm-12 col-md-4 col-lg-3">
                                        <label class="w-100">
                                            City
                                            <select class="form-select" id="comaker_permanent_city" name="comaker[permanent_city]" required>
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
                                                name="comaker[permanent_zipcode]"
                                                placeholder="Zipcode"
                                                required
                                            >
                                        </label>
                                    </div>

                                    <!--Permanent Address -->
                                    <div class="col-12">
                                        <label class="w-100">
                                            Permanent Address
                                            <textarea class="form-control permanentAddress"
                                                placeholder="Permanent Address"
                                                name="comaker[permanent_address]"
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
                                        data-target=".comaker-job-informations"
                                    >
                                        <i class="fa fa-plus text-white"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row comaker-job-informations">

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
                                        data-target=".comaker-payroll-accounts"
                                    >
                                        <i class="fa fa-plus text-white"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row comaker-payroll-accounts">
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

                    </div>
                </div>

            </div>

            <div class="modal-footer">
                <div class="row">
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary btn-add-co-maker"><i class="fa fa-save"></i> Add Co-Maker</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


@push('js')
<script>
    $('.btn-add-co-maker').on('click', function() {
        console.log('test')
    })
</script>
@endpush