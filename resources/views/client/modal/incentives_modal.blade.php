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
                        <input type="hidden" id="incentive_id" name="incentive_id" />

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
                                <input type="text"
                                    id="amount"
                                    name="amount"
                                    class="form-control decimal-only"
                                    placholder="Amount"
                                    required
                                    onblur="numberFormat(this)"
                                >
                            </label>
                        </div>

                        <div class="col-12" id="mode_of_payment_container">
                            <div class="form-group">
                                <label class="form-label" for="mode_of_payment">Mode of Payment</label>
                                @php
                                    $modes = config('settings.mode_of_payment');
                                @endphp
                                <select name="mode_of_payment" id="mode_of_payment" class="form-control form-control-sm">
                                    @foreach($modes as $key => $mode)
                                        <option value="{{ $key }}">{{ $mode }}</option>
                                    @endforeach
                                    <option value="authorized_person">Authorized Person</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group attachment d-none">
                            <label class="form-label" for="attachment">Attachment</label>
                            <input type="file" class="form-control" id="attachment" name="attachment" accept="image/*,.pdf" />
                        </div>

                        <div class="col-12" id="mop_transfer_container">
                            <div class="form-group">
                                <label class="form-label" for="transaction_code">Transaction Code</label>
                                <input type="text" class="form-control form-control-sm" name="transaction_code" id="transaction_code" placeholder="Transaction Code"/>
                            </div>
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
@push('js')
<script>
    $(function() {
        $("#mode_of_payment").on("change", function() {
            $('.attachment').toggleClass('d-none', $(this).val() !== 'authorized_person');
        }).trigger("change");
    });
</script>
@endpush