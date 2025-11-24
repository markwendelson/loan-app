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

          <input type="hidden" id="checkId" name="id">
          <input type="hidden" id="oldPicture" name="oldPicture">

          <div class="row">
            
            <div class="col-md-6 mb-3">
              <label>Date</label>
              <input type="date" id="transDate" name="transDate" class="form-control" required>
            </div>

            <div class="col-md-6 mb-3">
              <label>Check No</label>
              <input type="text" id="checkNo" name="checkNo" class="form-control" required>
            </div>

            <div class="col-md-6 mb-3">
              <label>Bank Name</label>
              <input type="text" id="bankName" name="bankName" class="form-control" required>
            </div>

            <div class="col-md-6 mb-3">
              <label>Check Amount</label>
              <input type="text" id="checkAmount" name="checkAmount" class="form-control" required>
              <!-- <input type="number" step="0.01" id="checkAmount" name="checkAmount" class="form-control" required> -->
            </div>

            <div class="col-md-6 mb-3">
              <label>Charge</label>
              <input type="text" id="charge" name="charge" class="form-control">
              <!-- <input type="number" step="0.01" id="charge" name="charge" class="form-control"> -->
            </div>

            <div class="col-md-6 mb-3">
              <label>Net Proceeds</label>
              <input type="text" id="netProceeds" name="net_proceeds" class="form-control" required>
              <!-- <input type="number" step="0.01" id="netProceeds" name="net_proceeds" class="form-control" required> -->
            </div>

            <div class="col-md-6 mb-3">
              <label>ID Type</label>
              <select id="idType" name="id_type" class="form-control" required>
                <option value="">Select ID Type</option>
                <option value="Driver's License">Driver's License</option>
                <option value="Passport">Passport</option>
                <option value="Postal ID">Postal ID</option>
                <option value="National ID">National ID</option>
                <option value="UMID">UMID</option>
              </select>
            </div>

            <div class="col-md-6 mb-3">
              <label>ID Number</label>
              <input type="text" id="idNumber" name="id_number" class="form-control" required>
            </div>

            <div class="col-md-12 mb-3">
              <label>ID Picture</label>
              <input type="file" id="idPicture" name="id_picture" class="form-control" accept="image/*">

              <div class="mt-2">
                <img id="idPicturePreview" 
                     src="" 
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


// Automatically calculate charge based on check amount
document.getElementById("checkAmount").addEventListener("input", function(e) {
    const amount = parseFloat(e.target.value) || 0;
    let charge = 0;

    if (amount <= 3000) {
        charge = amount * 0.035;  // 3.5%
    } else if (amount <= 5000) {
        charge = amount * 0.03;   // 3%
    } else {
        charge = amount * 0.02;   // 2%
    }

    // Round to 2 decimal places
    charge = Math.round(charge * 100) / 100;

    // Set charge field
    document.getElementById("charge").value = charge;

    // Update net proceeds
    const netProceeds = amount - charge;
    document.getElementById("netProceeds").value = Math.round(netProceeds * 100) / 100;
});
</script>
