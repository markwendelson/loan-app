<div class="modal fade" id="modalMultipleEntries" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog" style="max-width: 93%;" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Savings Multiple Entries</h4>
            </div>
            <div class="modal-body">
                <form id="frmMultipleSavings">
                    <div class="card">
                        <div class="card-header d-flex justify-content-end">
                            <button type="button" class="btn btn-light btnMoreInputs"><i class="fa fa-plus txt-primary"></i></button>
                        </div>
                        <div class="card-body p-0">
                            <table class="table table-bordered" id="tblMultipleSavings">
                                <thead>
                                    <tr>
                                        <th class="align-middle" width="250">Client</th>
                                        <th class="align-middle">Transaction Type</th>
                                        <th class="align-middle">Date</th>
                                        <th class="align-middle">Classification</th>
                                        <th class="align-middle">Transaction Code</th>
                                        <th class="align-middle" width="150">ATM Balance</th>
                                        <th class="align-middle" width="150">Amount Withdrawn</th>
                                        <th class="align-middle" colspan="2">Description</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>
                                            <select class="form-select selectClient">
                                                <option value="" selected hidden disabled>-- Client --</option>
                                                @if(isset($clientData) && $clientData->isNotEmpty())
                                                    @foreach($clientData as $key => $val)
                                                        <option value="{{ $val->id }}">{{ ucwords(strtolower($val->firstname)) . ' ' . ucwords(strtolower($val->lastname))  . ' ' . ucfirst(substr($val->middlename, 0, 1)) . '.' }}</option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        </td>
                                        <td>
                                            <select class="form-select selectTransactionType">
                                                <option value="" selected hidden disabled>-- Transaction Type --</option>
                                                <option value="atm_withdrawal">ATM Withdrawal</option>
                                                <option value="bank_transfer">Bank Transfer </option>
                                                <option value="palawan">Palawan</option>
                                                <option value="cash">Cash</option>
                                            </select>
                                        </td>
                                        <td>
                                            <input type="date"
                                                class="form-control inputDate"
                                            >
                                        </td>
                                        <td>
                                            <select class="form-select selectClassifications">
                                                <option value="" selected hidden disabled>-- Classification --</option>
                                                @foreach(config('constants.savings_classifications') as $key => $val)
                                                    <option value="{{ $val }}">{{ $val }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <input type="text"
                                                class="form-control inputTransactionCode"
                                                placeholder="ex. TXN123456789"
                                            >
                                        </td>
                                        <td>
                                            <input type="number"
                                                class="form-control text-end inputAtmBalance"
                                                placeholder="0.00"
                                            >
                                        </td>
                                        <td>
                                            <input type="number"
                                                class="form-control text-end inputAmountWithdrawn"
                                                placeholder="0.00"
                                            >
                                        </td>
                                        <td>
                                            <input type="text"
                                                class="form-control inputDescription"
                                                placeholder="ex. hello 123"
                                            >
                                        </td>
                                        <td class ="text-center">
                                            <a href="#" class="btnRemove"><i class="fa fa-trash text-danger fs-6"></i></a>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            
                        </div>
                    </div>
                    
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary btnSubmit"><i class="fa fa-save"></i> Submit Entries</button>
                <button type="button" class="btn btn-danger btnCancel"><i class="fa fa-times"></i> Cancel</button>
            </div>
        </div>
    </div>
</div>
