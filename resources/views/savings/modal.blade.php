
<div class="modal fade" id="modalTransaction" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addModalLabel">Transaction Information</h5>
            </div>

            <div class="modal-body">
                <form id="frmTransactionModal" class="g-3 needs-validation custom-input" novalidate="">
                    @csrf
                    <input type="hidden" value="" name="id" id="id">
                    <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label" for="transaction_date">Transaction Date</label>
                                    <input type="date" class="form-control form-control-sm" name="transaction_date" placeholder="Transaction Date" value="{{ date('Y-m-d') }}" required />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label" for="transaction_type">Transaction Type</label>
                                    <select name="transaction_type" id="transaction_type" class="form-control form-control-sm">
                                        <option value="">-select-</option>
                                        @can('deposit-transactions')
                                        <option value="deposit">amount withdrawn</option>
                                        @endcan
                                        @can('withdraw-transactions')
                                        <option value="withdraw">payment transaction</option>
                                        @endcan
                                        @can('cash-transactions')
                                        <option value="deposit">cash</option>
                                        @endcan
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6" id="mode_of_payment_container">
                                    <div class="form-group">
                                        <label class="form-label" for="mode_of_payment">Mode of Payment</label>
                                        @php
                                            $modes = config('settings.mode_of_payment');
                                        @endphp
                                        <select name="mode_of_payment" id="mode_of_payment" class="form-control form-control-sm">
                                            @foreach($modes as $key => $mode)
                                                <option value="{{ $key }}">{{ $mode }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6" id="mop_transfer_container" style="display: none;">
                                    <div class="form-group">
                                        <label class="form-label" for="transaction_code">Transaction Code</label>
                                        <input type="text" class="form-control form-control-sm" name="transaction_code" id="transaction_code" placeholder="Transaction Code"/>
                                    </div>
                                </div>
                            </div>
                        <div class="col-6 deposit-withdraw">
                            <div class="form-group">
                                <label class="form-label" for="amount">Amount</label>
                                <input type="number" class="form-control form-control-sm" name="amount" placeholder="Amount" required min="0" autocomplete="off"/>
                            </div>
                        </div>
                        <div class="col-6 deposit-withdraw">
                            <div class="form-group">
                                <label class="form-label" for="classifications">Classifications</label>
                                <select class="form-control form-control-sm" name="classifications" id="classifications">
                                    <option value="">-select-</option>
                                    <option value="Salary">Salary</option>
                                    <option value="Receivable">Receivable</option>
                                    <option value="Pension">Pension</option>
                                    <option value="Allotment">Allotment</option>
                                    <option value="Cash">Cash</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-12 deposit-withdraw">
                            <div class="form-group">
                                <label class="form-label" for="description">Description</label>
                                <input class="form-control form-control-sm" type="text" name="description" id="description" placeholder="Description"/>
                            </div>
                        </div>
                        <div class="withdraw">
                            <div class="row mb-3">
                                <div class="col-12 form-group">
                                    <label class="form-label" for="loan_id">Select Loan</label>
                                    <select name="loan_id" id="loan_id" class="form-control form-control-sm">
                                        <option value="">-select-</option>
                                        @foreach($loans as $loan)
                                            <option value="{{$loan->id}}"
                                                data-loan-product-id="{{$loan->loan_product_id}}"
                                                data-principal="{{$loan->principal}}"
                                                data-interest-rate="{{$loan->interest_rate}}"
                                                data-term="{{$loan->loan_duration}}"
                                                data-loan-type="{{Str::slug($loan->loanProduct->loanType->name)}}"
                                            >{{$loan->is_special ? 'Special Loan | '.$loan->description .' | ' .$loan->loan_no : 'Principal Loan | '.$loan->loan_no}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4 form-group">
                                    <label class="form-label" for="interest">Interest</label>
                                    <input type="number" class="form-control form-control-sm" name="interest" min="0" readonly/>
                                </div>
                                <div class="col-md-8 form-group">
                                    <label class="form-label" for="interest_period" class="form-label">Interest Period</label>
                                    <input type="text" name="interest_period" id="interest_period" class="form-control form-control-sm"/>
                                </div>

                                {{-- <div class="col-md-4 form-group">
                                    <label class="form-label" for="interest_month" class="form-label">Month</label>
                                    <select name="interest_month" id="interest_month" class="form-control form-control-sm">
                                        <option value="" disabled>Select</option>
                                        @foreach (month_names() as $number => $name)
                                            <option value="{{ $number }}">{{ $name }}</option>
                                        @endforeach

                                    </select>
                                </div>
                                <div class="col-md-4 form-group">
                                    <label class="form-label" for="interest_year" class="form-label">Year</label>
                                    <input type="text" class="form-control form-control-sm" id="interest_year" name="interest_year">
                                </div> --}}
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-4 form-group">
                                    <label class="form-label" for="insurance">Insurance</label>
                                    <input type="number" class="form-control form-control-sm" name="insurance" min="0"/>
                                </div>
                                <div class="col-md-4 form-group">
                                    <label class="form-label" for="insurance_month" class="form-label">Month</label>
                                    <select name="insurance_month" id="insurance_month" class="form-control form-control-sm my-insurance">
                                        <option value="" disabled>Select</option>
                                        @foreach (month_names() as $number => $name)
                                            <option value="{{ $number }}"  {{ $number == date('m') ? 'selected' : ''}}>{{ $name }}</option>
                                        @endforeach

                                    </select>
                                </div>
                                <div class="col-md-4 form-group">
                                    <label class="form-label" for="insurance_year" class="form-label">Year</label>
                                    <input type="text" class="form-control form-control-sm my-insurance" id="insurance_year" name="insurance_year" value="{{date('Y')}}" min="0">
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <div class="form-group mb-2">
                                        <label class="form-label" for="principal">Principal</label>
                                        <input type="number" class="form-control form-control-sm" name="principal" min="0" readonly/>
                                    </div>
                                    <div class="form-group mb-2">
                                        <label class="form-label" for="membership_fee">Membership Fee</label>
                                        <input type="number" class="form-control form-control-sm" name="membership_fee" min="0"/>
                                    </div>
                                    <div class="form-check">
                                        <input type="checkbox" name="is_early_settlement" id="is_early_settlement" class="form-check-input" />
                                        <label class="form-check-label" for="is_early_settlement"><small>Early Settlement</small></label>
                                    </div>
                                    <div class="special-loan-group form-check">
                                        <input type="checkbox" name="include_special_loan_balance" id="include_special_loan_balance" class="form-check-input" />
                                        <label class="form-check-label" for="include_special_loan_balance"><small>Include Special Loan Balance</small></label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label" for="due_change">Change</label>
                                        <input type="number" class="form-control form-control-sm" name="due_change" readonly min="0"/>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <div class="modal-footer gap-1">
                <button type="button" class="btn btn-primary btnSubmit"><i class="fa fa-save"></i> Submit</button>
                <button type="button" class="btn btn-light" onclick="hideModal('modalTransaction')"><i class="fa fa-close"></i> Cancel</button>
            </div>
        </div>
    </div>
</div>


