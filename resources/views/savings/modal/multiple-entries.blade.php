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
                    <div class="mb-2 rounded border p-0 client-container shadow-sm" data-id="{{ $val->id }}">
                        <div class="d-flex justify-content-between align-items-center p-2">
                            <!-- Client information -->
                            <div class="d-flex flex-column">
                                <a data-bs-toggle="collapse" href= "#" class="collapseHeader" data-target-collapse="#collapse{{ $val->id }}">
                                    <h5>{{ ucwords($val->firstname) . ' ' . ucwords($val->lastname) . ' ' . ucwords(substr($val->middlename, 0,1)) . '.' }}</h5>
                                    <span class="text-danger">{{ $val->client_code ?? ''; }}
                                </a>
                            </div>
                            <div>
                                <button type="button" class="btn btn-light btn-sm btnMoreInputs"><i class="fa fa-plus txt-primary"></i></button>
                            </div>
                        </div>

                        <div class="collapse {{ $key == 0 ? 'show' : ''; }}" id="collapse{{ $val->id }}">
                            <table class="table table-bordered table-hover border-start-0 border-end-0">
                                <thead>
                                    <tr>
                                        <th class="align-middle">Transaction Type</th>
                                        <th class="align-middle">Date</th>
                                        <th class="align-middle">Classification</th>
                                        <th class="align-middle">Transaction Code</th>
                                        <th class="align-middle">ATM Balance</th>
                                        <th class="align-middle">Amount Withdrawn</th>
                                        <th class="align-middle">Description</th>
                                        <th class="align-middle"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr data-increment="0">
                                        <td>
                                            <select class="form-select slctTransType">
                                                <option value="" selected hidden disabled>-- Select --</option>
                                                <option value="atm_withdrawal">ATM Withdrawal</option>
                                                <option value="bank_transfer">Bank Transfer</option>
                                                <option value="palawan">Palawan</option>
                                                <option value="cash">Cash</option>
                                            </select>
                                        </td>
                                        <td>
                                            <input type="date"
                                                class="form-control"
                                                name="{{ $val->id }}[0][date]"
                                            >
                                        </td>
                                        <td>
                                            <select class="form-select" name="{{ $val->id }}[0][classification]">
                                                <option value="" selected hidden disabled>-- Classification --</option>
                                                @foreach(config('constants.savings_classifications') as $key => $classifications)
                                                    <option value="{{ $classifications }}">{{ $classifications }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <input type="text"
                                                class="form-control"
                                                name="{{ $val->id }}[0][transaction_code]"
                                                placeholder="TXN123456789..."
                                            >
                                        </td>
                                        <td>
                                            <input type="number"
                                                class="form-control text-end atmBalance"
                                                name="{{ $val->id }}[0][atm_balance]"
                                                placeholder="0.00"
                                                disabled
                                            >
                                        </td>
                                        <td>
                                            <input type="number"
                                                class="form-control text-end"
                                                name="{{ $val->id }}[0][amount_withdrawn]"
                                                placeholder="0.00"
                                            >
                                        </td>
                                        <td>
                                            <input type="text"
                                                class="form-control"
                                                name="{{ $val->id }}[0][description]"
                                                placeholder="Description..."
                                            >
                                        </td>
                                        <td class="text-center">
                                            <a href="#" class="btnRemove"><i class="fa fa-trash text-danger fs-6"></i></a>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    @if(!$loop->last)
                        <hr style="border: 2px dashed !important">
                    @endif
                @endforeach
            @endif
        </form>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-primary btnSubmit"><i class="fa fa-save"></i> Submit Entries</button>
        <button type="button" class="btn btn-danger btnCancel"><i class="fa fa-times"></i> Cancel</button>
    </div>
</div>
