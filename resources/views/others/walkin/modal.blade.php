<!-- resources/views/others/walkin/modal.blade.php -->
<div class="modal fade" id="walkinModal" tabindex="-1">
 <div class="modal-dialog">
    <div class="modal-content">
      <form id="walkinForm">

        <input type="hidden" id="service_charge_percent" value="{{ $serviceCharge }}">
        <input type="hidden" name="no_service_charge" id="no_service_charge_value" value="0">

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

          <div class="form-check mb-2">
            <input class="form-check-input" type="checkbox" id="no_service_charge">
            <label class="form-check-label" for="no_service_charge">
              No Service Charge
            </label>
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
    const noChargeCheckbox = document.getElementById('no_service_charge');
    const noChargeValue = document.getElementById('no_service_charge_value');

    const percentField = document.getElementById('service_charge_percent');
    const servicePercent = parseFloat(percentField.value) || 0;

    function recalcFromCharge() {
        const amount = parseFloat(amountField.value) || 0;
        const charge = parseFloat(chargeField.value) || 0;
        netField.value = (amount - charge).toFixed(2);
    }

    function recalcAutoCharge() {
        const amount = parseFloat(amountField.value) || 0;
        const charge = amount * (servicePercent / 100);
        chargeField.value = charge.toFixed(2);
        recalcFromCharge();
    }

    amountField.addEventListener('input', () => {
        noChargeCheckbox.checked ? recalcNoCharge() : recalcAutoCharge();
    });

    chargeField.addEventListener('input', recalcFromCharge);

    function recalcNoCharge() {
          const amount = parseFloat(amountField.value) || 0;
    chargeField.value = '0.00';
    netField.value = amount.toFixed(2);
    chargeField.disabled = true;
        // chargeField.value = '0.00';
        // chargeField.disabled = true;
        // recalcFromCharge();
    }

    noChargeCheckbox.addEventListener('change', () => {
        noChargeValue.value = noChargeCheckbox.checked ? 1 : 0;

        if (noChargeCheckbox.checked) {
            recalcNoCharge();
        } else {
            chargeField.disabled = false;
            recalcAutoCharge();
        }
    });


    // const amountField = document.getElementById('amount_withdrawn');
    // const chargeField = document.getElementById('charge');
    // const netField = document.getElementById('net_proceeds');

    // amountField.addEventListener('input', () => {
    //     const amount = parseFloat(amountField.value) || 0;

        // Compute charge (3% of amount)
        // const charge = amount * 0.03;

        // Compute net proceeds
        // const net = amount - charge;

        // Update fields
    //     chargeField.value = charge.toFixed(2);
    //     netField.value = net.toFixed(2);
    // });

});
</script>