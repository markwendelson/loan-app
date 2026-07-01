<!-- resources/views/others/walkin/modal.blade.php -->
<div class="modal fade" id="walkinModal" tabindex="-1">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="walkinModalLabel">Add Walk-in</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
			</div>

			<div class="modal-body">
				<form id="walkinForm">
					<input type="hidden" id="service_charge_percent" value="{{ $serviceCharge }}">
					<input type="hidden" name="no_service_charge" id="no_service_charge_value" value="0">

					<input type="hidden" id="walkinId" name="id">

					<div class="txt-primary fw-semibold bg-primary-subtle p-2 rounded"><i class="fa fa-calendar me-1"></i>PERSONAL INFORMATION</div>

					<div class="row mt-3">
						<div class="col-lg-3">
							<label class="w-100">
								Date <span class="txt-danger">*</span>
								<input type="date" 
									class="form-control" 
									id="date" 
									name="date" 
									required
								>

							</label>
						</div>

						<div class="col-lg-3">
							<label class="w-100">
								Last name <span class="txt-danger">*</span>
								<input type="text" 
									class="form-control" 
									id="last_name" 
									name="last_name" 
									placeholder="Enter last name"
									required
								>

							</label>
						</div>

						<div class="col-lg-3">
							<label class="w-100">
								First Name <span class="txt-danger">*</span>
								<input type="text" 
									class="form-control" 
									id="first_name" 
									name="first_name"
									placeholder="Enter first name"
									required
								>

							</label>
						</div>
						
						<div class="col-lg-2">
							<label class="w-100">
								M.I.
							<input type="text" 
								class="form-control" 
								id="middle_initial" 
								name="middle_initial"
								placeholder="Enter M.I."
							>

							</label>
						</div>
					</div>

					<div class="txt-primary fw-semibold bg-primary-subtle p-2 rounded my-3"><i class="fa fa-money me-1"></i>TRANSACTION DETAILS</div>

					<div class="row">
						<div class="col-lg-12">
							
							<div class="row align-items-center">
								<div class="col-lg-6">
									<label class="w-100">
										Amount Withdawn <span class="txt-danger">*</span>
										<input type="number" 
											class="form-control text-end" 
											id="amount_withdrawn" 
											name="amount_withdrawn" 
											placeholder="0.00"
											required
										>
									</label>
								</div>

								<div class="col-lg-6">
									<div class="form-check checkbox checkbox-primary mb-0">
										<input class="form-check-input" id="no_service_charge" type="checkbox">
										<label class="form-check-label mb-1" for="no_service_charge"><small>No Service Charge</small></label>
									</div>
								</div>
							</div>
						</div>

						<div class="col">
							<div class="row">
								<div class="col-lg-6">
									<label class="w-100">
										Charge
										<input type="number" 
											class="form-control text-end" 
											id="charge" 
											name="charge" 
											placeholder="0.00"
											readonly
										>
									</label>
								</div>
								<div class="col-lg-6">
									<label class="w-100">
										Net Proceeds
										<input type="number" 
											class="form-control text-end" 
											id="net_proceeds" 
											name="net_proceeds"
											placeholder="0.00"
											readonly
										>
									</label>
								</div>
							</div>
						</div>
					</div>
				</form>

			</div>

			<div class="modal-footer">
				<button type="button" class="btn btn-primary btnSubmit"><i class="fa fa-save me-1"></i>Submit Walkin</button>
				<button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i class="fa fa-times me-1"></i>Cancel</button>
			</div>
		</div>
    </div>
</div>


<script>
	
	document.addEventListener('DOMContentLoaded', () => {

		$("#amount_withdrawn").on("change", function(e) {
			$("#no_service_charge").is(":checked") ? recalcNoCharge() : recalcAutoCharge();
		});

		$("charge").on("change", function(e) {
			recalcFromCharge();
		})

		//handle checkbox change event
		$("#no_service_charge").change(function(e){
			e.preventDefault();

			if($(this).is(":checked")) {
				recalcNoCharge();
			}else{
				$("#charge").prop("disabled", false);
				recalcAutoCharge();
			}
		})
	});

	function recalcFromCharge() {
		const amount = parseFloat($("#amount_withdrawn").val()) || 0;
		const charge = parseFloat($("#charge").val()) || 0;
		$("#net_proceeds").val((amount - charge).toFixed(2));
	}

	function recalcAutoCharge() {
		const amount = parseFloat($("#amount_withdrawn").val()) || 0;
		const charge = amount * ($("#service_charge_percent").val() / 100);
		$("#charge").val(charge.toFixed(2));
		recalcFromCharge();
	}

	function recalcNoCharge() {
		const amount = parseFloat($("#amount_withdrawn").val()) || 0;
		$("#charge").val("0.00")
		$("#net_proceeds").val(amount.toFixed(2));

		$("#charge").prop("disabled", true);
		chargeField.disabled = true;
	}
</script>