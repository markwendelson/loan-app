<div class="modal fade" id="vendorModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Vendor Information</h4>
            </div>
            <div class="modal-body">
                <form id="frmVendor">
                    <!-- vendor id -->
                    <input type="hidden" name="vendor_id">

                    <div class="row">
                        <!-- name -->
                        <div class="col-lg-8">
                            <label class="w-100">
                                Name <span class="text-danger">*</span>
                                <input type="text" 
                                    class="form-control"
                                    placeholder="Juan Dela Cruz..."
                                    name="name"
                                    required
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
                                    name="tin"
                                    required
                                >
                            </label>
                        </div>

                        <!-- vendor type -->
                        <div class="col-lg-5">
                            <label class="w-100">
                                Type
                                <select class="form-select" name="vendor_type">
                                    <option value=""  selected hidden disabled>-- Type --</option>
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
                                    name="website"
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
                                    name="contact_no"
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
                                    name="email"
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
                                    name="fax"
                                >
                            </label>
                        </div>

                        <!-- country code -->
                        <div class="col-lg-4">
                            <label class="w-100">
                                Country
                                <select class="form-select" name="country_id">
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
                                <textarea class="form-control" name="billing_address" placeholder="House No., Street, Barangay, City/Municipality, Province, Postal Code"></textarea>
                            </label>
                        </div>

                        <!-- shipping address -->
                        <div class="col-lg-12">
                            <label class="w-100">
                                Shipping Address
                                <textarea class="form-control" name="shipping_address" placeholder="House No., Street, Barangay, City/Municipality, Province, Postal Code"></textarea>
                            </label>
                        </div>
                        
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary btnSubmit" data-submit-url="" data-method=""><i class="fa fa-save"></i> Submit Vendor Data</button>
                <button type="button" class="btn btn-danger btnCancel"><i class="fa fa-times"></i> Cancel</button>
            </div>
        </div>
    </div>
</div>