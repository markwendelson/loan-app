
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
                       <label for="disbursed_date">Disbursement Date</label>
                        <input type="date" class="form-control" name="disbursed_date" id="disbursed_date" value="{{ date('Y-m-d') }}"/>
                    </div>

                    <div class="col-12">
                        <label for="amount">Amount</label>
                        <input type="text" class="form-control form-control-sm" name="amount" placeholder="0.00" readonly/>
                    </div>

                     <div class="col-12">
                       <label for="mode_of_payment">Mode of Payment</label>
                            @php
                                $modes = config('settings.mode_of_payment');
                            @endphp
                        <select name="mode_of_payment" id="mode_of_payment" class="form-select form-select-sm">
                            @foreach($modes as $key => $mode)
                                @if($key == 'atm_withdrawal')
                                @else
                                <option value="{{ $key }}">{{ $mode }}</option>
                                @endif
                            @endforeach
                            <option value="authorized_person">Authorized Person</option>
                        </select>
                    </div>

                    <div class="form-group attachment d-none">
                        <label class="form-label" for="attachment">Attachment</label>
                        <input type="file" class="form-control" id="attachment" name="attachment" accept="image/*,.pdf" />
                    </div>

                    {{-- <div class="col-12">
                        <label class="w-100" for="transaction_code">Transaction Code</label>
                        <input type="text" class="form-control form-control-sm" name="transaction_code" placeholder="Transaction Code" />
                    </div> --}}

                    <div class="col-12">
                        <label class="w-100" for="notes">Notes</label>
                        <input type="text" class="form-control form-control-sm" name="notes" placeholder="Notes" />
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


