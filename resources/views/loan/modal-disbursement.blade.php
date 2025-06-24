
<div class="modal fade" id="modalDisbursement" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-sm   ">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addModalLabel">Disbursement Information</h5>
            </div>

            <div class="modal-body">
                <form id="frmDisbursementModal" class="row g-3 needs-validation custom-input" novalidate="">
                    @csrf
                    <input type="hidden" value="" name="id" id="id">
                    <div class="col-12">
                       <label for="disbursement_date">Disbursement Date</label>
                        <input type="date" class="form-control" name="disbursement_date" id="disbursement_date" value="{{ date('Y-m-d') }}"/>
                    </div>

                    <div class="col-12">
                        <label class="w-100" for="notes">Notes</label>
                        <input type="text" class="form-control form-control-sm" name="notes" placeholder="Notes" required />
                    </div>
                </form>
            </div>

            <div class="modal-footer gap-1">
                <button type="button" class="btn btn-primary btnSubmit w-100"><i class="fa fa-save"></i> Submit</button>
                <button type="button" class="btn btn-light w-100" onclick="hideModal('modalDisbursement')"><i class="fa fa-close"></i> Cancel</button>
            </div>
        </div>
    </div>
</div>


