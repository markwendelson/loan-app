
<div class="modal fade" id="modalTransaction" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addModalLabel">Transaction Information</h5>
            </div>

            <div class="modal-body">
                <form id="frmTransactionModal" class="row g-3 needs-validation custom-input" novalidate="">
                    @csrf
                    <input type="hidden" value="" name="id" id="id">
                    <div class="col-12">
                        <label class="w-100" for="transaction_date">Transaction Date</label>
                        <input type="date" class="form-control form-control-sm" name="transaction_date" placeholder="Transaction Date" required />
                    </div>

                    <div class="col-12">
                        <label class="w-100" for="transaction_type">Transaction Type</label>
                        <select name="transaction_type" id="transaction_type" class="form-control">
                            <option value="deposit">deposit</option>
                            <option value="withdraw">withdraw</option>
                        </select>

                    <div class="col-12">
                        <label class="w-100" for="amount">Amount</label>
                        <input type="number" class="form-control form-control-sm" name="amount" placeholder="Amount" required />
                    </div>

                    <div class="col-12">
                        <label for="description">Description</label>
                        <input class="form-control" type="text" name="description" id="description" placeholder="Description"/>
                    </div>

                    <div class="col-12">
                        <label for="notes">Notes</label>
                        <textarea class="form-control" name="notes" id="notes" cols="30" rows="5"></textarea>
                    </div>

                    <div class="col-12">
                        <label for="classifications">Classifications</label>
                        <input class="form-control" type="text" name="classifications" id="classifications" placeholder="Classifications"/>
                    </div>

                </form>
            </div>

            <div class="modal-footer gap-1">
                <button type="button" class="btn btn-primary btnSubmit w-100"><i class="fa fa-save"></i> Submit</button>
                <button type="button" class="btn btn-ligh w-100" onclick="hideModal('modalTransaction')"><i class="fa fa-close"></i> Cancel</button>
            </div>
        </div>
    </div>
</div>


