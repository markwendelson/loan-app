<div class="modal-content">
    <div class="modal-header">
        <h4 class="modal-title">Savings Multiple Entries</h4>
    </div>
    <div class="modal-body">
        <form id="frmMultipleSavings">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <div class="align-middle">
                    <h5 class="">Accounts - {{ count($clientData) }}</h5>
                </div>
                
                <div class="form-check checkbox checkbox-primary mb-0">
                    <input class="form-check-input chckAll" id="chckAutoHide" type="checkbox" value="" checked>
                    <label class="form-check-label mb-1" for="chckAutoHide"><small>Auto Hide</small></label>
                </div>
            </div>

            @if(isset($clientData) && $clientData)
                @foreach($clientData as $key => $val)
                    <div class="mb-2 rounded border p-3 client-container" data-id="{{ $val->id }}">
                        <div class="d-flex justify-content-between align-items-center">
                            <!-- Client information -->
                            <div class="d-flex flex-column">
                                <a data-bs-toggle="collapse" href= "#" class="collapseHeader" data-target-collapse="#collapse{{ $val->id }}">
                                    <h5>{{ ucwords($val->firstname) . ' ' . ucwords($val->lastname) . ' ' . ucwords(substr($val->middlename, 0,1)) . '.' }}</h5>
                                    <span class="text-danger">{{ $val->client_code ?? ''; }}
                                </a>
                            </div>
                            <div>
                                <div class="btn-group" role="group" aria-label="Basic radio toggle button group">
                                    <!-- Radio buttons for each transaction type -->
                                    <input type="radio" class="btn-check" name="{{ $val->id }}[transaction_type]" id="btnradioATM{{ $val->id }}" autocomplete="off" value="atm_withdrawal" data-bs-target="#tabAtmWithdrawal{{ $val->id }}" checked>
                                    <label class="btn btn-outline-primary btn-sm" for="btnradioATM{{ $val->id }}">ATM Withdrawal</label>

                                    <input type="radio" class="btn-check" name="{{ $val->id }}[transaction_type]" id="btnradioBankTransfer{{ $val->id }}" autocomplete="off" value="bank_transfer" data-bs-target="#tabBankTransfer{{ $val->id }}">
                                    <label class="btn btn-outline-primary btn-sm" for="btnradioBankTransfer{{ $val->id }}">Bank Transfer</label>

                                    <input type="radio" class="btn-check" name="{{ $val->id }}[transaction_type]" id="btnradioPalawan{{ $val->id }}" autocomplete="off" value="palawan" data-bs-target="#tabPalawan{{ $val->id }}">
                                    <label class="btn btn-outline-primary btn-sm" for="btnradioPalawan{{ $val->id }}">Palawan</label>

                                    <input type="radio" class="btn-check" name="{{ $val->id }}[transaction_type]" id="btnradioCash{{ $val->id }}" autocomplete="off" value="cash" data-bs-target="#tabCash{{ $val->id }}">
                                    <label class="btn btn-outline-primary btn-sm" for="btnradioCash{{ $val->id }}">Cash</label>
                                </div>
                            </div>
                        </div>

                        <div class="collapse {{ $key == 0 ? 'show' : ''; }}" id="collapse{{ $val->id }}">
                            {{-- <hr class="bg-secondary-subtle my-1"> --}}
                            <div class="tab-content border-top pt-2 mt-2">
                                <!-- ATM Withdrawal Tab -->
                                <div class="tab-pane fade show active" id="tabAtmWithdrawal{{ $val->id }}" role="tabpanel" aria-labelledby="atm-withdrawal">
                                    <div class="row">
                                        <!-- date -->
                                        <div class="col-lg-3">
                                            <label class="w-100">
                                                Date
                                                <input type="date" class="form-control" name="{{ $val->id }}[atm_withdrawal][date]">
                                            </label>
                                        </div>

                                        <!-- classification -->
                                        <div class="col-lg-3">
                                            <label class="w-100">
                                                Classification
                                                <select class="form-select" name="{{ $val->id }}[atm_withdrawal][classification]">
                                                    <option value="" selected hidden disabled>-- Classification --</option>
                                                    @foreach(config('constants.savings_classifications') as $key => $classifications)
                                                        <option value="{{ $classifications }}">{{ $classifications }}</option>
                                                    @endforeach
                                                </select>
                                            </label>
                                        </div>

                                        <div class="clearfix"></div>

                                        <!-- transaction code -->
                                        <div class="col-lg-3">
                                            <label class="w-100">
                                                Transaction Code
                                                <input type="text"
                                                    class="form-control"
                                                    name="{{ $val->id }}[atm_withdrawal][transaction_code]"
                                                    placeholder="TXN123456789..."
                                                >
                                            </label>
                                        </div>

                                        <!-- atm balance -->
                                        <div class="col-lg-3">
                                            <label class="w-100">
                                                ATM Balance
                                                <input type="number"
                                                    name="{{ $val->id }}[atm_withdrawal][atm_balance]"
                                                    class="form-control text-end"
                                                    placeholder="0.00"
                                                >
                                            </label>
                                        </div>

                                        <!-- amount -->
                                        <div class="col-lg-3">
                                            <label class="w-100">
                                                Amount Withdrawn
                                                <input type="number"
                                                    name="{{ $val->id }}[atm_withdrawal][amount_withdrawn]"
                                                    class="form-control text-end"
                                                    placeholder="0.00"
                                                >
                                            </label>
                                        </div>

                                        <!-- description -->
                                        <div class="col-lg-12">
                                            <label class="w-100">
                                                Description
                                                <textarea class="form-control" 
                                                    name="{{ $val->id }}[atm_withdrawal][description]"
                                                    placeholder="Enter description here..." 
                                                    rows="3"
                                                ></textarea>
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <!-- Bank Transfer Tab -->
                                <div class="tab-pane fade" id="tabBankTransfer{{ $val->id }}" role="tabpanel" aria-labelledby="bank-transfer">
                                    <div class="row">
                                        <!-- date -->
                                        <div class="col-lg-3">
                                            <label class="w-100">
                                                Date
                                                <input type="date" 
                                                    class="form-control" 
                                                    name="{{ $val->id }}[bank_transfer][date]"
                                                >
                                            </label>
                                        </div>

                                        <!-- transaction code -->
                                        <div class="col-lg-3">
                                            <label class="w-100">
                                                Transaction Code
                                                <input type="text"
                                                    name="{{ $val->id }}[bank_transfer][transaction_code]"
                                                    class="form-control"
                                                    placeholder="TXN123456789..."
                                                >
                                            </label>
                                        </div>

                                        <div class="clearfix"></div>

                                        <!-- amount -->
                                        <div class="col-lg-3">
                                            <label class="w-100">
                                                Amount Withdrawn
                                                <input type="number"
                                                    name="{{ $val->id }}[bank_transfer][amount_withdrawn]"
                                                    class="form-control text-end"
                                                    placeholder="0.00"
                                                >
                                            </label>
                                        </div>

                                        <!-- classification -->
                                        <div class="col-lg-3">
                                            <label class="w-100">
                                                Classification
                                                <select class="form-select" name="{{ $val->id }}[bank_transfer][classification]">
                                                    <option value="" selected hidden disabled>-- Classification --</option>
                                                    @foreach(config('constants.savings_classifications') as $key => $classifications)
                                                        <option value="{{ $classifications }}">{{ $classifications }}</option>
                                                    @endforeach
                                                </select>
                                            </label>
                                        </div>

                                        <!-- description -->
                                        <div class="col-lg-12">
                                            <label class="w-100">
                                                Description
                                                <textarea class="form-control" 
                                                    name="{{ $val->id }}[bank_transfer][description]"
                                                    placeholder="Enter description here..." 
                                                    rows="3"
                                                ></textarea>
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <!-- Palawan Tab -->
                                <div class="tab-pane fade" id="tabPalawan{{ $val->id }}" role="tabpanel" aria-labelledby="palawan">
                                    <div class="row">
                                        <!-- date -->
                                        <div class="col-lg-3">
                                            <label class="w-100">
                                                Date
                                                <input type="date" 
                                                    class="form-control" 
                                                    name="{{ $val->id }}[palawan][date]"
                                                >
                                            </label>
                                        </div>

                                        <!-- transaction code -->
                                        <div class="col-lg-3">
                                            <label class="w-100">
                                                Transaction Code
                                                <input type="text"
                                                    name="{{ $val->id }}[palawan][transaction_code]"
                                                    class="form-control"
                                                    placeholder="TXN123456789..."
                                                >
                                            </label>
                                        </div>

                                        <div class="clearfix"></div>

                                        <!-- amount -->
                                        <div class="col-lg-3">
                                            <label class="w-100">
                                                Amount Withdrawn
                                                <input type="number"
                                                    name="{{ $val->id }}[palawan][amount_withdrawn]"
                                                    class="form-control text-end"
                                                    placeholder="0.00"
                                                >
                                            </label>
                                        </div>

                                        <!-- classification -->
                                        <div class="col-lg-3">
                                            <label class="w-100">
                                                Classification
                                                <select class="form-select" name="{{ $val->id }}[palawan][classification]">
                                                    <option value="" selected hidden disabled>-- Classification --</option>
                                                    @foreach(config('constants.savings_classifications') as $key => $classifications)
                                                        <option value="{{ $classifications }}">{{ $classifications }}</option>
                                                    @endforeach
                                                </select>
                                            </label>
                                        </div>

                                        <!-- description -->
                                        <div class="col-lg-12">
                                            <label class="w-100">
                                                Description
                                                <textarea class="form-control" 
                                                    name="{{ $val->id }}[palawan][description]"
                                                    placeholder="Enter description here..." 
                                                    rows="3"
                                                ></textarea>
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <!-- Cash Tab -->
                                <div class="tab-pane fade" id="tabCash{{ $val->id }}" role="tabpanel" aria-labelledby="cash">
                                    <div class="row">
                                        <!-- date -->
                                        <div class="col-lg-3">
                                            <label class="w-100">
                                                Date
                                                <input type="date" 
                                                    class="form-control" 
                                                    name="{{ $val->id }}[cash][date]"
                                                >
                                            </label>
                                        </div>

                                        <!-- transaction code -->
                                        <div class="col-lg-3">
                                            <label class="w-100">
                                                Transaction Code
                                                <input type="text"
                                                    name="{{ $val->id }}[cash][transaction_code]"
                                                    class="form-control"
                                                    placeholder="TXN123456789..."
                                                >
                                            </label>
                                        </div>

                                        <div class="clearfix"></div>

                                        <!-- amount -->
                                        <div class="col-lg-3">
                                            <label class="w-100">
                                                Amount Withdrawn
                                                <input type="number"
                                                    name="{{ $val->id }}[cash][amount_withdrawn]"
                                                    class="form-control text-end"
                                                    placeholder="0.00"
                                                >
                                            </label>
                                        </div>

                                        <!-- classification -->
                                        <div class="col-lg-3">
                                            <label class="w-100">
                                                Classification
                                                <select class="form-select" name="{{ $val->id }}[cash][classification]">
                                                    <option value="" selected hidden disabled>-- Classification --</option>
                                                    @foreach(config('constants.savings_classifications') as $key => $classifications)
                                                        <option value="{{ $classifications }}">{{ $classifications }}</option>
                                                    @endforeach
                                                </select>
                                            </label>
                                        </div>

                                        <!-- description -->
                                        <div class="col-lg-12">
                                            <label class="w-100">
                                                Description
                                                <textarea class="form-control" 
                                                    name="{{ $val->id }}[cash][description]"
                                                    placeholder="Enter description here..." 
                                                    rows="3"
                                                ></textarea>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach

                
            @endif
        </form>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-primary btnSubmit"><i class="fa fa-save"></i> Submit Entries</button>
        <button type="button" class="btn btn-danger btnCancel"><i class="fa fa-times"></i> Cancel</button>
    </div>
</div>
