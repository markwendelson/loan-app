<div class="modal fade" id="vendorModal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Vendor Information</h4>
            </div>
            <div class="modal-body">
                <form id="frmVendor">
                    <div class="row">
                        <!-- name -->
                        <div class="col-lg-8">
                            <label class="w-100">
                                Name <span class="text-danger">*</span>
                                <input type="text" 
                                    class="form-control"
                                    placeholder="Juan Dela Cruz..."
                                >
                            </label>
                        </div>

                        <!-- TIN -->
                        <div class="col-lg-4">
                            <label class="w-100">
                                TIN <span class="text-danger">*</span>
                                <input type="text"
                                    class="form-control"
                                    placeholder="000-000-000..."
                                >
                            </label>
                        </div>

                        <!-- vendor type -->
                        <div class="col-lg-5">
                            <label class="w-100">
                                Type
                                <select class="form-select">
                                    <option value="" selected hidden disabled>-- Type --</option>
                                    @foreach(config('constants.vendor_types') as $key => $val)
                                        <option value="{{ $key }}">{{ $val }}</option>
                                    @endforeach
                                </select>
                            </label>
                        </div>

                        <!-- website -->
                        <div class="col-lg-7">
                            <label class="w-100">
                                Website
                                <input type="text"
                                    class="form-control"
                                    placeholder="ex., https://www.example.com"
                                >
                            </label>
                        </div>

                        <!-- contact no -->
                        <div class="col-lg-4">
                            <label class="w-100">
                                Contact No
                                <input type="tel"
                                    class="form-control"
                                    placeholder="ex. +639123456789"
                                >
                            </label>
                        </div>
                        
                        <!-- email -->
                        <div class="col-lg-4">
                            <label class="w-100">
                                Email
                                <input type="email"
                                    class="form-control"
                                    placeholder="ex. juandelacruz@gmail.com"
                                >
                            </label>
                        </div>

                        <!-- fax -->
                        <div class="col-lg-4">
                            <label class="w-100">
                                Fax
                                <input type="tel"
                                    class="form-control"
                                    placeholder="ex., +1 (555) 123-4567"
                                >
                            </label>
                        </div>

                        <!-- country code -->
                        <div class="col-lg-4">
                            <label class="w-100">
                                Country
                                <select class="form-select">
                                    <option value="" selected hidden disabled>-- Country --</option>
                                    @if(isset($countryCode) && $countryCode)
                                        @foreach($countryCode as $key => $val)
                                            <option value="{{ $val['id'] }}"  {{ $val['id'] == config('constants.default_country_id') ? 'selected' : ''; }}>{{ $val['country_name'] }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </label>
                        </div>

                        <!-- billing address -->
                        <div class="col-lg-12">
                            <label class="w-100">
                                Billing Address
                                <textarea class="form-control" placeholder="House No., Street, Barangay, City/Municipality, Province, Postal Code"></textarea>
                            </label>
                        </div>

                        <!-- shipping address -->
                        <div class="col-lg-12">
                            <label class="w-100">
                                Shipping Address
                                <textarea class="form-control" placeholder="House No., Street, Barangay, City/Municipality, Province, Postal Code"></textarea>
                            </label>
                        </div>
                        
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary"><i class="fa fa-save"></i> Submit Vendor Data</button>
                <button type="button" class="btn btn-danger btnCancel"><i class="fa fa-times"></i> Cancel</button>
            </div>
        </div>
    </div>
</div>