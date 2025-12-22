<!-- others/check-encashments/modal.blade.php -->
<div class="modal fade" id="checkModal" tabindex="-1" aria-labelledby="checkModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <form id="checkForm" enctype="multipart/form-data">
        <div class="modal-header">
          <h5 class="modal-title" id="checkModalLabel">Add Check</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body">

    <!-- Hidden fields -->
    <input type="hidden" id="checkId" name="id">
    <input type="hidden" id="oldPicture" name="oldPicture">

    <!-- Date -->
    <div class="row g-3 mb-3">
        <div class="col-12 col-md-6">
            <label class="form-label">Date</label>
            <input type="date" id="transDate" name="transDate" class="form-control" required>
        </div>
    </div>

    <!-- Name fields -->
    <div class="row g-3 mb-3">
        <div class="col-12 col-md-4">
            <label class="form-label">Last Name</label>
            <input type="text" id="lastName" name="last_name" class="form-control" required>
        </div>

        <div class="col-12 col-md-5">
            <label class="form-label">First Name</label>
            <input type="text" id="firstName" name="first_name" class="form-control" required>
        </div>

        <div class="col-6 col-md-3">
            <label class="form-label">M.I</label>
            <input type="text" id="middleName" name="middle_name" class="form-control text-uppercase" maxlength="1">
        </div>
    </div>

    <!-- Check No / Bank -->
    <div class="row g-3 mb-3">
        <div class="col-12 col-md-6">
            <label class="form-label">Check No</label>
            <input type="text" id="checkNo" name="checkNo" class="form-control" required>
        </div>

        <div class="col-12 col-md-6">
            <label class="form-label">Bank Name</label>
            <input type="text" id="bankName" name="bankName" class="form-control" required>
        </div>
    </div>

    <!-- Amount / Charges -->
    <div class="row g-3 mb-2">
        <div class="col-12 col-md-4">
            <label class="form-label">Check Amount</label>
            <input type="text" id="checkAmount" name="checkAmount" class="form-control" required>
        </div>

        <div class="col-12 col-md-4">
            <label class="form-label">Charge</label>
            <input type="text" id="charge" name="charge" class="form-control">
        </div>

        <div class="col-12 col-md-4">
            <label class="form-label">Net Proceeds</label>
            <input type="text" id="netProceeds" name="net_proceeds" class="form-control" required>
        </div>
    </div>

    <!-- No Service Charge -->
    <div class="row mb-3">
        <div class="col-12">
            <div class="form-check">
                <input class="form-check-input" type="checkbox"
                       id="noServiceCharge" name="no_service_charge">
                <label class="form-check-label fw-semibold" for="noServiceCharge">
                    No Service Charge
                </label>
            </div>
        </div>
    </div>

    <!-- ID Type / Number -->
    <div class="row g-3 mb-3">
        <div class="col-12 col-md-6">
            <label class="form-label">ID Type</label>
            <select id="idType" name="id_type" class="form-select" required>
                <option value="">Select ID Type</option>
                <option value="Driver's License">Driver's License</option>
                <option value="Passport">Passport</option>
                <option value="Postal ID">Postal ID</option>
                <option value="National ID">National ID</option>
                <option value="UMID">UMID</option>
            </select>
        </div>

        <div class="col-12 col-md-6">
            <label class="form-label">ID Number</label>
            <input type="text" id="idNumber" name="id_number" class="form-control" required>
        </div>
    </div>

    <!-- ID Picture -->
    <div class="row g-3 mb-2">
        <div class="col-12">
            <label class="form-label">ID Picture</label>
            <input type="file" id="idPicture" name="id_picture"
                   class="form-control" accept="image/*">

            <div class="mt-2">
                <img id="idPicturePreview"
                     class="img-thumbnail"
                     width="120"
                     style="display:none;">
            </div>
        </div>
    </div>

</div>


    

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">Save</button>
        </div>

      </form>
    </div>
  </div>
</div>

<script>


// Set default date to today
document.addEventListener("DOMContentLoaded", function() {
    const today = new Date().toISOString().split('T')[0]; // format: YYYY-MM-DD
    document.getElementById("transDate").value = today;
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
