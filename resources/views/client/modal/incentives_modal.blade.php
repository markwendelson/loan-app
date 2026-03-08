<div class="modal fade" id="modalIncentives" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5>Incentives Information</h5>
            </div>
            <div class="modal-body">
                <div class="row">
                    <form id="frmIncentivesModal">
                        <input type="hidden" id="client_id" name="client_id" value="{{ $client->id }}" />

                        <div class="col-12">
                            <label class="w-100">
                                Transaction date
                                <input type="date"
                                    class="form-control"
                                    id="transaction_date"
                                    name="transaction_date"
                                    placholder="Transaction date"
                                    required
                                >
                            </label>
                        </div>

                        <div class="col-12">
                            <label class="w-100">
                                Description
                                <input type="text"
                                    id="description"
                                    name="description"
                                    class="form-control"
                                    placholder="Description"
                                    required
                                >
                            </label>
                        </div>

                        <div class="col-12">
                            <label class="w-100">
                                Amount
                                <input type="number"
                                    id="amount"
                                    name="amount"
                                    class="form-control"
                                    placholder="Amount"
                                    required
                                >
                            </label>
                        </div>
                    </form>

                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-primary btn-sm btnSubmit" ><i class="fa fa-save"></i> Add</button>
                <button type="button" class="btn btn-danger btn-sm btnCancel" onclick="hideIncentivesModal(this)"><i class="fa fa-close"></i> Cancel</button>
            </div>
        </div>
    </div>
</div>
