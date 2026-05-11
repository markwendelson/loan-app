<div class="modal-content">
    <div class="modal-header">
        <h4 class="modal-title">Savings Multiple Entries</h4>
    </div>
    <div class="modal-body">

        <h5 class="mb-2">Total Account(s) - {{ count($clientData) }}</h5>
        @if(isset($clientData) && $clientData)
            @foreach($clientData as $key => $val)
                <div class="mb-2 rounded border p-3 client-container" data-id="{{ $val->id }}">
                    <div class="d-flex justify-content-between align-items-center">
                        <!-- Client information -->
                        <div class="d-flex flex-column">
                            <a data-bs-toggle="collapse" href="#collapse{{ $val->id }}" role="button" aria-expanded="false" aria-controls="collapse{{ $val->id }}">
                                <h5>{{ ucwords($val->firstname) . ' ' . ucwords($val->lastname) . ' ' . ucwords(substr($val->middlename, 0,1)) . '.' }}</h5>
                                <span class="text-danger">{{ $val->client_code ?? ''; }}</span>
                            </a>
                        </div>
                        <div>
                            <div class="btn-group" role="group" aria-label="Basic radio toggle button group">
                                <!-- Radio buttons for each transaction type -->
                                <input type="radio" class="btn-check" name="transaction_type[{{ $val->id }}]" id="btnradioATM{{ $val->id }}" autocomplete="off" value="ATM Withdrawal" data-bs-target="#tabAtmWithdrawal{{ $val->id }}" checked>
                                <label class="btn btn-outline-primary btn-sm" for="btnradioATM{{ $val->id }}">ATM Withdrawal</label>

                                <input type="radio" class="btn-check" name="transaction_type[{{ $val->id }}]" id="btnradioBankTransfer{{ $val->id }}" autocomplete="off" value="Bank Transfer" data-bs-target="#tabBankTransfer{{ $val->id }}">
                                <label class="btn btn-outline-primary btn-sm" for="btnradioBankTransfer{{ $val->id }}">Bank Transfer</label>

                                <input type="radio" class="btn-check" name="transaction_type[{{ $val->id }}]" id="btnradioPalawan{{ $val->id }}" autocomplete="off" value="Palawan" data-bs-target="#tabPalawan{{ $val->id }}">
                                <label class="btn btn-outline-primary btn-sm" for="btnradioPalawan{{ $val->id }}">Palawan</label>

                                <input type="radio" class="btn-check" name="transaction_type[{{ $val->id }}]" id="btnradioCash{{ $val->id }}" autocomplete="off" value="Cash" data-bs-target="#tabCash{{ $val->id }}">
                                <label class="btn btn-outline-primary btn-sm" for="btnradioCash{{ $val->id }}">Cash</label>
                            </div>
                        </div>
                    </div>

                    <div class="collapse {{ $key == 0 ? 'show' : ''; }}" id="collapse{{ $val->id }}">
                        <hr class="bg-secondary-subtle my-1">
                        <div class="tab-content">
                            <!-- ATM Withdrawal Tab -->
                            <div class="tab-pane fade show active" id="tabAtmWithdrawal{{ $val->id }}" role="tabpanel" aria-labelledby="atm-withdrawal">
                                <div class="row">
                                    <!-- date -->
                                    <div class="col-lg-3">
                                        <label class="w-100">
                                            Date
                                            <input type="date" class="form-control" name="date[{{ $val->id }}][]">
                                        </label>
                                    </div>

                                    <!-- classification -->
                                    <div class="col-lg-3">
                                        <label class="w-100">
                                            Classification
                                            <select class="form-select">
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
                                                placeholder="TXN123456789..."
                                            >
                                        </label>
                                    </div>

                                    <!-- atm balance -->
                                    <div class="col-lg-3">
                                        <label class="w-100">
                                            ATM Balance
                                            <input type="number"
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
                                                class="form-control text-end"
                                                placeholder="0.00"
                                            >
                                        </label>
                                    </div>

                                    <!-- description -->
                                    <div class="col-lg-12">
                                        <label class="w-100">
                                            Description
                                            <textarea class="form-control" placeholder="Enter description here..." rows="3"></textarea>
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
                                            <input type="date" class="form-control" name="date[{{ $val->id }}][]">
                                        </label>
                                    </div>

                                    <!-- transaction code -->
                                    <div class="col-lg-3">
                                        <label class="w-100">
                                            Transaction Code
                                            <input type="text"
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
                                                class="form-control text-end"
                                                placeholder="0.00"
                                            >
                                        </label>
                                    </div>

                                    <!-- classification -->
                                    <div class="col-lg-3">
                                        <label class="w-100">
                                            Classification
                                            <select class="form-select">
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
                                            <textarea class="form-control" placeholder="Enter description here..." rows="3"></textarea>
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
                                            <input type="date" class="form-control" name="date[{{ $val->id }}][]">
                                        </label>
                                    </div>

                                    <!-- transaction code -->
                                    <div class="col-lg-3">
                                        <label class="w-100">
                                            Transaction Code
                                            <input type="text"
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
                                                class="form-control text-end"
                                                placeholder="0.00"
                                            >
                                        </label>
                                    </div>

                                    <!-- classification -->
                                    <div class="col-lg-3">
                                        <label class="w-100">
                                            Classification
                                            <select class="form-select">
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
                                            <textarea class="form-control" placeholder="Enter description here..." rows="3"></textarea>
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
                                            <input type="date" class="form-control" name="date[{{ $val->id }}][]">
                                        </label>
                                    </div>

                                    <!-- transaction code -->
                                    <div class="col-lg-3">
                                        <label class="w-100">
                                            Transaction Code
                                            <input type="text"
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
                                                class="form-control text-end"
                                                placeholder="0.00"
                                            >
                                        </label>
                                    </div>

                                    <!-- classification -->
                                    <div class="col-lg-3">
                                        <label class="w-100">
                                            Classification
                                            <select class="form-select">
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
                                            <textarea class="form-control" placeholder="Enter description here..." rows="3"></textarea>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    

                </div>

                
            @endforeach

            
        @endif
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-primary btnSubmit"><i class="fa fa-save"></i> Submit Entries</button>
        <button type="button" class="btn btn-danger btnCancel"><i class="fa fa-times"></i> Cancel</button>
    </div>
</div>
