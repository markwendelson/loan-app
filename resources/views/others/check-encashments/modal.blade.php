<!-- others/check-encashments/modal.blade.php -->
<div class="modal fade" id="checkModal" tabindex="-1" aria-labelledby="checkModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <div class="modal-header">
            <h5 class="modal-title" id="checkModalLabel">Add Check</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <form id="checkForm" enctype="multipart/form-data">
                    <div class="row">
                        <!-- Hidden fields -->
                        <input type="hidden" id="checkId" name="id">
                        <input type="hidden" id="oldPicture" name="oldPicture">

                        <!-- branch_id -->
                        <input type="hidden" name="branch_id" value="{{ auth()->user()->current_branch_id }}">

                        <!-- Date -->
                        <div class="col-lg-4">
                            <label class="w-100">
                                Date <span class="txt-danger">*</span>
                            </label>
                            <input type="date"
                                id="transaction_date"
                                name="transaction_date"
                                class="form-control"
                                required
                            >
                        </div>

                        <div class="clearfix"></div>

                        <!-- lastn name -->
                        <div class="col-lg-4">
                            <label class="w-100">
                                Last Name <span class="txt-danger">*</span>
                                <input type="text"
                                    id="lastName"
                                    name="last_name"
                                    class="form-control"
                                    placeholder="Enter last name"
                                    required
                                >
                            </label>
                        </div>

                        <!-- first name -->
                        <div class="col-lg-4">
                            <label class="w-100">
                                First Name <span class="txs-danger">*</span>
                                <input type="text"
                                    id="firstName"
                                    name="first_name"
                                    class="form-control"
                                    placeholder="Enter first name"
                                    required
                                >
                            </label>
                        </div>

                        <div class="col-lg-4">
                            <label class="w-100">
                                M.I
                                <input type="text"
                                    id="middleName"
                                    name="middle_name"
                                    class="form-control"
                                    placeholder="e.g D"
                                    maxlength="1"
                                >
                            </label>
                        </div>

                        <div class="col-lg-6">
                            <label class="w-100">
                                Check No <span class="txt-danger">*</span>
                                <input type="text"
                                    id="check_no"
                                    name="check_no"
                                    class="form-control"
                                    placeholder="Enter check no"
                                    required


                                >
                            </label>
                        </div>

                        <div class="col-lg-6">
                            <label class="w-100">
                                Bank Name <span class="txt-danger">*</span>
                                <select class="form-select" name="bank_id">
                                    <option value="">-- Select Bank --</option>
                                    @if(isset($banks) && $banks)
                                        @foreach($banks as $key => $val)
                                            <option value="{{ $val->id }}">{{ $val->name }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </label>
                        </div>

                        <div class="col-lg-12">
                            <div class="form-check checkbox checkbox-primary mb-0">
                                <input class="form-check-input" id="noServiceCharge" name="no_service_charge" type="checkbox" value="1">
                                <label class="form-check-label mb-1" for="noServiceCharge"><small>No Service Charge</small></label>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <label class="w-100">
                                Check Amount <span class="txt-danger">*</span>
                                <input type="text"
                                    id="checkAmount"
                                    name="checkAmount"
                                    class="form-control text-end"
                                    placeholder="0.00"
                                    required
                                >

                            </label>
                        </div>

                        <div class="col-lg-4">
                            <label class="w-100">
                                Charge
                                <input type="text"
                                    id="charge"
                                    name="charge"
                                    class="form-control text-end"
                                    placeholder="0.00"
                                >
                            </label>
                        </div>

                        <div class="col-lg-4">
                            <label class="w-100">
                                Net Proceeds <span class="txt-danger">*</span>
                                <input type="text"
                                    id="netProceeds"
                                    name="net_proceeds"
                                    class="form-control text-end"
                                    placeholder="0.00"
                                    required
                                >
                            </label>
                        </div>

                        <!-- No Service Charge -->
                        {{-- <div class="row mb-3">
                            <div class="col-12">
                                <div class="form-check">
                                    <input class="form-check-input"
                                        type="checkbox"
                                        id="noServiceCharge"
                                        name="no_service_charge"
                                    >
                                    <label class="form-check-label fw-semibold" for="noServiceCharge">
                                        No Service Charge
                                    </label>
                                </div>
                            </div>
                        </div> --}}

                        <!-- ID Type / Number -->
                        <div class="col-lg-6">
                            <label class="w-100">
                                ID Type <span class="txt-danger">*</span>
                                <select id="idType" name="id_type" class="form-select" required>
                                    <option value="">Select ID Type</option>
                                    <option value="Driver's License">Driver's License</option>
                                    <option value="Passport">Passport</option>
                                    <option value="Postal ID">Postal ID</option>
                                    <option value="National ID">National ID</option>
                                    <option value="UMID">UMID</option>
                                </select>
                            </label>

                        </div>

                        <div class="col-lg-6">
                            <label class="w-100">
                                ID Number <span class="txt-danger">*</span>
                                <input type="text"
                                    id="idNumber"
                                    name="id_number"
                                    class="form-control"
                                    required
                                >
                            </label>
                        </div>

                        <!-- ID Picture -->
                        <div class="col-lg-12">
                            <label class="w-100">
                                ID Picture
                                <input type="file"
                                    id="idPicture"
                                    name="id_picture"
                                    class="form-control" accept="image/*"
                                >
                            </label>

                            <div class="mt-2">
                                <img id="idPicturePreview"
                                    class="img-thumbnail"
                                    width="120"
                                    style="display:none;"
                                >
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-primary btnSubmit"><i class="fa fa-save me-1"></i>Submit Check</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i class="fa fa-times me-1"></i>Cancel</button>
            </div>
        </div>
    </div>
</div>

<script>
    // Set default date to today
    document.addEventListener("DOMContentLoaded", function() {
        const today = new Date().toISOString().split('T')[0]; // format: YYYY-MM-DD
        document.getElementById("transaction_date").value = today;
    });

    // Show image preview when a new file is selected
    document.getElementById("idPicture").addEventListener("change", function(e) {
        const file = e.target.files[0];
        const preview = document.getElementById("idPicturePreview");
        if (file) {
            preview.src = URL.createObjectURL(file);
            preview.style.display = "block";
        } else {
            preview.src = '';
            preview.style.display = "none";
        }
    });



    // Capitalize first letter of Last Name and First Name
    function capitalizeFirstLetter(input) {
        input.value = input.value
            .split(' ')
            .map(word => word.charAt(0).toUpperCase() + word.slice(1).toLowerCase())
            .join(' ');
    }


    function calculateCharges() {
        const amount = parseFloat(unformatNumber($('#checkAmount').val())) || 0;
        const noCharge = $('#noServiceCharge').is(':checked');

        if (amount <= 0) {
            $('#charge').val('');
            $('#netProceeds').val('');
            return;
        }

        // Override: No Service Charge
        if (noCharge) {
            $('#charge').val(formatNumberWithCommas(0));
            $('#netProceeds').val(formatNumberWithCommas(amount));
            return;
        }

        const percentage = getServiceChargePercentage(amount);

        let charge = amount * (percentage / 100);
        charge = Math.round(charge * 100) / 100;

        const net = Math.round((amount - charge) * 100) / 100;

        $('#charge').val(formatNumberWithCommas(charge));
        $('#netProceeds').val(formatNumberWithCommas(net));
    }


    // Event listeners
    document.getElementById("lastName").addEventListener("input", function() {
        capitalizeFirstLetter(this);
    });

    document.getElementById("firstName").addEventListener("input", function() {
        capitalizeFirstLetter(this);
    });

    // Uppercase Middle Initial
    document.getElementById("middleName").addEventListener("input", function() {
        this.value = this.value.toUpperCase();
    });
</script>
