
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
                            @if(isset($client))
                            <img id="client_photo_preview" class="img-thumbnail" src="{{ $client->details->photo ? asset('storage/'.$client->details->photo) : url('/assets/images/user/6.jpg')}}">
                            @else
                                <img id="client_photo_preview" class="img-thumbnail" src="{{url('/assets/images/user/6.jpg')}}">
                            @endif
                        </div>
                        <div class="col-12">
                            <input type="file" id="client_details[photo]" name="client_details[photo]" accept="image/*" style="display: none;" />
                            <button id="btn_img_upload" type="button" class="btn btn-primary mt-1 btnUploadImage"><i class="fa fa-camera"></i> Upload Image</button>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <!-- branch -->
                    <div class="col-12">
                        <label class="w-100">
                            Processing Branch
                                @php
                                    if(isset($client)) {
                                        $branch_id = $client->branch_id;
                                    } else {
                                        $branch_id = auth()->user()->current_branch_id;
                                    }
                                @endphp
                            <select class="form-select" name="client_details[branch_id]" required>
                                <option value="" selected hidden disabled>-- Select --</option>
                                    @foreach($branches as $branch)
                                        <option value="{{$branch->id}}"
                                            {{ (string) old('client_details.branch_id', $branch_id ?? '') === (string) $branch->id ? 'selected' : '' }}
                                        >{{$branch->name}}</option>
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
                                value="{{$client->details->firstname ?? old('client_details.firstname')}}"
                                required
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
                                value="{{$client->details->middlename ?? old('client_details.middlename')}}"
                                required
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
                                value="{{$client->details->lastname ?? old('client_details.lastname')}}"
                                required
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
                                value="{{$client->details->birthdate ?? old('client_details.birthdate')}}"
                                required
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
                                    <option value="{{$key}}" {{ (string) old('client_details.gender',$client->details->gender ?? '') === (string) $key ? 'selected' : ''}}>{{$val}}</option>
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
                                required
                                value="{{$client->details->email ?? old('client_details.email')}}"
                            >
                        </label>
                    </div>

                    <!-- Contact -->
                    <div class="col-sm-12 col-md-4 col-lg-3">
                        <label class="w-100">
                            Contact No.
                            <div class="input-group">
                                <span class="input-group-text">{{config('constants.default_area_code')}}</span>
                                <input type="number" class="form-control" name="client_details[contact_info]" placeholder="Contact No." oninput="this.value=this.value.slice(0, 10)" required value="{{$client->details->contact_info ?? old('client_details.contact_info')}}"/>
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
                                        <option value="{{$key}}" {{ (string) old('client_details.suffix',$client->details->suffix ?? '') == (string) $key ? 'selected' : ''}}>{{$val}}</option>
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
                                    <option value="{{$key}}" {{ (string) old('client_details.civil_status',$client->details->civil_status ?? '') == (string) $key ? 'selected' : ''}}>{{$val}}</option>
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
                                        <option value="{{ $nationality->id }}" 
                                            {{ (string) old('client_details.nationality', $client->details->nationality ?? config('constants.default_country_id')) == (string) $nationality->id ? 'selected' : '' }}>
                                            {{ $nationality->nationality }}
                                        </option>
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
                            Province {{ old('client_details.present_province', $client->details->present_province_id ?? '') }}
                            <select class="form-select" name="client_details[present_province]" data-target="client_present_city" required>
                                <option value="" selected hidden disabled>-- Select --</option>
                                    @foreach($provinces as $key  => $val)
                                        <option value="{{$val['id']}}" {{ old('client_details.present_province', $client->details->present_province_id ?? '') == $val['id'] ? 'selected' : ''}}>{{$val['name']}}</option>
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
                                value="{{$client->details->present_zipcode ?? old('client_details.present_zipcode')}}"
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
                            >{{$client->details->present_address ?? old('client_details.present_address')}}</textarea>
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

                    <div class="form-check checkbox checkbox-primary mb-0">
                        <input class="form-check-input" name="client_details[same_as_present_address]" id="client_details[same_as_present_address]" type="checkbox" {{ old('client_details.same_as_present_address', isset($client) ? $client->details->sameAsPresentAddressAttribute() : false) ? 'checked' : '' }}/>
                        <label class="form-check-label" for="client_details[same_as_present_address]">Same as Present Address</label>
                    </div>

                    <div class="row permanent-address">
                        <!-- present address province -->
                        <div class="col-sm-12 col-md-5 col-lg-4">
                            <label class="w-100">
                                Province
                                <select class="form-select" name="client_details[permanent_province]" data-target="client_permanent_city" required>
                                    <option value="" selected hidden disabled>-- Select --</option>
                                        @foreach($provinces as $key  => $val)
                                            <option value="{{$val['id']}}" {{ old('client_details.permanent_province',$client->details->permanent_province_id ?? '') == $val['id'] ? 'selected' : ''}}>{{$val['name']}}</option>
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
                                    value="{{$client->details->permanent_zipcode ?? old('client_details.permanent_zipcode')}}"
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
                                >{{$client->details->permanent_address ?? old('client_details.permanent_address')}}</textarea>
                            </label>
                        </div>
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

                    @isset($client)
                        @foreach($client->jobs as $client_job)
                            <div class="col-sm-12 col-md-4 col-lg-3 client-job-information-items">
                                <div class="card">
                                    <div class="card-body">
                                        <button type="button" class="btn btn-danger btn-xs float-end"><i class="fa fa-times"></i></button>
                                        <h5>{{$client_job->job->title}}</h5>
                                        <p class="mb-0">Office: {{$client_job->office_name}}</p>
                                        <p class="mb-0">Salary: {{$client_job->salary}}</p>
                                    </div>
                                    <div class="card-footer">
                                        <p>{{$client_job->status === 1 ? 'Active' : 'Inactive'}}</p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @endisset
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
                    <input type="hidden" value="" name="client_payroll_account[deleted_id]">
                    @isset($client)
                        @foreach($client->bankAccount as $account)
                            <div class="col-sm-12 col-md-4 col-lg-3 client-payroll-items">
                                <div class="card" data-id="{{$account->id}}">
                                    <div class="card-body">
                                        @if(isset($editMode) && $editMode)
                                        <button type="button" class="btn btn-danger btn-xs float-end btnRemovePayrollAccount"><i class="fa fa-times"></i></button>
                                        @endif

                                        <h5><strong>{{$account->bank->name}}</strong></h5>
                                        <p class="mb-0">Account No: {{$account->account_no}}</p>
                                        <p class="mb-0">Account Name: {{$account->account_name}}</p>

                                        @if(isset($account->account_expiry) && $account->account_expiry)
                                        <p class="mb-0">Account Expiry: {{ date('M d, Y', strtotime($account->account_expiry)) }}</p>
                                        @endif

                                        @if(isset($account->is_primary))
                                        <p>Account: {{ $account->is_primary === 1 ? 'Primary' : 'Secondary' }}</p>
                                        @endif
                                    </div>
                                    <div class= "card-footer">
                                        <p>{{$account->is_active === 1 ? 'Active' : 'Inactive'}}</p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @endisset
                </div>
            </div>
        </div>

        <!-- interview notes -->
        <div class="card">
            <div class="card-header">
                <h5>Interview Notes:</h5>
            </div>
            <div class="card-body">
                @if(isset($client))
                <textarea id="interviewNotes" class="form-control" rows="5" name="client_details[notes]">{{$client->details->notes ?? old('client_details.notes')}}</textarea>
                @else
                <textarea id="interviewNotes" class="form-control" rows="5" name="client_details[notes]">{{old('client_details.notes')}}</textarea>
                @endif
            </div>
        </div>

        <!-- Recommendations -->
        <div class="card">
            <div class="card-header">
                <h5>Recommendations:</h5>
            </div>
            <div class="card-body">
                @if(isset($client))
                <textarea class="form-control" rows="5" name="client_details[recommendations]">{{$client->details->recommendations ?? old('client_details.recommendations')}}</textarea>
                @else
                <textarea class="form-control" rows="5" name="client_details[recommendations]">{{old('client_details.recommendations')}}</textarea>
                @endif
            </div>
        </div>

    </div>
</div>
