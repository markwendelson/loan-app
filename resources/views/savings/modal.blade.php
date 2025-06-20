
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
                                        <option value="deposit">deposit</option>
                                        <option value="withdraw">withdraw</option>
                                    </select>
                                </div>
                            </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label class="form-label" for="amount">Amount</label>
                                <input type="number" class="form-control form-control-sm" name="amount" placeholder="Amount" required min="0"/>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label class="form-label" for="classifications">Classifications</label>
                                <select class="form-control form-control-sm" name="classifications" id="classifications">
                                    <option value="">-select-</option>
                                    <option value="1Q">1st Quincena</option>
                                    <option value="2Q">2nd Quincena</option>
                                    <option value="P">P</option>
                                    <option value="D">D</option>
                                    <option value="F">F</option>
                                    <option value="S">S</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label class="form-label" for="description">Description</label>
                                <input class="form-control form-control-sm" type="text" name="description" id="description" placeholder="Description"/>
                            </div>
                        </div>
                        <div class="withdraw">
                            <div class="row mb-3">
                                <div class="col-12 form-group">
                                    <label for="loan_id">Select Loan</label>
                                    <select name="loan_id" id="loan_id" class="form-control form-control-sm">
                                        <option value="">-select-</option>
                                        @foreach($loans as $loan)
                                            <option value="{{$loan->id}}" 
                                                data-loan-product-id="{{$loan->loan_product_id}}"
                                                data-principal="{{$loan->principal}}"
                                                data-interest-rate="{{$loan->interest_rate}}"
                                                data-term="{{$loan->loan_duration}}"
                                                data-loan-type="{{Str::slug($loan->loanProduct->loanType->name)}}"
                                            >{{$loan->loan_no}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4 form-group">
                                    <label class="form-label" for="interest">Interest</label>
                                    <input type="number" class="form-control form-control-sm" name="interest" placeholder="0" min="0"/>
                                </div>
                                <div class="col-md-4 form-group">
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
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-4 form-group">
                                    <label class="form-label" for="insurance">Insurance</label>
                                    <input type="number" class="form-control form-control-sm" name="insurance" placeholder="0" min="0"/>
                                </div>
                                <div class="col-md-4 form-group">
                                    <label class="form-label" for="insurance_month" class="form-label">Month</label>
                                    <select name="insurance_month" id="insurance_month" class="form-control form-control-sm">
                                        <option value="" disabled>Select</option>
                                        @foreach (month_names() as $number => $name)
                                            <option value="{{ $number }}">{{ $name }}</option>
                                        @endforeach

                                    </select>
                                </div>
                                <div class="col-md-4 form-group">
                                    <label class="form-label" for="insurance_year" class="form-label">Year</label>
                                    <input type="text" class="form-control form-control-sm" id="insurance_year" name="insurance_year">
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6 form-group">
                                    <label class="form-label" for="principal">Principal</label>
                                    <input type="number" class="form-control form-control-sm" name="principal" placeholder="0" min="0"/>
                                </div>
                                <div class="col-md-6 form-group">
                                    <label class="form-label" for="change">Change</label>
                                    <input type="number" class="form-control form-control-sm" name="change" placeholder="0" readonly min="0"/>
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


