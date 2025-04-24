<div class = "modal fade" id = "modalPayrollAccount">
    <div class = "modal-dialog">
        <div class = "modal-content">
            <div class = "modal-header">
                <h5>Payroll Account Information</h5>
            </div>

            <div class = "modal-body">
                <div class = "row">
                    <!-- target field -->
                    <input type = "hidden" id = "payrollModalTarget" value = "">

                    <!-- bank -->
                    <div class = "col-12">
                        <label class = "w-100">
                            Bank
                            <select class = "form-select" id = "bank_id">
                                <option value = "" selected hidden disabled>-- Select --</option>
                                @if(isset($banks_data) && $banks_data)
                                    @foreach($banks_data as $key => $val)
                                        <option value = "{{$val['id']}}">{{$val['name']}}</option>
                                    @endforeach
                                @endif
                            </select>
                        </label>
                    </div>

                    <!-- account no -->
                    <div class = "col-12">
                        <label class = "w-100">
                            Account No.
                            <input type = "number"
                                id = "account_no"
                                class = "form-control"
                                placeholder = "Account No."
                            >
                        </label>
                    </div>

                    <!-- account name -->
                    <div class = "col-12">
                        <label class = "w-100">
                            Account Name
                            <input type = "text"
                                class = "form-control"
                                id = "account_name"
                                placeholder = "Account Name"

                            >
                        </label>
                    </div>

                    <!-- status -->
                    <div class = "col-12">
                        <label class = "w-100">
                            Status
                            <select class = "form-select"  id = "status">
                                <option value = "" selected hidden disabled>-- Select --</option>
                                @foreach(config('constants.status_value') as $key => $val)
                                    <option value = "{{$key}}">{{$val}}</option>
                                @endforeach
                            </select>
                        </label>
                    </div>
                </div>
            </div>

            <div class = "modal-footer">
                <button type = "button" class = "btn btn-primary btn-sm btnAdd"><i class = "fa fa-save"></i> Add</button>
                <button type = "button" class = "btn btn-danger btn-sm btnCancel"><i class = "fa fa-close"></i> Cancel</button>
            </div>
        </div>
    </div>
</div>
