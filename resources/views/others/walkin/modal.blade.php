<!-- resources/views/others/walkin/modal.blade.php -->
<div class="modal fade" id="walkinModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="walkinForm">
        <div class="modal-header">
          <h5 class="modal-title" id="walkinModalLabel">Add Walk-in</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body">
          <input type="hidden" id="walkinId" name="id">

          <div class="mb-2">
            <label>Date</label>
            <input type="date" class="form-control" id="date" name="date" required>
          </div>
          <div class="mb-2">
            <label>Last Name</label>
            <input type="text" class="form-control" id="last_name" name="last_name" required>
          </div>
          <div class="mb-2">
            <label>First Name</label>
            <input type="text" class="form-control" id="first_name" name="first_name" required>
          </div>
          <div class="mb-2">
            <label>M.I</label>
            <input type="text" class="form-control" id="middle_initial" name="middle_initial">
          </div>
          <div class="mb-2">
            <label>Amount Withdrawn</label>
            <input type="number" class="form-control" id="amount_withdrawn" name="amount_withdrawn" required>
          </div>
          <div class="mb-2">
            <label>Charge</label>
            <input type="number" class="form-control" id="charge" name="charge" readonly>
          </div>
          <div class="mb-2">
            <label>Net Proceeds</label>
            <input type="number" class="form-control" id="net_proceeds" name="net_proceeds" readonly>
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
document.addEventListener('DOMContentLoaded', () => {

    const amountField = document.getElementById('amount_withdrawn');
    const chargeField = document.getElementById('charge');
    const netField = document.getElementById('net_proceeds');

    amountField.addEventListener('input', () => {
        const amount = parseFloat(amountField.value) || 0;

        // Compute charge (3% of amount)
        const charge = amount * 0.03;

        // Compute net proceeds
        const net = amount - charge;

        // Update fields
        chargeField.value = charge.toFixed(2);
        netField.value = net.toFixed(2);
    });

});
</script>