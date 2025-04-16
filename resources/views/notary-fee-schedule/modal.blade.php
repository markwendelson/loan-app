
<div class="modal fade" id="modalNotaryFeeSchedule" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addModalLabel">Notar Fee Schedule Information</h5>
            </div>

            <div class="modal-body">
                <form id="frmNotaryFeeScheduleModal" class="row g-3 needs-validation custom-input" novalidate="">
                    @csrf
                    <input type="hidden" value="" name="id" id="id">
                    <div class="col-12">
                        <label class="w-100" for="min_amount">Loan Min. Amount</label>
                        <input type="text" class="form-control form-control-sm" name="min_amount" placeholder="Loan Min. Amount" required />
                    </div>

                    <div class="col-12">
                        <label class="w-100" for="max_amount">Loan Max. Amount</label>
                        <input type="text" class="form-control form-control-sm" name="max_amount" placeholder="Loan Max. Amount" required />
                    </div>

                    <div class="col-12">
                        <label class="w-100">Type</label>
                        <select class="form-select form-select-sm" name="type">
                            <option value="" disabled>-- Select --</option>
                                <option value="fixed">Fixed</option>
                                <option value="percentage">Percentage</option>
                        </select>
                    </div>

                    <div class="col-12">
                        <label class="w-100" for="fee">Fee</label>
                        <input type="text" class="form-control form-control-sm" name="fee" placeholder="Type" required />
                    </div>

                </form>
            </div>

            <div class="modal-footer gap-1">
                <button type="button" class="btn btn-primary btnSubmit w-100"><i class="fa fa-save"></i> Submit</button>
                <button type="button" class="btn btn-ligh w-100" onclick="hideModal('modalNotaryFeeSchedule')"><i class="fa fa-close"></i> Cancel</button>
            </div>
        </div>
    </div>
</div>


